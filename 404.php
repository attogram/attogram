<?php
// Attogram - 404
header("HTTP/1.0 404 Not Found"); 

$title = '404 Page Not Found';
include($this->templates_dir . '/header.php');

?>

<h1 style="text-align:center; font-size:60px;">Error 404 Page Not Found</h1>

<?php
include($this->templates_dir . '/footer.php');