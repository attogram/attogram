<?php
// Attogram Framework - Users Admin v0.0.1

namespace Attogram;

$this->page_header('Attogram - Admin - Users');

tabler(
  $attogram = $this,
  $table = 'user',
  $name_singular = 'user',
  $name_plural = 'users',
  $public_link = FALSE,
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
  $show_edit = TRUE
);

$this->page_footer();
