<?php
// Attogram - action - admin

$attogram_tables = array('user','contact','list');

$title = 'Attogram - Admin - DB setup';
include($this->templates_dir . '/header.php');
?>
<div class="body">
Config: <a href="./">Database Tables</a>
<ul>
<li><a href="./?create">Create Attogram Tables</a>
</ul>
<?php
if( isset($_GET['create']) ) {
  foreach($attogram_tables as $table) {
    print "<br />Creating table <b>$table</b>: ";
    if( $this->create_table($table) ) { print 'OK'; } else { print 'ERROR'; }    
  }
}
?>
</div>
<?php
include($this->templates_dir . '/footer.php');