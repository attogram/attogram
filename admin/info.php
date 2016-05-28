<?php
// Attogram - Site Info v0.0.1

namespace Attogram;

global $debug;

$this->page_header('Site Info');

$info = array();

$info['<h3><span class="glyphicon glyphicon-info-sign"></span> <em>Attogram:</em></h3>'] = '';
$info['ATTOGRAM_VERSION'] = ATTOGRAM_VERSION;
$info['site_name'] = $this->site_name;
$info['site_url'] = '<a href="' . $this->get_site_url() . '">' . $this->get_site_url() . '</a>';
$info['path'] = ( $this->path ? $this->path : '<code>Top Level</code>' );
$info['pathInfo'] = $this->pathInfo;
$info['requestUri'] = $this->requestUri;
$info['uri'] = implode($this->uri,',');
$info['debug'] = ( $debug ? 'TRUE' : '<code>FALSE</code>' );
$info['depth'] =  info_array($this->depth,$keyed=1);
$info['force_slash_exceptions'] =  info_array($this->force_slash_exceptions);
$info['admins'] = info_array($this->admins);

$info['<h3><span class="glyphicon glyphicon-play"></span> <em>Actions:</em></h3>'] = '';
$info['actions'] = info_actions($this->actions);
$info['admin_actions'] = info_actions($this->admin_actions);

$info['<h3><span class="glyphicon glyphicon-folder-open"></span> <em>Directories:</em></h3>'] = '';
$info['attogram_directory'] = info_dir($this->attogram_directory);
$info['actions_dir'] = info_dir($this->actions_dir);
$info['admin_dir'] = info_dir($this->admin_dir);
$info['templates_dir '] = info_dir($this->templates_dir);
$info['configs_dir'] = info_dir($this->configs_dir);
$info['tables_dir'] = info_dir($this->tables_dir);
$info['functions_dir'] = info_dir($this->functions_dir);
$info['skip_files'] = info_array( $this->skip_files);

$info['<h3><span class="glyphicon glyphicon-file"></span> <em>Files:</em></h3>'] = '';
$info['action'] = info_file($this->action);
$info['autoloader'] = info_file($this->autoloader);
$info['fof'] = info_file($this->fof);
$info['header'] = info_file($this->templates_dir . '/header.php');
$info['nav_bar'] = info_file($this->templates_dir . '/navbar.php');
$info['footer'] = info_file($this->templates_dir . '/footer.php');

$info['<h3><span class="glyphicon glyphicon-paperclip"></span> <em>Objects:</em></h3>'] = '';
$info['request'] = info_object($this->request);
$info['log'] = info_object($this->log);

$info['<h3><span class="glyphicon glyphicon-hdd"></span> <em>Database:</em></h3>'] = '';
$info['sqlite_database'] = info_object($this->sqlite_database);
$info['db_name'] = info_file($this->db_name);
$info['database_size'] = (file_exists($this->db_name) ? filesize($this->db_name) : '<code>null</code>') . ' bytes';

$info['<h3><span class="glyphicon glyphicon-user"></span> <em>User:</em></h3>'] = '';
$info['attogram_id'] = isset($_SESSION['attogram_id']) ? htmlentities($_SESSION['attogram_id']) : '<code>null</code>';
$info['attogram_username'] = isset($_SESSION['attogram_username']) ? htmlentities($_SESSION['attogram_username']) : '<code>null</code>';
$info['attogram_level'] = isset($_SESSION['attogram_level']) ? htmlentities($_SESSION['attogram_level']) : '<code>null</code>';
$info['attogram_email'] = isset($_SESSION['attogram_email']) ? htmlentities($_SESSION['attogram_email']) : '<code>null</code>';


print '
<div class="container">
 <h1>Site Info</h1>
 <table class="table">
';

foreach( $info as $name => $value ) {
  print '<tr><td>' . $name . '</td><td>' . $value . '</td></tr>';
}
print '</table>';
print '</div>';

$this->page_footer();


// Helper functions
function info_array($array, $keyed=FALSE) {
  if( !is_array($array) )  { return '<code>ERROR</code>'; }
  if( !$keyed ) {
    return '<li>' . implode($array, '</li><li>') . '</li>';
  }
  $r = '';
  foreach( $array as $name=>$value ) {
    $r .= '<li><strong>' . $name .'</strong> = <code>' . $value . '</code></li>';
  }
  return $r;
}

function info_object($obj) {
  if( is_object($obj) ) {
    $gn = 'ok'; $gt = 'success'; $n = get_class($obj);
  } else {
    $gn = 'remove'; $gt = 'danger'; $n = '<code>?</code>';
  }
  return '<span class="glyphicon glyphicon-' . $gn . ' text-' . $gt . '" aria-hidden="true"></span> ' . $n;
}

function info_file($file) {
  if( is_file($file) && is_readable($file) ) { $gn = 'ok'; $gt = 'success'; } else { $gn = 'remove'; $gt = 'danger'; }
  return '<span class="glyphicon glyphicon-' . $gn . ' text-' . $gt . '" aria-hidden="true"></span> ' . $file;
}

function info_dir($dir) {
  if( is_dir($dir) ) { $gn = 'ok'; $gt = 'success'; } else { $gn = 'remove'; $gt = 'danger'; }
  return '<span class="glyphicon glyphicon-' . $gn . ' text-' . $gt . '" aria-hidden="true"></span> ' . $dir;
}

function info_actions( $actions ) {
  $r = '';
  foreach( array_keys($actions) as $a ) {
    $r .= '<li><a href="../' . $a . '/"><strong>' . $a . '</strong></a>'
      . ' - file:<strong>' . $actions[$a]['file'] . '</strong>'
      . ' - parser:<strong>' . $actions[$a]['parser'] . '</strong></li>';
  }
  return $r;
}
