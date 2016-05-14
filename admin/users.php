<?php
// Attogram - action - admin - users

$title = 'Attogram - Admin - Users';
include($this->templates_dir . '/header.php');

$users = $this->sqlite_database->query('SELECT * FROM user ORDER BY id');

print '<div class="container"><p><b>' . count($users) . '</b> <a href="">Users</a>';
print ' &nbsp; - &nbsp; <a target="_db" href="../database-phpLiteAdmin/?table=user&action=row_create">Create New User</a></p>';
print '<table class="table table-bordered">
<tr>
<td>ID</td>
<td>edit</td>
<td>delete</td>
<td>username</td>
<td>password</td>
<td>email</td>
<td>level</td>
<td>last_login</td>
<td>last_host</td>
</tr>';
foreach($users as $u) {
  print '<tr><td>' . $u['id'] . '</td>';
  print '<td><a target="_db" href="../database-phpLiteAdmin/?table=user&action=row_editordelete&pk=[' . $u['id'] . ']&type=edit">edit</a></td>';
  print '<td><a target="_db" href="../database-phpLiteAdmin/?table=user&action=row_editordelete&pk=[' . $u['id'] . ']&type=delete">delete</a></td>';
  print '<td>' . htmlentities($u['username']) . '</td>';
  print '<td>' . htmlentities($u['password']) . '</td>';
  print '<td>' . htmlentities($u['email']) . '</td>';
  print '<td>' . htmlentities($u['level']) . '</td>';
  print '<td>' . htmlentities($u['last_login']) . '</td>';
  print '<td>' . htmlentities($u['last_host']) . '</td></tr>';
}
print '</table></div>';

include($this->templates_dir . '/footer.php');