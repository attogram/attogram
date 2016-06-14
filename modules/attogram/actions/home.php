<?php // Attogram Framework - Home Page v0.0.3

namespace Attogram;

$this->page_header('Attogram Framework v' . self::ATTOGRAM_VERSION);

?>

<div class="jumbotron text-center">

  <h1>Attogram Framework</h1>

  <p>giving developers a jumpstart to quickly create web sites</p>

  <a href="https://github.com/attogram/attogram" class="btn btn-primary btn-lg active" role="button" style="margin-bottom:15px;">
    <span class="glyphicon glyphicon-search" aria-hidden="true"></span> View Attogram on GitHub
  </a>
  &nbsp;
  <a href="https://github.com/attogram/attogram/archive/master.zip" class="btn btn-primary btn-lg active" role="button" style="margin-bottom:15px;">
    <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Download Attogram Now!
  </a>

</div>

<div class="container">

  <p>Attogram is a <strong><a href="http://php.net/">PHP</a></strong>-based framework
     that provides developers a skeleton site with:</p>
  <ul>
  <li>file-based <strong>URL routing</strong></li>
  <li>IP-protected <strong>backend</strong></li>
  <li>simple <strong>user system</strong></li>
  <li>integrated <strong><a href="http://sqlite.org/">SQLite</a></strong> database with <a href="https://www.phpliteadmin.org/"><strong>phpLiteAdmin</strong></a></li>
  <li><strong><a href="http://parsedown.org/">Markdown parser</a></strong>
  <li><strong><a href="http://jquery.com/">jQuery</a></strong> and <strong><a href="http://getbootstrap.com/">Bootstrap</a></strong></li>
  </ul>
  <p>After that, Attogram tries to stay out of your way while you <strong>do your thing</strong>!</p>
  <p>Attogram is <a href="license/">Dual Licensed</a> under the
     <a href="http://opensource.org/licenses/MIT">The MIT License</a>
     or the <a href="http://opensource.org/licenses/GPL-3.0">GNU General Public License</a>,
     at your choosing.
  <p>Read more <strong><a href="about/">about Attogram</a></strong>.</p>

</div>

<?php
$this->page_footer();
