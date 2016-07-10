<?php // Attogram Framework - Guru Meditation Loader - v0.2.0

namespace Attogram;

global $config;

// Default configuration
// Values may be overriden by ./public/config.php, and then ./modules/*/configs/*.php
$config['attogram_dir']  = '../'; // with trailing slash
$config['autoloader']    = $config['attogram_dir'] . 'vendor/autoload.php';
$config['modules_dir']   = $config['attogram_dir'] . 'modules';   // without trailing slash
$config['templates_dir'] = $config['attogram_dir'] . 'templates'; // without trailing slash
$config['debug']         = false;
$config['site_name']     = 'The Attogram Framework';
$config['admins']        = array( '127.0.0.1', '::1', );
$config['db_name']       = $config['attogram_dir'] . 'db/global';

// Load the Project
$guru = new guru_meditation_loader(
  $project_name        = $config['site_name'],
  $config_file         = './config.php',
  $project_classes     = $config['attogram_dir'] . 'attogram/',
  $vendor_autoloader   = $config['autoloader'],
  $vendor_download     = 'https://github.com/attogram/attogram-vendor/archive/master.zip',
  $required_classes    = array(
                         '\Attogram\attogram_fs',            // Attogram File System
                         '\Attogram\attogram',               // The Attogram Framework
                         '\Symfony\Component\HttpFoundation\Request', // HTTP Request Object
                         '\Parsedown',                       // Markdown Parser
                         '\Psr\Log\NullLogger',              // PSR-3 Null Logger Object
                         '\Monolog\Formatter\LineFormatter', // Monolog Line Formatter
                         '\Monolog\Handler\BufferHandler',   // Monolog Buffer Handler
                         '\Monolog\Handler\StreamHandler',   // Monolog Stream Handle
                         '\Monolog\Logger',                  // Monolog PSR-3 logger
                         ),
  $required_interfaces = array(
                         '\Psr\Log\LoggerInterface', // PSR-3 Logger Interface
                         )
);

/** ************************************************************************* */
class guru_meditation_loader
{

  public $project_name;
  public $config_file;
  public $project_classes;
  public $default_autoloader;
  public $vendor_download;
  public $required_classes;
  public $required_interfaces;
  public $autoloader;

  /**
   * set the Guru vars
   */
  function __construct( $project_name, $config_file,$project_classes, $default_autoloader,
                        $vendor_download, array $required_classes, array $required_interfaces )
  {
    error_reporting( E_ALL );
    ini_set( 'display_errors', E_ALL );
    //error_reporting(            E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR ); // dev - hide errors
    //ini_set( 'display_errors',  E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR ); // dev - hide errors
    set_error_handler(array( $this, 'guru_meditation_error_handler' ));
    register_shutdown_function(array( $this, 'guru_meditation_shutdown' ));
    $this->project_name        = $project_name;
    $this->config_file         = $config_file;
    $this->project_classes     = $project_classes;
    $this->default_autoloader  = $default_autoloader;
    $this->vendor_download     = $vendor_download;
    $this->required_classes    = $required_classes;
    $this->required_interfaces = $required_interfaces;

    $this->debug( 'START Guru Meditation Loader: ' . $this->project_name );

    $this->meditate();             // load the attogram configuration -- get config[ autoloader, modules_dir, debug ]
    $this->expand_consciousness(); // run the composer vendor autoloader
    $this->focus_mind();           // include attogram project classes
    $this->focus_inner_eye();      // include modules includes
    $this->inner_awareness();      // check for required classes
    $this->inner_emptiness();      // check for required interfaces
    $this->meditate_deeper();      // load the modules configurations - (needs attogram_fs class)
    $this->tranquility();          // Load The Attogram Framework

  } // end function __construct()

  /**
   * Catch any errors
   */
  function guru_meditation_error_handler( $level, $message, $file = '', $line = '', $context = array() )
  {

    switch( $level ) {
      case 1:
        $this->debug("E_ERROR: file:$file line:$line $message");
        break;
      case 2:
        $this->debug("E_WARNING: file:$file line:$line $message");
        return;
      case 4:
        $this->debug("E_PARSE: file:$file line:$line $message");
        return;
      case 8:
        $this->debug("E_NOTICE: file:$file line:$line $message");
        return;
      case 16:
        $this->debug("E_CORE_ERROR: file:$file line:$line $message");
        break;
      case 32:
        $this->debug("E_CORE_WARNING: file:$file line:$line $message");
        return;
      case 64:
        $this->debug("E_COMPILE_ERROR: file:$file line:$line $message");
        break;
      case 128:
        $this->debug("E_COMPILE_WARNING: file:$file line:$line $message");
        return;
      case 256:
        $this->debug("E_USER_ERROR: file:$file line:$line $message");
        break;
      case 512:
        $this->debug("E_USER_WARNING: file:$file line:$line $message");
        return;
      case 1024:
        $this->debug("E_USER_NOTICE: file:$file line:$line $message");
        return;
      case 2048:
        $this->debug("E_STRICT: file:$file line:$line $message");
        return;
      case 4096:
        $this->debug("E_RECOVERABLE_EROR: file:$file line:$line $message");
        return;
      case 8192:
        $this->debug("E_DEPECIATED: file:$file line:$line $message");
        return;
      case 16384:
        $this->debug("E_USER_DEPECIATED: file:$file line:$line $message");
        return;
      case 30719:
        $this->debug("E_ALL: file:$file line:$line $message");
        break;
      default:
        $this->debug("E_UNKNOWN: file:$file line:$line $message");
        break;
    }

    $this->guru_meditation_error(
      "Sadness $level: $message"
      . ( (isset( $file ) && $file) ? "<pre>File: $file</pre>" : '' )
      . ( (isset( $line ) && $line) ? "<pre>Line: $line</pre>" : '' )
      . ( isset( $context['project_name'] ) ? '<pre>Context: ' . $context['project_name'] . '</pre>' : '')
    );
    exit;
  }

  /**
   * Catch any fatal errors at shutdown
   */
  function guru_meditation_shutdown()
  {
    $last = error_get_last();
    switch( $last['type'] ) {
      case E_ERROR:
        $this->guru_meditation_error( 'Shutdown due to Fatal Error:<br />' . str_replace( "\n", '<br />', $last['message'] ) );
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
      $this->debug('meditate: OK: ' . $this->config_file);
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
   * load module configs
   */
  function meditate_deeper() {
    global $config;
    //if( !class_exists('attogram_fs') ) ....
    $count = attogram_fs::load_module_subdirectories( $config['modules_dir'], 'configs' );
    foreach( $count as $c ) {
      $this->debug('meditate_deeper: OK: ' . $c);
    }
  }

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
      'autoloader file not found: ' . $this->autoloader,
      'Possibile Fixes:'
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

  function focus_inner_eye()
  {
    global $config;
    //if( !class_exists('attogram_fs') ) ....
    $count = attogram_fs::load_module_subdirectories( $config['modules_dir'], 'includes' );
    foreach( $count as $c ) {
      $this->debug('focus_inner_eye: OK: ' . $c);
    }
  }

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
    $this->guru_meditation_error( 'Required Class Missing: ' . implode(', ', $missing));
  } // end function inner_awareness()

  function inner_emptiness()
  {
    $missing = array();
    foreach( $this->required_interfaces as $c ) {
      if( !interface_exists($c) ) {
        $missing[] = $c;
        $this->debug('inner_emptiness: Required Inteface NOT FOUND: ' . $c);
      }
      $this->debug('inner_emptiness: OK: ' . $c);
    }
    if( !$missing ) {
      return;
    }
    $this->guru_meditation_error( 'Required Interface Missing: ' . implode(', ', $missing));
  } // end function inner_emptiness()

  function tranquility()
  {
      global $config;

      // Speed things up! gz compession
      if( ob_start('ob_gzhandler') ) {
        $this->debug('tranquility: ob_gzhandler active');
      }

      // Create the Debug Logger
      if(
          ( isset($config['debug']) && is_bool($config['debug']) && $config['debug'] ) // $config['debug'] = true
        ||
          ( isset($_GET['debug'])   // admin debug url override ?debug
            && isset($config['admins'])
            && is_array($config['admins'])
            && in_array($_SERVER['REMOTE_ADDR'], $config['admins'])
          )
      ) {
        $log = new \Monolog\Logger('debug');
        $streamHandler = new \Monolog\Handler\StreamHandler('php://output');
        $format = "<p class=\"text-danger squished\">%datetime%|%level_name%: %message% %context%</p>"; // %extra%
        $dateformat = 'Y-m-d|H:i:s:u';
        $streamHandler->setFormatter( new \Monolog\Formatter\LineFormatter( $format, $dateformat ) );
        $log->pushHandler( new \Monolog\Handler\BufferHandler($streamHandler) );
        // $log->pushHandler( new \Monolog\Handler\BrowserConsoleHandler ); // dev
      } else {
        $log = new \Psr\Log\NullLogger();
      }

      // Save guru startup log to the Debug logger
      if( isset($config['guru_meditation_loader']) && is_array($config['guru_meditation_loader']) ) {
        foreach( $config['guru_meditation_loader'] as $g ) {
          $log->debug($g);
        }
      }

      // Create database object
      $database = false;
      if( class_exists('Attogram\sqlite_database') ) {
        $database = new sqlite_database( $config['db_name'], $config['modules_dir'], $log );  // init the database, sans-connection
        if( !$database ) {
          $log->error('guru_meditation_loader: sqlite_database initialization failed');
        }
      }


      // Create the Event logger
      if( !$database ) {
        $event = new \Psr\Log\NullLogger(); // no database, use null logger
      } else {
        // Setup the Event Logger
        $event = new \Monolog\Logger('event');
        $event->pushHandler( new \Attogram\event_logger( $database ) );
      }

      // create Request object
      $request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

      // Start the Attogram Framework!
      new attogram(
        $log,
        $event,
        $database,
        $request,
        $config['debug']
      );

  } // end function tranquility()

  function debug( $msg )
  {
    global $config;
    $config['guru_meditation_loader'][] = $msg;
  }

  function guru_meditation_error( $error='', $fix='' )
  {
    global $config;
    print '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8">'
    . '<meta name="viewport" content="width=device-width, initial-scale=1">'
    . '<title>Guru Meditation Error</title>'
    . '<style>'
    . ' body { margin:0 0 0 30px; font-size:22px; font-family:"Helvetica Neue",Helvetica,Arial,sans-serif; }'
    . ' a { text-decoration:none; }'
    . ' .icon { font-size:60px; vertical-align:middle; padding:0px; margin:10px; }'
    . ' .err { color:red; }'
    . ' .fix { font-size:18px; color:black;  }'
    . ' .log { font-size:15px; color:#333366; }'
    . '</style></head><body>'
    . '<p><a href=""><span class="icon">😢</span></a> Guru Meditation Error</p>';
    if( $error ) {
      print '<p class="err"><a href=""><span class="icon">💔</span></a> ' . $error . '</p>';
    }
    if( $fix ) {
      print '<p class="fix"><a href=""><span class="icon">🔧</span></a> ' . $fix . '</p>';
    }
    if( isset($_GET['debug']) && isset($config['guru_meditation_loader']) ) {
      print '<p class="log">🕑 ' . gmdate('Y-m-d H:i:s') . ' UTC<br />💭 ';
      print implode( '<br />💭 ', $config['guru_meditation_loader'] );
    }
    print '</body></html>';
    exit;
  } // end function guru_meditation_error()

} // end class guru_meditation_error()
