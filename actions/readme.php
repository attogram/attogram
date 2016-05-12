<?php
// Attogram - action - readme

$title = 'Attogram - Readme';
include($this->templates_dir . '/header.php');
print '<div class="container">';

$file = 'README.md';
if( is_file($file) && is_readable($file) ) {
  if( class_exists('Parsedown') ) {
    print Parsedown::instance()->text( file_get_contents($file) );
  } else {
    print 'Error: can not find parser';
  }
} else {
  print 'Error: can not read ' . $file;
}

print '</div>';
include($this->templates_dir . '/footer.php');
