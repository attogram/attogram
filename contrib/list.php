<?php
// Attogram - action - list

$this->page_header('Attogram - List');

print '<div class="container">';

$sql = 'SELECT count(id) AS count, list FROM list GROUP BY list';
$lists = $this->db->query($sql);

$sql = 'SELECT * FROM list ORDER BY list, ordering, id DESC';
$items = $this->db->query($sql);


foreach( $lists as $list ) {
  print '<b>' . $list['list'] . '</b>:<ol>';
  foreach( $items as $item ) {
    if( $item['list'] != $list['list'] ) { continue; }
    print '<li><b>' .  $item['item'] . '</b>';
  }
  print '</ol>';
}

if( $this->is_admin() ) { print '<p>(<a href="../lists/">List Admin</a>)</p>'; }

print '</div>';

$this->page_footer();
