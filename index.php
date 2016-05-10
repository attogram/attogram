<?php
/* *******************************************************************

Attogram PHP Framework
version 0.2.0

Copyright (c) 2016 Attogram Developers
https://github.com/attogram/attogram/
Dual licensed: MIT License and/or GNU General Public License V3

******************************************************************* */

namespace Attogram;

$attogram = new attogram();

//////////////////////////////////////////////////////////////////////
class attogram {

  var $version, $config, $admins, $fof, $error,
      $path, $uri, $sqlite_database, $templates_dir, $functions_dir,
      $plugins_dir, $plugins,
      $actions_dir, $default_action, $actions, $action;

  ////////////////////////////////////////////////////////////////////
  function __construct() {
    $this->functions_dir = 'functions';    
    $this->get_functions(); // Get all global utility functions
    $this->plugins_dir = 'plugins';
    $this->hook('PRE-INIT');
    session_start();
    if( isset($_GET['logoff']) ) { $_SESSION = array(); session_destroy(); session_start(); }
    $this->version = '0.2.0';
    $this->actions_dir = 'actions';
    $this->templates_dir = 'templates';

    $this->default_action = 'home';

    $this->sqlite_database = new sqlite_database();

    $this->fof = '404.php';
    $this->config = 'config.php';
    $this->load_config();

    $this->hook('POST-INIT');
    $this->route();
    $this->action();
  }

  ////////////////////////////////////////////////////////////////////
  function load_config() {
    $this->hook('PRE-CONFIG');
    if( !is_readable_php_file($this->config) ) { return; }
    include_once($this->config);
    if( isset($admins) && is_array($admins) && $admins ) { $this->admins = $admins; }
    $this->hook('POST-CONFIG');
  }

  ////////////////////////////////////////////////////////////////////
  function route() {
    $this->hook('PRE-ROUTE');
    $this->uri = explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    $this->path = str_replace($_SERVER['DOCUMENT_ROOT'],'',str_replace('\\', '/', getcwd()));

    if( $this->path == '' ) { // top level install
      if( $this->uri[0] == '' && $this->uri[1] == '' ) { // homepage
        $this->uri[0] = $this->action = $this->default_action;
        $this->hook('POST-ROUTE');
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
      $this->hook('POST-ROUTE');
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
    
    $this->hook('POST-ROUTE');
  }

  ////////////////////////////////////////////////////////////////////
  function action() {
    $this->hook('PRE-ACTION');
    $f = $this->actions_dir . '/' . $this->action . '.php';
    if( !is_file($f) ) {
    $this->error = 'Missing action.  Please create ' . htmlspecialchars($f);
    $this->hook('ERROR-ACTION');
    exit;
    }
    if( !is_readable($f) ) {
      $this->error = 'Unreadable action. Please make readable ' . htmlspecialchars($f);
      $this->hook('ERROR-ACTION');
      exit;
    }
    include($f);
    $this->hook('POST-ACTION');
  }

  ////////////////////////////////////////////////////////////////////
  function error404() { 
    $this->hook('PRE-404');
    if( is_readable_php_file($this->fof) ) {
      include($this->fof);
    } else {
      header('HTTP/1.0 404 Not Found');
      print '404 Not Found';
    }
    $this->hook('POST-404');
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
        //print "<pre>ERROR: no class $p in file $f</pre>";
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

    $this->hook('PRE-LOGIN');

    if( !isset($_POST['u']) || !isset($_POST['p']) || !$_POST['u'] || !$_POST['p'] ) {
      $this->error = 'Please enter username and password';
      $this->hook('ERROR-LOGIN');
      return FALSE;
    }

    $user = $this->sqlite_database->query(
      'SELECT id, username, level, email FROM user WHERE username = :u AND password = :p',
      $bind=array(':u'=>$_POST['u'],':p'=>$_POST['p']) );

    if( $this->sqlite_database->db->errorCode() != '00000' ) { // query failed
      $this->error = 'Login system offline';
      $this->hook('ERROR-LOGIN');
      return FALSE;
    }

    if( !$user ) { // no user, or wrong password
      $this->error = 'Invalid login'; 
      $this->hook('ERROR-LOGIN'); 
      return FALSE; 
    }
    if( !sizeof($user) == 1 ) { // corrupt data
      $this->error = 'Invalid login'; 
      $this->hook('ERROR-LOGIN'); 
      return FALSE; 
    }

    $user = $user[0];
    $_SESSION['attogram_id'] = $user['id'];
    $_SESSION['attogram_username'] = $user['username'];
    $_SESSION['attogram_level'] = $user['level'];
    $_SESSION['attogram_email'] = $user['email'];

    $s = $this->sqlite_database->queryb(
      "UPDATE user SET last_login = datetime('now'), last_host = :last_host WHERE id = :id",
      $bind = array(':id'=>$user['id'], ':last_host'=>$_SERVER['REMOTE_ADDR'])
    );

    $this->hook('POST-LOGIN');
    return TRUE;
  }

  ////////////////////////////////////////////////////////////////////
  function is_logged_in() {
    if( isset($_SESSION['attogram_id']) && $_SESSION['attogram_id'] && isset($_SESSION['attogram_username']) && $_SESSION['attogram_username'] ) { return TRUE; }
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
      $this->error = 'sqlite PDO driver not found';
      return $this->db = FALSE;
    }
    try {
      $this->db = new \PDO('sqlite:'. $this->db_name);
    } catch(PDOException $e) {
      $this->error = 'error connnecting to PDO sqlite database';
      return $this->db = FALSE;
    }
    return $this->db;
  }

  //////////////////////////////////////////////////////////////////////
  function query( $sql, $bind=array() ) {
    $db = $this->get_db();
    if( !$this->db ) {
      $this->error = 'Can not get database';
      return array();
    }
    $statement = $this->query_prepare($sql);
    if( !$statement ) { 
      $this->error .= 'Can not prepare sql';
      return array();
    }
    while( $x = each($bind) ) { $statement->bindParam( $x[0], $x[1]); }
    if( !$statement->execute() ) {
      $this->error = 'Can not execute query';
      return array();
    }
    $r = $statement->fetchAll(\PDO::FETCH_ASSOC);
    if( !$r && $this->db->errorCode() != '00000') { // query failed
      $this->error = 'Query failed';
      $r = array();
    }
    return $r;
  }

  //////////////////////////////////////////////////////////////////////
  function queryb( $sql, $bind=array() ) {
    $db = $this->get_db();
    if( !$this->db ) { return false; }
    $statement = $this->query_prepare($sql);
    if( !$statement ) { return false; }
    while( $x = each($bind) ) {
      $statement->bindParam($x[0], $x[1]);
    }
    if( !$statement->execute() ) { return false; }
    return true;
  }

  //////////////////////////////////////////////////////////////////////
  function query_prepare( $sql ) {
    $statement = $this->db->prepare($sql);
    if( $statement ) { return $statement; }
    list($sqlstate, $error_code, $error_string) = @$this->db->errorInfo();
    $this->error = "Can not prepare sql: $sqlstate $error_code $error_string";

    if( $sqlstate == 'HY000' && $error_code == '1' && preg_match('/^no such table/', $error_string) ) { // table not found
      $table = str_replace('no such table: ', '', $error_string); // get table name
      if( $this->create_table($table) ) { // create table
        $this->error .= ' - Created table: ' . $table;
        $statement = $this->db->prepare($sql);
        if( $statement ) { return $statement; } // try again
        $this->error .= ' - Still can not prepare sql';
        return FALSE;
      } else {
        $this->error .= ' - Can not create table';
        return FALSE;
      }
    }
  }

  //////////////////////////////////////////////////////////////////////
  function get_tables() {
    if( isset($this->tables) && is_array($this->tables) ) { return TRUE;}
    if( !is_readable_dir($this->tables_directory) ) { return FALSE; }
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
    if( !isset($this->tables[$table]) ) {
      $this->error = 'Unknown table';
      return FALSE;
    }
    if( !$this->queryb( $this->tables[$table] ) ) {
      $this->error .= ' - Cannot create table';
      return FALSE;
    }
    return TRUE;
  }
  
} // END of class sqlite_database
