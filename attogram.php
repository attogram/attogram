<?php // Attogram Framework - system loader v0.0.1

namespace Attogram;

if( !ob_start("ob_gzhandler") ) { // speed things up! gzip buffer
  ob_start(); // if gzip handler not available, do normal buffer
}

include_once('attogram/logger.php');
include_once('attogram/attogram_utils.php');
include_once('attogram/attogram.php');

$attogram = new attogram();
