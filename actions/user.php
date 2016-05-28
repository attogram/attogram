<?php
// Attogram Framekwork - User Page v0.0.1

namespace Attogram;

if( !$this->is_logged_in() ) {
  header('Location: ' . $this->path . '/login/');
  exit;
}

$this->page_header('Attogram - User');

print '<div class="container">
ID: ' . @$_SESSION['attogram_id'] . '
<br />username: ' . @htmlentities($_SESSION['attogram_username']) . '
<br />level: ' . @htmlentities($_SESSION['attogram_level']) . '
<br />email: ' . @htmlentities($_SESSION['attogram_email']) . '
</div>';

$this->page_footer();
