<?php
// Attogram - action - admin - info

namespace Attogram;

$this->page_header('Attogram - Admin - Info');

$info = array();
$info['ATTOGRAM_VERSION'] = ATTOGRAM_VERSION;
$info['site_name'] = $this->site_name;
$info['site_url'] = '<a href="' . $this->get_site_url() . '">' . $this->get_site_url() . '</a>';
$info['path'] = ( $this->path ? $this->path : '<code>empty</code>' );
$info['uri'] = '<li>' . to_list($this->uri,'<li>');
$info['action'] = $this->action;

$autoloader = 'vendor/autoload.php';
$info['autoloader'] = ( is_readable_file($autoloader, '.php') ? $autoloader : '<code>null</code>' );
$info['log'] = ( is_object($this->log) ? get_class($this->log) : '<code>?</code>' );

$info['actions_dir'] = $this->actions_dir;
$info['default_action'] = $this->default_action;
$info['actions'] = list_actions($this->actions);

$info['admin_dir'] = $this->admin_dir;
$info['admin_actions'] = list_actions($this->admin_actions);
$info['admins'] = '<li>' . to_list($this->admins, '<li>');

$info['fof'] = $this->fof;
$info['templates_dir '] = $this->templates_dir ;
$info['functions_dir'] = $this->functions_dir;
$info['skip_files'] = '<li>' . to_list($this->skip_files, '<li>');

$info['db_name'] = $this->db_name;
$info['tables_dir'] = $this->tables_dir;
$info['database_size'] = (file_exists($this->db_name) ? filesize($this->db_name) : '<code>null</code>') . ' bytes';
$info['sqlite_database'] = get_class($this->sqlite_database);

$info['attogram_id'] = isset($_SESSION['attogram_id']) ? htmlentities($_SESSION['attogram_id']) : '<code>null</code>';
$info['attogram_username'] = isset($_SESSION['attogram_username']) ? htmlentities($_SESSION['attogram_username']) : '<code>null</code>';
$info['attogram_level'] = isset($_SESSION['attogram_level']) ? htmlentities($_SESSION['attogram_level']) : '<code>null</code>';
$info['attogram_email'] = isset($_SESSION['attogram_email']) ? htmlentities($_SESSION['attogram_email']) : '<code>null</code>';

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


function list_actions( $actions ) {
  $r = '';
  foreach( array_keys($actions) as $a ) {
    $r .= '<li><a href="../' . $a . '/"><strong>' . $a . '</strong></a>'
      . ' - file:<strong>' . $actions[$a]['file'] . '</strong>'
      . ' - parser:<strong>' . $actions[$a]['parser'] . '</strong></li>';
  }
  return $r;
  return '<pre>' . print_r($actions,1) . '</pre>';

}
