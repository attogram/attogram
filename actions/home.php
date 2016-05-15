<?php
// Attogram - action - home

namespace Attogram;

$this->page_header('Attogram Framework');

?>

<div class="jumbotron text-center">
  <h1>Attogram Framework</h1>
  <p>giving developers a jumpstart to quickly create web sites</p>
  <p><a href="https://github.com/attogram/attogram"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> View Attogram on GitHub</a></p>
  <p><a href="https://github.com/attogram/attogram/archive/master.zip"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Download Attogram Now!</a></p>
</div>

<div class="container">
  <p>Attogram runs on <b>PHP</b> with <b>Apache</b>.</p>
  <p>It provides <b>URL routing</b>, an IP-protected <b>backend</b>, a <b>user system</b>, a <b>SQLite</b> database with web admin, a <b>Markdown</b> parser, <b>jQuery</b> and <b>Bootstrap</b>.</p>
  <p>After that, Attogram tries to stay out of your way while you <b>do your thing</b>!</p>
</div>

<?php
$this->page_footer();
