<?php
// Attogram - action - admin

$title = 'Attogram - Admin - DB setup';
include('templates/header.php');
?>
<div class="body">
<a href="./">Config: Database Tables</a>
<ul>
<li><a href="./?create">Create Attogram Tables</a>
</ul>
<?php
  if( isset($_GET['create']) ) {
    print '<br />Creating table <b>user</b>: ';
    if( $this->create_table('user') ) { print 'OK'; } else { print 'ERROR'; }
	print '<br />Creating table <b>contact</b>: ';
    if( $this->create_table('contact') ) { print 'OK'; } else { print 'ERROR'; }
  }
?>
</div>
<?php
include('templates/footer.php');