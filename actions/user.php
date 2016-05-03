<?php
// Attogram - action - user

if( !$this->is_logged_in() ) {
  header('Location: ' . $this->path . '/login/');
  exit;
}
$title = 'Attogram - User';
include('templates/header.php');

print '<div class="body">
ID: ' . @$_SESSION['attogram_id'] . '
<br />username: ' . @htmlentities($_SESSION['attogram_username']) . '
<br />level: ' . @htmlentities($_SESSION['attogram_level']) . '
<br />email: ' . @htmlentities($_SESSION['attogram_email']) . '
</div>';

include('templates/footer.php');