<?php
// Attogram - action - user

if( !$this->is_logged_in() ) {
  header('Location: ' . $this->path . '/login/');
  exit;
}
$title = 'Attogram - User';
include($this->templates_dir . '/header.php');

print '<div class="container">
ID: ' . @$_SESSION['attogram_id'] . '
<br />username: ' . @htmlentities($_SESSION['attogram_username']) . '
<br />level: ' . @htmlentities($_SESSION['attogram_level']) . '
<br />email: ' . @htmlentities($_SESSION['attogram_email']) . '
</div>';

include($this->templates_dir . '/footer.php');