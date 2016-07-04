<?php // Attogram Framework - README page v0.1.2

namespace Attogram;

$f = $this->attogram_dir . 'README.md';

if( !is_readable($f) ) {
  $this->log->error('readme.php: file not found: ' . $f );
  $this->error404('README file lost in the wind');
}

$this->do_markdown( $f, 'README' );
