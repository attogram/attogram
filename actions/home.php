<?php
// Attogram - action - home
include($this->templates_dir . '/header.php');
$this->hook('PRE-HOME');
?>
<div class="body">

<p>Welcome to the <b>Attogram PHP Framework</b> version <?php print $this->version; ?></p>

<?php
print '<p>Pages:<ul>';
foreach( $this->actions as $action ){
  print '<li><a href="./' . $action . '/">' . $action . '</a></li>';
}
print '</ul></p>';

if( $this->is_admin() ) {
  $this->get_admin_actions();
  print '<p>Admin Pages:<ul>';
  foreach( $this->admin_actions as $action ){
    print '<li><a href="./' . $action . '/">' . $action . '</a></li>';
  }
  print '</ul></p>';
}
?>
</div>
<?php
$this->hook('POST-HOME');
include($this->templates_dir . '/footer.php');
