<?php
// Attogram - action - readme

namespace Attogram;

$this->page_header('Attogram - README');

print '<div class="container">';

$file = 'README.md';

if( is_readable_file($file, '.md' ) ) {
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
$this->page_footer();
