<?php
// Attogram - action - admin - info

namespace Attogram;

$this->page_header('Attogram - Admin - Info');

$info = array();
$info['version'] = $this->version;
$info['path'] = $this->path;
$info['uri'] = '<li>' . to_list($this->uri,'<li>'); 
$info['site_url'] = '<a href="' . $this->get_site_url() . '">' . $this->get_site_url() . '</a>';
$info['action'] = $this->action;

$info['actions_dir'] = $this->actions_dir;
$info['default_action'] = $this->default_action;
$info['actions'] = '<li>' . to_list($this->actions, '<li>');

$info['admin_dir'] = $this->admin_dir;
$info['admin_actions'] = '<li>' . to_list($this->admin_actions, '<li>');
$info['admins'] = '<li>' . to_list($this->admins, '<li>');

$info['fof'] = $this->fof;
$info['templates_dir '] = $this->templates_dir ;
$info['functions_dir'] = $this->functions_dir;
$info['skip_files'] = '<li>' . to_list($this->skip_files, '<li>');

$info['db_name'] = $this->db_name;
$info['tables_dir'] = $this->tables_dir;
$info['database_size'] = (file_exists($this->db_name) ? filesize($this->db_name) : 'NULL') . ' bytes';
$info['sqlite_database'] = get_class($this->sqlite_database);

$info['attogram_id'] = htmlentities(@$_SESSION['attogram_id']);
$info['attogram_username'] = htmlentities(@$_SESSION['attogram_username']);
$info['attogram_level'] = htmlentities(@$_SESSION['attogram_level']);
$info['attogram_email'] = htmlentities(@$_SESSION['attogram_email']);


print '
<div class="container">
  <h1>Attogram Framework Info</h1>
  <table class="table">
';

foreach( $info as $name => $value ) {
  print '<tr><td>' . $name . '</td><td>' . $value . '</td></tr>';
  
}

print '</table></div>';

//phpinfo();

$this->page_footer();
