<?php
/**
 * Attogram Framework
 *
 * The Attogram Framework provides developers a skeleton starter site
 * with file-based URL routing, IP-protected backend, simple user system,
 * integrated SQLite database with phpLiteAdmin, Markdown parser, jQuery and Bootstrap.
 * Attogram is Dual Licensed under the The MIT License or the GNU General Public License, at your choosing.
 *
 * @version 0.5.0-dev-module
 * @license MIT
 * @license GPL
 * @copyright 2016 Attogram Developers https://github.com/attogram/attogram
 */

namespace Attogram;

define('ATTOGRAM_VERSION', '0.5.0-dev-module');

$attogram = new attogram();

class attogram_utils {

  public $debug, $log, $skip_files;

  function __construct() {
    $this->debug = FALSE;
    $this->log = new Logger(); // logger for startup tasks
    $this->skip_files = array('.','..','.htaccess');
  }

  /**
   * get_all_subdirectories()
   */
  function get_all_subdirectories( $dir, $name ) {
    //$this->log->debug('get_all_subdirectories: scanning for: ' . $dir . '/*/' . $name);
    if( !isset($dir) || !$dir || !is_string($dir)) {
      $this->log->error('get_all_subdirectories: UNDEFINED dir');
      return array();
    }
    if( !is_dir($dir) || !is_readable($dir) ) {
      $this->log->error('get_all_subdirectories: UNREADABLE dir=' . $dir);
      return array();
    }
    $r = array();
    foreach( array_diff(scandir($dir), $this->skip_files) as $d ) {
      //$this->log->debug('get_all_subdirectories: checking d='. $d);
      $md = $dir . '/' . $d;
      if( !is_dir($md) ) { continue; }
      $md .= '/' . $name;
      //$this->log->debug('get_all_subdirectories: checking '. $md);
      if( !is_dir($md) || !is_readable($md) ) { continue; }
      //$this->log->debug('get_all_subdirectories: OK: ' . $md);
      $r[] = $md;
    }
    return $r;
  } // end function get_all_subdirectories()

  /**
   * include_all_php_files_in_directory()
   * @param string $dir The directory to search
   */
  function include_all_php_files_in_directory( $dir ) {
    if( !is_dir($dir) || !is_readable($dir)) {
      $this->log->error('include_all_php_files_in_directory: Directory not found: ' . $dir);
      return;
    }
    //$this->log->debug('include_all_php_files_in_directory: dir = ' . $dir);
    foreach( array_diff(scandir($dir), $this->skip_files) as $f ) {
      $ff = $dir . '/' . $f;
      if( $this->is_readable_file($ff,'.php') ) {
        $this->log->debug('include: ' . $ff);
        include_once($ff);
      } else {
        $this->log->error('include_all_php_files_in_directory: can not include: ' . $ff);
      }
    }
  } // end function include_all_php_files_in_directory()

  /**
   * is_readable_file() - Tests if is a file exist, is readable, and is of a certain type.
   *
   * @param string $file The name of the file to test
   * @param string $type (optional) The file extension to allow. Defaults to '.php'
   *
   * @return boolean
   */
  function is_readable_file( $file=FALSE, $type='.php' ) {
    if( !$file || !is_file($file) || !is_readable($file) ) {
      return FALSE;
    }
    if( !$type || $type == '' || !is_string($type) ) { // error
      return FALSE;
    }
    if( preg_match('/' . $type . '$/',$file) ) {
      return TRUE;
    }
    return FALSE;
  }

} // end class attogram_utils


/**
 * Attogram Framework
 */
class attogram extends attogram_utils {

  public $attogram_directory, $modules_dir, $templates_dir, $autoloader;
  public $site_name, $depth, $force_slash_exceptions, $fof;
  public $request, $host, $clientIp, $pathInfo, $requestUri, $path, $uri, $session;
  public $db, $db_name;
  public $actions, $action, $admins, $is_admin, $admin_actions, $admin_dir;

  /**
   * __construct() - startup Attogram!
   *
   * @return void
   */
  function __construct() {

    parent::__construct();

    $this->log->debug('START Attogram v' . ATTOGRAM_VERSION);

    $this->load_config('config.php');  // Load the main configuration file

    //$this->__DIR__ = __DIR__; // dev
    //$this->log->debug('__DIR__ = ' . $this->__DIR__); // dev

    $this->autoloader(); // load up all the vendor goodies!

    $this ->init_logger(); // Start full logging via monolog

    // \Symfony\Component\Debug\Debug::enable(); // dev - kills phpLiteAdmin
    // \Symfony\Component\Debug\ErrorHandler::register(); // dev
    // \Symfony\Component\Debug\ExceptionHandler::register(); // dev
    // \Symfony\Component\Debug\DebugClassLoader::enable(); // dev

    $this->set_request(); // set all the request-related variables we need
    $this->exception_files(); // do robots.txt, sitemap.xml
    $this->end_slash(); // force slash at end, or force no slash at end

    $this->set_uri();

    $this->check_depth(); // is URI short enough?

    $this->get_functions(); // load any files in ./functions/

    $this->db = new sqlite_database(
      $this->db_name,
      $this->modules_dir,
      $this->log,
      $this->debug
    );

    $this->sessioning();

    $this->route();

    $this->log->debug('END Attogram v' . ATTOGRAM_VERSION);

  } // end function __construct()

  function set_request() {
    $this->request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
    $this->host = $this->request->getHost();
    $this->clientIp = $this->request->getClientIp();
    $this->log->debug("host: $this->host  IP: $this->clientIp");
    $this->pathInfo = $this->request->getPathInfo();
    $this->requestUri = $this->request->getRequestUri();
    $this->path = $this->request->getBasePath();
  }

  function set_uri() {
    $this->uri = explode('/', $this->pathInfo);
    $this->log->debug('raw uri:', $this->uri);
    $trash = array_shift($this->uri); // take off first entry
    if( $this->uri[sizeof($this->uri) - 1] != '' ) {
      $this->uri[] = '';  // pretend there is a slash at end
    }
    $this->log->debug('uri:',$this->uri);
  }

  function end_slash() {
    if( !preg_match('/\/$/', $this->pathInfo)) { // No slash at end of url
      if( is_array($this->force_slash_exceptions) && !in_array( $this->uri[0], $this->force_slash_exceptions ) ) {
         // This action IS NOT excepted from force slash at end
        $url = str_replace($this->pathInfo, $this->pathInfo . '/', $this->requestUri);
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $url ); // Force Trailing Slash
        exit;
      }
    } else { // Yes slash at end of url
      if( is_array($this->force_slash_exceptions) && in_array( $this->uri[0], $this->force_slash_exceptions ) ) {
        // This action IS excepted from force slash at end
        $url = str_replace($this->pathInfo, rtrim($this->pathInfo, ' /'), $this->requestUri);
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $url ); // Remove Trailing Slash
        exit;
      }
    }
  }

  function check_depth() {
    $depth = $this->depth['*']; // default depth
    if( isset($this->depth[$this->uri[0]]) ) {
      $depth = $this->depth[$this->uri[0]];
    }
    if( $depth < sizeof($this->uri)) {
      $this->log->error('URI Depth ERROR. uri=' . sizeof($this->uri) . ' allowed=' . $depth);
      $this->error404('No Swimming in the deep end');
    }
  }

  /**
   * load_config() - load the system configuration file
   *
   * @param string $config_file
   *
   * @return void
   */
  function load_config( $config_file='' ) {

    // Load the main configuration file, if available
    $this->log->debug('include main config: ' . $config_file);
    if( !$this->is_readable_file($config_file, '.php') ) {
      $this->log->notice('LOAD_CONFIG: config file not found, using defaults.');
    } else {
      include_once($config_file);
      if( !isset($config) || !is_array($config) ) {
        $this->log->notice('LOAD_CONFIG: $config array not found, using defaults');
      }
    }

    // Set installation, directory and file locations
    $this->set_config('attogram_directory', @$config['attogram_directory'], '../');
    $this->modules_dir = $this->attogram_directory . 'modules';
    $this->templates_dir = $this->attogram_directory . 'templates';
    $this->autoloader = $this->attogram_directory . 'vendor/autoload.php';
    $this->fof = $this->attogram_directory . 'templates/404.php';
    $this->db_name = $this->attogram_directory . 'db/global';

    $this->load_module_configs(); // Load modules configuration files, if available

    // Set configuration variables
    $this->set_config('debug', @$config['debug'], FALSE);
    $this->set_config('site_name', @$config['site_name'], 'Attogram Framework <small>v' . ATTOGRAM_VERSION . '</small>');
    $this->set_config('admins', @$config['admins'], array('127.0.0.1','::1'));
    $this->set_config('depth', @$config['depth'], array('*'=>2,''=>1)); // default depth 2, homepage depth 1
    if( !isset($this->depth['*']) ) { $this->depth['*'] = 2; } // set default depth, if not found
    if( !isset($this->depth['']) ) { $this->depth[''] = 1; } // set homepage depth, if not found
    $this->set_config('force_slash_exceptions', @$config['force_slash_exceptions'], array() );
    $this->set_config('autoloader', @$config['autoloader'], $this->autoloader );

    // admin debug overrride?
    if( isset($_GET['debug']) && $this->is_admin() ) {
      $this->debug = TRUE;
      $this->log->debug('Admin Debug turned ON');
    }

  } // end function load_config()

  /**
   * set_config() - set a system configuration variable
   *
   * @param string $var_name
   * @param string $config_val
   * @param string $default_val
   *
   * @return void
   */
  function set_config( $var_name, $config_val='', $default_val ) {
    if( $config_val ) {
      $this->{$var_name} = $config_val;
    } else {
      $this->{$var_name} = $default_val;
    }
    $this->log->debug('SET ' . $var_name . ' = ' . print_r($this->{$var_name},1));
  }

  /**
   * load_module_configs()
   * Examines each module for a directory named 'configs'
   * and loads all *.php files in that directory
   * @return void
   */
  function load_module_configs() {
    $dirs = $this->get_all_subdirectories( $this->modules_dir, 'configs');
    $this->log->debug('load_module_configs', $dirs);
    if( !$dirs ) {
      $this->log->debug('load_module_configs: No module configs found');
    }
    foreach( $dirs as $d ) {
      //$this->log->debug('load_module_configs: d='. $d);
      $this->include_all_php_files_in_directory( $d );
    }
  } // end function load_module_configs()

  /**
   * autoloader() - auto load, or display fatal error
   */
  function autoloader() {
    $error = $missing = '';
    if( isset($this->autoloader) && $this->is_readable_file($this->autoloader,'.php') ) {
      include_once($this->autoloader);
      $check = array(
        '\Symfony\Component\HttpFoundation\Request', // REQUIRED
        '\Symfony\Component\HttpFoundation\Session\Session', // REQUIRED
        //'\Monolog\Logger',  // Optional
        //'\Monolog\Handler\StreamHandler',  // Optional
        //'\Monolog\Formatter\LineFormatter',  // Optional
        //'\Monolog\Handler\BufferHandler',  // Optional
        //'Parsedown',  // Optional
      );
      foreach( $check as $c ) {
        if( !class_exists($c) ) {
          $error = 'Required class not found:';
          $missing[] = $c;
        }
      }
    } else {
      $error = 'vendor autoloader not found:';
      $missing[] = $this->autoloader;
    }
    if( !$error ){
      $this->log->debug('autoloader success');
      return;
    }
    $fix = 'Maybe run composer?  Or download and install the '
    . '<a href="https://github.com/attogram/attogram-vendor/archive/master.zip">attogram-vendor</a> package.';
    $this->guru_meditation_error( $error, $missing, $fix );
  }

  /**
   * init_logger() - initialize the logger object, based on debug setting
   */
  function init_logger() {
    if( isset($this->log->stack) ) {
      $saved_stack = $this->log->stack; // save any startup logs
    }

    if( $this->debug && class_exists('\Monolog\Logger') ) {
      $this->log = new \Monolog\Logger('attogram');
      $sh = new \Monolog\Handler\StreamHandler('php://output');
      $format = "<p class=\"small text-danger\" style=\"padding:0;margin:0;\">SYS|%datetime%|%level_name%: %message% %context%</p>"; // %extra%
      $dateformat = 'Y-m-d|H:i:s:u';
      $sh->setFormatter( new \Monolog\Formatter\LineFormatter($format, $dateformat) );
      $this->log->pushHandler( new \Monolog\Handler\BufferHandler($sh) );
      //$this->log->pushHandler( new \Monolog\Handler\BrowserConsoleHandler ); // dev
      $load_saved_stack = TRUE;
    } else {
      if( !isset($this->log) ) {
        $this->log = new logger();
        $load_saved_stack = TRUE;
      } else {
        $load_saved_stack = FALSE;
      }
    }
    if( isset($saved_stack) && $load_saved_stack) {
      foreach( $saved_stack as $event) {
        $this->log->debug('STARTUP: ' . $event);
      }
    }
  }

  /**
   * sessioning() - start the session, logoff if requested
   *
   * @return void
   */
  function sessioning() {
    $this->session = new \Symfony\Component\HttpFoundation\Session\Session();
    $this->session->start();
    $this->log->debug('Session started.', $this->session->all());
    if( isset($_GET['logoff']) ) {
      $this->session->invalidate();
      $this->log->info('User loggged off');
    }
  }

  /**
   * route() - decide what action to take based on URI request
   *
   * @return void
   */
  function route() {

    if( is_dir($this->uri[0]) ) {  // requesting a directory?
      $this->log->error('ROUTE: 403 Action Forbidden');
      $this->error404('No spelunking allowed');
    }

    if( $this->uri[0] == '' ) { // The Homepage
      $this->uri[0] = 'home';
    }

    $this->log->debug('action: uri[0]: ' . $this->uri[0]);

    $actions = $this->get_actions();

    if( $this->is_admin() ) {
        $actions = array_merge($actions, $this->get_admin_actions());
    }

    if( isset($actions[$this->uri[0]]) ) {
      switch( $actions[$this->uri[0]]['parser'] ) {
        case 'php':
          $this->action = $actions[$this->uri[0]]['file'];
          if( !is_file($this->action) ) {
            $this->log->error('ROUTE: Missing action');
            $this->error404('Attempted actionless');
          }
          if( !is_readable($this->action) ) {
            $this->log->error('ROUTE: Unreadable action');
            $this->error404('The pages of the book are blank');
          }
          $this->log->debug('include ' . $this->action);
          include($this->action);
          return;
        case 'md':
          $this->do_markdown( $actions[$this->uri[0]]['file'] );
          return;
        default:
          $this->log->error('ACTION: No Parser Found');
          $this->error404('No Way Out');
          break;
      } // end switch on parser
    } //end if action set
    $this->log->error('ACTION: Action not found.  action=' . @$actions[$this->uri[0]]);
    $this->error404('This is not the action you are looking for');
  } // end function route()

  /**
   * exception_files() - checks URI for exception files sitemap.xml, robots.txt
   *
   * @return void
   */
  function exception_files() {

    switch( $this->requestUri ) {

      case '/robots.txt':
        header('Content-Type: text/plain');
        print 'Sitemap: ' . $this->get_site_url() . '/sitemap.xml';
        exit;

      case '/sitemap.xml':
        $site = $this->get_site_url() . '/';
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        $sitemap .= ' <url><loc>' . $site . '</loc></url>' . "\n";
        foreach( array_keys($this->get_actions()) as $action ){
          if( $action == 'home' || $action == 'user' ) { continue; }
          $sitemap .= ' <url><loc>' . $site . $action . '/</loc></url>' . "\n";
        }
        $sitemap .= '</urlset>';
        header ('Content-Type:text/xml');
        print $sitemap;
        exit;
    }
  }

  /**
   * do_markdown() - parse and display a Markdown document
   *
   * @param string $file The markdown file to load
   *
   * @return void
   */
  function do_markdown( $file ) {
    $title = $content = '';
    if( $this->is_readable_file($file, '.md' ) ) {
      $page = @file_get_contents($file);
      if( $page === FALSE ) {
          $this->log->error('DO_MARKDOWN: can not get file');
      } else {
        if( class_exists('Parsedown') ) {
          $title = trim( strtok($page, "\n") ); // get first line of file, use as page title
          $content = \Parsedown::instance()->text( $page );
        } else {
          $this->log->error('DO_MARKDOWN: can not find parser');
        }
      }
    } else {
      $this->log->error('DO_MARKDOWN: can not read file');
    }
    $this->page_header($title);
    print '<div class="container">' . $content . '</div>';
    $this->page_footer();
    $this->log->debug('do_markdown ' . $file);
    exit;
  }

  /**
   * get_site_url()
   *
   * @return string
   */
  function get_site_url() {
    return $this->request->getSchemeAndHttpHost() . $this->path;
  }

  /**
   * error404() - display a 404 error page to user and exit
   *
   * @return void
   */
  function error404( $error='' ) {
    header('HTTP/1.0 404 Not Found');
    if( $this->is_readable_file($this->fof, '.php') ) {
      include($this->fof);
      exit;
    }
    // Default 404 page
    $this->log->error('ERROR404: 404 template not found');
    $this->page_header('404 Not Found');
    print '<div class="container"><h1>404 Not Found</h1>';
    if( $error ) {
      print '<p>' . htmlentities($error) . '</p>';
    }
    print '</div>';
    $this->page_footer();
    exit;
  }

  /**
   * page_header() - the web page header
   *
   * @param string $title The web page title
   *
   * @return void
   */
  function page_header( $title='' ) {
    $file = $this->templates_dir . '/header.php';
    if( $this->is_readable_file($file,'.php') ) {
      include($file);
      $this->log->debug('page_header, title: ' . $title);
      return;
    }
    // Default page header
    print '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8">'
    . '<meta name="viewport" content="width=device-width, initial-scale=1">'
    . '<title>' . $title . '</title></head><body>';
    $this->log->error('missing page_header ' . $file . ' - using default header');
  }

  /**
   * page_footer() - the web page footer
   *
   * @return void
   */
  function page_footer() {
    $file = $this->templates_dir . '/footer.php';
    if( $this->is_readable_file($file,'.php') ) {
      include($file);
      $this->log->debug('page_footer');
      return;
    }
    // Default page footer
    print '<hr /><p>Powered by <a href="https://github.com/attogram/attogram">Attogram v' . ATTOGRAM_VERSION . '</a></p>';
    print '</body></html>';
    $this->log->error('missing page_footer ' . $file . ' - using default footer');
  }

  /**
   * get_actions() - create list of all pages from the actions directory
   *
   * @return array
   */
  function get_actions() {
    if( is_array($this->actions) ) {
      return $this->actions;
    }
    $dirs = $this->get_all_subdirectories( $this->modules_dir, 'actions');
    //$this->log->debug('get_action:', $dirs);
    if( !$dirs ) {
      $this->log->debug('get_actions: No module actions found');
    }
    $this->actions = array();
    foreach( $dirs as $d ) {
      //$this->log->debug('get_actions: d='. $d);
      $this->actions = array_merge($this->actions, $this->get_actionables($d) );
    }
    asort($this->actions);
    $this->log->debug('get_actions: ', array_keys($this->actions));
    return $this->actions;
  } // end function get_actions()

  /**
   * get_admin_actions() - create list of all admin pages from the admin directory
   *
   * @return array
   */
  function get_admin_actions() {
    if( is_array($this->admin_actions) ) {
      return $this->admin_actions;
    }
    $dirs = $this->get_all_subdirectories( $this->modules_dir, 'admin');
    //$this->log->debug('get_admin_actions:', $dirs);
    if( !$dirs ) {
      $this->log->debug('get_admin_actions: No module admin actions found');
    }
    $this->admin_actions = array();
    foreach( $dirs as $d ) {
      //$this->log->debug('get_admin_actions: d='. $d);
      $this->admin_actions = array_merge($this->admin_actions, $this->get_actionables($d) );
    }
    asort($this->admin_actions);
    $this->log->debug('get_admin_actions: ', array_keys($this->admin_actions));
    return $this->admin_actions;
  } // end function get_admin_actions()

  /**
   * get_actionables - create list of all useable action files from a directory
   *
   * @return array
   */
  function get_actionables( $dir ) {
    $r = array();
    if( !is_readable($dir) ) {
      $this->log->error('GET_ACTIONABLES: directory not readable: ' . $dir);
      return $r;
    }
    foreach( array_diff(scandir($dir), $this->skip_files) as $f ) {
      $file = $dir . "/$f";
      if( $this->is_readable_file($file, '.php') ) { // PHP files
        $r[ str_replace('.php','',$f) ] = array( 'file'=>$file, 'parser'=>'php' );
      } elseif( $this->is_readable_file($file, '.md') ) { // Markdown files
        $r[ str_replace('.md','',$f) ] = array( 'file'=>$file, 'parser'=>'md'
        );
      }
    }
    return $r;
  }

  /**
   * get_functions() - include all PHP files in from the functions directory
   *
   * @return void
   */
  function get_functions() {
    $dirs = $this->get_all_subdirectories( $this->modules_dir, 'functions');
    //$this->log->debug('get_functions', $dirs);
    if( !$dirs ) {
      $this->log->debug('get_functions: No module functions found');
    }
    foreach( $dirs as $d ) {
      //$this->log->debug('get_functions: d='. $d);
      $this->include_all_php_files_in_directory( $d );
    }
  } // end function get_functions()

  /**
   * is_admin() - is access from an admin IP?
   *
   * @return boolean
   */
  function is_admin() {
    if( isset($this->is_admin) && is_bool($this->is_admin) ) {
      return $this->is_admin;
    }
    if( isset($_GET['noadmin']) ) {
      $this->is_admin = FALSE;
      $this->log->debug('is_admin FALSE - noadmin override');
      return FALSE;
    }
    if( !isset($this->admins) || !is_array($this->admins) ) {
      $this->is_admin = FALSE;
      $this->log->error('is_admin FALSE - missing $this->admins  array');
      return FALSE;
    }
    if( is_object($this->request) ) {
      $cip = $this->request->getClientIp();
    } else {
      $cip = $_SERVER['REMOTE_ADDR'];
    }
    if( @in_array($cip,$this->admins) ) {
      $this->is_admin = TRUE;
      $this->log->debug('is_admin TRUE ' . $cip);
      return TRUE;
    }
    $this->is_admin = FALSE;
    $this->log->debug('is_admin FALSE ' . $cip);
    return FALSE;
  }

  /**
   * login() - login a user into the system
   *
   * @return boolean
   */
  function login() {
    if( !isset($_POST['u']) || !isset($_POST['p']) || !$_POST['u'] || !$_POST['p'] ) {
      $this->log->error('LOGIN: Please enter username and password');
      return FALSE;
    }
    $user = $this->db->query(
      'SELECT id, username, level, email FROM user WHERE username = :u AND password = :p',
      $bind=array(':u'=>$_POST['u'],':p'=>$_POST['p']) );

    if( $this->db->db->errorCode() != '00000' ) { // query failed
      $this->log->error('LOGIN: Login system offline');
      return FALSE;
    }
    if( !$user ) { // no user, or wrong password
      $this->log->error('LOGIN: Invalid login');
      return FALSE;
    }
    if( !sizeof($user) == 1 ) { // corrupt data
      $this->log->error('LOGIN: Invalid login');
      return FALSE;
    }
    $user = $user[0];
    $this->session->set('attogram_id', $user['id']);
    $this->session->set('attogram_username', $user['username']);
    $this->session->set('attogram_level', $user['level']);
    $this->session->set('attogram_email', $user['email']);
    if( !$this->db->queryb(
      "UPDATE user SET last_login = datetime('now'), last_host = :last_host WHERE id = :id",
      $bind = array(':id'=>$user['id'], ':last_host'=>$_SERVER['REMOTE_ADDR'])
      ) ) {
        $this->log->error('LOGIN: can not updated last login info');
    }
    $this->log->debug('User Logged in');
    return TRUE;
  }

  /**
   * is_logged_in() - is a user logged into the system?
   *
   * @return boolean
   */
  function is_logged_in() {
    if( !is_object($this->session) ) { return FALSE; } // dev
    if( $this->session->get('attogram_id', FALSE) && $this->session->get('attogram_username', FALSE) ) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * guru_meditation_error()
   */
  function guru_meditation_error( $error='', $context=array(), $message='' ) {
    $this->log->error('Guru Meditation Error: ' . $error, $context);
    $this->page_header();
    print '<div class="container text-center bg-danger"><h1><strong>Guru Meditation Error</strong></h1>';
    if( $error ) {
      print '<h2>' . $error . '</h2>';
    }
    if( $context && is_array($context) ) {
      print '<p class="bg-warning">' . implode($context,'<br />') . '</p>';
    }
    if( $message ) {
      print '<p>' . $message . '</p>';
    }
    print '</div>';
    if( isset($this->log->stack) && $this->log->stack ) {
      print '<div class="container"><pre class="alert alert-debug">System Debug:<br />'
      . implode($this->log->stack, '<br />') . '</pre></div>';
    }
   $this->page_footer();
   exit;
  } // end function guru_meditation_error()

} // END of class attogram


/**
 * Attogram sqlite_database
 */
class sqlite_database extends attogram_utils {

  public $db_name, $modules_directory, $db;

  /**
   * __construct() - initialize database settings
   *
   * @param string $db_name relative path to the SQLite database file
   * @param string $modules_directory relative path to the Attogram modules directory
   * @param object $log psr3 logger object
   * @param bool $debug (optional) defaults to FALSE
   *
   * @return void
   */
  function __construct( $db_name, $modules_directory, $log, $debug=FALSE ) {
    parent::__construct();
    $this->debug = $debug;
    $this->log = $log;
    $this->modules_directory = $modules_directory;
    $this->db_name = $db_name;
  }

  /**
   * get_db() - Get the SQLite database object
   *
   * @return boolean
   */
  function get_db() {
    if( is_object($this->db) && get_class($this->db) == 'PDO' ) {
      return TRUE; // if PDO database object already set
    }
    if( !in_array('sqlite', \PDO::getAvailableDrivers() ) ) {
      $this->log->error('GET_DB: SQLite PDO driver not found');
      return FALSE;
    }
    if( is_file( $this->db_name ) && !is_writeable( $this->db_name ) ) {
      $this->log->error('GET_DB: NOTICE: database file not writeable: ' . $this->db_name);
      // SELECT will work, UPDATE will not work
    }
    if( !is_file( $this->db_name ) ) {
      $this->log->debug('GET_DB: NOTICE: creating database file: ' . $this->db_name);
    }
    try {
      $this->db = new \PDO('sqlite:'. $this->db_name);
    } catch(PDOException $e) {
      $this->log->error('GET_DB: error opening database');
      return FALSE;
    }
    $this->log->debug("GET_DB: Got SQLite database: $this->db_name");
    return TRUE; // got database, into $this->db
  }

  /**
   * query() - Query the database, return an array of results
   *
   * @param string $sql The SQL query
   * @param array $bind Optional, Array of values to bind into the SQL query
   *
   * @return array
   */
   function query( $sql, $bind=array() ) {
    if( !$this->get_db() ) {
      $this->log->error('QUERY: Can not get database');
      return array();
    }
    $statement = $this->query_prepare($sql);
    if( !$statement ) {
      list($sqlstate, $error_code, $error_string) = @$this->db->errorInfo();
      $this->log->error("QUERY: prepare failed: $sqlstate:$error_code:$error_string");
      return array();
    }
    while( $x = each($bind) ) { $statement->bindParam( $x[0], $x[1]); }
    if( !$statement->execute() ) {
      $this->log->error('QUERY: Can not execute query');
      return array();
    }
    $r = $statement->fetchAll(\PDO::FETCH_ASSOC);
    if( !$r && $this->db->errorCode() != '00000') { // query failed
      $this->log->error('QUERY: Query failed');
      $r = array();
    }
    $this->log->debug('QUERY: result', $r);
    return $r;
  }

  /**
   * queryb() - Query the database, return only TRUE or FALSE
   *
   * @param string $sql The SQL query
   * @param array $bind Optional, Array of values to bind into the SQL query
   *
   * @return boolean
   */
   function queryb( $sql, $bind=array() ) {
    if( !$this->get_db() ) {
      $this->log->error('QUERYB: Can not get database');
      return FALSE;
    }
    $statement = $this->query_prepare($sql);
    if( !$statement ) {
      list($sqlstate, $error_code, $error_string) = @$this->db->errorInfo();
      $this->log->error("QUERYB: prepare failed: $sqlstate:$error_code:$error_string");
      return FALSE;
    }
    while( $x = each($bind) ) {
      $statement->bindParam($x[0], $x[1]);
    }
    if( !$statement->execute() ) {
      $this->log->error('QUERYB: execute failed');
      return FALSE;
    }
    $this->log->debug('QUERYB TRUE');
    return TRUE;
  }

  /**
   * query_prepare()
   *
   * @param string $sql The SQL query to prepare
   *
   * @return object|boolean
   */
  function query_prepare( $sql ) {
    $this->log->debug("QUERY_PREPARE: prepare: $sql");
    $statement = $this->db->prepare($sql);
    if( $statement ) { return $statement; }
    list($sqlstate, $error_code, $error_string) = @$this->db->errorInfo();
    $this->log->error("QUERY_PREPARE: Can not prepare sql: $sqlstate:$error_code:$error_string");
    if( $sqlstate == 'HY000' && $error_code == '1' && preg_match('/^no such table/', $error_string) ) { // table not found
      $table = str_replace('no such table: ', '', $error_string); // get table name
      if( $this->create_table($table) ) { // create table
        $this->log->error("QUERY_PREPARE: Created table: $table");
        $statement = $this->db->prepare($sql);
        if( $statement ) { return $statement; } // try again
        $this->log->error('QUERY_PREPARE: Still can not prepare sql');
        return FALSE;
      } else {
        $this->log->error("QUERY_PREPARE: Can not create table: $table");
        return FALSE;
      }
    }
  }

  /**
   * get_tables()
   *
   * @return boolean
   */
   function get_tables() {
    if( isset($this->tables) && is_array($this->tables) ) {
      return TRUE;
    }
    $dirs = $this->get_all_subdirectories( $this->modules_directory, 'tables');
    $this->log->debug('GET_TABLES', $dirs);
    if( !$dirs ) {
      $this->log->debug('GET_TABLES: No module tables found');
      return FALSE;
    }
    $this->tables = array();
    foreach( $dirs as $d ) {
      $this->log->debug('GET_TABLES: d='. $d);
      foreach( array_diff(scandir($d), $this->skip_files) as $f ) {
        $file = $d . '/' . $f;
        if( !is_file($file) || !is_readable($file) || !preg_match('/\.sql$/',$file) ) {
          continue; // .sql files only
        }
        $this->log->debug(' .. f=' . $file);
        $table_name = str_replace('.sql','',$f);
        $this->tables[$table_name] = file_get_contents($file);
      }
    }
    return TRUE;
  }

  /**
   * create_table() - Create a table in the active SQLite database
   *
   * @param string $table The name of the table to create
   *
   * @return boolean
   */
   function create_table( $table='' ) {
    $this->get_tables();
    if( !isset($this->tables[$table]) ) {
      $this->log->error("CREATE_TABLE: Unknown table: $table");
      return FALSE;
    }
    if( !$this->queryb( $this->tables[$table] ) ) {
      $this->log->error("CREATE_TABLE: failed to create: $table");
      return FALSE;
    }
    return TRUE;
  }

} // END of class sqlite_database


/**
 * Nullish Stackish PSR3ish Logger
 *
 */
class logger {
  public $stack;
  public function log($level, $message, array $context = array()) {
    $this->stack[] = "$level: $message" . ( $context ? ': ' . print_r($context,1) : '');
  }
  public function emergency($message, array $context = array()) { $this->log('emergency',$message,$context); }
  public function alert($message, array $context = array()) { $this->log('alert',$message,$context); }
  public function critical($message, array $context = array()) { $this->log('critical',$message,$context); }
  public function error($message, array $context = array()) { $this->log('error',$message,$context); }
  public function warning($message, array $context = array()) { $this->log('warning',$message,$context); }
  public function notice($message, array $context = array()) { $this->log('notice',$message,$context); }
  public function info($message, array $context = array()) { $this->log('info',$message,$context); }
  public function debug($message, array $context = array()) { $this->log('debug',$message,$context); }
} // end class logger
