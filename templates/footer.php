<?php
// Attogram - template - footer

$this->hook('PRE-FOOTER');
?><div class="footer">
Powered by <a target="github" href="https://github.com/attogram/attogram">Attogram PHP Framework</a>
&nbsp; - &nbsp;
<?php $this->hook('POST-FOOTER'); ?>
</div>
<?php
if( isset($this->error) && $this->error ) {
  print '<pre>Errors: ' . print_r($this->error,1) . '</pre>';
}

if( isset($this->sqlite_database->error) && $this->sqlite_database->error ) {
  print '<pre>DB Errors: ' . print_r($this->sqlite_database->error,1) . '</pre>';
}

?>
</body></html>
