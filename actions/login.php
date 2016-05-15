<?php
// Attogram - action - login
$message = '';
if( isset($_POST['login']) ) { // attempt to login, buffer errors to show later
  if( $this->login() ) {
    header('Location: ' . $this->path . '/');
    exit;
  }
  $message = '<p class="alert alert-warning">Login failed</p>';
}

$this->page_header('Attogram - Login');
?>
<div class="container">
  <?php if( $message ) { print $message; } ?>
  <form action="." method="POST">
    <div class="form-group">
      <input type="hidden" name="login" value="login">
    </div>
    <div class="form-group">
      Username: <input class="form-control" type="text" name="u">
    </div>
    <div class="form-group">
      Password: <input class="form-control" type="password" name="p">
    </div>
    <button type="submit" class="btn btn-info" style="width:50%;"> Login </button>
  </form>
</div>
<?php
$this->page_footer();
