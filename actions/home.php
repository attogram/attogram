<?php
// Attogram - action - home
include($this->templates_dir . '/header.php');
$this->hook('PRE-HOME');
?>



<div class="container">

  <div class="jumbotron">
    <h1>Attogram PHP Framework</h1>
    <p>Version <?php print $this->version; ?></p> 
  </div>
  
  <div class="row">
    <div class="col-sm-4">
      <h3>Pages</h3>
      <p><ul>
<?php
foreach( $this->actions as $action ){
  print '<li><a href="./' . $action . '/">' . $action . '</a></li>';
}
?>
    </ul></p>
    </div>
    
    <div class="col-sm-4">
<?php
    if( $this->is_admin() ) {
  $this->get_admin_actions();
  print '<h3>Admin Pages:</h3><ul>';
  foreach( $this->admin_actions as $action ){
    print '<li><a href="./' . $action . '/">' . $action . '</a></li>';
  }
  print '</ul>';
}
?>
    </div>
    
    <div class="col-sm-4">
      <h3>More</h3>        
      <ul><li><a href="https://github.com/attogram/attogram">Attogram @ GitHub</a></li></ul>
    </div>
  </div>
</div>










</div>
<?php
$this->hook('POST-HOME');
include($this->templates_dir . '/footer.php');
