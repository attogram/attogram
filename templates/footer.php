<?php
// Attogram - template - footer

namespace Attogram;

$this->hook('PRE-FOOTER');
?>

<style>
/* Sticky footer styles
-------------------------------------------------- */
html {
  position: relative;
  min-height: 100%;
}
body {
  /* Margin bottom by footer height */
  margin-bottom: 60px;
}
.footer {
  position: absolute;
  bottom: 0;
  width: 100%;
  /* Set the fixed height of the footer here */
  height: 60px;
  background-color: #f5f5f5;
}

/* Custom page CSS
-------------------------------------------------- */
/* Not required for template or sticky footer method. */

.container {
  width: auto;
  max-width: 680px;
  padding: 0 15px;
}
.container .text-muted {
  margin: 20px 0;
}
</style>

<footer class="footer">
 <div class="container">
  <p>
    Powered by <a target="github" href="https://github.com/attogram/attogram">Attogram PHP Framework</a>
    &nbsp; - &nbsp; <?php $this->hook('POST-FOOTER'); ?>
  </p>
 </div>
</footer>
<?php
if( isset($this->error) && $this->error ) {
  print '<div class="alert alert-danger">System Errors:<br />- ' . to_list($this->error, '<br />- ') . '</div>';
}

if( isset($this->sqlite_database->error) && $this->sqlite_database->error ) {
  //print '<pre>DB Errors: ' . print_r($this->sqlite_database->error,1) . '</pre>';
  print '<div class="alert alert-danger">DB Errors:<br />- ' . to_list($this->sqlite_database->error, '<br />- ') . '</div>';
}
?>

</body></html>
