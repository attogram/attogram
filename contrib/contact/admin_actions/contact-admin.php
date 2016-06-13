<?php // Attogram Framework - Message Admin v0.0.1

namespace Attogram;

$this->page_header('Attogram - Admin - Messages');
?>
<div class="container">
<?php

$sql = 'SELECT * FROM contact ORDER BY id DESC';
$m = $this->db->query($sql);

print '<strong>' . count($m) . '</strong> <a href="">Messages</a><hr />';

foreach( $m as $message ) {
  print 'ID: ' . $message['id']
  . ' <a target="_db" href="../db-admin/?table=contact&action=row_editordelete&pk=[' . $message['id'] . ']&type=edit">edit</a>'
  . ' <a target="_db" href="../db-admin/?table=contact&action=row_editordelete&pk=[' . $message['id'] . ']&type=delete">delete</a>'
  . '<br />IP: ' . $message['ip']
  . '<br />Time: ' . $message['time']
  . '<br />Agent: ' . htmlentities($message['agent'])
  . '<br />Email: ' . htmlentities($message['email'])
  . '<br />Message:<br />' . htmlentities($message['msg'])
  . '<hr />';
}
?>
</div>
<?php
$this->page_footer();
