<?php
// Attogram Framework - Check Script v0.1.10

namespace Attogram;

$c = new AttogramCheck();
$c->check();

////////////////////////////////////////////////////////////////////////////////
class AttogramCheck
{
    public $pass;
    public $fail;
    public $unknown;
    public $divider;
    public $spacer;
    public $apacheOverrideDir;

    public function __construct()
    {
        $this->pass = '<span class="icon-s"><strong>💚 Pass</strong></span>';
        $this->fail = '<span class="icon-s"><strong>🔴 Fail</strong></span>';
        $this->unknown = '<span class="icon-s"><strong>🔳 Skip</strong></span>';
        $this->divider = '<hr />';
        $this->spacer = ' &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ';
    }

    public function check()
    {
        $this->pageHeader();
        echo '<h1><span class="icon">🔬</span> Attogram Framework Check</h1>'
        .'<p><span class="icon">👉</span> <strong>'.__DIR__.'</strong>'
        .$this->spacer.'<a href="'.@$_SERVER['SCRIPT_NAME'].'"><span class="icon">🔃</span></a>'
        .$this->spacer.'<span class="icon">🕑</span> '.gmdate('Y-m-d H:i:s').' UTC'
        .$this->spacer.'<a href="./"><span class="icon">🏠</span></a></p>';
        echo $this->divider;
        $this->checkIndex();
        echo $this->divider;
        $this->checkHtaccess();
        echo $this->divider;
        $this->checkPhp();
        echo $this->divider;
        $this->checkApache();
        echo $this->divider.'<p><span class="icon">🏁</span> Attogram Framework Check completed</p>';
        $this->pageFooter();
    }

    public function checkIndex()
    {
        $indexFile = './index.php';
        file_exists($indexFile) ? $fileexists = 'pass' : $fileexists = 'fail';
        echo '<pre class="'.$fileexists.'">'.$this->{$fileexists}.' 1.0 - <strong>'.$indexFile.'</strong> exists</pre>';
        is_readable($indexFile) ? $result = 'pass' : $result = 'fail';
        echo '<pre class="'.$result.'">'.$this->{$result}.' 1.1 - <strong>'.$indexFile.'</strong> is readable</pre>';
        $result = 'fail';
        $val = 'Not Found';
        if ($fileexists == 'pass') {
            $file = new SplFileObject($indexFile);
            $result = $val = 'fail';
            foreach ($file as $val) {
                if (preg_match('/new\s+GuruMeditationLoader/', $val)) {
                    $result = 'pass';
                    break;
                }
            }
        }
        echo '<pre class="'.$result.'">'.$this->{$result}.' 1.2 - <strong>'.$indexFile.'</strong> is Project Loader (found: new GuruMeditationLoader)</pre>';
    }

    public function checkHtaccess()
    {
        $htaccessFile = './.htaccess';
        file_exists($htaccessFile) ? $result = 'pass' : $result = 'fail';
        echo '<pre class="'.$result.'">'.$this->{$result}.' 2.0 - <strong>'
            .$htaccessFile.'</strong> exists</pre>';
        is_readable($htaccessFile) ? $filereadable = 'pass' : $filereadable = 'fail';
        echo '<pre class="'.$filereadable.'">'.$this->{$filereadable}
            .' 2.1 - <strong>'.$htaccessFile.'</strong> is readable</pre>';
        $count = $found = array();
        $count['FallbackResource'] = $count['ErrorDocument 404']
            = $count['ErrorDocument 403'] = $count['DirectoryIndex'] = 0;
        $found['FallbackResource'] = $found['ErrorDocument 404']
            = $found['ErrorDocument 403'] = $found['DirectoryIndex'] = '?';
        $result22 = $result23 = $result24 = $result25 = $result26
            = $result27 = $result28 = $result29 = 'fail';
        $goodUri = $this->goodUri();
        if ($filereadable == 'pass') {
            $file = new SplFileObject($htaccessFile);
            foreach ($file as $val) {
                if (preg_match('/^#/', $val)) {
                    continue; // # comments
                }
                if (preg_match('/FallbackResource/', $val)) {
                    ++$count['FallbackResource'];
                    if (preg_match('/FallbackResource\s+(.*)\s+$/', $val, $match)) {
                        $found['FallbackResource'] = trim($match[1]);
                        if (trim($match[1]) == $goodUri) {
                            $result26 = 'pass';
                        }
                    }
                    continue;
                }
                if (preg_match('/ErrorDocument\s+403/', $val)) {
                    ++$count['ErrorDocument 403'];
                    if (preg_match('/ErrorDocument\s+403\s+(.*)\s+$/', $val, $match)) {
                        $found['ErrorDocument 403'] = trim($match[1]);
                        if (trim($match[1]) == $goodUri) {
                            $result27 = 'pass';
                        }
                    }
                    continue;
                }
                if (preg_match('/ErrorDocument\s+404/', $val)) {
                    ++$count['ErrorDocument 404'];
                    if (preg_match('/ErrorDocument\s+404\s+(.*)\s+$/', $val, $match)) {
                        $found['ErrorDocument 404'] = trim($match[1]);
                        if (trim($match[1]) == $goodUri) {
                            $result28 = 'pass';
                        }
                    }
                    continue;
                }
                if (preg_match('/DirectoryIndex\s+/', $val)) {
                    ++$count['DirectoryIndex'];
                    if (preg_match('/DirectoryIndex\s+(.*)\s+$/', $val, $match)) {
                        $found['DirectoryIndex'] = trim($match[1]);
                        if (trim($match[1]) == 'index.php') {
                            $result29 = 'pass';
                        }
                    }
                    continue;
                }
            } // end foreach line of file
            if (isset($count['FallbackResource']) && $count['FallbackResource'] == 1) {
                $result22 = 'pass';
            }
            if (isset($count['ErrorDocument 403']) && $count['ErrorDocument 403'] == 1) {
                $result23 = 'pass';
            }
            if (isset($count['ErrorDocument 404']) && $count['ErrorDocument 404'] == 1) {
                $result24 = 'pass';
            }
            if (isset($count['DirectoryIndex']) && $count['DirectoryIndex'] == 1) {
                $result25 = 'pass';
            }
        } // end file check
        $result = 'unknown';
        echo '<pre class="'.$result22.'">'.$this->{$result22}
            .' 2.2 - <strong>FallbackResource</strong> found once (found: '
            .$count['FallbackResource'].')</pre>';
        echo '<pre class="'.$result23.'">'.$this->{$result23}
            .' 2.3 - <strong>ErrorDocument 403</strong> found once (found: '
            .$count['ErrorDocument 403'].')</pre>';
        echo '<pre class="'.$result24.'">'.$this->{$result24}
            .' 2.4 - <strong>ErrorDocument 404</strong> found once (found: '
            .$count['ErrorDocument 404'].')</pre>';
        echo '<pre class="'.$result25.'">'.$this->{$result25}
            .' 2.5 - <strong>DirectoryIndex</strong> found once (found: '
            .$count['DirectoryIndex'].')</pre>';
        echo '<pre class="'.$result26.'">'.$this->{$result26}
            .' 2.6 - <strong>FallbackResource '.$goodUri.'</strong> (found: '
            .$found['FallbackResource'].')</pre>';
        echo '<pre class="'.$result27.'">'.$this->{$result27}
            .' 2.7 - <strong>ErrorDocument 403 '.$goodUri.'</strong> (found: '
            .$found['ErrorDocument 403'].')</pre>';
        echo '<pre class="'.$result28.'">'.$this->{$result28}
            .' 2.8 - <strong>ErrorDocument 404 '.$goodUri.'</strong> (found: '
            .$found['ErrorDocument 404'].')</pre>';
        echo '<pre class="'.$result29.'">'.$this->{$result29}
            .' 2.9 - <strong>DirectoryIndex index.php</strong> (found: '
            .$found['DirectoryIndex'].')</pre>';
    }

    public function checkPhp()
    {
        $version = phpversion();
        (version_compare($version, '5.3.3') >= 0)
            ? $result = 'pass' : $result = 'fail';
        echo '<pre class="'.$result.'">'.$this->{$result}
            .' 3.0 - <strong>PHP</strong> Version is >= 5.3.3 (current is '
            .$version.')</pre>';
        class_exists('PDO') ? $result = 'pass' : $result = 'fail';
        echo '<pre class="'.$result.'">'.$this->{$result}
            .' 3.1 - <strong>PDO</strong> extension enabled</pre>';
        $result = 'fail';
        if (class_exists('PDO')) {
            in_array('sqlite', \PDO::getAvailableDrivers())
                ? $result = 'pass' : $result = 'fail';
        }
        echo '<pre class="'.$result.'">'.$this->{$result}
            .' 3.2 - <strong>PDO sqlite</strong> driver installed</pre>';
    }

    public function checkApache()
    {
        isset($_SERVER['SERVER_SOFTWARE'])
            ? $serverSoftware = $_SERVER['SERVER_SOFTWARE'] : $serverSoftware = '';
        $serverSoftwareArray = explode(' ', $serverSoftware);
        $serverNameString = $serverSoftwareArray[0];
        $serverNameArray = explode('/', $serverNameString);
        $apache = $serverNameArray[0];
        $apacheVersion = $serverNameArray[1];
        ($apache == 'Apache') ? $result = 'pass' : $result = 'fail';
        echo '<pre class="'.$result.'">'.$this->{$result}
            .' 4.0 - <strong>Apache Server</strong> in use</pre>';
        (version_compare($apacheVersion, '2.2.16') >= 0)
            ? $result = 'pass' : $result = 'fail';
        echo '<pre class="'.$result.'">'.$this->{$result}
            .' 4.1 - <strong>Apache version</strong> is >= 2.2.16 (current is '
            .$apacheVersion.')</pre>';
        $apacheFinder = array(
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
        $apacheFound = array();
        foreach ($apacheFinder as $afc) {
            if (is_file($afc) && is_readable($afc)) {
                $apacheFound[] = $afc;
                break; // only do first found...
            }
        }
        $result42 = $result43 = 'fail';
        $result42Found = 'Not Found';
        if (sizeof($apacheFound) == 1) {
            $result42 = 'pass';
            $result42Found = $apacheFound[0];
            $result43 = $this->apacheConfExamine($apacheFound[0]);
        }
        echo '<pre class="'.$result42.'">'.$this->{$result42}
            .' 4.2 - <strong>Apache conf</strong> exists ('.$result42Found.')</pre>';
        echo '<pre class="'.$result43.'">'.$this->{$result43}
            .' 4.3 - <strong>Apache conf</strong> has "AllowOveride all" (Directory section = '
            .(isset($this->apacheOverrideDir) ? $this->apacheOverrideDir : '?')
            .')</pre>';
    }

    public function goodUri()
    {
        $indexFile = '/index.php';
        if (!isset($_SERVER['DOCUMENT_ROOT'])) {
            return $indexFile;
        }
        $docroot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
        return str_replace(
            $docroot,
            '',
            str_replace('\\', '/', __DIR__)
        ).$indexFile;
    }

    public function apacheConfExamine($conf)
    {
        $thisDir = '';
        $allowOverride = array();
        $file = new SplFileObject($conf);
        foreach ($file as $val) {
            $val = trim($val);
            if (!$val || $val[0] == '#') {
                continue;
            }
            if (preg_match('/^<Directory [\'"](.*)[\'"]>/', $val, $match)) {
                $thisDir = $match[1];
            }
            if (preg_match('/^AllowOverride /', $val)) {
                $allowOverride[$thisDir] = $val;
            }
        //if( preg_match('/^DirectoryIndex /', $val) ) {
        //}
        }
        $homeDir = str_replace('\\', '/', __DIR__);
        if (!$allowOverride) {
            return 'fail';
        }
        foreach ($allowOverride as $dir => $allowCheck) {
            if ($dir && preg_match('~'.$dir.'~', $homeDir) && $allowCheck == 'AllowOverride All') {
                $this->apacheOverrideDir = $dir;
                return 'pass';
            }
        }

        return 'fail';
    }

    public function pageHeader()
    {
        echo '<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body { margin:0 40px 0 40px; font-size:18px; font-family:"Helvetica Neue",Helvetica,Arial,sans-serif; }
a { text-decoration:none; }
h1, p, pre { padding:0; margin:0; }
hr { height:1px; border:0; background:#E0E0E0; }
.pass { color:#006600; }
.fail { color:#FF0000; }
.unknown { color:#778899; }
.icon { font-size:30px; vertical-align:middle; padding:0; margin:0; }
.icon-s { font-size:18px; vertical-align:middle; padding:0; margin:0; }
</style>
<title>Attogram Framework Check</title></head><body>';
    }

    public function pageFooter()
    {
        echo '</body></html>';
    }
} // end class AttogramCheck
