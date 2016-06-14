<?php // Attogram Framework - attogram class v0.0.5

namespace Attogram;

/**
 * Attogram Framework
 *
 * The Attogram Framework provides developers a PHP skeleton starter site with
 * content modules, file-based URL routing, IP-protected backend, user system,
 * integrated SQLite database with web admin, Markdown parser, jQuery and Bootstrap.
 *
 * The Attogram Framework is Dual Licensed under the MIT License (MIT)
 * _or_ the GNU General Public License version 3 or higher (GPL-3.0+).
 *
 * @version 0.5.9-dev
 * @license MIT
 * @license GPL-3.0+
 * @copyright 2016 Attogram Framework Developers https://github.com/attogram/attogram
 */
class attogram
{

  const ATTOGRAM_VERSION = '0.5.9-dev';

  public $start_time, $debug, $log, $skip_files, $project_github;
  public $attogram_directory, $modules_dir, $templates_dir;
  public $site_name, $depth, $force_slash_exceptions, $fof;
  public $request, $host, $clientIp, $pathInfo, $requestUri, $path, $uri;
  public $db, $db_name;
  public $actions, $action, $admins, $is_admin, $admin_actions, $admin_dir;

  /**
   * @param obj $log PSR-3 compliant log object
   * @param bool $debug (optional) Debug True/False.  Defaults to False.
   */
  public function __construct( $log, $debug = false ) {
    $this->start_time = microtime(1);
    $this->debug = $debug;
    $this->log = $log;
    $this->log->debug('START The Attogram Framework v' . self::ATTOGRAM_VERSION);
    $this->skip_files = attogram_fs::get_skip_files();
    $this->project_github = 'https://github.com/attogram/attogram';
    $this->awaken('config.php');
    $this->set_request(); // set all the request-related variables we need
    $this->exception_files(); // do robots.txt, sitemap.xml
    $this->set_uri();
    $this->end_slash(); // force slash at end, or force no slash at end
    $this->check_depth(); // is URI short enough?
    attogram_fs::load_module_includes( $this->modules_dir ); // Load modules includes files, if available
    $this->sessioning(); // start sessions
    // dev -- inject db object into __construct instead...
    if( class_exists('Attogram\sqlite_database') ) { // if database module is loaded
      $this->db = new sqlite_database($this->db_name, $this->modules_dir, $this->log, $this->debug);  // init the database, sans-connection
      $this->log->debug('__construct: sqlite_database init OK');
    } else {
      $this->db = false;
      $this->log->error('__construct: sqlite_database class not found');
    }
    $this->route(); // Send us where we want to go
    $this->log->debug('END Attogram v' . self::ATTOGRAM_VERSION . ' timer: ' . (microtime(1) - $this->start_time));
  } // end function __construct()

  /**
   * Awaken The Attogram Framework
   * @param string $config_file (optional)
   * @return void
   */
  public function awaken( $config_file = '' ) {
    global $config; // The Global Configuration Array
    if( !isset($config) || !is_array($config) ) {
      $config = array();
    }
    if( !attogram_fs::is_readable_file($config_file, '.php') ) { // Load the main configuration file, if available
      $this->log->notice('awaken: config file not found, using defaults.');
    } else {
      $this->log->debug('awaken: include: ' . $config_file);
      include_once($config_file); // any $config['setting'] = value;
    }
    $this->remember('modules_dir', @$config['modules_dir'], '../modules');
    attogram_fs::load_module_configs( $this->modules_dir ); // Load modules configuration files, if available
    $this->remember('attogram_directory', @$config['attogram_directory'], '../');
    $this->remember('templates_dir', @$config['templates_dir'], '../templates');
    $this->remember('fof', @$config['fof'], '../templates/404.php');
    $this->remember('db_name', @$config['db_name'], '../db/global');
    $this->remember('site_name', @$config['site_name'], 'Attogram Framework <small>v' . self::ATTOGRAM_VERSION . '</small>');
    $this->remember('force_slash_exceptions', @$config['force_slash_exceptions'], array() );
    $this->remember('depth', @$config['depth'], $this->depth ); // Depth settings
    if( !isset($this->depth['']) ) { // check:  homepage depth defined
      $this->depth[''] = 1;
      $this->log->debug('awaken: set homepage depth: 1');
    }
    if( !isset($this->depth['*']) ) {  // check: default depth defined
      $this->depth['*'] = 1;
      $this->log->debug('awaken: set default depth: 1');
    }
    $this->remember('admins', @$config['admins'], array('127.0.0.1','::1')); // The Site Administrator IP addresses
    // To Debug or Not To Debug, That is the quesion
    $this->remember('debug', @$config['debug'], false);
    if( isset($_GET['debug']) && $this->is_admin() ) { // admin debug overrride?
      $this->debug = true;
      $this->log->debug('awaken: Admin Debug turned ON');
    }
  } // end function load_config()

  /**
   * set a system configuration variable
   * @param string $var_name     The name of the variable
   * @param string $config_val   The setting for the variable
   * @param string $default_val  The default setting for the variable, if $config_val is empty
   * @return void
   */
  public function remember( $var_name, $config_val = '', $default_val ) {
    if( $config_val ) {
      $this->{$var_name} = $config_val;
    } else {
      $this->{$var_name} = $default_val;
    }
    $this->log->debug('remember: ' . $var_name . ' = ' . print_r($this->{$var_name},1));
  }

  /**
   * set_request()
   */
  public function set_request() {
    $this->request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
    $this->host = $this->request->getHost();
    $this->clientIp = $this->request->getClientIp();
    $this->log->debug("host: $this->host  IP: $this->clientIp");
    $this->pathInfo = $this->request->getPathInfo();
    $this->requestUri = $this->request->getRequestUri();
    $this->path = $this->request->getBasePath();
  }

  /**
   * set uri array
   */
  public function set_uri() {
    $this->uri = explode('/', $this->pathInfo);
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

  /**
   * end_slash()
   */
  public function end_slash() {
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

  /**
   * check_depth()
   */
  public function check_depth() {
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
   * sessioning() - start the session, logoff if requested
   * @return void
   */
  public function sessioning() {
    session_start();
    $this->log->debug('Session started.', $_SESSION);
    if( isset($_GET['logoff']) ) {
      session_unset();
      session_destroy();
      session_start();
      $this->log->info('User loggged off');
    }
  }

  /**
   * route() - decide what action to take based on URI request
   * @return void
   */
  public function route() {

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
   * @return void
   */
  public function exception_files() {
    switch( $this->pathInfo ) {
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
   * @param string $file The markdown file to load
   * @return void
   */
  public function do_markdown( $file ) {
    $title = $content = '';
    if( attogram_fs::is_readable_file($file, '.md' ) ) {
      $page = @file_get_contents($file);
      if( $page === false ) {
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
   * @return string
   */
  public function get_site_url() {
    return $this->request->getSchemeAndHttpHost() . $this->path;
  }

  /**
   * get_actions() - create list of all pages from the actions directory
   * @return array
   */
  public function get_actions() {
    if( is_array($this->actions) ) {
      return $this->actions;
    }
    $dirs = attogram_fs::get_all_subdirectories( $this->modules_dir, 'actions');
    if( !$dirs ) {
      $this->log->debug('get_actions: No module actions found');
    }
    $this->actions = array();
    foreach( $dirs as $d ) {
      $this->actions = array_merge($this->actions, $this->get_actionables($d) );
    }
    asort($this->actions);
    $this->log->debug('get_actions: ', array_keys($this->actions));
    return $this->actions;
  } // end function get_actions()

  /**
   * get_admin_actions() - create list of all admin pages from the admin directory
   * @return array
   */
  public function get_admin_actions() {
    if( is_array($this->admin_actions) ) {
      return $this->admin_actions;
    }
    $dirs = attogram_fs::get_all_subdirectories( $this->modules_dir, 'admin_actions');
    if( !$dirs ) {
      $this->log->debug('get_admin_actions: No module admin actions found');
    }
    $this->admin_actions = array();
    foreach( $dirs as $d ) {
      $this->admin_actions = array_merge($this->admin_actions, $this->get_actionables($d) );
    }
    asort($this->admin_actions);
    $this->log->debug('get_admin_actions: ', array_keys($this->admin_actions));
    return $this->admin_actions;
  } // end function get_admin_actions()

  /**
   * get_actionables - create list of all useable action files from a directory
   * @return array
   */
  public function get_actionables( $dir ) {
    $r = array();
    if( !is_readable($dir) ) {
      $this->log->error('GET_ACTIONABLES: directory not readable: ' . $dir);
      return $r;
    }
    foreach( array_diff(scandir($dir), $this->skip_files) as $f ) {
      $file = $dir . "/$f";
      if( attogram_fs::is_readable_file($file, '.php') ) { // PHP files
        $r[ str_replace('.php','',$f) ] = array( 'file'=>$file, 'parser'=>'php' );
      } elseif( attogram_fs::is_readable_file($file, '.md') ) { // Markdown files
        $r[ str_replace('.md','',$f) ] = array( 'file'=>$file, 'parser'=>'md'
        );
      }
    }
    return $r;
  }

  /**
   * is_admin() - is access from an admin IP?
   * @return boolean
   */
  public function is_admin() {
    if( isset($this->is_admin) && is_bool($this->is_admin) ) {
      return $this->is_admin;
    }
    if( isset($_GET['noadmin']) ) {
      $this->is_admin = false;
      $this->log->debug('is_admin false - noadmin override');
      return false;
    }
    if( !isset($this->admins) || !is_array($this->admins) ) {
      $this->is_admin = false;
      $this->log->error('is_admin false - missing $this->admins  array');
      return false;
    }
    if( is_object($this->request) ) {
      $cip = $this->request->getClientIp();
    } else {
      $cip = $_SERVER['REMOTE_ADDR'];
    }
    if( @in_array($cip,$this->admins) ) {
      $this->is_admin = true;
      $this->log->debug('is_admin true ' . $cip);
      return true;
    }
    $this->is_admin = false;
    $this->log->debug('is_admin false ' . $cip);
    return false;
  }

  /**
   * page_header() - the web page header
   * @param string $title The web page title
   * @return void
   */
  public function page_header( $title = '' ) {
    $file = $this->templates_dir . '/header.php';
    if( attogram_fs::is_readable_file($file,'.php') ) {
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
   * @return void
   */
  public function page_footer() {
    $file = $this->templates_dir . '/footer.php';
    if( attogram_fs::is_readable_file($file,'.php') ) {
      include($file);
      $this->log->debug('page_footer');
      return;
    }
    // Default page footer
    print '<hr /><p>Powered by <a href="' . $this->project_github . '">Attogram v' . ATTOGRAM_VERSION . '</a></p>';
    print '</body></html>';
    $this->log->error('missing page_footer ' . $file . ' - using default footer');
  }

  /**
   * error404() - display a 404 error page to user and exit
   * @return void
   */
  public function error404( $error = '' ) {
    header('HTTP/1.0 404 Not Found');
    if( attogram_fs::is_readable_file($this->fof, '.php') ) {
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
