<?php
// Attogram - action - home

include($this->templates_dir . '/header.php');

?>
<style>
ul, ol {
  padding: 0px 16px;
}
</style>

<div class="jumbotron">
 <div class="container text-center">
  <h1>Attogram Framework</h1>
  <p>Version <?php print $this->version; ?></p>
 </div>
</div>

<div class="container">

  <div class="row">

    <div class="col-sm-3">
      <h3>Pages:</h3>
      <ul>
      <?php
        foreach( $this->actions as $action ){
          print '<li><a href="./' . $action . '/">' . $action . '</a></li>';
        }
      ?>
      </ul>
    </div>

    <div class="col-sm-3">
      <?php
        if( $this->is_admin() ) {
          $this->get_admin_actions();
          print '<h3>Admin:</h3><ul>';
          foreach( $this->admin_actions as $action ){
            print '<li><a href="./' . $action . '/">' . $action . '</a></li>';
          }
          print '</ul>';
        }
      ?>
    </div>

    <div class="col-sm-6">
      <p>The Attogram Framework gives developers a jumpstart for creating new web sites.</p>
      <p>Attogram uses PHP with Apache2.  It includes URL routing, an IP-protected backend, a user system,
      an integrated SQLite database with web administration, jQuery and Bootstrap.
      After that, Attogram tries to stay out of your way while you build what you want.</p>

      <p><a href="https://github.com/attogram/attogram"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> View Attogram on GitHub</a></p>
      
      <p><a href="https://github.com/attogram/attogram/archive/master.zip"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Download Attogram Now!</a></p>


    </div>
  </div>

  </div>

</div>

<?php
include($this->templates_dir . '/footer.php');
