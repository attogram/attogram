<?php // Attogram Framework - Navbar v0.1.5

namespace Attogram;

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
      <a class="navbar-brand" href="<?php echo $this->path; ?>/"><?php echo $this->siteName; ?></a>
    </div>
    <div id="navbar" class="navbar-collapse collapse">
      <ul class="nav navbar-nav"><?php
        foreach (array_keys($this->getActions()) as $a) {
            switch ($a) {
              case 'home': case 'login': case 'user':
                continue;
              default:
                print '<li><a href="'.$this->path.'/'.$a.'/">'.$a.'</a></li>';
                break;
            }
        }
    ?></ul>
      <ul class="nav navbar-nav navbar-right">
<?php
    if (class_exists('\attogram\AttogramUser')) {
        if (\attogram\AttogramUser::is_logged_in()) {
            echo '<li><a href="'.$this->path.'/user/"><span class="icon-s">👤</span> <b>'
            .((isset($_SESSION['AttogramUsername']) && $_SESSION['AttogramUsername'])  ? $_SESSION['AttogramUsername'] : 'user')
            .'</b></a></li>';
            echo '<li><a href="?logoff"><span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span> logoff</a></li>';
        } else {

            if (array_key_exists('login', $this->getActions())) { // if User Module is loaded
                echo '<li><a href="'.$this->path
                .'/login/">login <span class="glyphicon glyphicon-log-in" aria-hidden="true"></span></a></li>';
            }
        }
    }  // end if user module active

    if ($this->isAdmin() && $this->getAdminActions()) {
        echo '<li class="dropdown">'
        .'<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">'
        .'Admin <span class="caret"></span></a><ul class="dropdown-menu">';
        foreach (array_keys($this->getAdminActions()) as $a) {
            echo '<li><a href="'.$this->path.'/'.$a.'/">'.$a.'</a></li>';
        }
        echo '</ul></li>';
    }
    ?></ul>
    </div><!--/.nav-collapse -->
  </div><!--/.container-fluid -->
</nav>
