<?php// Attogram Framework - Contact Page v0.1.0

namespace Attogram;

$this->page_header('Attogram - Contact');

if( isset($_POST['msg']) && isset($_POST['email']) ) {

  $sql = 'INSERT INTO contact (time,email,msg,ip,agent) VALUES (DATETIME("now"),:email,:msg,:ip,:agent)';

  $bind = array(
   'email'=>$_POST['email'],
   'msg'=>$_POST['msg'],
   'ip'=>@$_SERVER['REMOTE_ADDR'],
   'agent'=>@$_SERVER['HTTP_USER_AGENT'],
  );

  $ok_msg = '<div class="container">Thank You.  Message received.</div>';

  if( $this->db->queryb($sql,$bind) ) {
    print $ok_msg;
    include($this->templates_dir . '/footer.php');
    exit;
  }

  //if( $this->db->get_db()->errorCode() == 'HY000' ) {
  //  $this->error[] = 'Message system offline';
  //  include($this->templates_dir . '/footer.php');
  //  exit;
  //}  // defunct call to get_db() -- needs rewrite

}

if( isset($_POST['msg']) || isset($_POST['email']) ) { print 'ERROR<hr />'; }

?>
<div class="container">

  <h3>Contact us</h3>

  <form action="." method="POST">

  <div class="form-group">
    <label for="email">Your Email:</label>
    <input class="form-control" type="text" name="email" size="55" value="<?php
      if (isset($_SESSION['attogram_email'])) {
          echo htmlentities($_SESSION['attogram_email']);
      } ?>" />
  </div>

  <div class="form-group">
    <label for="msg">Your Message:</label>
    <textarea class="form-control" name="msg" rows="10" cols="70"></textarea>
  </div>

  <button type="submit" class="btn btn-primary"> Send your message now </button>

</form>
</div>
<?php
$this->page_footer();
