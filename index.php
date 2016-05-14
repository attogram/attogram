<?php
/* *******************************************************************

Attogram Framework
Version 0.2.8

Copyright (c) 2016 Attogram Developers
https://github.com/attogram/attogram/
Dual licensed: MIT License and/or GNU General Public License V3

******************************************************************* */

namespace Attogram;

$attogram = new attogram();

//////////////////////////////////////////////////////////////////////
class attogram {

  public $version, $path, $uri, $fof, $error,
         $sqlite_database, $db_name, $tables_dir,
         $templates_dir, $functions_dir,
         $actions_dir, $default_action, $actions, $action,
         $admins, $admin_dir, $admin_actions;

  ////////////////////////////////////////////////////////////////////
  function __construct() {
    $this->version = '0.2.8';
    $this->load_config('config.php');
    $this->sessioning();
    $this->get_functions();
    $this->sqlite_database = new sqlite_database( $this->db_name, $this->tables_dir );
    $this->route();
    if( !$this->action() ) {
      $this->error404();
    }
  }

  ////////////////////////////////////////////////////////////////////
  function sessioning() {
    session_start();
    if( isset($_GET['logoff']) ) {
      $_SESSION = array();
      session_destroy();
      session_start();
    }
  }

  ////////////////////////////////////////////////////////////////////
  function load_config( $config_file='' ) {
    if( !is_readable_php_file($config_file) ) {
      $this->error[] = 'LOAD_CONFIG: config file not found';
    } else {
      include_once($config_file);
    }
    if( !isset($config) || !is_array($config) ) {
      $this->error[] = 'LOAD_CONFIG: $config array not found';
    }
    $this->set_config( 'admins',         @$config['admins'],         array('127.0.0.1','::1') );
    $this->set_config( 'admin_dir',      @$config['admin_dir'],      'admin' );
    $this->set_config( 'default_action', @$config['default_action'], 'home' );
    $this->set_config( 'actions_dir',    @$config['actions_dir'],    'actions' );
    $this->set_config( 'templates_dir',  @$config['templates_dir'],  'templates' );
    $this->set_config( 'functions_dir',  @$config['functions_dir'],  'functions' );
    $this->set_config( 'fof',            @$config['fof'],            'templates/404.php' );
    $this->set_config( 'db_name',        @$config['db_name'],        'db/global' );
    $this->set_config( 'tables_dir',     @$config['tables_dir'],     'tables' );
  }

  ////////////////////////////////////////////////////////////////////
  function set_config( $var_name, $config_val='', $default_val ) {
    if( $config_val ) {
      $this->{$var_name} = $config_val;
    } else {
      $this->{$var_name} = $default_val;
    }
  }

  ////////////////////////////////////////////////////////////////////
  function trim_uri() {
    $this->uri = explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    //$this->error[] = 'TRIM_URI: URI:' . print_r($this->uri,1);
    
    $this->path = str_replace($_SERVER['DOCUMENT_ROOT'],'',str_replace('\\', '/', getcwd()));
    //$this->error[] = 'TRIM_URI: path: ' . $this->path;

    if( $this->path == '' ) { // top level install
      $this->error[] = 'TRIM_URI: top level install';
      if( $this->uri[0] == '' && $this->uri[1] == '' ) { // homepage
        $this->action = $this->actions_dir . '/' . $this->default_action . '.php';
        //$this->error[] = 'TRIM_URI: default action: ' . $this->action;
        return;
      } else {
        $trash = array_shift($this->uri);
      }
    } else { // sub level install
      //$this->error[] = 'TRIM_URI: sub level install';
      for( $i = 0; $i < sizeof($this->uri); $i++ ) {
        //$this->error[] = 'TRIM_URI: loop uri[' . $i . '] = ' . $this->uri[$i] . ' -- uri: ' . print_r($this->uri,1);
        if( $this->uri[$i] == basename($this->path) && $this->uri[$i] != '' ) {
          break; // found our level
        }
        $trash = array_shift($this->uri);
      }
    }
    //$this->error[] = 'TRIM_URI: trimmed URI:' . print_r($this->uri,1);
  }
  ////////////////////////////////////////////////////////////////////
  function route() {

    // todo: force trailing slash
    // todo: RESERVED WORDS: exceptions for existing attogram directories
    // $this->action_exceptions = array('actions','admin','db','functions','plugins','tables','templates','web',);
    
    $this->trim_uri();
  
    if( !$this->uri || !is_array($this->uri) || !isset($this->uri[0]) ) {
      $this->error[] = 'ROUTE: Invalid URI';
      $this->error404();
    }

    if( // The Homepage
        ($this->uri[0] == '' && !isset($this->uri[1])) //  top level: host/
     || ($this->uri[0] == '' && isset($this->uri[1]) && $this->uri[1]=='') ) // sublevel: host/dir/
    {
      $this->action = $this->actions_dir . '/' . $this->default_action . '.php';
      //$this->error[] = 'ROUTE: OK: default action: ' . $this->action;
      return;
    }

    if( isset($this->uri[2]) /*|| (isset($this->uri[1]) && !isset($this->uri[2])) */ ) { // if has subpath
      $this->error[] = 'ROUTE: subpath not supported';
      $this->error404();
    }

    if( !in_array($this->uri[0],$this->get_actions()) ) { // is action not available?
      if( $this->is_admin() && in_array($this->uri[0],$this->get_admin_actions()) ) { // check admin actions
        $this->action = $this->admin_dir . '/' . $this->uri[0] . '.php';
        return;
      }
      $this->error[] = 'ROUTE: action not found';
      $this->error404();
    }

    $this->action = $this->actions_dir . '/' . $this->uri[0] . '.php';
    //$this->error[] = 'ROUTE: OK: action: ' . $this->action;
}

  ////////////////////////////////////////////////////////////////////
  function action() {
    if( !isset($this->action) || !$this->action ) {
      $this->error[] = 'ACTION: action undefined';
      return FALSE;
    }
    if( !is_file($this->action) ) {
      $this->error[] = 'ACTION: Missing action: ' . htmlspecialchars($this->action);
      return FALSE;
    }
    if( !is_readable($this->action) ) {
      $this->error[] = 'ACTION:  Unreadable action: ' . htmlspecialchars($this->action);
      return FALSE;
    }
    include($this->action);
    return TRUE;
  }

  ////////////////////////////////////////////////////////////////////
  function error404() {
    if( is_readable_php_file($this->fof) ) {
      include($this->fof);
    } else {
      $this->error[] = '404 file not found';
      header('HTTP/1.0 404 Not Found');
      print '404 Not Found';
      if( isset($this->error) && $this->error ) {
        print '<pre>ERRORS: ' . print_r($this->error,1) . '</pre>';
      }
    }
    exit;
  }

  //////////////////////////////////////////////////////////////////////
  function get_actions() {
    if( is_array($this->actions) ) {
      return $this->actions;
    }
    $this->actions = array();
    if( !is_readable_dir($this->actions_dir) ) {
      return $this->actions;
    }
    foreach( array_diff(scandir($this->actions_dir), array('.','..','.htaccess','home.php')) as $f ) {
      if( is_readable_php_file($this->actions_dir . "/$f") ) { // php files only
        $this->actions[] = str_replace('.php','',$f); 
      }
    }
    return $this->actions;
  }

  //////////////////////////////////////////////////////////////////////
  function get_admin_actions() {
    if( !$this->is_admin() ) {
      return FALSE;
    }
    if( is_array($this->admin_actions) ) {
      return $this->admin_actions;
    }
    $this->admin_actions = array();
    if( !is_readable_dir($this->admin_dir) ) {
      return $this->admin_actions;
    }
    foreach( array_diff(scandir($this->admin_dir), array('.','..','.htaccess')) as $f ) {
      if( is_readable_php_file($this->admin_dir . "/$f") ) { // php files only
        $this->admin_actions[] = str_replace('.php','',$f); 
      }
    }
    return $this->admin_actions;
  }

  //////////////////////////////////////////////////////////////////////
  function get_functions() {
    if( !is_dir($this->functions_dir) || !is_readable($this->functions_dir) ) {
      return FALSE;
    }
    foreach( array_diff(scandir($this->functions_dir), array('.','..','.htaccess')) as $f ) {
      $file = $this->functions_dir . "/$f";
      if( !is_readable_php_file($file) ) { continue; } // php files only
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
      $this->error[] = 'LOGIN: Please enter username and password';
      return FALSE;
    }

    $user = $this->sqlite_database->query(
      'SELECT id, username, level, email FROM user WHERE username = :u AND password = :p',
      $bind=array(':u'=>$_POST['u'],':p'=>$_POST['p']) );

    if( $this->sqlite_database->db->errorCode() != '00000' ) { // query failed
      $this->error[] = 'LOGIN: Login system offline';
      return FALSE;
    }

    if( !$user ) { // no user, or wrong password
      $this->error[] = 'LOGIN: Invalid login';
      return FALSE;
    }
    if( !sizeof($user) == 1 ) { // corrupt data
      $this->error[] = 'LOGIN: Invalid login';
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
        $this->error[] = 'LOGIN: can not updated last login info';
    }

    return TRUE;
  }

  /**
   * is_logged_in() - is a user logged into the system?
   *
   * @return boolean
   */
   function is_logged_in() {
    if( isset($_SESSION['attogram_id'])
     && $_SESSION['attogram_id']
     && isset($_SESSION['attogram_username'])
     && $_SESSION['attogram_username'] ) {
      return TRUE;
    }
    return FALSE;
  }

} // END of class attogram



//////////////////////////////////////////////////////////////////////
class sqlite_database {

  public $db_name, $db, $tables_directory, $tables, $error;

  /**
   * __construct() - initialize database settings
   *
   * @param string $db_name relative path to the SQLite database file
   * @param string $tables_dir relative path to the table definitions directory
   *
   * @return void
   */
  function __construct( $db_name, $tables_dir ) {
    $this->db_name = $db_name;
    $this->tables_directory = $tables_dir;
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
      $this->error[] = 'GET_DB: SQLite PDO driver not found';
      return FALSE;
    }

    if( is_file( $this->db_name ) && !is_writeable( $this->db_name ) ) {
      $this->error[] = 'GET_DB: NOTICE: database file not writeable: ' . $this->db_name;
      // SELECT will work, UPDATE will not work
    }

    if( !is_file( $this->db_name ) ) {
      $this->error[] = 'GET_DB: NOTICE: creating database file: ' . $this->db_name;
    }

    try {
      $this->db = new \PDO('sqlite:'. $this->db_name);
    } catch(PDOException $e) {
      $this->error[] = 'GET_DB: error opening database';
      return FALSE;
    }

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
      $this->error[] = 'QUERY: Can not get database';
      return array();
    }
    $statement = $this->query_prepare($sql);
    if( !$statement ) {
      list($sqlstate, $error_code, $error_string) = @$this->db->errorInfo();
      $this->error[] = "QUERY: prepare failed: $sqlstate:$error_code:$error_string";;
      return array();
    }
    while( $x = each($bind) ) { $statement->bindParam( $x[0], $x[1]); }
    if( !$statement->execute() ) {
      $this->error[] = 'QUERY: Can not execute query';
      return array();
    }
    $r = $statement->fetchAll(\PDO::FETCH_ASSOC);
    if( !$r && $this->db->errorCode() != '00000') { // query failed
      $this->error[] = 'QUERY: Query failed';
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
      $this->error[] = 'QUERYB: Can not get database';
      return FALSE;
    }
    $statement = $this->query_prepare($sql);
    if( !$statement ) {
      list($sqlstate, $error_code, $error_string) = @$this->db->errorInfo();
      $this->error[] = "QUERYB: prepare failed: $sqlstate:$error_code:$error_string";;
      return FALSE;
    }
    while( $x = each($bind) ) {
      $statement->bindParam($x[0], $x[1]);
    }
    if( !$statement->execute() ) {
      $this->error[] = 'QUERYB: execute failed';
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
    $statement = $this->db->prepare($sql);
    if( $statement ) { return $statement; }
    list($sqlstate, $error_code, $error_string) = @$this->db->errorInfo();
    $this->error[] = "QUERY_PREPARE: Can not prepare sql: $sqlstate:$error_code:$error_string";
    if( $sqlstate == 'HY000' && $error_code == '1' && preg_match('/^no such table/', $error_string) ) { // table not found
      $table = str_replace('no such table: ', '', $error_string); // get table name
      if( $this->create_table($table) ) { // create table
        $this->error[] = "QUERY_PREPARE: Created table: $table";
        $statement = $this->db->prepare($sql);
        if( $statement ) { return $statement; } // try again
        $this->error[] = 'QUERY_PREPARE: Still can not prepare sql';
        return FALSE;
      } else {
        $this->error[] = "QUERY_PREPARE: Can not create table: $table";
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
    if( !is_readable_dir($this->tables_directory) ) {
      $this->error[] = 'GET_TABLES: Tables directory not readable';
      return FALSE;
    }
    $this->tables = array();
    foreach( array_diff(scandir($this->tables_directory), array('.','..','.htaccess')) as $f ) {
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
      $this->error[] = "CREATE_TABLE: Unknown table: $table";
      return FALSE;
    }
    if( !$this->queryb( $this->tables[$table] ) ) {
      $this->error[] = "CREATE_TABLE: failed to create: $table";
      return FALSE;
    }
    return TRUE;
  }

} // END of class sqlite_database



// Global Utility Functions

/**
 * is_readable_dir() - Tests if is a directory and is readable
 *
 * @param string $dir The name of the directory to test
 *
 * @return boolean
 */
function is_readable_dir( $dir=FALSE ) {
  if( is_dir($dir) && is_readable($dir) ) {
    return TRUE;
  }
  return FALSE;
}

/**
 * is_readable_php_file() - Tests if is a file exist, is readable, and is PHP
 *
 * @param string $file The name of the file to test
 * @param array $types Optional.  Array of file extensions to allow. Defaults to '.php'
 *
 * @return boolean
 */
function is_readable_file( $file=FALSE, $types=FALSE ) {
  if( !$file || !is_file($file) || !is_readable($file) ) {
    return FALSE;
  }
  if( !is_array($types) ) {
    $types = array('.php'); // default to PHP files only
  }
  foreach( $types as $type) {
    if( preg_match('/' . $type . '$/',$file) ) {
    return TRUE;
   }
  }
  return FALSE;
}

/**
 * is_readable_php_file() - Tests if is a file exist, is readable, and is PHP
 *
 * @param string $file The name of the file to test
 *
 * @return boolean
 */
function is_readable_php_file( $file=FALSE ) {
  return is_readable_file($file);
}

/**
 * to_list() - make a comma seperated list of items within an array or object
 *
 * @param mixed $x The input to be listed
 * @param string $sep The seperator between items
 *
 * @return string
 */
function to_list( $x, $sep=', ') {
  if( is_array($x) ) {
    $r = '';
    foreach($x as $v) {
      if( !is_object($v) && !is_array($v) ) {
        $r .= $v . $sep;
      } else {
        $r .= to_list($v) . $sep;
      }
    }
    return trim($r,$sep);
  }
  if( is_object($x) ) {
    return get_class($x);
  }
  return $x;
}
