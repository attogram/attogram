<?php
// Attogram - action - admin - check

$title = 'Attogram - Admin - Check';
include('templates/header.php');




print '<div class="body"><div class="center">
<table><tr class="h"><td><h1 class="p">Attogram PHP Framework Version ' . $this->version . '</h1></td></tr></table>
<table>';

$info = get_object_vars($this);

foreach($info as $key => $var) {
	print '<tr><td class="e">' . $key . ' </td><td class="v">' . print_r($var,1) . '</td></tr>';
}

print '<tr><td class="e">Database size </td><td class="v">' 
. (file_exists($this->db_name) ? filesize($this->db_name) : 'NULL') . ' bytes</td></tr>';

print '</table>';

phpinfo();
include('templates/footer.php');
