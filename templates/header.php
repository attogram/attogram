<?php
// Attogram - templates - header

if( !isset($title) || !$title ) { $title = 'Attogram Framework'; }

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title><?php print $title; ?></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="<?php print $this->path; ?>/web/css.css">
  <link rel="stylesheet" href="<?php print $this->path; ?>/web/bootstrap.min.css">
  <script src="<?php print $this->path; ?>/web/jquery.min.js"></script>
  <script src="<?php print $this->path; ?>/web/bootstrap.min.js"></script>
</head>
<body>

    <nav class="navbar navbar-default">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" href="<?php print $this->path; ?>/">Attogram Framework</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
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
}          
?>                
              </ul>
            </li>
          </ul>
<?php
if( $this->is_admin() ) { 
?>
          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Admin <span class="caret"></span></a>
              <ul class="dropdown-menu">
<?php
foreach( $this->get_admin_actions() as $a ) {
  print '<li><a href="' . $this->path . '/' . $a . '/">' . $a . '</a></li>';
}      
?>  
              </ul>
            </li>
          </ul>
<?php
}
?>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
    


<?php




