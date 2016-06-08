<?php
/**
 * Attogram Framework
 *
 * The Attogram Framework provides developers a skeleton starter site
 * with file-based URL routing, IP-protected backend, simple user system,
 * integrated SQLite database with phpLiteAdmin, Markdown parser, jQuery and Bootstrap.
 * Attogram is Dual Licensed under the The MIT License or the GNU General Public License, at your choosing.
 *
 * @version 0.5.4
 * @license MIT
 * @license GPL
 * @copyright 2016 Attogram Developers https://github.com/attogram/attogram
 */

namespace Attogram;

define('ATTOGRAM_VERSION', '0.5.4');

$attogram = new attogram();

/**
 * attogram_utils class
 */
class attogram_utils
{

  public $debug, $log, $skip_files;
  public $project_github, $project_packagist;

  function __construct() {
    $this->debug = FALSE;
    $this->log = new Logger(); // logger for startup tasks
    $this->skip_files = array('.','..','.htaccess');
    $this->project_github = 'https://github.com/attogram/attogram';
    $this->project_packagist = 'https://packagist.org/packages/attogram/attogram-framework';
  }

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
    $this->log->debug('set_config: ' . $var_name . ' = ' . print_r($this->{$var_name},1));
  }

  /**
   * get_all_subdirectories()
   */
  function get_all_subdirectories( $dir, $name ) {
    //$this->log->debug('get_all_subdirectories: scanning for: ' . $dir . '/*/' . $name);
    if( !isset($dir) || !$dir || !is_string($dir)) {
      $this->log->error('get_all_subdirectories: UNDEFINED dir:' . print_r($dir,1));
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
 * attogram class - The Attogram Framework
 */
class attogram extends attogram_utils
{

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
    $this->startup_attogram('config.php');
    $this->set_request(); // set all the request-related variables we need
    $this->exception_files(); // do robots.txt, sitemap.xml
    $this->set_uri();
    $this->end_slash(); // force slash at end, or force no slash at end
    $this->check_depth(); // is URI short enough?
    $this->get_includes(); // load any files in ./functions/
    $this->db = new sqlite_database($this->db_name, $this->modules_dir, $this->log, $this->debug);  // init the database, sans-connection
    $this->sessioning(); // start sessions
    $this->route(); // Send us where we want to go
    $this->log->debug('END Attogram v' . ATTOGRAM_VERSION);
  } // end function __construct()

  /**
   * startup_attogram()
   * @param string $config_file (optional)
   * @return void
   */
  function startup_attogram( $config_file='' ) {

    global $config;

    $config = array();

    // Load the main configuration file, if available
    if( !$this->is_readable_file($config_file, '.php') ) {
      $this->log->notice('startup_attogram: config file not found, using defaults.');
    } else {
      $this->log->debug('startup_attogram: include: ' . $config_file);
      include_once($config_file); // any $config['setting'] = value;
    }

    // Set installation, directory, file locations and defaults
    $this->set_config('attogram_directory', @$config['attogram_directory'], '../');
    $this->modules_dir = $this->attogram_directory . 'modules';
    $this->templates_dir = $this->attogram_directory . 'templates';
    $this->fof = $this->attogram_directory . 'templates/404.php';
    $this->db_name = $this->attogram_directory . 'db/global';

    $this->autoloader = $this->attogram_directory . 'vendor/autoload.php';
    $this->set_config('autoloader', @$config['autoloader'], $this->autoloader );
    $this->autoloader(); // load up all the vendor goodies, or shut down

    // The Site Administrator IP addresses
    $this->set_config('admins', @$config['admins'], array('127.0.0.1','::1'));

    // To Debug or Not To Debug, That is the quesion
    $this->set_config('debug', @$config['debug'], FALSE);
    // admin debug overrride?
    if( isset($_GET['debug']) && $this->is_admin() ) {
      $this->debug = TRUE;
      $this->log->debug('startup_attogram: Admin Debug turned ON');
    }

    $this ->init_logger(); // Start full logging via monolog

    $this->load_module_configs(); // Load modules configuration files, if available

    // Set configuration variables
    $this->set_config('site_name', @$config['site_name'], 'Attogram Framework <small>v' . ATTOGRAM_VERSION . '</small>');
    $this->set_config('force_slash_exceptions', @$config['force_slash_exceptions'], array() );
    $this->set_config('depth', @$config['depth'], $this->depth );
    if( !isset($this->depth['']) ) { $this->depth[''] = 1; } // reset: default homepage depth
    if( !isset($this->depth['*']) ) { $this->depth['*'] = 1; } // reset: default p age depth

  } // end function load_config()

  /**
   * autoloader() - auto load, or display fatal error
   */
  function autoloader() {
    $error = $missing = '';
    if( isset($this->autoloader) && $this->is_readable_file($this->autoloader,'.php') ) {
      include_once($this->autoloader);
      $missing = $this->check_required_classes();
      if( $missing ) {
        $error = 'Missing required classes';
      }
    } else {
      $error = 'vendor autoloader not found:';
      $missing[] = $this->autoloader;
    }
    if( !$error ){
      $this->log->debug('autoloader success');
      return;
    }
    $fix = '* Maybe need to run composer?'
    .'<br />* Or fix path to the autoloader file?'
    .'<br/>* Or download and install the '
    . '<a href="https://github.com/attogram/attogram-vendor/archive/master.zip">attogram-vendor</a> package.';
    $this->guru_meditation_error( $error, $missing, $fix );
  } // end function autoloader()

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
    //$this->log->debug('set_uri: pathInfo=' . $this->pathInfo);
    $this->uri = explode('/', $this->pathInfo);
    //$this->log->debug('raw uri:', $this->uri);
    //$this->log->debug('sizeof uri=' . sizeof($this->uri));
    if( sizeof($this->uri) == 1 ) {
      $this->log->debug('set_uri', $this->uri);
      return; // super top level request
    }
    if( $this->uri[0] == '' ) {
      $trash = array_shift($this->uri); // take off first blank entry
    }
    if( sizeof($this->uri) == 1 ) {
      $this->log->debug('set_uri', $this->uri);
      return; // top level request
    }
    if( $this->uri[sizeof($this->uri) - 1] == '' ) {
      $trash = array_pop($this->uri); // take off last blank entry
    }
    $this->log->debug('set_uri', $this->uri);
  }

  function end_slash() {
    if( !preg_match('/\/$/', $this->pathInfo)) { // No slash at end of url
      if( is_array($this->force_slash_exceptions) && !in_array( $this->uri[0], $this->force_slash_exceptions ) ) {
         // This action IS NOT excepted from force slash at end
        $url = str_replace($this->pathInfo, $this->pathInfo . '/', $this->requestUri);
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $url );  // Force Trailing Slash
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
   * load_module_configs()
   * Examines each module for a directory named 'configs'
   * and loads all *.php files in that directory
   * @return void
   */
  function load_module_configs() {
    global $config;
    $dirs = $this->get_all_subdirectories( $this->modules_dir, 'configs');
    //$this->log->debug('load_module_configs', $dirs);
    if( !$dirs ) {
      $this->log->debug('load_module_configs: No module configs found');
    }
    foreach( $dirs as $d ) {
      //$this->log->debug('load_module_configs: d='. $d);
      $this->include_all_php_files_in_directory( $d );
    }
  } // end function load_module_configs()

  /**
   * check_required_classes
   * @return array List of missing classes, or empty array
   */
  function check_required_classes() {
    $missing = array();
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
        $missing[] = $c;
      }
    }
    return $missing;
  } // end function check_required_classes()

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
      $format = "<p class=\"text-danger squished\">%datetime%|%level_name%: %message% %context%</p>"; // %extra%
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
          $this->log->error('ROUTE: No Parser Found');
          $this->error404('No Way Out');
          break;
      } // end switch on parser
    } //end if action set
    if( $this->uri[0] == 'home' ) { // missing the Home Page!
      // Default Home Page
      $this->log->error('ROUTE: missing home action - using default homepage');
      $this->page_header('Home');
      print 'Welcome to the Attogram Framework.  Do you know where my home page is?';
      $this->page_footer();
      return;
    }
    $this->log->error('ACTION: Action not found.  uri[0]=' . $this->uri[0] );
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
    $dirs = $this->get_all_subdirectories( $this->modules_dir, 'admin_actions');
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
   * get_includes() - include all PHP files, from all modules' includes/ directory
   *
   * @return void
   */
  function get_includes() {
    $dirs = $this->get_all_subdirectories( $this->modules_dir, 'includes');
    //$this->log->debug('get_includes', $dirs);
    if( !$dirs ) {
      $this->log->debug('get_includes: No module functions found');
    }
    foreach( $dirs as $d ) {
      //$this->log->debug('get_includes: d='. $d);
      $this->include_all_php_files_in_directory( $d );
    }
  } // end function get_includes()

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
   $this->page_footer();
   exit;
  } // end function guru_meditation_error()

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

} // END of class attogram


/**
 * Nullish Stackish PSR3ish Logger
 *
 */
class logger
{
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
