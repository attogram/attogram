<?php
// Attogram - action - user

if( !isset($_SESSION['attogram_username']) ) {
	header('Location: ' . $this->path . '/login/');
	exit;
}
$title = 'Attogram - User';
include('templates/header.php');


print '<div class="body">
<pre>
ID: ' . $_SESSION['attogram_id'] . ' 
username: ' . $_SESSION['attogram_username'] . ' 
level: ' . @$_SESSION['attogram_level'] . ' 
email: ' . @$_SESSION['attogram_email'] . ' 
</pre>
</div>';

include('templates/footer.php');

