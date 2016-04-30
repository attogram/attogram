<?php
// Attogram - action - admin - users

$title = 'Attogram - Admin - Users';
include('templates/header.php');
print '<div class="body">Attogram - Users<hr />';

$users = $this->query('SELECT * FROM user');

print count($users) . ' users';
print ' - <a href="../admin-db/?table=user&action=row_create">New User<a><pre>';

foreach($users as $u) {	
	print '<hr />ID        : ' . $u['id'];
	print ' <a href="../admin-db/?table=user&action=row_editordelete&pk=[' . $u['id'] . ']&type=edit">edit</a>';
    print ' <a href="../admin-db/?table=user&action=row_editordelete&pk=[' . $u['id'] . ']&type=delete">delete</a>';
	print '<br />username  : ' . $u['username'];
	print '<br />password  : ' . $u['password'];
	print '<br />email     : ' . $u['email'];
	print '<br />level     : ' . $u['level'];
	print '<br />created   : ' . $u['created'];
	print '<br />updated   : ' . $u['updated'];
	print '<br />last_login: ' . $u['last_login'];
	print '<br />last_host : ' . $u['last_host'];

}
print '</pre></div>';

include('templates/footer.php');

