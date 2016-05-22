<?php
// Attogram - action - admin - users

$this->page_header('Attogram - Admin - Users');

$users = $this->sqlite_database->query('SELECT * FROM user ORDER BY id');

print '<div class="container"><p><strong>' . count($users) . '</strong> <a href="">Users</a>';
print ' &nbsp; - &nbsp; <a target="_db" href="../db-admin/?table=user&action=row_create">Create New User</a></p>';
print '<table class="table table-bordered"><thead><tr>
<th>ID</th>
<th>edit</th>
<th>delete</th>
<th>username</th>
<th>password</th>
<th>email</th>
<th>level</th>
<th>last_login</th>
<th>last_host</th>
</tr></thead><tbody>';
foreach($users as $u) {
  print '<tr><td>' . $u['id'] . '</td>';
  print '<td><a target="_db" href="../db-admin/?table=user&action=row_editordelete&pk=[' . $u['id'] . ']&type=edit">edit</a></td>';
  print '<td><a target="_db" href="../db-admin/?table=user&action=row_editordelete&pk=[' . $u['id'] . ']&type=delete">delete</a></td>';
  print '<td>' . htmlentities($u['username']) . '</td>';
  print '<td>' . htmlentities($u['password']) . '</td>';
  print '<td>' . htmlentities($u['email']) . '</td>';
  print '<td>' . htmlentities($u['level']) . '</td>';
  print '<td>' . htmlentities($u['last_login']) . '</td>';
  print '<td>' . htmlentities($u['last_host']) . '</td></tr>';
}
print '</tbody></table></div>';

$this->page_footer();
