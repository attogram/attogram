<?php // Attogram Framework - Page Footer v0.0.6

namespace Attogram;

?>
<footer class="footer">
 <div class="container-fluid">
  <p>
    <nobr>🚀 Powered by the
    <a target="github" href="<?php print $this->project_github; ?>">Attogram Framework v<?php print ATTOGRAM_VERSION; ?></a></nobr>
    &nbsp;&nbsp; <small>|</small> &nbsp;&nbsp;
    <nobr>📆 <?php print gmdate('Y-m-d H:i:s'); ?> UTC</nobr>
    &nbsp;&nbsp; <small>|</small> &nbsp;&nbsp;
    <nobr>💙 Page generated in <?php print round( (microtime(1) - $this->start_time), 4); ?> seconds</nobr>
  </p>
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
