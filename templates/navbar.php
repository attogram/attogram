<?php
// Attogram Framework - Navbar v0.0.2

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
      <a class="navbar-brand" href="<?php print $this->path; ?>/"><?php print $this->site_name; ?></a>
    </div>
    <div id="navbar" class="navbar-collapse collapse">
      <ul class="nav navbar-nav"><?php
        foreach( array_keys($this->get_actions()) as $a ) {
            switch( $a ) {
              case 'home': case 'login': case 'user':
                continue;
              default:
                print '<li><a href="' . $this->path . '/' . $a . '/">' . $a . '</a></li>';
                break;
            }
          }
    ?></ul>
      <ul class="nav navbar-nav navbar-right">
<?php
      if( $this->is_logged_in() ) {
        print '<li><a href="' . $this->path . '/user/"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> <b>' . $this->session->get('attogram_username','user') . '</b></a></li>';
        print '<li><a href="?logoff"><span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span> logoff</a></li>';
      } else {
        print '<li><a href="' . $this->path . '/login/">login <span class="glyphicon glyphicon-log-in" aria-hidden="true"></span></a></li>';
      }

      if( $this->is_admin() ) {
        ?><li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Admin <span class="caret"></span></a>
          <ul class="dropdown-menu"><?php
          foreach( array_keys($this->get_admin_actions()) as $a ) {
            print '<li><a href="' . $this->path . '/' . $a . '/">' . $a . '</a></li>';
          }
          ?></ul>
        </li><?php
      }
      ?></ul>
    </div><!--/.nav-collapse -->
  </div><!--/.container-fluid -->
</nav>
