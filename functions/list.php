<?php
// Attogram - functions - list

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