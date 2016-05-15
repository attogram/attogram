<?php
// Attogram - Admin - Generate Sitemap

$this->page_header('Attogram - Admin - Sitemap');

$sitemap = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
';

$site = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $this->path . '/';

foreach( array_keys($this->get_actions()) as $action ){
  $sitemap .= ' <url><loc>' . $site . $action . '/</loc></url>' . "\n";
}

$sitemap .= '</urlset>';
?>

<div class="container">
  <h3>Sitemap for <a href="<?php print $site; ?>"><?php print $site; ?></a></h3>
  <textarea name="sitemap" rows="20" cols="100"><?php print $sitemap; ?></textarea>
</div>

<?php
$this->page_footer();
