<?php
// Attogram - action - admin - info
 
$title = 'Attogram - Admin - Info';
include($this->templates_dir . '/header.php');

function to_list($x) {
  if( is_array($x) ) {
    $r = '';
    foreach($x as $v) {
      if( !is_object($v) && !is_array($v) ) {
        $r .= $v . ', ';
      } else {
        $r .= to_list($v);
      }
    }
    return trim($r,', ');
  }
  if( is_object($x) ) {
    return print_r($x,1) . '<br />';
  }
  return $x;
}

print '<div class="body"><div class="center">
<table><tr class="h"><td><h1 class="p">Attogram PHP Framework Version ' 
. $this->version . '</h1></td></tr></table><table>';

$info = get_object_vars($this);

foreach($info as $key => $var) {
  print '<tr><td class="e">' . $key . ' </td><td class="v">' . to_list($var) . '</td></tr>';
}

print '<tr><td class="e">Database size </td><td class="v">'
. (file_exists($this->db_name) ? filesize($this->db_name) : 'NULL') . ' bytes</td></tr>';


print '<tr><td class="e">attogram_id </td><td class="v">' . htmlentities(@$_SESSION['attogram_id']) . '</td></tr>';
print '<tr><td class="e">attogram_username </td><td class="v">' . htmlentities(@$_SESSION['attogram_username']) . '</td></tr>';
print '<tr><td class="e">attogram_level </td><td class="v">' . htmlentities(@$_SESSION['attogram_level']) . '</td></tr>';
print '<tr><td class="e">attogram_email </td><td class="v">' . htmlentities(@$_SESSION['attogram_email']) . '</td></tr>';

print '</table>';

phpinfo();

include($this->templates_dir . '/footer.php');