<?php // Attogram Framework - Guru Meditation Loader - v0.0.5

namespace Attogram;

$guru = new guru_meditation_loader( // wake up the guru
  $project_name      = 'The Attogram Framework',
  $config_file       = './config.php',
  $project_classes   = '../attogram/',
  $vendor_autoloader = '../vendor/autoload.php',
  $vendor_download   = 'https://github.com/attogram/attogram-vendor/archive/master.zip',
  $required_classes  = array( '\Attogram\attogram_fs',    // Attogram File System
                              //'\Psr\Log\LoggerInterfac', // PSR-3 Logger Interface
                              '\Attogram\logger',         // Null Stack PSR-3 Logger
                              '\Attogram\attogram',       // The Attogram Framework
                              '\Symfony\Component\HttpFoundation\Request', // HTTP Request Object
                              '\Parsedown',               // Markdown Parser
                              '\Monolog\Formatter\LineFormatter',
                              '\Monolog\Handler\BufferHandler',
                              '\Monolog\Handler\StreamHandler',
                              '\Monolog\Logger',
                            ) );

/** ****************************************************************************
********************************************************************************
********************************************************************************

Guru Meditation Loader v0.0.5

Copyright 2016 Attogram Framework Developers https://github.com/attogram/attogram

Open Source Dual License: (MIT or GPL-3.0+) at your choosing

********************************************************************************
********************************************************************************
***************************************************************************** */
class guru_meditation_loader
{

  public $project_name;
  public $config_file;
  public $project_classes;
  public $default_autoloader;
  public $vendor_download;
  public $required_classes;
  public $autoloader;

  /**
   * set the Guru vars
   */
  function __construct( $project_name,
                       $config_file,
                       $project_classes,
                       $default_autoloader,
                       $vendor_download,
                       array $required_classes )
  {
    set_error_handler(array( $this, 'guru_meditation_error_handler' ));

    register_shutdown_function(array( $this, 'guru_meditation_shutdown' ));

    $this->project_name       = $project_name;
    $this->config_file        = $config_file;
    $this->project_classes    = $project_classes;
    $this->default_autoloader = $default_autoloader;
    $this->vendor_download    = $vendor_download;
    $this->required_classes   = $required_classes;
    $this->debug('START Guru Meditation Loader: ' . $this->project_name);
    $this->meditate();               // load the attogram configuration into global $config array
    //$this->meditate_deeper();        // load the modules configurations into global $config array
    $this->expand_consciousness();   // run the composer vendor autoloader
    $this->focus_mind();             // include attogram project classes
    $this->inner_awareness();        // check for required classes
    $this->tranquility();            // Load The Attogram Framework
  }

  function guru_meditation_error_handler( $level, $message, $file='', $line='', $context=array() ) {

    $show_all = false;

    switch( $level ) {
      case 1: break; // E_ERROR
      case 2: $this->debug("E_WARNING: file:$file line:$line message:$message"); if($show_all){break;}else{return;}
      case 4: $this->debug("E_PARSE: file:$file line:$line message:$message"); if($show_all){break;}else{return;}
      case 8: $this->debug("E_NOTICE: file:$file line:$line message:$message"); if($show_all){break;}else{return;}
      case 16: break; // E_CORE_ERROR
      case 32: $this->debug("E_CORE_WARNING: file:$file line:$line message:$message"); if($show_all){break;}else{return;}
      case 64: break; // E_COMPILE_ERROR
      case 128: $this->debug("E_COMPILE_WARNING: file:$file line:$line message:$message"); if($show_all){break;}else{return;}
      case 256: break; // E_USER_ERROR
      case 512: $this->debug("E_USER_WARNING: file:$file line:$line message:$message"); if($show_all){break;}else{return;}
      case 1024: $this->debug("E_USER_NOTICE: file:$file line:$line message:$message"); if($show_all){break;}else{return;}
      case 2048: $this->debug("E_STRICT: file:$file line:$line message:$message"); if($show_all){break;}else{return;}
      case 4096: $this->debug("E_RECOVERABLE_EROR: file:$file line:$line message:$message"); if($show_all){break;}else{return;}
      case 8192: $this->debug("E_DEPECIATED: file:$file line:$line message:$message"); if($show_all){break;}else{return;}
      case 16384: $this->debug("E_USER_DEPECIATED: file:$file line:$line message:$message"); if($show_all){break;}else{return;}
      case 30719: break; // E_ALL
      default: break; // E_UNKNOWN
    }
    $this->guru_meditation_error(
      "Sadness $level: $message"
      . ( (isset( $file ) && $file) ? "<pre>File: $file</pre>" : '' )
      . ( (isset( $line ) && $line) ? "<pre>Line: $line</pre>" : '' )
      . ( isset( $context['project_name'] ) ? '<pre>Context: ' . $context['project_name'] . '</pre>' : '')
    );
    exit;
  }

  function guru_meditation_shutdown() {
    $last = error_get_last();
    switch( $last['type'] ) {
      case E_ERROR:
        $this->guru_meditation_error( 'Fatal Error:<br />' . str_replace("\n",'<br />', $last['message']));
    }
  }

  /**
   * load the config file
   */
  function meditate()
  {
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
  function expand_consciousness()
  {
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

  function focus_mind()
  {
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

  function inner_awareness()
  {
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

  function tranquility()
  {
      global $config;
      $this->debug('tranquility');

      if( !ob_start("ob_gzhandler") ) { // speed things up! gzip buffer
        ob_start(); // if gzip handler not available, do normal buffer
      }

      // Setup Monolog
      if(
          ( isset($config['debug']) && is_bool($config['debug']) && $config['debug'] ) // $config['debug'] = true
        ||
          ( isset($_GET['debug'])   // admin debug url override ?debug
            && isset($config['admins'])
            && is_array($config['admins'])
            && in_array($_SERVER['REMOTE_ADDR'], $config['admins'])
          )
      ) {
        $log = new \Monolog\Logger('attogram');
        $sh = new \Monolog\Handler\StreamHandler('php://output');
        $format = "<p class=\"text-danger squished\">%datetime%|%level_name%: %message% %context%</p>"; // %extra%
        $dateformat = 'Y-m-d|H:i:s:u';
        $sh->setFormatter( new \Monolog\Formatter\LineFormatter($format, $dateformat) );
        $log->pushHandler( new \Monolog\Handler\BufferHandler($sh) );
        // $log->pushHandler( new \Monolog\Handler\BrowserConsoleHandler ); // dev
      } else {
        $log = new \Attogram\logger();
      }

      if( isset($config['guru_meditation_loader']) && is_array($config['guru_meditation_loader']) ) {
        foreach( $config['guru_meditation_loader'] as $g ) {
          $log->debug($g); // save loader debug log
        }
      }

      if( !isset($config['debug']) ) {
        $config['debug'] = false;
      }

      $attogram = new attogram( $log, $config['debug'] ); // Start Attogram Framework!

  } // end function tranquility()

  function debug( $msg )
  {
    global $config;
    $config['guru_meditation_loader'][] = $msg;
  }

  function guru_meditation_error( $error='', $fix='' )
  {
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
