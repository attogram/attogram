<?php
// // Attogram Framework - Login Page v0.0.1

namespace Attogram;

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
 <div class="col-md-6 col-md-offset-2">
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
    <button type="submit" class="btn btn-info" style="width:50%;">
    &nbsp; &nbsp; &nbsp; &nbsp; Login &nbsp; &nbsp; &nbsp; &nbsp; </button>
  </form>
 </div>
</div>
<?php
$this->page_footer();