<?php
// Attogram - Global Utility Functions

namespace Attogram;

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
