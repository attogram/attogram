<?php
// Attogram Framework - 404 Page Template v0.1.0

header('HTTP/1.0 404 Not Found');

$title = '😕 404 Not Found';

$this->page_header($title);

echo '<div class="container"><h1>'.$title.'</h1>';

if (isset($error) && $error) {
    echo '<h2>💔 <code>'.htmlentities($error).'</code></h2>';
}

echo '</div>';

$this->page_footer();
