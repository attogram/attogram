<?php
// Attogram - action - login

if( isset($_POST['login']) ) { // attempt to login, buffer errors to show later
  ob_start();
  if( $this->login() ) {
    ob_end_clean();
    header('Location: ' . $this->path);
    exit;
  }
  $notices = ob_get_contents();
  ob_end_clean();
}

include($this->templates_dir . '/header.php');
?>
<div class="body">
<?php
  if( isset($notices) ) { print $notices; }
?>
<form action="" method="POST">
<input type="hidden" name="login" value="login">
<p>Username: <input type="text" name="u" size="25"></p>
<p>Password: <input type="password" name="p" size="25"></p>
<p><input type="submit" value="                Login                "></p>
</form>

</div>
<?php
include($this->templates_dir . '/footer.php');