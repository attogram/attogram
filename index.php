<?php
/* *******************************************************************

Attogram PHP Framework
version 0.1.3

Copyright (c) 2016 Attogram Developers 
https://github.com/attogram/attogram/
Dual licensed: MIT License and/or GNU General Public License V3

******************************************************************* */

$a = new attogram();

//////////////////////////////////////////////////////////////////////
class attogram {

  var $version, $config, $admins, $fof, $error,
      $path, $uri, $db, $db_name, $templates_dir,
      $plugins_dir, $plugins,
      $actions_dir, $default_action, $actions;

  ////////////////////////////////////////////////////////////////////
  function __construct() {
    $this->plugins_dir = 'plugins';
    $this->hook('PRE-INIT');
    session_start();
    if( isset($_GET['logoff']) ) { $_SESSION = array(); session_destroy(); session_start(); }
    $this->version = '0.1.3';
    $this->actions_dir = 'actions';
    $this->templates_dir = 'templates';
    $this->default_action = 'home';
    $this->db_name = 'db/global';
    $this->fof = '404.php';
    $this->config = 'config.php';
    $this->load_config();
    $this->hook('POST-INIT');
    $this->route();
    $this->action();
  }

  ////////////////////////////////////////////////////////////////////
  function login() {
    $this->hook('PRE-LOGIN');
    if( !isset($_POST['login']) || !$_POST['login'] ) { $this->error = 'Invalid login'; $this->hook('ERROR-LOGIN'); return FALSE; }
    if( (!isset($_POST['u']) && !isset($_POST['u'])) || (!$_POST['u'] && !$_POST['p']) ) { $this->error = 'Invalid login'; $this->hook('ERROR-LOGIN'); return FALSE; }
    if( !isset($_POST['u']) || !$_POST['u'] ) { $this->error = 'Missing username'; $this->hook('ERROR-LOGIN'); return FALSE; }
    if( !isset($_POST['p']) || !$_POST['p'] ) { $this->error ='Missing password'; $this->hook('ERROR-LOGIN'); return FALSE; }

    $user = $this->query(
      'SELECT id, username, level, email FROM user WHERE username = :u AND password = :p',
      $bind=array(':u'=>$_POST['u'],':p'=>$_POST['p']) );

    if( $this->db->errorCode() != '00000') { $this->error = 'Login system offline'; $this->hook('ERROR-LOGIN'); return FALSE; }

    if( !$user ) { $this->error = 'Invalid login'; $this->hook('ERROR-LOGIN'); return FALSE; } // no user, or wrong password
    if( !sizeof($user) == 1 ) { $this->error = 'Invalid login'; $this->hook('ERROR-LOGIN'); return FALSE; } // corrupt data

    $user = $user[0];
    $_SESSION['attogram_id'] = $user['id'];
    $_SESSION['attogram_username'] = $user['username'];
    $_SESSION['attogram_level'] = $user['level'];
    $_SESSION['attogram_email'] = $user['email'];

    $s = $this->queryb(
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

  ////////////////////////////////////////////////////////////////////
  function load_config() {
    $this->hook('PRE-CONFIG');
    if( !$this->is_readable_php_file($this->config) ) { return; }
    include_once($this->config);
    if( isset($admins) && is_array($admins) && $admins ) { $this->admins = $admins; }
    $this->hook('POST-CONFIG');
  }

  ////////////////////////////////////////////////////////////////////
  function route() {
    $this->hook('PRE-ROUTE');
    $this->uri = explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    $this->path = str_replace($_SERVER['DOCUMENT_ROOT'],'',str_replace('\\', '/', getcwd()));
    for( $i = 0; $i < sizeof($this->uri); $i++ ) { 
      if( $this->uri[$i] == basename($this->path) && $this->uri[$i] != '' ) { break; }
      $trash = array_shift($this->uri); 
    }
    if( !$this->uri || !is_array($this->uri) ) { $this->error404(); }
    if( // The Homepage
        ($this->uri[0]=='' && !isset($this->uri[1])) //  top level: host/
     || ($this->uri[0]=='' && isset($this->uri[1]) && $this->uri[1]=='') ) // sublevel: host/dir/
    {
      $this->uri[0]=$this->default_action;
      $this->uri[1]='';
      $this->hook('POST-ROUTE');
      return;
    }
    if( !in_array($this->uri[0],$this->get_actions()) || !$this->uri[1]=='' || isset($this->uri[2]) ) { $this->error404(); } // available actions
    if( preg_match('/^admin/',$this->uri[0]) ) { if( !$this->is_admin() ) { $this->error404(); } } // admin only
    if( $this->uri[sizeof($this->uri)-1]!='' ) { header('Location: ' . $_SERVER['REQUEST_URI'] . '/',TRUE,301); exit; } // add trailing slash
    $this->hook('POST-ROUTE');
  }

  ////////////////////////////////////////////////////////////////////
  function action() {
    $this->hook('PRE-ACTION');
    $f = $this->actions_dir . '/' . $this->uri[0] . '.php';
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
    if( $this->is_readable_php_file($this->fof) ) {
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

  ////////////////////////////////////////////////////////////////////
  function is_readable_dir($dir) {
    if( is_dir($dir) && is_readable($dir) ) { return TRUE; }
    return FALSE;
  }

  //////////////////////////////////////////////////////////////////////
  function is_readable_php_file($file) {
    if( is_file($file) && is_readable($file) && preg_match('/\.php$/',$file) ) { return TRUE; }
    return FALSE;
  }

  //////////////////////////////////////////////////////////////////////
  function get_plugins() {
    if( is_array($this->plugins) ) { return $this->plugins; }
    $this->plugins = array();
    if( !$this->is_readable_dir($this->plugins_dir) ) { return $this->plugins; }
    foreach( array_diff(scandir($this->plugins_dir), array('.','..','.htaccess')) as $f ) {
      if( !$this->is_readable_php_file($this->plugins_dir . "/$f") ) { continue; } // php files only
      include_once($this->plugins_dir . "/$f");
      $p = 'plugin_' . str_replace('.php','',$f);
      if( !class_exists($p) ) { continue; }
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
    if( !$this->is_readable_dir($this->actions_dir) ) { return $this->actions; }
    foreach( array_diff(scandir($this->actions_dir), array('.','..','.htaccess','home.php')) as $f ) {
      if( !$this->is_readable_php_file($this->actions_dir . "/$f") ) { continue; } // php files only
      if( preg_match('/^admin/',$f) && !$this->is_admin() ) { continue; } // admin only
      $this->actions[] = str_replace('.php','',$f);
    }
    return $this->actions;
  }

  //////////////////////////////////////////////////////////////////////
  function is_admin() {
    if( isset($_GET['noadmin']) ) { return false; }
    if( !isset($this->admins) || !is_array($this->admins) ) { return false; }
    if( @in_array($_SERVER['REMOTE_ADDR'],$this->admins) ) { return true; }
    return false;
  }

  //////////////////////////////////////////////////////////////////////
  function query( $sql, $bind=array() ) {
    $this->hook('PRE-QUERY');
    $db = $this->get_db();
    if( !$db ) { $this->hook('ERROR-QUERY'); return array(); }
    $statement = $db->prepare($sql);
    if( !$statement ) { $this->hook('ERROR-QUERY'); return array(); }
    while( $x = each($bind) ) { $statement->bindParam( $x[0], $x[1]); }
    if( !$statement->execute() ) { $this->hook('ERROR-QUERY'); return array(); }
    $r = $statement->fetchAll(PDO::FETCH_ASSOC);
    if( !$r && $this->db->errorCode() != '00000') { $this->hook('ERROR-QUERY'); $r = array(); }
    $this->hook('POST-QUERY');
    return $r;
  }

  //////////////////////////////////////////////////////////////////////
  function queryb( $sql, $bind=array() ) {
    $this->hook('PRE-QUERY');
    $db = $this->get_db();
    if( !$db ) { $this->hook('ERROR-QUERY'); return false; }
    $statement = $db->prepare($sql);
    if( !$statement ) { $this->hook('ERROR-QUERY'); return false; }
    while( $x = each($bind) ) { $statement->bindParam( $x[0], $x[1]); }
    if( !$statement->execute() ) {$this->hook('ERROR-QUERY'); return false; }
    $this->hook('POST-QUERY');
    return true;
  }

  //////////////////////////////////////////////////////////////////////
  function get_db() {
    if( is_object($this->db) ) { return $this->db; }
    $this->hook('PRE-DB');
    if( !in_array('sqlite', PDO::getAvailableDrivers() ) ) {
      $this->error = 'sqlite PDO driver not found';
      $this->hook('ERROR-DB');
     return false; }
    try {
      $this->db = new PDO('sqlite:'. $this->db_name);
    } catch(PDOException $e) {
      $this->error = 'error connnecting to PDO sqlite database';
      $this->hook('ERROR-DB');
      $this->db = false;
    }
    $this->hook('POST-DB');
    return $this->db;
  }

  //////////////////////////////////////////////////////////////////////
  function create_table($table='') {
    switch($table) {

      default: return FALSE; break;

        case 'user': 
          $sql = "CREATE TABLE 'user' (
 'id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
 'username' TEXT UNIQUE NOT NULL,
 'password' TEXT NOT NULL,
 'email' TEXT NOT NULL,
 'level' INTEGER NOT NULL DEFAULT '0',
 'last_login' DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
 'last_host' TEXT NOT NULL
)";
        break;

        case 'contact':
          $sql = "CREATE TABLE 'contact' (
 'id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
 'time' DATETIME,
 'email' TEXT,
 'msg' TEXT,
 'ip' TEXT,
 'agent' TEXT
)";
        break;
    }
    if( $this->queryb($sql) ) { return TRUE; }

    $this->error = 'Cannot create table';
    $this->hook('ERROR-CREATE-TABLE');
    return FALSE;
  }
  
} // END of class attogram