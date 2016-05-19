<?php
/**
 * Attogram Framework
 *
 * The Attogram Framework provides developers a skeleton starter site
 * with auto file-based URL routing, IP-protected backend, simple user system,
 * integrated SQLite database with phpLiteAdmin, Markdown parser, jQuery and Bootstrap.
 * Attogram is Dual Licensed under the The MIT License or the GNU General Public License, at your choosing.
 *
 * @version 0.4.1
 * @license MIT
 * @license GPL
 * @copyright 2016 Attogram Developers https://github.com/attogram/attogram
 */

  // todo: force trailing slash

namespace Attogram;
define('ATTOGRAM_VERSION', '0.4.1');
$debug = FALSE;
error_reporting(E_ALL);
ini_set('display_errors', '1');
$attogram = new attogram();

/**
 * Attogram Framework
 */
class attogram {

  public $autoloader, $request, $path, $uri, $fof, $site_name, $skip_files, $log;
  public $sqlite_database, $db_name, $tables_dir;
  public $templates_dir, $functions_dir;
  public $actions_dir, $default_action, $actions, $action;
  public $admins, $admin_dir, $admin_actions;

  /**
   * __construct() - startup Attogram!
   *
   * @return void
   */
  function __construct() {

    $this->load_config('config.php');

    $this->autoloader();

    $this->init_logger();

    $this->request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
    
    $this->sessioning();
    $this->get_functions();
    $this->sqlite_database = new sqlite_database( $this->db_name, $this->tables_dir );
    $this->route();
    $this->log->debug('END log @ ' . date('r') );
    exit;
  }

  /**
   * autoloader() - auto load, or display fatal error
   */
  function autoloader() {
    $this->autoloader = 'vendor/autoload.php';
    if( !is_readable_file($this->autoloader,'.php') ) {
      $this->page_header();
      print '<h1>Attogram Fatal Error</h1><h2>vendor autoloader not found</h2>'
      . '<p>Please run composer, or download and install the '
      . '<a href="https://github.com/attogram/attogram-vendor/archive/master.zip">attogram-vendor</a> package</p>';
      $this->page_footer();
      exit;
    }
    include_once($this->autoloader); 
    if( !class_exists('\Symfony\Component\HttpFoundation\Request') ) {
      // fatal error
    }
  }

  /**
   * init_logger() - initialize the logger object, based on debug setting
   */
  function init_logger() {
    if( $this->debug && class_exists('\Monolog\Logger') ) {

      $this->log = new \Monolog\Logger('attogram');

      $sh = new \Monolog\Handler\StreamHandler('php://output');
      $format = "<p class=\"small text-danger\" style=\"padding:0;margin:0;\">SYS: %datetime% > %level_name% > %message% %context% %extra%</p>";
      $sh->setFormatter( new \Monolog\Formatter\LineFormatter($format) );     
      $this->log->pushHandler( new \Monolog\Handler\BufferHandler($sh) );

      //$this->log->pushHandler( new \Monolog\Handler\BrowserConsoleHandler );
        
    } else {
      $this->log = new logger();
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
    global $debug;
    if( !is_readable_file($config_file) ) {
      $this->log->warning('LOAD_CONFIG: config file not found');
    } else {
      include_once($config_file);
      if( !isset($config) || !is_array($config) ) {
        $this->log->warning('LOAD_CONFIG: $config array not found');
      }
    }

    $this->set_config( 'debug',          @$config['debug'],          FALSE );
    $debug = $this->debug;
    
    $this->set_config( 'site_name',      @$config['site_name'],      'Attogram Framework <small>v' . ATTOGRAM_VERSION . '</small>' );
    $this->set_config( 'admins',         @$config['admins'],         array('127.0.0.1','::1') );
    $this->set_config( 'admin_dir',      @$config['admin_dir'],      'admin' );
    $this->set_config( 'default_action', @$config['default_action'], 'actions/home.php' );
    $this->set_config( 'actions_dir',    @$config['actions_dir'],    'actions' );
    $this->set_config( 'templates_dir',  @$config['templates_dir'],  'templates' );
    $this->set_config( 'functions_dir',  @$config['functions_dir'],  'functions' );
    $this->set_config( 'fof',            @$config['fof'],            'templates/404.php' );
    $this->set_config( 'db_name',        @$config['db_name'],        'db/global' );
    $this->set_config( 'tables_dir',     @$config['tables_dir'],     'tables' );
    
    $this->skip_files = array('.','..','.htaccess');

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
  }

  /**
   * sessioning() - start the session, logoff if requested
   *
   * @return void
   */
  function sessioning() {
    session_start();
    if( isset($_GET['logoff']) ) {
      $_SESSION = array();
      session_destroy();
      session_start();
      $this->log->info('User loggged off');
    }
  }

  /**
   * route() - decide what action to take based on URI request
   *
   * @return void
   */
  function route() {

    $this->uri = explode('/', $this->request->getPathInfo());
    $trash = array_shift($this->uri); 
    $this->path = $this->request->getBasePath();

    $this->log->debug('ROUTE: uri: ' . implode($this->uri,', ') );
    if( !$this->uri || !is_array($this->uri) || !isset($this->uri[0]) ) {
      $this->log->error('ROUTE: Invalid URI');
      $this->error404();
    }
    $this->exception_files();
    if( isset($this->uri[2]) || ( isset($this->uri[1]) && $this->uri[1] != '' ) ) { // if has subpath
      $this->log->error('ROUTE: subpath not supported');
      $this->error404();
    }
    if( is_dir($this->uri[0]) ) {  // requesting a directory?
      $this->log->error('ROUTE: 403 Action Forbidden');
      $this->error404();
    }

    $actions = $this->get_actions();
    if( $this->is_admin() ) {
        $actions = array_merge($actions, $this->get_admin_actions());
    }
    if( $this->uri[0] == '' ) { // The Homepage
      $this->uri[0] = 'home';
    }
    $this->log->debug('action: ' . $this->uri[0]);
    if( isset($actions[$this->uri[0]]) ) {
      switch( $actions[$this->uri[0]]['parser'] ) {
        case 'php':
          $this->action = $actions[$this->uri[0]]['file'];
          if( !is_file($this->action) ) {
            $this->log->error('ROUTE: Missing action');
            $this->error404();
          }
          if( !is_readable($this->action) ) {
            $this->log->error('ROUTE: Unreadable action');
            $this->error404();
          }
          include($this->action);
          return;
        case 'md':
          $this->do_markdown( $actions[$this->uri[0]]['file'] );
          return;
        default:
          $this->log->error('ACTION: No Parser Found');
          $this->error404();
          break;
      } // end switch on parser
    } //end if action set
    $this->error[] = 'ACTION: Action not found';
    $this->error404();
  } // end function route()

  /**
   * exception_files() - checks URI for exception files sitemap.xml, robots.txt
   *
   * @return void
   */
  function exception_files() {
    if( $this->uri[0] == 'sitemap.xml' && !isset($this->uri[1]) ) {
      $site = $this->get_site_url() . '/';
      $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
      $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
      $sitemap .= ' <url><loc>' . $site . '</loc></url>' . "\n";
      foreach( array_keys($this->get_actions()) as $action ){
        $sitemap .= ' <url><loc>' . $site . $action . '/</loc></url>' . "\n";
      }
      $sitemap .= '</urlset>';
      header ('Content-Type:text/xml');
      print $sitemap;
      exit;
    }
    if( $this->uri[0] == 'robots.txt' && !isset($this->uri[1]) ) {
      header('Content-Type: text/plain');
      print 'Sitemap: ' . $this->get_site_url() . '/sitemap.xml';
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
    if( is_readable_file($file, '.md' ) ) {
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
  function error404() {
    $err = '404 Not Found';
    header('HTTP/1.0 ' . $err);
    if( is_readable_file($this->fof) ) {
      include($this->fof);
      exit;
    }
    // Default 404 page
    $this->log->error('ERROR404: 404 template not found');
    $this->page_header($err);
    print '<div class="container"><h1>' . $err . '</h1></div>';
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
    if( is_readable_file($file,'php') ) {
      include($file);
      return;
    }
    // Default page header
    print '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>' . $title . '</title></head><body>';
  }

  /**
   * page_footer() - the web page footer
   *
   * @return void
   */
  function page_footer() {
    $file = $this->templates_dir . '/footer.php';
    if( is_readable_file($file,'php') ) {
      include($file);
      return;
    }
    // Default page footer
    print '<hr /><p>Powered by <a href="https://github.com/attogram/attogram">Attogram v' . ATTOGRAM_VERSION . '</a></p>';
    print '</body></html>';
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
    return $this->actions = $this->get_actionables($this->actions_dir); 
  }

  /**
   * get_admin_actions() - create list of all admin pages from the admin directory
   *
   * @return array
   */
  function get_admin_actions() {
    if( !$this->is_admin() ) {
      return array();
    }
    if( is_array($this->admin_actions) ) {
      return $this->admin_actions;
    }
    return $this->admin_actions = $this->get_actionables($this->admin_dir); 
  }

  /**
   * get_actionables - create list of all useable action files from a directory
   *
   * @return array
   */
  function get_actionables( $dir ) {
    $r = array();
    if( !is_readable($dir) ) {
      $this->error[] = 'GET_ACTIONABLES: directory not readable: ' . $dir;
      return $r;
    }
    foreach( array_diff(scandir($dir), $this->skip_files) as $f ) {
      $file = $dir . "/$f";
      if( is_readable_file($file, '.php') ) { // PHP files 
        $r[ str_replace('.php','',$f) ] = array( 'file'=>$file, 'parser'=>'php' );
      } elseif( is_readable_file($file, '.md') ) { // Markdown files 
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
    if( !is_dir($this->functions_dir) || !is_readable($this->functions_dir) ) {
      return FALSE;
    }
    foreach( array_diff(scandir($this->functions_dir), $this->skip_files) as $f ) {
      $file = $this->functions_dir . "/$f";
      if( !is_readable_file($file) ) { continue; } // php files only
      include_once($file);
    }
  }

  /**
   * is_admin() - is access from an admin IP?
   *
   * @return boolean
   */
  function is_admin() {
    if( isset($_GET['noadmin']) ) { return FALSE; }
    if( !isset($this->admins) || !is_array($this->admins) ) { return FALSE; }
    if( @in_array($_SERVER['REMOTE_ADDR'],$this->admins) ) { return TRUE; }
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
    $user = $this->sqlite_database->query(
      'SELECT id, username, level, email FROM user WHERE username = :u AND password = :p',
      $bind=array(':u'=>$_POST['u'],':p'=>$_POST['p']) );

    if( $this->sqlite_database->db->errorCode() != '00000' ) { // query failed
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
    $_SESSION['attogram_id'] = $user['id'];
    $_SESSION['attogram_username'] = $user['username'];
    $_SESSION['attogram_level'] = $user['level'];
    $_SESSION['attogram_email'] = $user['email'];
    if( !$this->sqlite_database->queryb(
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
    if( isset($_SESSION['attogram_id']) && $_SESSION['attogram_id']
     && isset($_SESSION['attogram_username']) && $_SESSION['attogram_username'] ) {
      return TRUE;
    }
    return FALSE;
  }

} // END of class attogram



/**
 * Attogram sqlite_database
 */
class sqlite_database {

  public $db_name, $db, $tables_directory, $tables, $skip_files, $log;

  /**
   * __construct() - initialize database settings
   *
   * @param string $db_name relative path to the SQLite database file
   * @param string $tables_dir relative path to the table definitions directory
   *
   * @return void
   */
  function __construct( $db_name, $tables_dir ) {
    global $debug;
    $this->db_name = $db_name;
    $this->tables_directory = $tables_dir;
    $this->skip_files = array('.','..','.htaccess');
    if( $debug && class_exists('\Monolog\Logger') ) {

      $this->log = new \Monolog\Logger('attogram');

      $sh = new \Monolog\Handler\StreamHandler('php://output');
      $format = "<p class=\"small text-danger\" style=\"padding:0;margin:0;\">SYS: %datetime% > %level_name% > %message% %context% %extra%</p>";
      $sh->setFormatter( new \Monolog\Formatter\LineFormatter($format) );     
      $this->log->pushHandler( new \Monolog\Handler\BufferHandler($sh) );
              
      //$bch = new \Monolog\Handler\BrowserConsoleHandler;
      //$this->log->pushHandler( $bch );
        
    } else {
      $this->log = new logger();
    }
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
    $this->log->debug("Got SQLite database: $this->db_name");
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
    $this->log->debug("prepare: $sql");
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
    if( !is_readable($this->tables_directory) ) {
      $this->log->error('GET_TABLES: Tables directory not readable');
      return FALSE;
    }
    $this->tables = array();
    foreach( array_diff(scandir($this->tables_directory), $this->skip_files) as $f ) {
      $file = $this->tables_directory . "/$f";
      if( !is_file($file) || !is_readable($file) || !preg_match('/\.sql$/',$file) ) {
        continue; // .sql files only
      }
      $table_name = str_replace('.sql','',$f);
      $this->tables[$table_name] = file_get_contents($file);
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
 * Null PSR3 logger
 *
 */
class logger {
  public $stack;
  public function log($level, $message, array $context = array()) {
    global $debug;
    if( !$debug ) { return; }
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

// Global Utility Functions

/**
 * is_readable_file() - Tests if is a file exist, is readable, and is of a certain type.
 *
 * @param string $file The name of the file to test
 * @param string $type Optional. The file extension to allow. Defaults to '.php'
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


