<?php // Attogram Framework - license page v0.1.1

namespace Attogram;

// Main License file
$f = $this->attogram_dir . 'LICENSE.md';
if( !is_readable($f) ) {
  $this->log->error('license.php: file not found: ' . $f );
  $this->error404('LICENSE file lost in the wind');
}

$this->do_markdown($f);
