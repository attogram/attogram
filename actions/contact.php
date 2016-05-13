<?php
// Attogram - action - contact
$title = 'Attogram - Contact';
include($this->templates_dir . '/header.php');

if( isset($_POST['msg']) && isset($_POST['email']) ) {

  $sql = 'INSERT INTO contact (time,email,msg,ip,agent) VALUES (DATETIME("now"),:email,:msg,:ip,:agent)';

  $bind = array(
   'email'=>$_POST['email'],
   'msg'=>$_POST['msg'],
   'ip'=>@$_SERVER['REMOTE_ADDR'],
   'agent'=>@$_SERVER['HTTP_USER_AGENT'],
  );

  $ok_msg = '<p>Thank You.  Message received.</p>';

  if( $this->sqlite_database->queryb($sql,$bind) ) {
    print $ok_msg;
    include($this->templates_dir . '/footer.php');
    exit;
  }

  if( $this->sqlite_database->get_db()->errorCode() == 'HY000' ) {
    $this->error[] = 'Message system offline';
    include($this->templates_dir . '/footer.php');
    exit;
  }

}

if( isset($_POST['msg']) || isset($_POST['email']) ) { print 'ERROR<hr />'; }


?>
<div class="container">
<form action="." method="POST">
Contact us:
<br />
<br />Your Email: <input type="text" name="email" size="55" value="<?php
  if( isset($_SESSION['attogram_email']) ) { print htmlentities($_SESSION['attogram_email']); }
?>" />
<br />
<br />Your Message:
<br /><textarea name="msg" rows="10" cols="70" /></textarea>
<br />
<br /><input type="submit" value="          Send your message now          " />
<br />
<br />
</form>
</div>
<?php
include($this->templates_dir . '/footer.php');
