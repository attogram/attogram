<?php // Attogram Framework - Check Script v0.0.1

$c = new attogram_check();
$c->page_header();
$c->check();
$c->page_footer();


////////////////////////////////////////////////////////////////////////////////

//
////////////////////////////////////////////////////////////////////////////////
class attogram_check {

  function __construct() {
    $this->pass = '<strong>✔</strong>';
    $this->fail = '<strong>❌</strong>';
    $this->unknown = '<strong>❔</strong>';
    // 💢 🆗 ☑ ✔ ✖ ❌ ❎ ❓ ❔ ❕ ❗ 🔴 🔵 💚
  }
  function check() {
    ?><h1>🔬 Attogram Check Script</h1>
    <p><a href="">🔃 Reload</a> &nbsp; &nbsp; <?php print gmdate('Y-m-d H:i:s'); ?> UTC</p><?php
    print '<hr />';
    $this->check_index();
    print '<hr />';
    $this->check_htaccess();
    print '<hr />';
    $this->check_php();
    print '<hr />';
    $this->check_apache();
    print '<hr />🔬 End of Attogram Check Script';
  }

  function check_index() {
    $f = './index.php';

    file_exists($f) ? $r = 'pass' : $r = 'fail';
    print '<pre class="' . $r . '">' . $this->{$r} . ' - 1.0 - ' . $f . ' file exists</pre>';

    is_readable($f) ? $r = 'pass' : $r = 'fail';
    print '<pre class="' . $r . '">' . $this->{$r} . ' - 1.1 - ' . $f . ' file is readable</pre>';

    $file = new SplFileObject($f);
    $r = $val = 'fail';
    foreach( $file as $linenum=>$val ) {
      if( preg_match('/class\s+guru_meditation_loader/', $val) ) {
        $r = 'pass';
        break;
      }
    }
    print '<pre class="' . $r . '">' . $this->{$r} . ' - 1.2 - ' . $f . ' file is Project Loader (' . trim($val) . ')</pre>';
  }

  function check_htaccess() {
    $f = './.htaccess';

    file_exists($f) ? $r = 'pass' : $r = 'fail';
    print '<pre class="' . $r . '">' . $this->{$r} . ' - 2.0 - ' . $f . ' file exists</pre>';

    is_readable($f) ? $r = 'pass' : $r = 'fail';
    print '<pre class="' . $r . '">' . $this->{$r} . ' - 2.1 - ' . $f . ' file is readable</pre>';

    $r = 'unknown';
    print '<pre class="' . $r . '">' . $this->{$r} . ' - 2.2 - FallbackResource exists</pre>';
    print '<pre class="' . $r . '">' . $this->{$r} . ' - 2.3 - FallbackResource has correct URI</pre>';
    print '<pre class="' . $r . '">' . $this->{$r} . ' - 2.4 - ErrorDocument 404 exists</pre>';
    print '<pre class="' . $r . '">' . $this->{$r} . ' - 2.5 - ErrorDocument 404 has correct URI</pre>';
    print '<pre class="' . $r . '">' . $this->{$r} . ' - 2.6 - ErrorDocument 403 exists</pre>';
    print '<pre class="' . $r . '">' . $this->{$r} . ' - 2.7 - ErrorDocument 403 has correct URI</pre>';
    print '<pre class="' . $r . '">' . $this->{$r} . ' - 2.8 - DirectoryIndex exists</pre>';
    print '<pre class="' . $r . '">' . $this->{$r} . ' - 2.9 - DirectoryIndex has correct file "index.php"</pre>';
  }

  function check_php() {

    $v = phpversion();
    (version_compare( $v, '5.3.3' ) >= 0) ? $r = 'pass' : $r = 'fail';
    print '<pre class="' . $r . '">' . $this->{$r} . ' - 3.0 - PHP Version is >= 5.3.3 (current is ' . $v . ')</pre>';

    class_exists('PDO') ? $r = 'pass' : $r = 'fail';
    print '<pre class="' . $r . '">' . $this->{$r} . ' - 3.1 - PDO extension enabled</pre>';

    if( class_exists('PDO') ) {
      in_array('sqlite', \PDO::getAvailableDrivers()) ? $r = 'pass' : $r = 'fail';
    } else {
      $r = 'fail';
    }
    print '<pre class="' . $r . '">' . $this->{$r} . ' - 3.2 - PDO sqlite driver installed</pre>';
  }

  function check_apache() {

    isset($_SERVER['SERVER_SOFTWARE']) ? $as = $_SERVER['SERVER_SOFTWARE'] : $as = array();
    $asr = explode(' ', $as);
    $av = $asr[0];
    $avr = explode('/', $av);
    $apache = $avr[0];
    $apache_version = $avr[1];
    ( $apache == 'Apache' ) ? $r = 'pass' : $r = 'fail';
    print '<pre class="' . $r . '">' . $this->{$r} . ' - 4.0 - Apache Server in use</pre>';

    (version_compare( $apache_version, '2.2.16' ) >= 0) ? $r = 'pass' : $r = 'fail';
    print '<pre class="' . $r . '">' . $this->{$r} . ' - 4.1 - Apache Version is >= 2.2.16 (current is ' . $apache_version . ')</pre>';

    $r = 'unknown';
    print '<pre class="' . $r . '">' . $this->{$r} . ' - 4.2 - Apache conf has "AllowOveride all" (Unable to find apache conf file)</pre>';
  }

  function page_header() {
    ?><!DOCTYPE html>
    <html lang="en"><head><meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
    body { margin:0px 0px 0px 40px; font-size: 16px; font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; }
    a { text-decoration: none; }
    h1, p, pre { padding:0; margin:0; }
    .pass { color: #006600; }
    .fail { color: #FF0000; }
    .unknown { color: #778899; }
    </style>
    <title>Attogram Check Script</title></head><body><?php
  }

  function page_footer() {
    ?></body></html><?php
  }

} // end class attogram_check
