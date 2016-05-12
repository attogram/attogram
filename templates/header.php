<?php
// Attogram - templates - header

$this->hook('PRE-HEADER');
if( !isset($title) || !$title ) { $title = 'Attogram PHP Framework'; }

?><!doctype html><html><head>
<title><?php print $title; ?></title>
<meta charset="utf-8" />
<link rel="stylesheet" type="text/css" href="<?php print $this->path; ?>/web/css.css">
<link rel="stylesheet" href="<?php print $this->path; ?>/web/bootstrap.min.css">
<link rel="stylesheet" href="<?php print $this->path; ?>/web/bootstrap-theme.min.css">
<script src="<?php print $this->path; ?>/web/bootstrap.min.js"></script>
</head><body>
<div class="header"><a href="<?php print $this->path; ?>/">Attogram PHP Framework</a>
<?php

$spacer = ' &nbsp;&nbsp;&nbsp;&nbsp; ';
foreach( $this->get_actions() as $a ) {
  if( $a == 'login' && $this->is_logged_in() ) { continue; }
  if( $a == 'user' && !$this->is_logged_in() ) { continue; }
  if( $a == 'user' && $this->is_logged_in() ) {
  print $spacer . '<a href="' . $this->path . '/user/">User: <b>'
  . $_SESSION['attogram_username'] . '</b></a>';
  continue;
  }
  print $spacer . '<a href="' . $this->path . '/' . $a . '/">' . $a . '</a>';
}

if( $this->is_logged_in() ) {
 print $spacer . '<a href="?logoff">logoff</a>';
}
?></div>
<?php
$this->hook('POST-HEADER');
