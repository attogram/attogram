<?php // Attogram Framework - Site Info v0.1.2

namespace Attogram;

$title = 'Information';
$this->page_header($title);

$info = array();

$info['<a name="attogram"></a><h3><span class="glyphicon glyphicon-flash"></span> <em>Attogram:</em></h3>'] = '';
$info['Attogram Version'] = self::ATTOGRAM_VERSION;
$info['Attogram Directory'] = info_dir($this->attogram_directory);
$info['PHP Version'] = phpversion();
$info['Server Software'] = $this->request->server->get('SERVER_SOFTWARE');
$info['debug'] = ( $this->debug ? 'true' : '<code>false</code>' );
$info['project_github'] = '<a href="' . $this->project_github . '">' . $this->project_github . '</a>';
$info['project_packagist'] = '<a href="' . $this->project_packagist . '">' . $this->project_packagist . '</a>';

$info['<a name="site"></a><h3><span class="glyphicon glyphicon-home"></span> <em>Site:</em></h3>'] = '';
$info['site_name'] = $this->site_name;
$info['site_url'] = '<a href="' . $this->get_site_url() . '">' . $this->get_site_url() . '</a>';
$info['path'] = ( $this->path ? $this->path : '<code>Top Level</code>' );
//$info['pathInfo'] = $this->pathInfo;
//$info['requestUri'] = $this->requestUri;
//$info['uri'] = implode($this->uri,',');
//$info['depth'] =  info_array($this->depth,$keyed=1);
//$info['force_slash_exceptions'] =  info_array($this->force_slash_exceptions);
$robotstxt = $this->get_site_url() . '/robots.txt';
$info['robots.txt'] = '<a href="' . $robotstxt . '">' . $robotstxt . '</a>';
$sitemapxml = $this->get_site_url() . '/sitemap.xml';
$info['sitemap.xml'] = '<a href="' . $sitemapxml . '">' . $sitemapxml . '</a>';

$info['admins'] = info_array($this->admins);

$info['<a name="actions"></a><h3><span class="glyphicon glyphicon-play"></span> <em>Actions:</em></h3>'] = '';
$info['actions'] = info_actions($this->actions, $this->depth, $this->force_slash_exceptions);
$info['admin_actions'] = info_actions($this->admin_actions, $this->depth, $this->force_slash_exceptions);

$info['<a name="directories"></a><h3><span class="glyphicon glyphicon-folder-open"></span> <em>Directories:</em></h3>'] = '';
$info['attogram_directory'] = info_dir($this->attogram_directory);
$info['modules_dir'] = info_dir($this->modules_dir);
$info['templates_dir'] = info_dir($this->templates_dir);
$info['skip_files'] = info_array($this->skip_files);

$info['<a name="files"></a><h3><span class="glyphicon glyphicon-file"></span> <em>Files:</em></h3>'] = '';
//$info['action'] = info_file($this->action);
$info['fof'] = info_file($this->fof);
$info['header'] = info_file($this->templates_dir . '/header.php');
$info['nav_bar'] = info_file($this->templates_dir . '/navbar.php');
$info['footer'] = info_file($this->templates_dir . '/footer.php');

$info['<a name="objects"></a><h3><span class="glyphicon glyphicon-paperclip"></span> <em>Objects:</em></h3>'] = '';
$info['request'] = info_object($this->request);
$info['log'] = info_object($this->log);
$info['db'] = info_object($this->db);

$info['<a name="database"></a><h3><span class="glyphicon glyphicon-hdd"></span> <em>Database:</em></h3>'] = '';
$info['db_name'] = info_file($this->db_name);
$info['database_size'] = (file_exists($this->db_name) ? filesize($this->db_name) : '<code>null</code>') . ' bytes';

$info['<a name="user"></a><h3><span class="glyphicon glyphicon-user"></span> <em>User:</em></h3>'] = '';
$info['# session attributes'] = sizeof($_SESSION);
$info['session attributes'] = info_array( $_SESSION, $key=1 );

print '
<div class="container">
 <h1><span class="glyphicon glyphicon-info-sign"></span> ' . $title . '</h1>
 <p>
 <a href="#attogram">attogram</a> -
 <a href="#site">site</a> -
 <a href="#actions">actions</a> -
 <a href="#directories">directories</a> -
 <a href="#files">files</a> -
 <a href="#objects">objects</a> -
 <a href="#database">database</a> -
 <a href="#user">user</a>
 </p>
 <table class="table table-condensed">
';

foreach( $info as $name => $value ) {
  print '<tr><td>' . $name . '</td><td>' . $value . '</td></tr>';
}
print '</table>';
print '</div>';

$this->page_footer();


// Helper functions
function info_array($array, $keyed=false) {
  if( !is_array($array) )  { return '<code>ERROR</code>'; }
  if( !$array ) {
    return '<code>null</code>';
  }
  if( !$keyed ) {
    return '<ul class="list-group"><li class="list-group-item">' . implode($array, '</li><li class="list-group-item">') . '</li></ul>';
  }
  $r = '';
  foreach( $array as $name=>$value ) {
    $r .= '<li class="list-group-item"><strong>' . $name .'</strong> = <code>' . print_r($value,1) . '</code></li>';
  }
  return '<ul class="list-group">' . $r . '</ul>';
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

function info_actions( $actions, $depths=array(), $endslashs=array() ) {
  $r = '';
  if( !isset($depths['']) ) {
    $depths[''] = '<code>ERROR</code>';  // homepage
  }
  if( !isset($depths['*']) ) {
    $depths['*'] = '<code>ERROR</code>'; // default
  }

  foreach( array_keys($actions) as $a ) {
    if( isset($depths[$a]) ) {
        $depth = $depths[$a] . ' OVERRIDE ';
    } else {
      if( $a == 'home') {
        $depth = $depths[''] . ' <code>home page</code>';
      } else {
        $depth = $depths['*'] . ' <code>default depth</code>'; // default
      }
    }
    if( isset($endslashs[$a]) ) {
      $endslash = '<code>Remove Slash at end</code>';
    } else {
      $endslash = '<code>Force Slash at end</code>';
    }
    $r .= '<li class="list-group-item"><a href="../' . $a . '/"><strong>' . $a . '</strong></a>'
      . '<br /> - file: <strong>' . info_file($actions[$a]['file']) . '</strong>'
      . '<br /> - parser: <strong>' . $actions[$a]['parser'] . '</strong>'
      . '<br /> - depth: <strong>'. $depth . '</strong>'
      . '<br /> - slash: <strong>'. $endslash . '</strong>'
      . '</li>';
  }
  return '<ul class="list-group">' . $r . '</ul>';
}
