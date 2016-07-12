<?php // Attogram Framework - List Module - List Admin v0.1.3

namespace attogram;

$this->pageHeader('Attogram - Admin - Lists');
?>
<div class="container">
<?php

$sql = 'SELECT count(id) AS count, list FROM list GROUP BY list';
$lists = $this->database->query($sql);
echo '<b>'.count($lists).'</b> <a href="">Lists</a>';
echo ' &nbsp; - &nbsp; <a target="_db" href="../db-admin/?table=list&action=row_create">Create New Item</a></p>';

$sql = 'SELECT * FROM list ORDER BY list, ordering, id DESC';
$items = $this->database->query($sql);

echo '<ul>';
foreach ($lists as $list) {
    echo '<hr><li><b>'.$list['list'].'</b> &nbsp; - &nbsp; <b>'.$list['count'].'</b> items <ol>';
    foreach ($items as $item) {
        if ($item['list'] != $list['list']) {
            continue;
        }
        echo '<li><b>'.$item['item'].'</b>'
    .' &nbsp; - &nbsp; ('
    .' <a target="_db" href="../db-admin/?table=list&action=row_editordelete&pk=['.$item['id'].']&type=edit">edit</a>'
    .' <a target="_db" href="../db-admin/?table=list&action=row_editordelete&pk=['.$item['id'].']&type=delete">delete</a>'
    .' ID:'.$item['id']
    .' order:'.$item['ordering']
    .')';
    }
    echo '</ol>';
}
echo '</ul>';

?>
</div>
<?php
$this->pageFooter();
