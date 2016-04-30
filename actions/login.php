<?php
// Attogram - action - login
include('templates/header.php');
?>
<div class="body">

<form action="" method="POST">
<input type="hidden" name="login" value="login">
<p>Username: <input type="text" name="u" size="25"></p>
<p>Password: <input type="password" name="p" size="25"></p>
<p><input type="submit" value="                Login                "></p>
</form>
<?php
	if( isset($_POST['login']) ) { login(); }
?>
</div>
<?php
include('templates/footer.php');

function login() {
	
	print "<PRE>POST: " . print_r($_POST,1) . '</PRE>';
}