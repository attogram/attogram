<?php
/* *******************************************************************

Attogram PHP Framework
version 0.2.1

Copyright (c) 2016 Attogram Developers
https://github.com/attogram/attogram/
Dual licensed: MIT License and/or GNU General Public License V3

******************************************************************* */

namespace Attogram;

$attogram = new attogram();

//////////////////////////////////////////////////////////////////////
class attogram {

  var $version, $admins, $fof, $error,
      $path, $uri, $sqlite_database, $templates_dir, $functions_dir,
      $plugins_dir, $plugins,
      $actions_dir, $default_action, $actions, $action;

  ////////////////////////////////////////////////////////////////////
  function __construct() {

    $this->version = '0.2.1';

    $this->load_config('config.php');

    $this->hook('INIT');
    
    session_start();

    if( isset($_GET['logoff']) ) {
      $_SESSION = array();
      session_destroy();
      session_start();
    }

    $this->get_functions();
        
    $this->sqlite_database = new sqlite_database();

    $this->route();

    $this->action();
  }

  ////////////////////////////////////////////////////////////////////
  function load_config( $config_file='' ) {

    if( !is_readable_php_file($config_file) ) {
      $this->error[] = 'LOAD_CONFIG: config file unreadable';
      return;
    }

    include_once($config_file);

    if( !isset($config) || !is_array($config) ) {
      $this->error[] = 'LOAD_CONFIG: config array not found';
      return;
    }

    if( isset($config['admins']) && is_array($config['admins'])  ) {
      $this->admins = $config['admins'];
    } else {
      $this->admins = array( '127.0.0.1', '::1' );  // Default value: IP4 localhost, IP6 localhost
    }
 
    if( isset($config['default_action']) && is_string($config['default_action']) ) {
      $this->default_action = $config['default_action'];
    } else {
      $this->default_action = 'home';
    }

    if( isset($config['actions_dir']) && is_string($config['actions_dir']) ) {
      $this->actions_dir = $config['actions_dir'];
    } else {
      $this->actions_dir = 'actions';
    }

    if( isset($config['plugins_dir']) && is_string($config['plugins_dir']) ) {
      $this->plugins_dir = $config['plugins_dir'];
    } else {
      $this->plugins_dir = 'plugins';
    }

    if( isset($config['templates_dir']) && is_string($config['templates_dir']) ) {
      $this->templates_dir = $config['templates_dir'];
    } else {
      $this->templates_dir = 'templates';
    }

    if( isset($config['functions_dir']) && is_string($config['functions_dir']) ) {
      $this->functions_dir = $config['functions_dir'];
    } else {
      $this->functions_dir = 'functions';
    }

    if( isset($config['fof']) && is_string($config['fof']) ) {
      $this->fof = $config['fof'];
    } else {
      $this->fof = '404.php';
    }

  }

  ////////////////////////////////////////////////////////////////////
  function route() {
    $this->uri = explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    $this->path = str_replace($_SERVER['DOCUMENT_ROOT'],'',str_replace('\\', '/', getcwd()));

    if( $this->path == '' ) { // top level install
      if( $this->uri[0] == '' && $this->uri[1] == '' ) { // homepage
        $this->uri[0] = $this->action = $this->default_action;
        return;
      } else {
        $trash = array_shift($this->uri);
      }
    } else { // sub level install
      for( $i = 0; $i < sizeof($this->uri); $i++ ) {
        if( $this->uri[$i] == basename($this->path) && $this->uri[$i] != '' ) { break; }
        $trash = array_shift($this->uri);
      }
    }

    if( !$this->uri || !is_array($this->uri) ) { $this->error404(); }

    
    if( // The Homepage
        ($this->uri[0] == '' && !isset($this->uri[1])) //  top level: host/
     || ($this->uri[0] == '' && isset($this->uri[1]) && $this->uri[1]=='') ) // sublevel: host/dir/
    {
      $this->uri[0] = $this->action = $this->default_action;
      $this->uri[1] = '';
      return;
    }
 
    if( !in_array($this->uri[0],$this->get_actions()) // is action not available?
      //|| !$this->uri[1]=='' // is not correct slash format?
      || isset($this->uri[2]) // if has subpath
      || (preg_match('/^admin/',$this->uri[0]) && !$this->is_admin() ) // admin only actions
    ) { 
      $this->error404(); 
    }
    
// buggy with ?vars at end of url    
//    if( $this->uri[sizeof($this->uri)-1]!='' ) { // add trailing slash
//      header('Location: ' . $_SERVER['REQUEST_URI'] . '/',TRUE,301); 
//      exit; 
//    } 
    
    $this->action = $this->uri[0];

}

  ////////////////////////////////////////////////////////////////////
  function action() {
    $f = $this->actions_dir . '/' . $this->action . '.php';
    if( !is_file($f) ) {
    $this->error[] = 'ACTION: Missing action.  Please create ' . htmlspecialchars($f);
    exit;
    }
    if( !is_readable($f) ) {
      $this->error[] = 'ACTION:  Unreadable action. Please make readable ' . htmlspecialchars($f);
      exit;
    }
    include($f);
  }

  ////////////////////////////////////////////////////////////////////
  function error404() { 
    if( is_readable_php_file($this->fof) ) {
      include($this->fof);
    } else {
      header('HTTP/1.0 404 Not Found');
      print '404 Not Found';
    }
    exit;
  }

  ////////////////////////////////////////////////////////////////////
  function hook( $hook, $return=false ) {
    $p = $this->get_plugins();
    $r = '';
    foreach( $p as $plugin ) { $r .= $plugin->hook($hook); }
    if( $return ) { return $r; }
  }

  //////////////////////////////////////////////////////////////////////
  function get_plugins() {
    if( is_array($this->plugins) ) { return $this->plugins; }
    $this->plugins = array();
    if( !is_readable_dir($this->plugins_dir) ) { return $this->plugins; }
    foreach( array_diff(scandir($this->plugins_dir), array('.','..','.htaccess')) as $f ) {
      if( !is_readable_php_file($this->plugins_dir . "/$f") ) { continue; } // php files only
      include_once($this->plugins_dir . "/$f");
      $p = '\\Attogram\\plugin_' . str_replace('.php','',$f);
      if( !class_exists($p) ) { 
        $this->error[] = "GET_PLUGINS: no class $p in file $f";
        continue; 
      }
      $po = new $p( $this );
      if( !method_exists($po,'is_active') ) { continue; }
      if( !$po->is_active() ) { continue; }
      if( !method_exists($po,'hook') ) { continue; }
      $this->plugins[] = $po;
    }
    return $this->plugins;
  }

  //////////////////////////////////////////////////////////////////////
  function get_actions() {
    if( is_array($this->actions) ) { return $this->actions; }
    $this->actions = array();
    if( !is_readable_dir($this->actions_dir) ) { return $this->actions; }
    foreach( array_diff(scandir($this->actions_dir), array('.','..','.htaccess','home.php')) as $f ) {
      if( !is_readable_php_file($this->actions_dir . "/$f") ) { continue; } // php files only
      if( preg_match('/^admin/',$f) && !$this->is_admin() ) { continue; } // admin only
      $this->actions[] = str_replace('.php','',$f);
    }
    return $this->actions;
  }

  //////////////////////////////////////////////////////////////////////
  function get_functions() {
    if( !is_dir($this->functions_dir) || !is_readable($this->functions_dir) ) {
      return FALSE;
    }
    foreach( array_diff(scandir($this->functions_dir), array('.','..','.htaccess')) as $f ) {
      $file = $this->functions_dir . "/$f";
      if( !is_file($file) || !is_readable($file) || !preg_match('/\.php$/',$file) ) { continue; } // php files only
      include_once($file);
    }
  }

  //////////////////////////////////////////////////////////////////////
  function is_admin() {
    if( isset($_GET['noadmin']) ) { return false; }
    if( !isset($this->admins) || !is_array($this->admins) ) { return false; }
    if( @in_array($_SERVER['REMOTE_ADDR'],$this->admins) ) { return true; }
    return false;
  }

  ////////////////////////////////////////////////////////////////////
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

  ////////////////////////////////////////////////////////////////////
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

  var $db_name, $db,
      $tables_directory, $tables, 
      $error;

  //////////////////////////////////////////////////////////////////////
  function __construct( $db_name='' ) {
    $this->db_name = $db_name ? $db_name : 'db/global'; // default name
    $this->tables_directory = 'tables';
    $this->error = false;
  }

  //////////////////////////////////////////////////////////////////////
  function get_db() {

    if( is_object($this->db) && get_class($this->db) == 'PDO' ) { 
      return $this->db; // if PDO database object already set
    } 
    
    if( !in_array('sqlite', \PDO::getAvailableDrivers() ) ) {
      $this->error[] = 'GET_DB: sqlite PDO driver not found';
      return $this->db = FALSE;
    }
    try {
      $this->db = new \PDO('sqlite:'. $this->db_name);
    } catch(PDOException $e) {
      $this->error[] = 'GET_DB: error connnecting to PDO sqlite database';
      return $this->db = FALSE;
    }
    return $this->db;
  }

  //////////////////////////////////////////////////////////////////////
  function query( $sql, $bind=array() ) {
    $db = $this->get_db();
    if( !$this->db ) {
      $this->error[] = 'QUERY: Can not get database';
      return array();
    }
    $statement = $this->query_prepare($sql);
    if( !$statement ) { 
      $this->error[] = 'QUERY: Can not prepare sql';
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

  //////////////////////////////////////////////////////////////////////
  function queryb( $sql, $bind=array() ) {
    $db = $this->get_db();
    if( !$this->db ) {
      $this->error[] = 'QUERYB: Unable to get Database';
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

  //////////////////////////////////////////////////////////////////////
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

  //////////////////////////////////////////////////////////////////////
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

  //////////////////////////////////////////////////////////////////////
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

////////////////////////////////////////////////////////////////////
function is_readable_dir( $dir=FALSE ) {
  if( is_dir($dir) && is_readable($dir) ) {
    return TRUE;
  }
  return FALSE;
}

//////////////////////////////////////////////////////////////////////
function is_readable_php_file( $file=FALSE ) {
  if( is_file($file) && is_readable($file) && preg_match('/\.php$/',$file) ) {
    return TRUE;
  }
  return FALSE;
}

//////////////////////////////////////////////
function to_list($x) {
  if( is_array($x) ) {
    $r = '';
    foreach($x as $v) {
      if( !is_object($v) && !is_array($v) ) {
        $r .= $v . ', ';
      } else {
        $r .= to_list($v);
      }
    }
    return trim($r,', ');
  }
  if( is_object($x) ) {
    return print_r($x,1) . '<br />';
  }
  return $x;
}
