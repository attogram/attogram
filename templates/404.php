<?php // Attogram Framework - 404 Page Template v0.0.3

header('HTTP/1.0 404 Not Found');

$title = '😕 404 Not Found';

$this->page_header($title);

print '<div class="container"><h1>' . $title . '</h1>';

if( isset($error) && $error ) {
  print '<h2>💔 <code>' . htmlentities($error) . '</code></h2>';
}

print '</div>';

$this->page_footer();
