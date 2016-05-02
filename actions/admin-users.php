<?php
// Attogram - action - admin - users

$title = 'Attogram - Admin - Users';
include('templates/header.php');
print '<div class="body">';

$users = $this->query('SELECT * FROM user');

print '<p><b>' . count($users) . '</b> Users</p>';
print '<p><a href="../admin-db/?table=user&action=row_create">Create New User<a></p>';
print '<table border="1"><tr>
<td>ID</td>
<td>edit</td>
<td>delete</td>
<td>username</td>
<td>password</td>
<td>email</td>
<td>level</td>
<td>created</td>
<td>updated</td>
<td>last_login</td>
<td>last_host</td>
</tr>';
foreach($users as $u) {	
    print '<tr><td>' . $u['id'] . '</td>';
    print '<td><a href="../admin-database-phpLiteAdmin/?table=user&action=row_editordelete&pk=[' . $u['id'] . ']&type=edit">edit</a></td>';
    print '<td><a href="../admin-database-phpLiteAdmin/?table=user&action=row_editordelete&pk=[' . $u['id'] . ']&type=delete">delete</a></td>';
    print '<td>' . $u['username'] . '</td>';
    print '<td>' . $u['password'] . '</td>';
    print '<td>' . $u['email'] . '</td>';
    print '<td>' . $u['level'] . '</td>';
    print '<td>' . $u['created'] . '</td>';
    print '<td>' . $u['updated'] . '</td>';
    print '<td>' . $u['last_login'] . '</td>';
    print '<td>' . $u['last_host'] . '</td></tr>';
}
print '</table></div>';

include('templates/footer.php');

