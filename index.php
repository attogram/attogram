<?php
/* *******************************************************************

Attogram PHP Framework
version 0.2.3

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
         $plugins_dir, $plugins,
         $actions_dir, $default_action, $actions, $action,
         $admins, $admin_dir, $admin_actions;

  ////////////////////////////////////////////////////////////////////
  function __construct() {
    $this->version = '0.2.3';
    $this->load_config('config.php');
    $this->hook('INIT');
    session_start();
    if( isset($_GET['logoff']) ) {
      $_SESSION = array();
      session_destroy();
      session_start();
    }
    $this->get_functions();
    $this->sqlite_database = new sqlite_database( $this->db_name, $this->tables_dir );
    $this->route();
    $this->action();
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
    $this->set_config( 'plugins_dir',    @$config['plugins_dir'],    'plugins' );
    $this->set_config( 'templates_dir',  @$config['templates_dir'],  'templates' );
    $this->set_config( 'functions_dir',  @$config['functions_dir'],  'functions' );
    $this->set_config( 'fof',            @$config['fof'],            '404.php' );
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
  function route() {
    $this->uri = explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    $this->path = str_replace($_SERVER['DOCUMENT_ROOT'],'',str_replace('\\', '/', getcwd()));

    if( $this->path == '' ) { // top level install
      if( $this->uri[0] == '' && $this->uri[1] == '' ) { // homepage
        $this->action = $this->actions_dir . '/' . $this->default_action . '.php';
        return;
      } else {
        $trash = array_shift($this->uri);
      }
    } else { // sub level install
      for( $i = 0; $i < sizeof($this->uri); $i++ ) {
        if( $this->uri[$i] == basename($this->path) && $this->uri[$i] != '' ) {
          break; // found our level
        }
        $trash = array_shift($this->uri);
      }
    }

    if( !$this->uri || !is_array($this->uri) ) {
      $this->error[] = 'ROUTE: Invalid URI';
      $this->error404();
    }

    if( // The Homepage
        ($this->uri[0] == '' && !isset($this->uri[1])) //  top level: host/
     || ($this->uri[0] == '' && isset($this->uri[1]) && $this->uri[1]=='') ) // sublevel: host/dir/
    {

      $this->action = $this->actions_dir . '/' . $this->default_action . '.php';
      $this->uri[1] = '';
      return;
    }

    if( !in_array($this->uri[0],$this->get_actions()) // is action not available?
      //|| !$this->uri[1]=='' // is not correct slash format?
      || isset($this->uri[2]) // if has subpath
      || (preg_match('/^admin/',$this->uri[0]) && !$this->is_admin() ) // admin only actions
    ) {

      if( $this->is_admin() ) { // check admin actions
        if( in_array($this->uri[0],$this->get_admin_actions()) ) {
          $this->action = $this->admin_dir . '/' . $this->uri[0] . '.php';
          return;
        }
      }
      $this->error[] = 'ROUTE: action not found';
      $this->error404();
  }

// buggy with ?vars at end of url
//    if( $this->uri[sizeof($this->uri)-1]!='' ) { // add trailing slash
//      header('Location: ' . $_SERVER['REQUEST_URI'] . '/',TRUE,301);
//      exit;
//    }

    $this->action = $this->actions_dir . '/' . $this->uri[0] . '.php';
}

  ////////////////////////////////////////////////////////////////////
  function action() {
    //$f = $this->actions_dir . '/' . $this->action . '.php';
    $f = $this->action;
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
    if( is_array($this->actions) ) {
      return $this->actions;
    }
    $this->actions = array();
    if( !is_readable_dir($this->actions_dir) ) {
      return $this->actions;
    }
    foreach( array_diff(scandir($this->actions_dir), array('.','..','.htaccess','home.php')) as $f ) {
      if( !is_readable_php_file($this->actions_dir . "/$f") ) {
        continue; // php files only
      }
    //  if( preg_match('/^admin/',$f) && !$this->is_admin() ) {
    //    continue; // admin only
    //  }
      $this->actions[] = str_replace('.php','',$f);
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
      if( !is_readable_php_file($this->admin_dir . "/$f") ) {
        continue; // php files only
      }
      $this->admin_actions[] = str_replace('.php','',$f);
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

  public $db_name, $db, $tables_directory, $tables, $error;

  //////////////////////////////////////////////////////////////////////
  function __construct( $db_name, $tables_dir ) {
    $this->db_name = $db_name;
    $this->tables_directory = $tables_dir;
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
