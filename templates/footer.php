<?php
// Attogram - template - footer

namespace Attogram;

?>

<footer class="footer">
 <div class="container">
  <p><small>
    Powered by <a target="github" href="https://github.com/attogram/attogram">Attogram Framework <small>v<?php print ATTOGRAM_VERSION; ?></small></a>
    &nbsp; @ &nbsp; <?php print gmdate('Y-m-d H:i:s'); ?> UTC
  </small></p>
 </div>
</footer>

<?php
if( isset($this->error) && $this->error ) {
  print '<div class="alert alert-danger">System Errors:<br />- ' . to_list($this->error, '<br />- ') . '</div>';
}

if( isset($this->sqlite_database->error) && $this->sqlite_database->error ) {
  print '<div class="alert alert-danger">DB Errors:<br />- ' . to_list($this->sqlite_database->error, '<br />- ') . '</div>';
}
?>

</body></html>
