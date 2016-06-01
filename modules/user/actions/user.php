<?php // Attogram Framework - User Module - User Page v0.0.2

namespace Attogram;

if( !$this->is_logged_in() ) {
  header('Location: ' . $this->path . '/login/');
  exit;
}

$this->page_header('Attogram - User');

print '<div class="container"><h1><span class="glyphicon glyphicon-user"></span> User</h1><hr />'
. 'ID: <code>' . $this->session->get('attogram_id', 'null') . '</code>'
. '<br />username: <code>' . $this->session->get('attogram_username', 'null') . '</code>'
. '<br />level: <code>' . $this->session->get('attogram_level', 'null') . '</code>'
. '<br />email: <code>' . $this->session->get('attogram_email', 'null') . '</code>'
. '<br />is_admin?:  ' . ($this->is_admin ? 'Yes' : 'No')
. '</div>';

$this->page_footer();
