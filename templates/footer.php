<?php 
// Attogram - template - footer

$this->hook('PRE-FOOTER');
?><div class="footer">
Powered by <a href="<?php print $this->path; ?>/">Attogram PHP Framework</a> 
&nbsp; - &nbsp;
<a target="code" href="https://github.com/attogram/attogram">Attogram @ GitHub</a>
&nbsp; - &nbsp;
<?php $this->hook('POST-FOOTER'); ?>
</div></body></html>