<?php // Attogram Framework - load.php v0.0.1

namespace Attogram;

global $config;

if( !ob_start("ob_gzhandler") ) { // speed things up! gzip buffer
  ob_start(); // if gzip handler not available, do normal buffer
}

// Load attogram classes
include_once('attogram/logger.php');
include_once('attogram/attogram_utils.php');
include_once('attogram/attogram.php');
  // dev - should check file existance & class existence -- here or via guru?
// Setup Monolog
if(
    (
      isset($config['debug']) && $config['debug']
    )
  ||
   (
     isset($_GET['debug'])   // admin debug override
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

$attogram = new attogram( $log ); // Start Attogram Framework!
