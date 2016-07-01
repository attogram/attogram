<?php // Attogram Framework - about page - README.md loader v0.0.1

namespace Attogram;

$readme = '../README.md';

if( !is_readable($readme) ) {
  $this->log->error('about.php: file not found: ' . $readme);
  $this->error404('README file lost in the wind');
}

$this->do_markdown($readme);
