<?php
/* *******************************************************************

Attogram PHP Framework
version 0.0.3

Copyright (c) 2014 Attogram Developers 
https://github.com/attogram/attogram/
Dual licensed: MIT License and/or GNU General Public License V3

function __construct() - loads config, routes request
function error404() - load ./404.php
function hook($hook, $return=false) - call a plugin hook
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

  var $version, $path, $admins, $actions, $plugins, $db;

  ////////////////////////////////////////////////////////////////////
  function __construct() {

    $this->hook('INIT');
    $this->version = '0.0.3';
    include_once('libs/config.php'); // sets $admins array
    if( is_array($admins) && $admins ) { $this->admins = $admins; }

    $this->hook('PRE-ROUTE');
    $this->path = str_replace($_SERVER['DOCUMENT_ROOT'],'',getcwd());    
    $base = substr_count($this->path, '/') + 1;
    $p = parse_url($_SERVER['REQUEST_URI']);
    $a = explode('/', $p['path']);
    for( $i = 0; $i < $base; $i++ ) { $b = array_shift($a); }
    $uri = $a;
    if( !$uri || !is_array($uri) ) { $this->error404(); }

    if( $uri[0]=='' && !isset($uri[1]) ) {
       $uri[0] = 'home'; // The Homepage
    } elseif( !isset($uri[2]) && isset($uri[1]) && $uri[1]=='' ) {
      if( !in_array($uri[0],$this->get_actions()) ) { $this->error404(); }
    } else {
      $this->error404();
    }
    $this->hook('POST-ROUTE');
  
    if( preg_match('/^admin/',$uri[0]) ) {
      if( !$this->is_admin() ) { $this->error404(); } // admin only
    }
    if($uri[sizeof($uri)-1]!='') {
      header('Location: ' . $_SERVER['REQUEST_URI'] . '/',TRUE,301); exit; // add trailing slash
    }

    $db = $this->get_db();
    
    $this->hook('PRE-ACTION');
    include('actions/' . $uri[0] . '.php');
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
  function hook($hook, $return=false) {
    $p = $this->get_plugins();
    $r = '';
    foreach( $p as $plugin ) {
      if( !method_exists($plugin,'hook') ) { continue; }
      $r .= $plugin->hook($hook);
    }
    if( $return ) { return $r; }
  }

  //////////////////////////////////////////////////////////////////////
  function get_plugins() {
    if( is_array($this->plugins) ) { return $this->plugins; }
    $this->plugins = array();
    $dir = 'plugins'; 
    foreach( array_diff(scandir($dir), array('.','..','.htaccess')) as $f ) {
      if( is_file("$dir/$f") && is_readable("$dir/$f") && preg_match('/\.php$/',$f) ) { // php files only
        include_once("$dir/$f");
        $pn = 'plugin_' . str_replace('.php','',$f);
        if( !class_exists($pn) ) { continue; }
        $pno = new $pn( $this );
        if( !method_exists($pno,'is_active') ) { continue; }
        if( !$pno->is_active() ) { continue; }
        $this->plugins[] = $pno;
      }
    }
    return $this->plugins;
  } 

  //////////////////////////////////////////////////////////////////////
  function get_actions() {
    if( is_array($this->actions) ) { return $this->actions; }
    $this->actions = array();
    $dir = 'actions';
    foreach( array_diff(scandir($dir), array('.','..','.htaccess','home.php')) as $f ) {
      if( is_file("$dir/$f") && is_readable("$dir/$f") && preg_match('/\.php$/',$f) ) { // php files only
        if( preg_match('/^admin/',$f) && !$this->is_admin() ) { continue; } // admin only
        $this->actions[] = str_replace('.php','',$f);
      }
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
    $statement = $db->prepare($sql);
    if( !$statement ) { $this->hook('ERROR_QUERY'); return array(); }
    while( $x = each($bind) ) { $statement->bindParam( $x[0], $x[1]); }	
    if( !$statement->execute() ) { $this->hook('ERROR_QUERY'); return array(); }
    $r = $statement->fetchAll(PDO::FETCH_ASSOC);
    if( !$r && $this->db->errorCode() != '00000') { $this->hook('ERROR_QUERY'); $r = array(); }
    $this->hook('POST-QUERY');
    return $r;
  }

  //////////////////////////////////////////////////////////////////////
  function queryb( $sql, $bind=array() ) {
    $this->hook('PRE-QUERY');
    $statement = $this->get_db()->prepare($sql);
    if( !$statement ) { $this->hook('ERROR_QUERY'); return false; }
    while( $x = each($bind) ) { $statement->bindParam( $x[0], $x[1]); }
    if( !$statement->execute() ) {  $this->hook('ERROR_QUERY'); return false; }
    $this->hook('POST-QUERY');
    return true;
  }

  //////////////////////////////////////////////////////////////////////
  function get_db() {
    if( is_object($this->db) ) { return $this->db; }
    $this->hook('PRE_DB');
    if( !in_array('sqlite', PDO::getAvailableDrivers() ) ) {  $this->hook('ERROR_DB'); return false; }
    $db_name = 'db/global';
    try {
      $this->db = new PDO('sqlite:'. $db_name);
    } catch(PDOException $e) {
      $this->hook('ERROR_DB');
      $this->db = false;
    }
    $this->hook('POST_DB');
    return $this->db;
  }

} // END of class attogram
