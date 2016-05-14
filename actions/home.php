<?php
// Attogram - action - home

namespace Attogram;

include($this->templates_dir . '/header.php');

?>
<style>
ul, ol {
  padding: 0px 16px;
}
</style>

<div class="jumbotron text-center">

  <h1>Attogram Framework</h1>

  <p>giving developers a jumpstart to quickly create web sites</p>
  
  <p><a href="https://github.com/attogram/attogram"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> View Attogram on GitHub</a></p>

  <p><a href="https://github.com/attogram/attogram/archive/master.zip"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Download Attogram Now!</a></p>

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
      <p>Attogram runs on <b>PHP</b> with <b>Apache</b>.</p> 
      <p>It provides <b>URL routing</b>, an IP-protected <b>backend</b>, a <b>user system</b>,
      a <b>SQLite</b> database with web admin, a <b>Markdown</b> parser, <b>jQuery</b> and <b>Bootstrap</b>.</p>
      <p>After that, Attogram tries to stay out of your way while you <b>do your thing</b>!</p>
    </div>
  </div>

  </div>

</div>

<?php
include($this->templates_dir . '/footer.php');
