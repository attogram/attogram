<?php
// Attogram Framework - List Module - List Page v0.1.3

namespace attogram;

$this->pageHeader('Attogram - List');

echo '<div class="container">';

$sql = 'SELECT count(id) AS count, list FROM list GROUP BY list';
$lists = $this->database->query($sql);

$sql = 'SELECT * FROM list ORDER BY list, ordering, id DESC';
$items = $this->database->query($sql);

foreach ($lists as $list) {
    echo '<b>'.$list['list'].'</b>:<ol>';
    foreach ($items as $item) {
        if ($item['list'] != $list['list']) {
            continue;
        }
        echo '<li><b>'.$item['item'].'</b>';
    }
    echo '</ol>';
}

if ($this->isAdmin()) {
    echo '<p>(<a href="../list-admin/">List Admin</a>)</p>';
}

echo '</div>';

$this->pageFooter();
