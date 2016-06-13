<?php // Attogram Framework - Guru Meditation Loader - v0.0.2

namespace Attogram;

$guru = new guru_meditation_loader( // wake up the guru
  $project_name      = 'The Attogram Framework',
  $config_file       = './config.php',
  $project_classes   = '../attogram/',
  $project_loader    = '../load.php',
  $vendor_autoloader = '../vendor/autoload.php',
  $vendor_download   = 'https://github.com/attogram/attogram-vendor/archive/master.zip',
  $required_classes  = array( '\Attogram\attogram',
                              '\Attogram\attogram_utils',
                              '\Attogram\logger',
                              '\Monolog\Formatter\LineFormatter',
                              '\Monolog\Handler\BufferHandler',
                              '\Monolog\Handler\StreamHandler',
                              '\Monolog\Logger',
                              '\Parsedown',
                              '\Symfony\Component\HttpFoundation\Request',
                            ) );

$guru->meditate();               // load the configuration
$guru->expand_consciousness();   // run the vendor autoloader
$guru->focus_mind();             // include project classes
$guru->inner_awareness();        // check for the required classes
$guru->tranquility();            // Load the project!

/** ****************************************************************************
********************************************************************************
********************************************************************************

Guru Meditation Loader v0.0.2

Copyright 2016 Attogram Framework Developers https://github.com/attogram/attogram

Open Source Dual License: (MIT or GPL-3.0+) at your choosing

********************************************************************************
********************************************************************************
***************************************************************************** */
class guru_meditation_loader
{

  public $project_name, $config_file, $project_classes, $project_loader,
         $default_autoloader, $vendor_download, $required_classes, $autoloader;

  /**
   * set the Guru vars
   */
  function __construct( string $project_name,
                        string $config_file,
                        string $project_classes,
                        string $project_loader,
                        string $default_autoloader,
                        string $vendor_download,
                        array  $required_classes ) {
    $this->project_name       = $project_name;
    $this->config_file        = $config_file;
    $this->project_classes    = $project_classes;
    $this->project_loader     = $project_loader;
    $this->default_autoloader = $default_autoloader;
    $this->vendor_download    = $vendor_download;
    $this->required_classes   = $required_classes;
    $this->debug('START Guru Meditation Loader: ' . $this->project_name);
  }

  /**
   * load the config file
   */
  function meditate() {
    global $config;
    if( is_file($this->config_file) ) {
      if( !is_readable($this->config_file) ) {
        $this->guru_meditation_error('Config file exists, but is not readable: ' . $this->config_file);
      }
      $included = (include($this->config_file));
      if( !$included ) {
        $this->guru_meditation_error('Config file exists, but include failed: ' . $this->config_file);
      }
      $this->debug('meditate: config OK: ' . $this->config_file);
    } else {
      $this->debug('meditate: config_file is NOT a file');
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
  } // end function meditate()

  /**
   * run the vendor autoloader
   */
  function expand_consciousness() {
    if( isset($this->autoloader) && is_file($this->autoloader) && is_readable($this->autoloader) ) {
      $included = ( include($this->autoloader) );
      if( !$included ) {
        $this->guru_meditation_error('Autoloader file exists, but include failed: ' . $this->autoloader);
      }
      $this->debug('expand_consciousness: OK: ' . $this->autoloader);
      return;
    }
    $this->guru_meditation_error(
      $err = 'autoloader file not found: ' . $this->autoloader,
      $fix = 'Possibile Fixes:'
      . '<br /><br />- Is the path to the autoloader wrong?  Edit <strong>' . $this->config_file
      . '</strong> and check for <strong>$config[\'autoloader\']</strong>'
      . '<br /><br />- Was <a href="http://getcomposer.org/">composer</a> not run yet?  Run <strong>composer install</strong>'
      . '<br /><br />- Can\'t run composer? <a href="' . $this->vendor_download
      . '"><strong>download the vendor zip file</strong></a> and install manually'
    );
  } // end function expand_consciousness()

  function focus_mind() {
    if( !is_dir($this->project_classes) ) {
      $this->guru_meditation_error('Missing project directory: ' . $this->project_classes);
    }
    if( !is_readable($this->project_classes) ) {
      $this->guru_meditation_error('Project directory is unreadable: ' . $this->project_classes);
    }
    foreach( array_diff(scandir($this->project_classes),array('.','..')) as $f ) {
      $included = ( include_once( $this->project_classes . $f ) );
      if( !$included ) {
        $this->guru_meditation_error('Failed to include project file: ' . $this->project_classes . $f);
      }
      $this->debug('focus_mind: OK: ' . $this->project_classes . $f);
    }
  } // end function focus_mind()

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
      if( !is_file($this->project_loader) ) {
        $this->guru_meditation_error('Project loader file missing: ' . $this->project_loader);
      }
      if( !is_readable($this->project_loader) ) {
        $this->guru_meditation_error('Project loader file exists, but is not readable: ' . $this->project_loader);
      }
      $project_loader_included = ( include($this->project_loader) );
      if( !$project_loader_included ) {
        $this->guru_meditation_error('Project loader file exists, but include failed: ' . $this->project_loader);
      }
  }

  function debug( $msg ) {
    global $config;
    $config['guru_meditation_loader'][] = $msg;
  }

  function guru_meditation_error( string $error='', string $fix='' ) {
    global $config;
    print '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Guru Meditation Error</title>
<style>
body { margin:0 0 0 30px; font-size:22px; font-family:"Helvetica Neue",Helvetica,Arial,sans-serif; }
a { text-decoration:none; }
.icon { font-size:60px; vertical-align:middle; padding:0px; margin:10px; }
.err { color:red; }
.fix { font-size:18px; color:black;  }
.log { font-size:15px; color:#333366; }
</style></head><body>
<p><a href=""><span class="icon">😢</span></a> Guru Meditation Error</p>';
  if( $error ) {
    print '<p class="err"><a href=""><span class="icon">💔</span></a> ' . $error . '</p>';
  }
  if( $fix ) {
    print '<p class="fix"><a href=""><span class="icon">🔧</span></a> ' . $fix . '</p>';
  }
  if( isset($_GET['debug']) && isset($config['guru_meditation_loader']) ) {
    print '<p class="log">🕑 ' . gmdate('Y-m-d H:i:s') . ' UTC<br />💭 ';
    print implode('<br />💭 ', $config['guru_meditation_loader']);
  }
  print '</body></html>';
  exit;
  } // end function guru_meditation_error()

} // end class guru_meditation_error()
