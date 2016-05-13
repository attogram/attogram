<?php
// Attogram - templates - header

namespace Attogram;

if( !isset($title) || !$title ) { $title = 'Attogram Framework'; }

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="<?php print $this->path; ?>/web/css.css">
<link rel="stylesheet" href="<?php print $this->path; ?>/web/bootstrap.min.css">
<title><?php print $title; ?></title>
<script src="<?php print $this->path; ?>/web/jquery.min.js"></script>
<script src="<?php print $this->path; ?>/web/bootstrap.min.js"></script>
</head>
<body>
<?php
$navbar = $this->templates_dir . '/navbar.php';
if( is_readable_php_file( $navbar ) ) {
  include( $navbar );
} else {
  $this->error[] = "Missing navbar template: $navbar";
}
