<?php
// Attogram - action - login

if( isset($_POST['login']) ) { 
  if( $this->login() ) {
	header('Location: ' . $this->path);
	exit;
  }
}


include('templates/header.php');
?>
<div class="body">
<form action="" method="POST">
<input type="hidden" name="login" value="login">
<p>Username: <input type="text" name="u" size="25"></p>
<p>Password: <input type="password" name="p" size="25"></p>
<p><input type="submit" value="                Login                "></p>
</form>

</div>
<?php
include('templates/footer.php');