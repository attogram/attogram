<?php
// Attogram - action - home
include('templates/header.php');
$this->hook('PRE-HOME');
?>
<div class="body">

<p>Welcome to the <b>Attogram PHP Framework</b> version <?php print $this->version; ?></p>

<ul><?php
foreach( $this->actions as $a ){
	print '<li><a href="./' . $a . '/">' . $a . '</a></li>';
}
?></ul>

</div>
<?php
$this->hook('POST-HOME');
include('templates/footer.php');

