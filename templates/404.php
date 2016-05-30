<?php
// Attogram Framework - 404 Page v0.0.2

header("HTTP/1.0 404 Not Found");

$this->page_header('404 Not Found');
?>
<div class="container">
  <h1>404 Not Found</h1><?php

  if( isset($error) && $error ) {
    print '<p>Error: <code>' . htmlentities($error) . '</code></p>';
  }
?></div>
<?php
$this->page_footer();
