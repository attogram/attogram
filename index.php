<?php
/* *******************************************************************

Attogram PHP Framework
version 0.0.6

Copyright (c) 2014 Attogram Developers 
https://github.com/attogram/attogram/
Dual licensed: MIT License and/or GNU General Public License V3

function __construct() - loads config, routes request
function error404() - load ./404.php
function hook( $hook, $return=false ) - call a plugin hook
function get_plugins() - get array of active plugin objects
function get_actions() - get array of action names
function is_admin() - is admin? based on admin IP whitelist in ./libs/config.php
function query( $sql, $bind=array() ) - database query, return results as array
function queryb( $sql, $bind=array() ) - database query, return boolean true/false
function get_db() - get the PDO database object

******************************************************************* */

$a = new attogram();

//////////////////////////////////////////////////////////////////////
class attogram {

  var $version, $path, $admins, $base, $actions, $plugins, $db;

  ////////////////////////////////////////////////////////////////////
  function __construct() {

    $this->hook('PRE-INIT');
    $this->version = '0.0.6';
    $config = 'libs/config.php';
    if( is_file($config) && is_readable($config) ) {
      include_once($config);
      if( isset($admins) && is_array($admins) && $admins ) { $this->admins = $admins; }
      if( isset($base) && is_numeric($base) && $base ) { $this->base = $base; }
    }
    $this->hook('POST-INIT');

    $this->hook('PRE-ROUTE');
    $uri = explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    $this->path = str_replace($_SERVER['DOCUMENT_ROOT'],'',getcwd());  // oops - doesn't work on windows
    for( $i = 0; $i < (substr_count($this->path, '/') + $this->base); $i++ ) { $b = array_shift($uri); }
    if( !$uri || !is_array($uri) ) { $this->error404(); }
    if( $uri[0]=='' && !isset($uri[1]) ) { $uri[0]='home'; $uri[1]=''; goto postroute; } // The Homepage
    if( !in_array($uri[0],$this->get_actions()) || !$uri[1]=='' || isset($uri[2]) ) { $this->error404(); } // available actions
    if( preg_match('/^admin/',$uri[0]) ) { if( !$this->is_admin() ) { $this->error404(); } } // admin only
    if( $uri[sizeof($uri)-1]!='' ) { header('Location: ' . $_SERVER['REQUEST_URI'] . '/',TRUE,301); exit; } // add trailing slash
    postroute: $this->hook('POST-ROUTE');

    $this->hook('PRE-ACTION');
    $f = 'actions/' . $uri[0] . '.php';
    if( !is_file($f) ) { $this->hook('ERROR-ACTION'); print 'Missing action.  Please create: ' . $f; exit; }
    if( !is_readable($f) ) { $this->hook('ERROR-ACTION'); print 'Unreadable action.  Please make readable: ' . $f; exit; }  
    include($f);
    $this->hook('POST-ACTION');
  }

  ////////////////////////////////////////////////////////////////////
  function error404() { 
    $this->hook('PRE-404');
    include('404.php'); 
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
    $dir = 'plugins';
    if( !is_dir($dir) || !is_readable($dir) ) { return $this->plugins; } 
    foreach( array_diff(scandir($dir), array('.','..','.htaccess')) as $f ) {
      if( !is_file("$dir/$f") || !is_readable("$dir/$f") || !preg_match('/\.php$/',$f) ) { continue; } // php files only
      include_once("$dir/$f");
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
    $dir = 'actions';
    if( !is_dir($dir) || !is_readable($dir) ) { return $this->actions; } 
    foreach( array_diff(scandir($dir), array('.','..','.htaccess','home.php')) as $f ) {
      if( !is_file("$dir/$f") || !is_readable("$dir/$f") || !preg_match('/\.php$/',$f) ) { continue; } // php files only
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
    if( !$statement->execute() ) {  $this->hook('ERROR-QUERY'); return false; }
    $this->hook('POST-QUERY');
    return true;
  }

  //////////////////////////////////////////////////////////////////////
  function get_db() {
    if( is_object($this->db) ) { return $this->db; }
    $this->hook('PRE-DB');
    if( !in_array('sqlite', PDO::getAvailableDrivers() ) ) {  $this->hook('ERROR-DB'); return false; }
    $db_name = 'db/global';
    try {
      $this->db = new PDO('sqlite:'. $db_name);
    } catch(PDOException $e) {
      $this->hook('ERROR-DB');
      $this->db = false;
    }
    $this->hook('POST-DB');
    return $this->db;
  }

} // END of class attogram
