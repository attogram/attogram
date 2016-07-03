<?php // Attogram Framework - User Module - User Admin v0.2.3

namespace Attogram;

$this->page_header('👥 User Admin');

print '<div class="container"><h1 class="squished">👥 User Admin</h1>';

$this->db->tabler(
  $table = 'user',
  $table_id = 'id',
  $name_singular = 'user',
  $name_plural = 'users',
  $public_link = false,
  $col = array(
    array('class'=>'col-md-1', 'title'=>'<code>ID</code>', 'key'=>'id'),
    array('class'=>'col-md-5', 'title'=>'username', 'key'=>'username'),
    array('class'=>'col-md-2', 'title'=>'password', 'key'=>'password'),
    array('class'=>'col-md-2', 'title'=>'email', 'key'=>'email'),
    array('class'=>'col-md-1', 'title'=>'level', 'key'=>'level'),
  ),
  $sql = 'SELECT * FROM user ORDER BY id',
  $admin_link = '../users/',
  $show_edit = true,
  $per_page = 20
);

print '</div>';
$this->page_footer();
