<?php
// Attogram - action - readme

namespace Attogram;

$title = 'Attogram - Readme';
include($this->templates_dir . '/header.php');

print '<div class="container">';

$file = 'README.md';

if( is_readable_file($file, $ext=array('.md') ) ) {
  $page = @file_get_contents($file);
  if( $page === FALSE ) {
      print 'Error: can not get file: ' . $file;
  } else {
    if( class_exists('Parsedown') ) {
      print \Parsedown::instance()->text( $page );
    } else {
      print 'Error: can not find parser';
    }    
  }
} else {
  print 'Error: can not read file: ' . $file;
}

print '</div>';
include($this->templates_dir . '/footer.php');
