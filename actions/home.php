<?php
// Attogram - action - home
include('templates/header.php');
$this->hook('PRE-HOME');
?>
<div class="body">
Attogram helps you quickly create web prototypes. Attogram is small, simple, and extendable.
<br /><br />
Attogram v<?php print $this->version; ?> quick specs:
<ul>
<li>PHP5, Apache2, SQLite, Plugin architecture
<li>IP protected backend, web-based database management
<li>Open source, MIT/GPL dual license, on <a target="code" href="https://github.com/attogram/attogram">GitHub</a>
</ul>
</div>
<?php
$this->hook('POST-HOME');
include('templates/footer.php');

