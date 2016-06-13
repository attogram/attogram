<?php // Attogram Framework - User Module - User Admin v0.0.4

namespace Attogram;

$this->page_header('User Admin');

if( !function_exists('Attogram\tabler') ) {
  $this->log->error('Users Admin: tabler function not found');
  print '<div class="container">error: tabler function does not exist</div>';
  $this->page_footer();
  exit;
}

tabler(
  $attogram = $this,
  $table = 'user',
  $name_singular = 'user',
  $name_plural = 'users',
  $public_link = false,
  $col = array(
    array('class'=>'col-md-1', 'title'=>'<code>ID</code>', 'key'=>'id'),
    array('class'=>'col-md-5', 'title'=>'username', 'key'=>'username'),
    array('class'=>'col-md-1', 'title'=>'password', 'key'=>'password'),
    array('class'=>'col-md-1', 'title'=>'email', 'key'=>'email'),
    array('class'=>'col-md-1', 'title'=>'level', 'key'=>'level'),
    array('class'=>'col-md-1', 'title'=>'last_login', 'key'=>'last_login'),
    array('class'=>'col-md-1', 'title'=>'last_host', 'key'=>'last_host'),
  ),
  $sql = 'SELECT * FROM user ORDER BY id',
  $admin_link = '../users/',
  $show_edit = true
);

$this->page_footer();
