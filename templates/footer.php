<?php // Attogram Framework - Page Footer v0.0.5

namespace Attogram;

?>
<footer class="footer">
 <div class="container-fluid">
  <p><small>
    <strong>🔧</strong> Powered by
    <a target="github" href="<?php print $this->project_github; ?>">Attogram Framework v<?php print ATTOGRAM_VERSION; ?></a>
    &nbsp;&nbsp; - &nbsp;&nbsp;
    📆 <?php print gmdate('Y-m-d H:i:s'); ?> UTC
    &nbsp;&nbsp; - &nbsp;&nbsp;
    Page made with 💙 in <?php print round( (microtime(1) - $this->start_time), 4); ?> seconds
  </small></p>
 </div>
</footer>
<?php
if( $this->debug && isset($this->log->stack) && $this->log->stack ) {
  print '<div class="container"><pre class="alert alert-debug">Debug Log:<br />'
  . implode($this->log->stack, '<br />')
  . '</pre></div>';
}
?>
</body></html>
