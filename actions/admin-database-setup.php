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
  if( isset($_GET['create']) ) { create_tables($this); }
?>
</div>
<?php
include('templates/footer.php');

function create_tables($attogram) {
    $create = array();
    $create['contact'] = "CREATE TABLE 'contact' (
 'id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
 'time' DATETIME,
 'email' TEXT,
 'msg' TEXT,
 'ip' TEXT,
 'agent' TEXT
)";
    $create['user'] = "CREATE TABLE 'user' (
 'id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
 'username' TEXT UNIQUE NOT NULL,
 'password' TEXT NOT NULL,
 'email' TEXT NOT NULL,
 'level' INTEGER NOT NULL DEFAULT '0',
 'last_login' DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
 'last_host' TEXT NOT NULL
)";

    print 'Create Tables:<br />';
    foreach($create as $c) {
        print "<hr>$c<br />";
        if( $attogram->queryb($c) ) { print 'OK'; } else { print 'ERROR'; }
    }
}