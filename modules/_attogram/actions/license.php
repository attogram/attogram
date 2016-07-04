<?php // Attogram Framework - LICENSE page v0.1.2

namespace Attogram;

$f = $this->attogram_dir . 'LICENSE.md';

if( !is_readable($f) ) {
  $this->log->error('license.php: file not found: ' . $f );
  $this->error404('LICENSE file lost in the wind');
}

$this->do_markdown( $f, 'LICENSE' );
