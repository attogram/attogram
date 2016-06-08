<?php // Attogram Framework - User Module - User Page v0.0.4

namespace Attogram;

if( !class_exists('Attogram\attogram_user') ) {
  $this->log->error('login.php: attogram_user class not found');
  $this->error404('attogram_user system kaput!');
}

if( !\Attogram\attogram_user::is_logged_in() ) {
  header('Location: ' . $this->path . '/login/');
  exit;
}

$this->page_header('Attogram - User');

print '<div class="container"><h1><span class="glyphicon glyphicon-user"></span> User</h1><hr />'
. 'ID: <code>' . @$_SESSION['attogram_id'] . '</code>'
. '<br />username: <code>' . @$_SESSION['attogram_username'] . '</code>'
. '<br />level: <code>' . @$_SESSION['attogram_level'] . '</code>'
. '<br />email: <code>' . @$_SESSION['attogram_email'] . '</code>'
. '<br />is_admin?:  ' . ($this->is_admin ? 'Yes' : 'No')
. '</div>';

$this->page_footer();
