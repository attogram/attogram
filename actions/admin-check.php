<?php
// Attogram - action - admin - check

$title = 'Attogram - Admin - Check';
include('templates/header.php');
print '<div class="body"><pre>Attogram CHECK @ ' . gmdate('r') . ' UTC:<br /><br />';
system('libs/check.sh');
print '<hr />Attogram test:<br />';
print '<br />Plugin test: '; $this->hook('ERROR_TEST'); 
print '<br />'; print_r($this); 
print '</pre></div>';
include('templates/footer.php');
