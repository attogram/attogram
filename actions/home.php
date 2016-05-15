<?php
// Attogram - action - home

namespace Attogram;

$this->page_header('Attogram Framework');

?>

<div class="jumbotron text-center">

  <h1>Attogram Framework</h1>

  <p>giving developers a jumpstart to quickly create web sites</p>

  <a href="https://github.com/attogram/attogram"><button type="submit" class="btn btn-info">
    <span class="glyphicon glyphicon-search" aria-hidden="true"></span> View Attogram on GitHub
  </button></a>

  <a href="https://github.com/attogram/attogram/archive/master.zip"><button type="submit" class="btn btn-info">
    <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Download Attogram Now!
  </button></a>

</div>

<div class="container">
  <p>Attogram runs on <strong>PHP</strong> with <strong>Apache</strong>.</p>
  <p>It provides <strong>URL routing</strong>, an IP-protected <strong>backend</strong>, a <strong>user system</strong>, a <strong>SQLite</strong> database with web admin, a <strong>Markdown</strong> parser, <strong>jQuery</strong> and <strong>Bootstrap</strong>.</p>
  <p>After that, Attogram tries to stay out of your way while you <strong>do your thing</strong>!</p>
</div>

<?php
$this->page_footer();
