<?php
// Attogram - templates - header

$this->hook('PRE-HEADER');
if( !isset($title) || !$title ) { $title = 'Attogram PHP Framework'; } 

?><!doctype html><html><head>
<title><?php print $title; ?></title>
<meta charset="utf-8" /><meta name="viewport" content="initial-scale=1" />
<link rel="stylesheet" type="text/css" href="/attogram/css.css">
</head><body><div class="header"><a href="/attogram/">Attogram PHP Framework</a>
<?php

$spacer = ' &nbsp;&nbsp;&nbsp;&nbsp; ';
foreach( $this->get_actions() as $a ) { 
  if( preg_match('/^admin/',$a) ) { continue; } 
  print $spacer . '<a href="/attogram/' . $a . '/">' . $a . '</a>'; 
}

if( $this->is_admin() ) { print $spacer . '<a href="/attogram/admin/">admin</a>'; } 
?>
</div>
<?php
$this->hook('POST-HEADER');

