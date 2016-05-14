<?php
// Attogram - action - admin

$title = 'Attogram - Admin - DB setup';
include($this->templates_dir . '/header.php');
?>
<div class="container">
Config: <a href="./">Database Tables</a>
<ul>
<li><a href="./?create">Create Attogram Tables</a>
</ul>
<?php
if( isset($_GET['create']) ) {

  if( !$this->sqlite_database->get_tables() ) {
    print '<pre>ERROR: no table definitions found</pre>';
  } else {
    foreach(array_keys( $this->sqlite_database->tables ) as $table) {
      print "<br />Creating table <b>$table</b>: ";
      if( $this->sqlite_database->create_table($table) ) {
        print 'OK';
      } else {
        print 'ERROR';
      }
    }
  }
}
?>
</div>
<?php
include($this->templates_dir . '/footer.php');