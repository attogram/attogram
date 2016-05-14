<?php
// Attogram - action - admin - messages

$title = 'Attogram - Admin - Messages';
include($this->templates_dir . '/header.php');
?>
<div class="container">
<?php

$sql = 'SELECT * FROM contact ORDER BY id DESC';
$m = $this->sqlite_database->query($sql);

print '<b>' . count($m) . '</b> <a href="">Messages</a><hr />';

foreach( $m as $message ) {
  print 'ID: ' . $message['id']
  . ' <a target="_db" href="../database-phpLiteAdmin/?table=contact&action=row_editordelete&pk=[' . $message['id'] . ']&type=edit">edit</a>'
  . ' <a target="_db" href="../database-phpLiteAdmin/?table=contact&action=row_editordelete&pk=[' . $message['id'] . ']&type=delete">delete</a>'
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
include($this->templates_dir . '/footer.php');