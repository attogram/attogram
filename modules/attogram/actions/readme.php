<?php // Attogram Framework - readme page v0.0.3

namespace Attogram;

// Main README file
$readme = $this->attogram_dir . 'README.md';
if( !is_readable($readme) ) {
  $this->log->error('readme.php: file not found: ' . $readme);
  //$this->error404('README file lost in the wind');
} else {
  $this->do_markdown($readme);
}

// Module README
$readme = $this->modules_dir . '/README.md';
if( !is_readable($readme) ) {
  $this->log->error('readme.php: file not found: ' . $readme);
  //$this->error404('README file lost in the wind');
} else {
  $this->do_markdown($readme);
}

// All Modules README's
foreach( attogram_fs::get_all_subdirectories( $this->modules_dir, $name='' ) as $m ) {
  $mrm = $m . 'README.md';
  if( !is_readable($mrm) ) {
    continue;
  }
  $this->do_markdown($mrm);
}
