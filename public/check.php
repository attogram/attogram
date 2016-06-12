<?php // Attogram Framework - Check Script v0.0.1

$c = new attogram_check();
$c->check();



////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
class attogram_check {

  function __construct() {
    $this->pass = '<span class="icon-s"><strong>💚 Pass</strong></span>'; // ✔ ☑  🆗 💚 😊
    $this->fail = '<span class="icon-s"><strong>🔴 Fail</strong></span>'; // ✖ ❌ ❎ ❕ ❗ 🔵 🔴 💔 😢 🙀 😿  🍅 💩
    $this->unknown = '<span class="icon-s"><strong>🔳 ????</strong></span>'; // 💢 ❓ ❔ 😥 😴 ⭕ 🔳
    $this->divider = '<hr />';
    $this->spacer = ' &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ';
  }

  function check() {
    $this->page_header();
    print '<h1><span class="icon">🔬</span> Attogram Framework Check</h1>'
    . '<p><span class="icon">👉</span> <strong>' . __DIR__ . '</strong>'
    . $this->spacer . '<a href=""><span class="icon">🔃</span></a>'
    . $this->spacer . '<span class="icon">🕑</span> ' . gmdate('Y-m-d H:i:s') . ' UTC'
    . $this->spacer . '<a href="./"><span class="icon">🏠</span></a></p>';
    print $this->divider;
    $this->check_index();
    print $this->divider;
    $this->check_htaccess();
    print $this->divider;
    $this->check_php();
    print $this->divider;
    $this->check_apache();
    print $this->divider . '<p><span class="icon">🏁</span> Attogram Framework Check completed</p>';
    $this->page_footer();
  }

  function check_index() {
    $f = './index.php';

    file_exists($f) ? $fe = 'pass' : $fe = 'fail';
    print '<pre class="' . $fe . '">' . $this->{$fe} . ' 1.0 - <strong>' . $f . '</strong> exists</pre>';

    is_readable($f) ? $r = 'pass' : $r = 'fail';
    print '<pre class="' . $r . '">' . $this->{$r} . ' 1.1 - <strong>' . $f . '</strong> is readable</pre>';

    if( $fe == 'pass' ) {
      $file = new SplFileObject($f);
      $r = $val = 'fail';
      foreach( $file as $linenum=>$val ) {
        if( preg_match('/new\s+guru_meditation_loader/', $val) ) {
          $r = 'pass';
          break;
        }
      }
    } else {
      $r = 'fail';
      $val = 'Not Found';
    }
    print '<pre class="' . $r . '">' . $this->{$r} . ' 1.2 - <strong>' . $f . '</strong> is Project Loader (found: new guru_meditation_loader)</pre>';
  }

  function check_htaccess() {
    $f = './.htaccess';

    file_exists($f) ? $r = 'pass' : $r = 'fail';
    print '<pre class="' . $r . '">' . $this->{$r} . ' 2.0 - <strong>' . $f . '</strong> exists</pre>';

    is_readable($f) ? $fr = 'pass' : $fr = 'fail';
    print '<pre class="' . $fr . '">' . $this->{$fr} . ' 2.1 - <strong>' . $f . '</strong> is readable</pre>';

    $count = $found = array();
    $count['FallbackResource'] = $count['ErrorDocument 404'] = $count['ErrorDocument 403'] = $count['DirectoryIndex'] = 0;
    $found['FallbackResource'] = $found['ErrorDocument 404'] = $found['ErrorDocument 403'] = $found['DirectoryIndex'] = '?';
    $r22 = $r23 = $r24 = $r25 = $r26 = $r27 = $r28 = $r29 = 'fail';

    $good_uri = $this->good_uri();

    if( $fr == 'pass' ) {
      $file = new SplFileObject($f);

      foreach( $file as $linenum=>$val ) {
        if( preg_match('/^#/', $val) ) { continue; } // # comments
        if( preg_match('/FallbackResource/', $val) ) {
          $count['FallbackResource']++;
          if( preg_match('/FallbackResource\s+(.*)\s+$/', $val, $match) ) {
            $found['FallbackResource'] = trim($match[1]);
            if( trim($match[1]) == $good_uri) { $r26 = 'pass'; }
          }
          continue;
        }
        if( preg_match('/ErrorDocument\s+403/', $val) ) {
          $count['ErrorDocument 403']++;
          if( preg_match('/ErrorDocument\s+403\s+(.*)\s+$/', $val, $match) ) {
            $found['ErrorDocument 403'] = trim($match[1]);
            if( trim($match[1]) == $good_uri) { $r27 = 'pass'; }
          }
          continue;
        }
        if( preg_match('/ErrorDocument\s+404/', $val) ) {
          $count['ErrorDocument 404']++;
          if( preg_match('/ErrorDocument\s+404\s+(.*)\s+$/', $val, $match) ) {
            $found['ErrorDocument 404'] = trim($match[1]);
            if( trim($match[1]) == $good_uri) { $r28 = 'pass'; }
          }
          continue;
        }
        if( preg_match('/DirectoryIndex\s+/', $val) ) {
          $count['DirectoryIndex']++;
          if( preg_match('/DirectoryIndex\s+(.*)\s+$/', $val, $match) ) {
            $found['DirectoryIndex'] = trim($match[1]);
            if( trim($match[1]) == 'index.php') { $r29 = 'pass'; }
          }
          continue;
        }
      } // end foreach line of file
      if( isset($count['FallbackResource']) && $count['FallbackResource'] == 1 ) {
        $r22 = 'pass';
      }
      if( isset($count['ErrorDocument 403']) && $count['ErrorDocument 403'] == 1 ) {
        $r23 = 'pass';
      }
      if( isset($count['ErrorDocument 404']) && $count['ErrorDocument 404'] == 1 ) {
        $r24 = 'pass';
      }
      if( isset($count['DirectoryIndex']) && $count['DirectoryIndex'] == 1 ) {
        $r25 = 'pass';
      }
    } // end file check

    $r = 'unknown';
    print '<pre class="' . $r22 . '">' . $this->{$r22} . ' 2.2 - <strong>FallbackResource</strong> found once (found: ' . $count['FallbackResource'] . ')</pre>';
    print '<pre class="' . $r23.  '">' . $this->{$r23} . ' 2.3 - <strong>ErrorDocument 403</strong> found once (found: ' . $count['ErrorDocument 403'] . ')</pre>';
    print '<pre class="' . $r24 . '">' . $this->{$r24} . ' 2.4 - <strong>ErrorDocument 404</strong> found once (found: ' . $count['ErrorDocument 404'] . ')</pre>';
    print '<pre class="' . $r25 . '">' . $this->{$r25} . ' 2.5 - <strong>DirectoryIndex</strong> found once (found: ' . $count['DirectoryIndex'] . ')</pre>';
    print '<pre class="' . $r26 . '">' . $this->{$r26} . ' 2.6 - <strong>FallbackResource ' . $good_uri . '</strong> (found: ' . $found['FallbackResource'] . ')</pre>';
    print '<pre class="' . $r27 . '">' . $this->{$r27} . ' 2.7 - <strong>ErrorDocument 403 ' . $good_uri . '</strong> (found: ' . $found['ErrorDocument 403'] . ')</pre>';
    print '<pre class="' . $r28 . '">' . $this->{$r28} . ' 2.8 - <strong>ErrorDocument 404 ' . $good_uri . '</strong> (found: ' . $found['ErrorDocument 404'] . ')</pre>';
    print '<pre class="' . $r29 . '">' . $this->{$r29} . ' 2.9 - <strong>DirectoryIndex index.php</strong> (found: ' . $found['DirectoryIndex'] . ')</pre>';
  }

  function check_php() {

    $v = phpversion();
    (version_compare( $v, '5.3.3' ) >= 0) ? $r = 'pass' : $r = 'fail';
    print '<pre class="' . $r . '">' . $this->{$r} . ' 3.0 - <strong>PHP</strong> Version is >= 5.3.3 (current is ' . $v . ')</pre>';

    class_exists('PDO') ? $r = 'pass' : $r = 'fail';
    print '<pre class="' . $r . '">' . $this->{$r} . ' 3.1 - <strong>PDO</strong> extension enabled</pre>';

    if( class_exists('PDO') ) {
      in_array('sqlite', \PDO::getAvailableDrivers()) ? $r = 'pass' : $r = 'fail';
    } else {
      $r = 'fail';
    }
    print '<pre class="' . $r . '">' . $this->{$r} . ' 3.2 - <strong>PDO sqlite</strong> driver installed</pre>';
  }

  function check_apache() {
    isset($_SERVER['SERVER_SOFTWARE']) ? $as = $_SERVER['SERVER_SOFTWARE'] : $as = array();
    $asr = explode(' ', $as);
    $av = $asr[0];
    $avr = explode('/', $av);
    $apache = $avr[0];
    $apache_version = $avr[1];
    ( $apache == 'Apache' ) ? $r = 'pass' : $r = 'fail';
    print '<pre class="' . $r . '">' . $this->{$r} . ' 4.0 - <strong>Apache Server</strong> in use</pre>';

    (version_compare( $apache_version, '2.2.16' ) >= 0) ? $r = 'pass' : $r = 'fail';
    print '<pre class="' . $r . '">' . $this->{$r} . ' 4.1 - <strong>Apache version</strong> is >= 2.2.16 (current is ' . $apache_version . ')</pre>';

    $apache_finder = array(
      '/etc/apache2/apache2.conf',
      '/etc/apache2/httpd.conf',
      '/etc/apache2/httpd2.conf',
      '/etc/httpd/conf/httpd.conf',
      '/etc/httpd/httpd.conf',
      '/Program Files/Apache Software Foundation/Apache2.2/conf/httpd.conf',
      '/usr/local/apache2/conf/httpd.conf',
      '/usr/local/etc/apache22/httpd.conf',
      '/usr/local/etc/apache2/httpd.conf',
      '/usr/pkg/etc/httpd/httpd.conf',
      '/var/www/conf/httpd.conf',
      '/xampp/apache/conf/httpd.conf',
    );
    $apache_found = array();
    foreach( $apache_finder as $af ) {
      if( is_file($af) && is_readable($af) ) {
        $apache_found[] = $af;
      }
      if( sizeof($apache_found) == 1 ) {
        $r42 = 'pass';
        $r42_found = $apache_found[0];
        $r43 = $this->apache_conf_examine( $apache_found[0] );

      } elseif( sizeof($apache_found) > 1 ) {
        $r42 = $r43 = 'fail';
        $r42_found = 'Error: ' . sizeof($apache_found) . ' conf files found';
      } else {
        $r42 = $r43 = 'fail';
        $r42_found = 'Not Found';
      }
    }

    print '<pre class="' . $r42 . '">' . $this->{$r42} . ' 4.2 - <strong>Apache conf</strong> exists (' . $r42_found . ')</pre>';
    print '<pre class="' . $r43 . '">' . $this->{$r43} . ' 4.3 - <strong>Apache conf</strong> has "AllowOveride all" (Directory section = '
    . (isset($this->apache_override_dir) ? $this->apache_override_dir : '?') . ')</pre>';



  }

  function good_uri() {
    $ep = '/index.php';
    if( !isset($_SERVER['DOCUMENT_ROOT']) ) {
      return $ep;
    }
    $docroot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
    return str_replace( $docroot, '', str_replace('\\', '/', __DIR__) ) . $ep;
  }

  function apache_conf_examine( $c ) {
    $this_dir = '';
    $ao = array();
    $file = new SplFileObject($c);
    foreach( $file as $linenum=>$val ) {
      $val = trim($val);
      if( !$val || $val[0] == '#' ) { continue; }
      //print '<pre>LINE' . $linenum . ': |' . htmlentities($val) . '|</pre>';
      if( preg_match('/^<Directory [\'"](.*)[\'"]>/', $val, $match) ) {
        $this_dir = $match[1];
        //$this_dir = str_replace('\\','/', $this_dir);
        //print '<pre>**** DIR=' . $this_dir . '</pre>';
      }
      if( preg_match('/^AllowOverride /', $val) ) {
        //print '<pre>**** DIR=' . $this_dir .'  AllowOverride=' . htmlentities($val)  . '</pre>';
        $ao[$this_dir] = $val;
      }
      if( preg_match('/^DirectoryIndex /', $val) ) {
        //print '<pre>**** DirectoryIndex  LINE: ' . htmlentities($val)  . '</pre>';
      }
    }

    $home_dir = str_replace('\\','/', __DIR__);
    if( !$ao ) { return 'fail'; }
    foreach( $ao as $d=>$a ) {
      //print "<pre>home_dir=$home_dir   d=$d    a=$a</pre>";
      if( $d && preg_match('~' . $d . '~', $home_dir) && $a == 'AllowOverride All') {
        //print "<pre>MATCHED home_dir=$home_dir  d=$d  a=$a</pre>";
        $this->apache_override_dir = $d;
        return 'pass';
      }
    }
    return 'fail';
  }

  function page_header() {
    ?><!DOCTYPE html>
    <html lang="en"><head><meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
    body { margin:0px 40px 0px 40px; font-size: 18px; font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; }
    a { text-decoration:none; }
    h1, p, pre { padding:0; margin:0; }
    hr { height:1px; border:0; background:#E0E0E0; }
    .pass { color: #006600; }
    .fail { color: #FF0000; }
    .unknown { color: #778899; }
    .icon {    font-size:30px; vertical-align:middle; padding:0px; margin:0px; }
    .icon-s { font-size: 18px; vertical-align:middle; padding:0px; margin:0px; }
    </style>
    <title>Attogram Framework Check</title></head><body><?php
  }

  function page_footer() {
    ?></body></html><?php
  }

} // end class attogram_check
