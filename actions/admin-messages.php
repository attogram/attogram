<?php
// Attogram - action - admin - messages

$title = 'Attogram - Admin - Messages';
include('templates/header.php');
?>
<div class="body"><pre><?php

$m = $this->query('SELECT * FROM contact ORDER BY id DESC');

print count($m) . ' Messages from the <a href="../contact/">Contact form</a>:<hr />';

foreach( $m as $message ) { 
  print 'ID: ' . $message['id'] 
  . ' <a href="../admin-database-phpLiteAdmin/?table=contact&action=row_editordelete&pk=[' . $message['id'] . ']&type=edit">edit</a>'
  . ' <a href="../admin-database-phpLiteAdmin/?table=contact&action=row_editordelete&pk=[' . $message['id'] . ']&type=delete">delete</a>'
  . '<br />IP/Time: ' . $message['ip'] . ' @ ' . $message['time']
  . '<br />Agent: ' . htmlentities($message['agent'])
  . '<br />Email: ' . htmlentities($message['email'])
  . '<br />Message:<br />' . htmlentities($message['msg'])
  . '<hr />';
}
?>
</pre></div>
<?php
include('templates/footer.php');
