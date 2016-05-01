<?php 
// Attogram - action - contact
$title = 'Attogram - Contact';
include('templates/header.php');

if( isset($_POST['msg']) && isset($_POST['email']) ) {

  $sql = 'INSERT INTO contact (time,email,msg,ip,agent) VALUES (DATETIME("now"),:email,:msg,:ip,:agent)';

  $bind = array(
   'email'=>$_POST['email'],
   'msg'=>$_POST['msg'],
   'ip'=>@$_SERVER['REMOTE_ADDR'],
   'agent'=>@$_SERVER['HTTP_USER_AGENT'],
  );

  $ok_msg = '<p>Thank You.  Message received.</p>';

  if( $this->queryb($sql,$bind) ) { 
    print $ok_msg; include('templates/footer.php'); $this->hook('POST-ACTION'); exit;
  }

  if( $this->get_db()->errorCode() == 'HY000' ) { 
  if( $this->queryb("CREATE TABLE IF NOT EXISTS 'contact' ( 'id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
      'time' DATETIME, 'email' TEXT, 'msg' TEXT, 'ip' TEXT, 'agent' TEXT)") ) { 
    if( $this->queryb($sql,$bind) ) {
      print '<p>contact table created OK</p>'.$ok_msg; include('templates/footer.php'); $this->hook('POST-ACTION'); exit;
    }
  } 
  } 
  print 'ERROR: message not saved.';
  include('templates/footer.php'); $this->hook('POST-ACTION'); exit;

}

if( isset($_POST['msg']) || isset($_POST['email']) ) { print 'ERROR<hr />'; }


?>
<div class="body">
<form action="." method="POST">
Contact the <a target="code" href="https://github.com/attogram/attogram/">Attogram Developers</a>, we're here to help!
<br />
<br />Your Email: <input type="text" name="email" size="55" value="" />
<br />
<br />Your Message:
<br /><textarea name="msg" rows="10" cols="70" />
Dear Attogram Developers,

  (your message here)

Sincerely,
(your name and contact info here)
</textarea>
<br />
<br /><input type="submit" value="          Send your message now          " />
<br />
<br />
</form>
</div>
<?php
include('templates/footer.php');
