<?php
// Attogram - action - readme

$title = 'Attogram - Readme';

include($this->templates_dir . '/header.php');
print '<div class="body">';

$pd = new Parsedown();

$rf = file_get_contents('readme.md');

$r = $pd->text($rf);

print $r;

print '</div>';

include($this->templates_dir . '/footer.php');
