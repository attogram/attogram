<?php
// Attogram - action - home
include($this->templates_dir . '/header.php');
$this->hook('PRE-HOME');
?>
<div class="body">

<p>Welcome to the <b>Attogram PHP Framework</b> version <?php print $this->version; ?></p>

<p>Pages:<ul><?php
foreach( $this->actions as $action ){
  if( preg_match('/^admin/',$action) ) { continue; }
  print '<li><a href="./' . $action . '/">' . $action . '</a></li>';
}
?>
<li><a href="README.md">README.md</a></li>
</ul></p>

<?php if( $this->is_admin() ) { 
  $this->get_admin_actions();

?>
<p>Admin Pages:<ul><?php
foreach( $this->admin_actions as $action ){
  print '<li><a href="./' . $action . '/">' . $action . '</a></li>';
}
?></ul></p>
<?php } ?>
</div>
<?php
$this->hook('POST-HOME');
include($this->templates_dir . '/footer.php');