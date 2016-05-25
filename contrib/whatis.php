<?php
/*
  Example Attogram subpath usage
  
  - Place this file in ./public/actions/whatis.php
  
  - add depth setting to ./public/config.php:
      $config['depth']['whatis'] = 3; // depth for action 'whatis'

*/

namespace Attogram;

$items = array(
  'PHP' => 'The PHP programming language ...',
  'Apache' => 'The Apache web server ...',
  'SQLite' => 'The SQLite database ...',
  'Markdown' => 'The Markdown markup language ...',
);
$default = 'This page has information about:<br /><ul>';
foreach( array_keys($items) as $i ) {
  $default .= '<li><a href="./' . $i . '">' . $i . '</a></li>';
}
$items['this page'] = $default . '</ul>';

if( !isset($this->uri[1]) || $this->uri[1] == '' ) {
  $this->log->error('whatis: missing item: ' . $this->uri[1]);
  $item = 'this page';
} else {
  $item = $this->uri[1];
}

if( !array_key_exists($item,$items) ) {
  $this->log->error('whatis: item not defined: ' . $item);
  $this->error404();
  exit;
}
$this->page_header("What is $item?");
?>
<div class="container">

<h1>What is <strong><?php print $item; ?></strong>?</h1>

<p><?php print $items[$item]; ?></p>

</div>
<?php
$this->page_footer();