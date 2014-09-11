<?php
// Attogram - action - admin

$title = 'Attogram - Admin';
include('templates/header.php');
$this->hook('PRE-ADMIN');
?>
<div class="body">
Attogram admin @ <?php print @$_SERVER['REMOTE_ADDR']; ?>
<?php
foreach( $this->get_actions() as $a ) {
  if( $a=='admin' || !preg_match('/^admin/',$a) ) { continue; } 
  print '<br /><br /><a href="../' . $a . '/">' . $a . '</a>';
}
?>
<br /><br /><hr />
Plugins:
<?php $this->hook('POST-ADMIN'); ?>
</div>
<?php
include('templates/footer.php');
