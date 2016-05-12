<?php
// Attogram - action - login
$message = '';
if( isset($_POST['login']) ) { // attempt to login, buffer errors to show later
  if( $this->login() ) {
    header('Location: ' . $this->path . '/');
    exit;
  }
  $message = 'Login failed';
}

include($this->templates_dir . '/header.php');
?>
<div class="container">
<?php if( $message ) { print "<pre>$message</pre>"; } ?>
<form action="" method="POST">
<input type="hidden" name="login" value="login">
<p>Username: <input type="text" name="u" size="25"></p>
<p>Password: <input type="password" name="p" size="25"></p>
<p><input type="submit" value="                Login                "></p>
</form>

</div>
<?php
include($this->templates_dir . '/footer.php');