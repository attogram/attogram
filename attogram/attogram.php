<?php // Attogram Framework - attogram class v0.2.4

namespace Attogram;

/**
 * Attogram Framework
 *
 * The Attogram Framework provides developers a PHP skeleton starter site with
 * content modules, file-based URL routing, IP-protected backend, user system,
 * integrated SQLite database with web admin, Markdown parser, jQuery and Bootstrap.
 *
 * The Attogram Framework is Dual Licensed under the MIT License (MIT)
 * _or_ the GNU General Public License version 3 (GPL-3.0+), at your choosing.
 *
 * @version 0.7.0
 * @license (MIT or GPL-3.0+)
 * @copyright 2016 Attogram Framework Developers https://github.com/attogram/attogram
 */
class attogram
{

  const ATTOGRAM_VERSION = '0.7.0-dev';

  public $start_time;    // (float) microsecond time of awakening
  public $debug;         // (boolean) debug on/off
  public $log;           // (object) Debug Log - PSR-3 Logger object
  public $event;         // (object) Event Log - PSR-3 Logger object
  public $db;            // (object) The Attogram Database Object
  public $request;       // (object) Symfony HttpFoundation Request object
  public $project_github;// (string) URL to Attogram Framework GitHub Project
  public $attogram_dir;  // (string) path to this installation
  public $modules_dir;   // (string) path to the modules directory
  public $templates_dir; // (string) path to the templates directory
  public $templates;     // (array) list of templates
  public $site_name;     // (string) The Site Name
  public $depth;         // (array) Allowed depth settings
  public $no_end_slash;  // (array) actions to NOT force slash at end
  public $host;          // (string) Client Hostname
  public $clientIp;      // (string) Client IP Address
  public $pathInfo;      // (string)
  public $requestUri;    // (string)
  public $path;          // (string) Relative URL path to this installation
  public $uri;           // (array) The Current URI
  public $db_name;       // (string) path + filename of the sqlite database file
  public $actions;       // (array) memory variable for $this->get_actions()
  public $action;        // (string) The Current Action name
  public $admins;        // (array) Administrator IP addresses
  public $is_admin;      // (boolean) memory variable for $this->is_admin()
  public $admin_actions; // (array) memory variable for $this->get_admin_actions()

  /**
   * @param obj  $log      Debug Log - PSR-3 logger object, interface:\Psr\Log\LoggerInterface
   * @param obj  $event    Event Log - PSR-3 logger object, interface: \Psr\Log\LoggerInterface
   * @param obj  $db       Attogram Database object, interface: \Attogram\attogram_database
   * @param obj  $request  \Symfony\Component\HttpFoundation\Request object
   * @param bool $debug    (optional) Debug True/False.  Defaults to False.
   */
  public function __construct( $log, $event, $db, $request, $debug = false )
  {
    $this->start_time = microtime(1);
    $this->log = $log;
    $this->event = $event;
    $this->db = $db;
    $this->request = $request;
    $this->debug = $debug;
    $this->log->debug('START The Attogram Framework v' . self::ATTOGRAM_VERSION);
    $this->project_github = 'https://github.com/attogram/attogram';
    $this->awaken(); // set the configuration
    $this->set_request(); // set all the request-related variables we need
    $this->log->debug("host: $this->host  IP: $this->clientIp");
    $this->exception_files(); // do robots.txt, sitemap.xml
    $this->virtual_web_directory(); // do virtual web directory requests
    $this->set_uri(); // make array of the URI request
    $this->end_slash(); // force slash at end, or force no slash at end
    $this->check_depth(); // is URI short enough?
    $this->sessioning(); // start sessions
    $this->route(); // Send us where we want to go
    $this->log->debug('END Attogram v' . self::ATTOGRAM_VERSION . ' timer: ' . (microtime(1) - $this->start_time));
  } // end function __construct()

  /**
   * Awaken The Attogram Framework
   * @return void
   */
  public function awaken()
  {
    global $config; // The Global Configuration Array

    if( !isset($config['admins']) ) { $config['admins'] = array('127.0.0.1','::1'); }
    $this->remember('admins', $config['admins'], array('127.0.0.1','::1')); // The Site Administrator IP addresses

    if( !isset($config['debug']) ) { $config['debug'] = false; }
    $this->remember('debug', $config['debug'], false);

    if( !isset($config['attogram_dir']) ) { $config['attogram_dir'] = '../'; }
    $this->remember('attogram_dir', $config['attogram_dir'],          '../');

    if( !isset($config['modules_dir']) ) { $config['modules_dir'] = $this->attogram_dir . 'modules'; }
    $this->remember('modules_dir', $config['modules_dir'],          $this->attogram_dir . 'modules');

    if( !isset($config['templates_dir']) ) { $config['templates_dir'] = $this->attogram_dir . 'templates'; }
    $this->remember('templates_dir', $config['templates_dir'],          $this->attogram_dir . 'templates');
    $this->set_module_templates();
    if( !isset($this->templates['header']) ) {
      $this->templates['header'] = $this->templates_dir . '/header.php';
    }
    if( !isset($this->templates['navbar']) ) {
      $this->templates['navbar'] = $this->templates_dir . '/navbar.php';
    }
    if( !isset($this->templates['footer']) ) {
      $this->templates['footer'] = $this->templates_dir . '/footer.php';
    }
    if( !isset($this->templates['fof']) ) {
      $this->templates['fof']    = $this->templates_dir . '/404.php';
    }

    if( !isset($config['db_name']) ) { $config['db_name'] = '../db/global'; }
    $this->remember('db_name', $config['db_name'], '../db/global');

    if( !isset($config['site_name']) ) { $config['site_name'] = 'Attogram Framework <small>v' . self::ATTOGRAM_VERSION . '</small>'; }
    $this->remember('site_name', $config['site_name'], 'Attogram Framework <small>v' . self::ATTOGRAM_VERSION . '</small>');

    if( !isset($config['no_end_slash']) ) { $config['no_end_slash'] = array(); }
    $this->remember('no_end_slash', $config['no_end_slash'], array() );

    if( !isset($config['depth']) ) { $config['depth'] = array(); }
    $this->remember('depth', $config['depth'], array() ); // Depth settings
    if( !isset($this->depth['']) ) { // check:  homepage depth defined
      $this->depth[''] = 1;
      $this->log->debug('awaken: set homepage depth: 1');
    }
    if( !isset($this->depth['*']) ) {  // check: default depth defined
      $this->depth['*'] = 1;
      $this->log->debug('awaken: set default depth: 1');
    }

  } // end function load_config()

  /**
   * Set module templates
   * @return void
   */
  public function set_module_templates()
  {
    $d = attogram_fs::get_all_subdirectories( $this->modules_dir, 'templates' );
    if( !$d ) {
      $this->log->debug('set_module_templates: no module templates found');
      return;
    }
    foreach( $d as $md ) {
      foreach( array_diff( scandir($md), attogram_fs::get_skip_files() ) as $f ) {
        $file = "$md/$f";
        if( attogram_fs::is_readable_file( $file, '.php' ) ) {
          $name = preg_replace( '/\.php$/', '', $f );
          $this->templates[$name] = $file; // Set the template
          $this->log->debug('set_module_templates: ' . $name. ' = ' . $file);
        } else {
          $this->log->error('set_module_templates: File not readable: ' . $file);
        }
      }
    }
  } // end function set_module_templates()

  /**
   * set a system configuration variable
   * @param string $var_name     The name of the variable
   * @param string $config_val   The setting for the variable
   * @param string $default_val  The default setting for the variable, if $config_val is empty
   * @return void
   */
  public function remember( $var_name, $config_val = '', $default_val )
  {
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
  public function set_request()
  {
    $this->host = $this->request->getHost();
    $this->clientIp = $this->request->getClientIp();
    $this->pathInfo = $this->request->getPathInfo();
    $this->requestUri = $this->request->getRequestUri();
    $this->path = $this->request->getBasePath();
  }

  /**
   * set uri array
   */
  public function set_uri()
  {
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
  public function end_slash()
  {
    if( !preg_match('/\/$/', $this->pathInfo)) { // No slash at end of url
      if( is_array($this->no_end_slash) && !in_array( $this->uri[0], $this->no_end_slash ) ) {
         // This action IS NOT excepted from force slash at end
        $url = str_replace($this->pathInfo, $this->pathInfo . '/', $this->requestUri);
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $url );  // Force Trailing Slash
        exit;
      }
    } else { // Yes slash at end of url
      if( is_array($this->no_end_slash) && in_array( $this->uri[0], $this->no_end_slash ) ) {
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
  public function check_depth()
  {
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
  public function sessioning()
  {
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
  public function route()
  {

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
      $this->default_homepage();
      return;
    }
    $this->log->error('ACTION: Action not found.  uri[0]=' . $this->uri[0] );
    $this->error404('This is not the action you are looking for');
  } // end function route()

  /**
   * checks if request is for the virtual web directory
   * and serve the appropriate module file
   * @return void
   */
  public function virtual_web_directory() {
    $virtual_web_directory = 'web';
    if( !preg_match( '/^\/' . $virtual_web_directory . '\//', $this->pathInfo ) ) {
      return; // not a virtual web directory request
    }
    $test = explode('/', $this->pathInfo);
    if( sizeof($test) < 3 || $test[2] == '') { // empty request
      $this->error404('Virtual Nothingness Found');
    }
    $trash = array_shift($test); // take off top level
    $trash = array_shift($test); // take off virtual web directory
    $req = implode('/', $test); // the virtual web request
    $mod = attogram_fs::get_all_subdirectories( $this->modules_dir, 'public' );
    $file = false;
    foreach( $mod as $m ) {
      $test_file = $m . '/' . $req;
      if( !is_readable($test_file) || is_dir($test_file) ) {
        continue;
      }
      $file = $test_file; // found file -- cascade set the file
    }
    if( !$file ) {
      $this->error404('Virtually Nothing Found');
    }

    $this->do_cache_headers( $file );

    $mime_type = attogram_fs::get_mime_type($file);
    if( $mime_type ) {
      header('Content-Type:' . $mime_type . '; charset=utf-8');
      $result = readfile($file); // send file to browser
      if( !$result ) {
        $this->log->error('virtual_web_directory: can not read file: ' . htmlentities($file) );
        $this->error404('Virtually unreadable');
      }
      exit;
    }
    if( !(include($file)) ) { // include native PHP file
      $this->log->error('virtual_web_directory: can not include file: ' . htmlentities($file) );
      $this->error404('Virtually unincludeable');
    }
    exit;
  } // end function virtual_web_directory()

  /**
   * send HTTP cache headers
   * @param string $file
   */
  public function do_cache_headers( $file ) {
    $lastmod = filemtime($file);
    if( !$lastmod ) {
      $lastmode = time();
    }
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastmod) . ' GMT');
    header('Etag: ' . $lastmod);
    $server_if_mod = @strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
    $server_if_none = trim($_SERVER['HTTP_IF_NONE_MATCH']);
    if (  $server_if_mod == $lastmod || $server_if_none == $lastmod ) {
        header('HTTP/1.1 304 Not Modified');
        exit;
    }
  } // end function do_cache_headers()

  /**
   * Do requests for exception files: sitemap.xml, robots.txt
   * @return void exit
   */
  public function exception_files()
  {
    switch( $this->pathInfo ) {
      case '/robots.txt':
        header('Content-Type: text/plain; charset=utf-8');
        print 'Sitemap: ' . $this->get_site_url() . '/sitemap.xml';
        exit;
      case '/sitemap.xml':
        $site = $this->get_site_url() . '/';
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>'
        . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
        . '<url><loc>' . $site . '</loc></url>';
        foreach( array_keys($this->get_actions()) as $action ){
          if( $action == 'home' || $action == 'user' ) {
            continue;
          }
          $sitemap .= '<url><loc>' . $site . $action . '/</loc></url>';
        }
        $sitemap .= '</urlset>';
        header ('Content-Type: text/xml; charset=utf-8');
        print $sitemap;
        exit;
    }
  }

  /**
   * get HTML from a markdown file
   * @param string $file The markdown file to parse
   * @return string      HTML fragment or false
   */
  public function get_markdown( $file )
  {
    if( !attogram_fs::is_readable_file($file, '.md' ) ) {
      $this->log->error('GET_MARKDOWN: can not read file: ' . $this->web_display($file));
      return false;
    }
    if( !class_exists('Parsedown') ) {
      $this->log->error('GET_MARKDOWN: can not find parser');
      return false;
    }
    $page = @file_get_contents($file);
    if( $page === false ) {
      $this->log->error('GET_MARKDOWN: can not get file contents: ' . $this->web_display($file));
      return false;
    }
    $content = \Parsedown::instance()->text( $page );
    if( !$content ) {
      $this->log->error('GET_MARKDOWN: parse failed on file: ' . $this->web_display($file));
      return false;
    }
    return $content;
  } // end function get_markdown

  /**
   * display a Markdown document, with standard page header and footer
   * @param string $file The markdown file to load
   * @param string $title (optional) Page title
   * @return void
   */
  public function do_markdown( $file, $title = '' )
  {
    $this->log->debug('DO_MARKDOWN: ' . $file);
    if( !$title ) {
        $title = 'MARKDOWN';
    }
    // TODO dev - $title input, and default to 1st line of file
    // $title = trim( strtok($page, "\n") );
    // get first line of file, use as page title

    $this->page_header($title);
    print '<div class="container">' . $this->get_markdown($file) . '</div>';
    $this->page_footer();
  }

  /**
   * get_site_url()
   * @return string
   */
  public function get_site_url()
  {
    return $this->request->getSchemeAndHttpHost() . $this->path;
  }

  /**
   * get_actions() - create list of all pages from the actions directory
   * @return array
   */
  public function get_actions()
  {
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
  public function get_admin_actions()
  {
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
  public function get_actionables( $dir )
  {
    $r = array();
    if( !is_readable($dir) ) {
      $this->log->error('GET_ACTIONABLES: directory not readable: ' . $dir);
      return $r;
    }
    foreach( array_diff( scandir($dir), attogram_fs::get_skip_files() ) as $f ) {
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
  public function is_admin()
  {
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
  public function page_header( $title = '' )
  {
    $file = $this->templates['header'];
    if( attogram_fs::is_readable_file( $file,'.php' ) ) {
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
  public function page_footer()
  {
    $file = $this->templates['footer'];
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
   * Show the default home page
   */
  public function default_homepage()
  {
    $this->log->error('ROUTE: missing home action - using default homepage');
    $this->page_header('Home');
    print '<div class="container">'
    . '<h1>Welcome to the Attogram Framework <small>v' . self::ATTOGRAM_VERSION . '</small></h1>'
    . '<p>To replace this page, create a file named '
    . '<code>home.php</code> or <code>home.md</code> '
    . ' in any <code>modules/*/actions/</code> directory</p>'
    . '<p>Public Actions:<ul>'
    ;
    foreach( $this->get_actions() as $name => $val ) {
      print '<li><a href="' . $this->path . '/' . urlencode($name) . '/">'
      . htmlentities($name) . '</a></li>';
    }
    print '</ul><p>';
    if( $this->is_admin() ) {
      print '<p>Admin Actions:<ul>';
      foreach( $this->get_admin_actions() as $name => $val ) {
        print '<li><a href="' . $this->path . '/' . urlencode($name) . '/">'
        . htmlentities($name) . '</a></li>';
      }
      print '</ul></p>';
    }
    print '</div>';
    $this->page_footer();
  }

  /**
   * error404() - display a 404 error page to user and exit
   * @return void
   */
  public function error404( $error = '' )
  {
    //$this->event->error('404 Not Found: uri: [' . implode(', ', $this->uri) . '] error: ' . $error);
    header('HTTP/1.0 404 Not Found');
    if( attogram_fs::is_readable_file( $this->templates['fof'], '.php' ) ) {
      include($this->templates['fof']);
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
   * clean a string for web display
   * @param string $string  The string to clean
   * @return string  The cleaned string, or false
   */
  public function web_display( $string )
  {
    if( !is_string($string) ) {
      return false;
    }
    return htmlentities( $string, ENT_COMPAT, 'UTF-8' );
  }

} // END of class attogram
