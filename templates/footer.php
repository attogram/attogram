<?php 
// Attogram - template - footer

$this->hook('PRE-FOOTER');
?><div class="footer">
<a href="<?php print $this->path; ?>/">Attogram PHP Framework</a> 
version <?php print $this->version; ?> @ <?php print gmdate('Y-m-d H:i:s'); ?> UTC<br />
<?php if( $this->is_admin() ) { ?><a href="<?php print $this->path; ?>/admin/">admin</a>:<?php } ?>
user: <?php print $_SERVER['REMOTE_ADDR']; ?>
<?php $this->hook('POST-FOOTER'); ?>
</div></body></html>
