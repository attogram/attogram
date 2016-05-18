<?php
// Attogram - template - footer

namespace Attogram;

?>

<footer class="footer">
 <div class="container">
  <p><small>
    Powered by <a target="github" href="https://github.com/attogram/attogram">Attogram Framework v<?php print ATTOGRAM_VERSION; ?></a>
    &nbsp; | &nbsp; <?php print gmdate('Y-m-d H:i:s'); ?> UTC
  </small></p>
 </div>
</footer>

<?php
if( isset($this->error) && $this->error ) {
  print '<pre class="alert alert-danger">System Errors:<br />' . rtrim(ltrim(print_r($this->error,1),"Array\n("),"\n)") . '</pre>';
}

if( isset($this->sqlite_database->error) && $this->sqlite_database->error ) {
  print '<pre class="alert alert-danger">DB Errors:<br />' . rtrim(ltrim(print_r($this->sqlite_database->error,1),"Array\n("),"\n)") . '</pre>';
}
?>

</body></html>