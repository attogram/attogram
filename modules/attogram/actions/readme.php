<?php // Attogram Framework - readme page v0.1.0

namespace Attogram;

$this->page_header('Readme');
print '<div class="container">';

// Main README file
$readme = $this->attogram_dir . 'README.md';
if( !is_readable($readme) ) {
  $this->log->error('readme.php: file not found: ' . $readme);
  //$this->error404('README file lost in the wind');
} else {
  print $this->get_markdown($readme);
}

// All Modules README's
foreach( attogram_fs::get_all_subdirectories( $this->modules_dir, $name='' ) as $m ) {
  $mrm = $m . 'README.md';
  if( !is_readable($mrm) ) {
    continue;
  }
  print '<p><hr /><em>' . htmlentities($mrm) . ':</em></p>';
  print $this->get_markdown($mrm);
}

print '</div>';
$this->page_footer();
