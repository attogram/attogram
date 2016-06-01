<?php
// Attogram - action - admin - lists

namespace Attogram;

$this->page_header('Attogram - Admin - Lists');
?>
<div class="container">
<?php

$sql = 'SELECT count(id) AS count, list FROM list GROUP BY list';
$lists = $this->db->query($sql);
print '<b>' . count($lists) . '</b> <a href="">Lists</a>';
print ' &nbsp; - &nbsp; <a target="_db" href="../db-admin/?table=list&action=row_create">Create New Item</a></p>';

$sql = 'SELECT * FROM list ORDER BY list, ordering, id DESC';
$items = $this->db->query($sql);

print '<ul>';
foreach( $lists as $list ) {
  print '<hr><li><b>' . $list['list'] . '</b> &nbsp; - &nbsp; <b>' . $list['count'] . '</b> items <ol>';
  foreach( $items as $item ) {
    if( $item['list'] != $list['list'] ) { continue; }
    print '<li><b>' .  $item['item'] . '</b>'
    . ' &nbsp; - &nbsp; ('
    . ' <a target="_db" href="../db-admin/?table=list&action=row_editordelete&pk=[' . $item['id'] . ']&type=edit">edit</a>'
    . ' <a target="_db" href="../db-admin/?table=list&action=row_editordelete&pk=[' . $item['id'] . ']&type=delete">delete</a>'
    . ' ID:' . $item['id']
    . ' order:' . $item['ordering']
    . ')';
  }
  print '</ol>';
}
print '</ul>';

?>
</div>
<?php
$this->page_footer();
