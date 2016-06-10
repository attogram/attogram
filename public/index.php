<?php // Attogram Framework - Guru Meditation Loader - v0.0.1

namespace Attogram;

$guru = new guru_meditation_loader( // wake up the guru
  $debug             = false,
  $project_name      = 'The Attogram Framework',
  $config_file       = './config.php',
  $project_loader    = '../load.php',
  $vendor_autoloader = '../vendor/autoload.php',
  $vendor_download   = 'https://github.com/attogram/attogram-vendor/archive/master.zip',
  $required_classes  = array( '\Symfony\Component\HttpFoundation\Request',
                              '\Monolog\Logger',
                              '\Monolog\Handler\BufferHandler',
                              '\Monolog\Handler\StreamHandler',
                              '\Monolog\Formatter\LineFormatter',
                              'Parsedown', ) );
$guru->meditate();               // setup the configuration
//$guru->inner_eye();              // check .htaccess setup
$guru->expand_consciousness();   // run the vendor autoloader
$guru->inner_awareness();        // check for the required classes
$guru->tranquility();            // Load the project!

/** ****************************************************************************
********************************************************************************
********************************************************************************

Guru Meditation Loader v0.0.1

Copyright 2016 by The Attogram Developers https://github.com/attogram/attogram

Open Source Dual License: (MIT or GPL-3.0+) at your choosing

********************************************************************************
********************************************************************************
***************************************************************************** */
class guru_meditation_loader
{

  public $debug, $project_name, $config_file, $project_loader,
         $default_autoloader, $vendor_download, $required_classes, $autoloader;

  function __construct( bool   $debug,
                        string $project_name,
                        string $config_file,
                        string $project_loader,
                        string $default_autoloader,
                        string $vendor_download,
                        array  $required_classes ) {
    $this->debug              = $debug;
    $this->project_name       = $project_name;
    $this->config_file        = $config_file;
    $this->project_loader     = $project_loader;
    $this->default_autoloader = $default_autoloader;
    $this->vendor_download    = $vendor_download;
    $this->required_classes   = $required_classes;
    $this->debug('Guru Meditation Loader: awakening: ' . $this->project_name);
  }

  function meditate() {
    global $config;
    if( is_file($this->config_file) ) {
      if( !is_readable($this->config_file) ) {
        $this->guru_meditation_error('Config file exists, but is not readable');
      }
      $config_included = (include($this->config_file));
      if( !$config_included ) {
        $this->guru_meditation_error('Config file exists, but include failed');
      }
      $this->debug('meditate: config OK: ' . $this->config_file);
    } else {
      $this->debug('meditate: config_file NOT file');
    }
    if( !isset($config) ) {
      $this->debug('meditate: $config NOT set');
      $config = array();
    }
    if( !is_array($config) ) {
      $this->guru_meditation_error('$config is not an array');
    }
    if( !isset($config['autoloader']) ) {
      $config['autoloader'] = $this->default_autoloader;
    }
    $this->autoloader = $config['autoloader'];
    $this->debug('meditate: ' . $this->autoloader);
  } // end function meditate()

  function expand_consciousness() {
    if( isset($this->autoloader) && is_file($this->autoloader) && is_readable($this->autoloader) ) {
      include($this->autoloader); // dev todo - include check like in meditate()
      $this->debug('expand_consciousness: OK: ' . $this->autoloader);
      return;
    }
    $this->guru_meditation_error( 'autoloader file not found: ' . $this->autoloader );
  } // end function expand_consciousness()

  function inner_awareness() {
    $missing = array();
    foreach( $this->required_classes as $c ) {
      if( !class_exists($c) ) {
        $missing[] = $c;
        $this->debug('inner_awareness: Required Class NOT FOUND: ' . $c);
      }
      $this->debug('inner_awareness: OK: ' . $c);
    }
    if( !$missing ) {
      return;
    }
    $this->guru_meditation_error( 'Required Classes Missing: ' . implode(', ', $missing));
  } // end function inner_awareness()

  function tranquility() {
      $this->debug('tranquility: ' . $this->project_loader);
      if( $this->debug ) {
        print '<p>This was only a test.  If this was a real guru, we would load up: ' . $this->project_loader . '</p>';
        exit;
      }
      if( !is_file($this->project_loader) ) {
        $this->guru_meditation_error('Project loader file missing: ' . $this->project_loader);
      }
      if( !is_readable($this->project_loader) ) {
        $this->guru_meditation_error('Project loader file exists, but is not readable: ' . $this->project_loader);
      }
      $project_loader_included = (include($this->project_loader));
      if( !$project_loader_included ) {
        $this->guru_meditation_error('Project loader file exists, but include failed: ' . $this->project_loader);
      }
      $this->debug('tranquility: project_loader OK: ' . $this->project_loader);
  }

  function debug( $msg ) { // dev todo - save stack and load in attogram
    if( !$this->debug ) {
      return;
    }
    print '<pre style="padding:0;margin:0;">DEBUG: ' . print_r($msg,1) . '</pre>';
  }


  function guru_meditation_error( $error='' ) {
    print '<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Attogram Framework</title>
<style>body { font-family:"Helvetica Neue",Helvetica,Arial,sans-serif; font-size:14px;
color:#000; background-color:#fff; text-align:center; } </style></head><body>
<h2>Guru Meditation Error</h2><h3>' . $this->project_name . '</h3>';
  if( $error ) {
    print '<h1 style="color:red">Error: ' . $error . '</h1>';
  }
  print '</body></html>';
  exit;
  } // end function guru_meditation_error()

} // end class guru_meditation_error()
