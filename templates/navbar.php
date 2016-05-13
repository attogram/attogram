<?php
// Attogram - templates - navbar

?>

<nav class="navbar navbar-default">

  <div class="container-fluid">

    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="<?php print $this->path; ?>/">Attogram</a>
    </div>

    <div id="navbar" class="navbar-collapse collapse">

      <ul class="nav navbar-nav navbar-right">
      <?php if( $this->is_admin() ) {  ?>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Admin <span class="caret"></span></a>
          <ul class="dropdown-menu">
          <?php foreach( $this->get_admin_actions() as $a ) {
            print '<li><a href="' . $this->path . '/' . $a . '/">' . $a . '</a></li>';
          } ?>
          </ul>
        </li>
      <?php } ?>

        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Pages <span class="caret"></span></a>
          <ul class="dropdown-menu">
          <?php
          foreach( $this->get_actions() as $a ) {
            if( $a == 'login' && $this->is_logged_in() ) { continue; }
            if( $a == 'user' && !$this->is_logged_in() ) { continue; }
            if( $a == 'user' && $this->is_logged_in() ) {
              print '<li><a href="' . $this->path . '/user/">User: <b>' . $_SESSION['attogram_username'] . '</b></a></li>';
              continue;
            }
            print '<li><a href="' . $this->path . '/' . $a . '/">' . $a . '</a></li>';
          }
          if( $this->is_logged_in() ) {
            print '<li><a href="?logoff">logoff</a></li>';
          } ?>
          </ul>
        </li>

      </ul>
    </div><!--/.nav-collapse -->
  </div><!--/.container-fluid -->
</nav>
