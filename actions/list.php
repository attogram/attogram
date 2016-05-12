<?php
// Attogram - action - list

$title = 'Attogram - List';
include($this->templates_dir . '/header.php');

print '<div class="container">';

$sql = 'SELECT count(id) AS count, list FROM list GROUP BY list';
$lists = $this->sqlite_database->query($sql);

$sql = 'SELECT * FROM list ORDER BY list, ordering, id DESC';
$items = $this->sqlite_database->query($sql);


foreach( $lists as $list ) {
  print '<b>' . $list['list'] . '</b>:<ol>';
  foreach( $items as $item ) {
    if( $item['list'] != $list['list'] ) { continue; }
    print '<li><b>' .  $item['item'] . '</b>';
  }
  print '</ol>';
}

print '</div>';

if( $this->is_admin() ) { print ' (<a href="../lists/">List Admin</a>)'; }
include($this->templates_dir . '/footer.php');