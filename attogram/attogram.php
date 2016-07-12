<?php
// Attogram Framework - attogram class v0.3.7

namespace Attogram;

/**
 * Attogram Framework.
 *
 * The Attogram Framework provides developers a PHP skeleton starter site with
 * content modules, file-based URL routing, IP-protected backend, user system,
 * integrated SQLite database with web admin, Markdown parser, jQuery and Bootstrap.
 *
 * The Attogram Framework is Dual Licensed under the MIT License (MIT)
 * _or_ the GNU General Public License version 3 (GPL-3.0+), at your choosing.
 *
 * @license (MIT or GPL-3.0+)
 * @copyright 2016 Attogram Framework Developers https://github.com/attogram/attogram
 */
class Attogram
{
    const ATTOGRAM_VERSION = '0.7.5';

    public $startTime;    // (float) microsecond time of awakening
    public $debug;         // (boolean) debug on/off
    public $log;           // (object) Debug Log - PSR-3 Logger object
    public $event;         // (object) Event Log - PSR-3 Logger object
    public $database;      // (object) The Attogram Database Object
    public $request;       // (object) Symfony HttpFoundation Request object
    public $projectRepository;  // (string) URL to Attogram Framework GitHub Project
    public $attogramDirectory;  // (string) path to this installation
    public $modulesDirectory;   // (string) path to the modules directory
    public $templatesDirectory; // (string) path to the templates directory
    public $templates;     // (array) list of templates
    public $siteName;      // (string) The Site Name
    public $depth;         // (array) Allowed depth settings
    public $noEndSlash;    // (array) actions to NOT force slash at end
    public $host;          // (string) Client Hostname
    public $clientIp;      // (string) Client IP Address
    public $pathInfo;      // (string)
    public $requestUri;    // (string)
    public $path;          // (string) Relative URL path to this installation
    public $uri;           // (array) The Current URI
    public $databaseName;  // (string) path + filename of the sqlite database file
    public $actions;       // (array) memory variable for $this->getActions()
    public $action;        // (string) The Current Action name
    public $admins;        // (array) Administrator IP addresses
    public $isAdmin;       // (boolean) memory variable for $this->isAdmin()
    public $adminActions;  // (array) memory variable for $this->getAdminActions()

    /**
     * @param obj  $log      Debug Log - PSR-3 logger object, interface:\Psr\Log\LoggerInterface
     * @param obj  $event    Event Log - PSR-3 logger object, interface: \Psr\Log\LoggerInterface
     * @param obj  $database Attogram Database object, interface: \AttogramDatabase
     * @param obj  $request  \Symfony\Component\HttpFoundation\Request object
     * @param bool $debug    (optional) Debug True/False.  Defaults to False.
     */
    public function __construct($log, $event, $database, $request, $debug = false)
    {
        $this->startTime = microtime(1);
        $this->log = $log;
        $this->event = $event;
        $this->database = $database;
        $this->request = $request;
        $this->debug = $debug;
        $this->log->debug('START The Attogram Framework v'.self::ATTOGRAM_VERSION);
        $this->projectRepository = 'https://github.com/attogram/attogram';
        $this->awaken(); // set the configuration
        $this->setRequest(); // set all the request-related variables we need
        $this->log->debug("host: $this->host  IP: $this->clientIp");
        $this->exceptionFiles(); // do robots.txt, sitemap.xml
        $this->virtualWebDirectory(); // do virtual web directory requests
        $this->setUri(); // make array of the URI request
        $this->endSlash(); // force slash at end, or force no slash at end
        $this->checkDepth(); // is URI short enough?
        $this->sessioning(); // start sessions
        $this->route(); // Send us where we want to go
        $this->log->debug('END Attogram v'.self::ATTOGRAM_VERSION.' timer: '.(microtime(1) - $this->startTime));
    } // end function __construct()

    /**
     * Awaken The Attogram Framework.
     */
    public function awaken()
    {
        global $config; // The Global Configuration Array
        if (!isset($config['admins'])) {
            $config['admins'] = array('127.0.0.1', '::1');
        }
        $this->remember('admins', $config['admins'], array('127.0.0.1', '::1')); // The Site Administrator IP addresses
        if (!isset($config['debug'])) {
            $config['debug'] = false;
        }
        $this->remember('debug', $config['debug'], false);
        if (!isset($config['attogramDirectory'])) {
            $config['attogramDirectory'] = '../';
        }
        $this->remember('attogramDirectory', $config['attogramDirectory'],          '../');
        if (!isset($config['modulesDirectory'])) {
            $config['modulesDirectory'] = $this->attogramDirectory.'modules';
        }
        $this->remember('modulesDirectory', $config['modulesDirectory'], $this->attogramDirectory.'modules');
        if (!isset($config['templatesDirectory'])) {
            $config['templatesDirectory'] = $this->attogramDirectory.'templates';
        }
        $this->remember('templatesDirectory', $config['templatesDirectory'], $this->attogramDirectory.'templates');
        $this->setModuleTemplates();
        if (!isset($this->templates['header'])) {
            $this->templates['header'] = $this->templatesDirectory.'/header.php';
        }
        if (!isset($this->templates['navbar'])) {
            $this->templates['navbar'] = $this->templatesDirectory.'/navbar.php';
        }
        if (!isset($this->templates['footer'])) {
            $this->templates['footer'] = $this->templatesDirectory.'/footer.php';
        }
        if (!isset($this->templates['fof'])) {
            $this->templates['fof'] = $this->templatesDirectory.'/404.php';
        }
        if (!isset($config['databaseName'])) {
            $config['databaseName'] = '../db/global';
        }
        $this->remember('databaseName', $config['databaseName'], '../db/global');
        if (!isset($config['siteName'])) {
            $config['siteName'] = 'Attogram Framework <small>v'.self::ATTOGRAM_VERSION.'</small>';
        }
        $this->remember('siteName', $config['siteName'], 'Attogram Framework <small>v'.self::ATTOGRAM_VERSION.'</small>');
        if (!isset($config['noEndSlash'])) {
            $config['noEndSlash'] = array();
        }
        $this->remember('noEndSlash', $config['noEndSlash'], array());
        if (!isset($config['depth'])) {
            $config['depth'] = array();
        }
        $this->remember('depth', $config['depth'], array()); // Depth settings
        if (!isset($this->depth[''])) { // check:  homepage depth defined
            $this->depth[''] = 1;
            $this->log->debug('awaken: set homepage depth: 1');
        }
        if (!isset($this->depth['*'])) {  // check: default depth defined
            $this->depth['*'] = 1;
            $this->log->debug('awaken: set default depth: 1');
        }
    } // end function load_config()

    /**
     * Set module templates.
     */
    public function setModuleTemplates()
    {
        $dirs = AttogramFS::getAllSubdirectories($this->modulesDirectory, 'templates');
        if (!$dirs) {
            $this->log->debug('setModuleTemplates: no module templates found');
            return;
        }
        foreach ($dirs as $moduleDir) {
            foreach (array_diff(scandir($moduleDir), AttogramFS::get_skip_files()) as $mfile) {
                $file = "$moduleDir/$mfile";
                if (AttogramFS::isReadableFile($file, '.php')) {
                    $name = preg_replace('/\.php$/', '', $mfile);
                    $this->templates[$name] = $file; // Set the template
                    $this->log->debug('setModuleTemplates: '.$name.' = '.$file);
                    continue;
                }
                $this->log->error('setModuleTemplates: File not readable: '.$file);
            }
        }
    } // end function setModuleTemplates()

    /**
     * set a system configuration variable.
     *
     * @param string $varName    The name of the variable
     * @param string $configVal  The setting for the variable
     * @param string $defaultVal The default setting for the variable, if $config_val is empty
     */
    public function remember($varName, $configVal = '', $defaultVal)
    {
        if ($configVal) {
            $this->{$varName} = $configVal;
            $this->log->debug('remember: '.$varName.' = '.print_r($this->{$varName}, 1));
            return;
        }
        $this->{$varName} = $defaultVal;
        $this->log->debug('remember: using default: '.$varName.' = '.print_r($this->{$varName}, 1));
    }

    /**
     * setRequest().
     */
    public function setRequest()
    {
        $this->host = $this->request->getHost();
        $this->clientIp = $this->request->getClientIp();
        $this->pathInfo = $this->request->getPathInfo();
        $this->requestUri = $this->request->getRequestUri();
        $this->path = $this->request->getBasePath();
    }

    /**
     * set uri array.
     */
    public function setUri()
    {
        $this->uri = explode('/', $this->pathInfo);
        if (sizeof($this->uri) == 1) {
            $this->log->debug('setUri', $this->uri);
            return; // super top level request
        }
        if ($this->uri[0] == '') {
            $trash = array_shift($this->uri); // take off first blank entry
        }
        if (sizeof($this->uri) == 1) {
            $this->log->debug('setUri', $this->uri);
            return; // top level request
        }
        if ($this->uri[sizeof($this->uri) - 1] == '') {
            $trash = array_pop($this->uri); // take off last blank entry
        }
        $this->log->debug('setUri', $this->uri);
    }

    /**
     * endSlash().
     */
    public function endSlash()
    {
        if (!is_array($this->noEndSlash)) {
            return;
        }
        if (!preg_match('/\/$/', $this->pathInfo)) { // No, there is no slash at end of current url
            if (!in_array($this->uri[0], $this->noEndSlash)) {
                // This action IS NOT excepted from force slash at end
                $url = str_replace($this->pathInfo, $this->pathInfo.'/', $this->requestUri);
                header('HTTP/1.1 301 Moved Permanently');
                header('Location: '.$url);  // Force Trailing Slash
                exit;
            }
            return;
        }
        // Yes, there is a slash at end of current url
        if (in_array($this->uri[0], $this->noEndSlash)) {
            // This action IS excepted from force slash at end
            $url = str_replace($this->pathInfo, rtrim($this->pathInfo, ' /'), $this->requestUri);
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: '.$url); // Remove Trailing Slash
            exit;
        }
    }

    /**
     * checkDepth().
     */
    public function checkDepth()
    {
        $depth = $this->depth['*']; // default depth
        if (isset($this->depth[$this->uri[0]])) {
            $depth = $this->depth[$this->uri[0]];
        }
        if ($depth < sizeof($this->uri)) {
            $this->log->error('URI Depth ERROR. uri='.sizeof($this->uri).' allowed='.$depth);
            $this->error404('No Swimming in the deep end');
        }
    }

    /**
     * sessioning() - start the session, logoff if requested.
     */
    public function sessioning()
    {
        session_start();
        $this->log->debug('Session started.', $_SESSION);
        if (isset($_GET['logoff'])) {
            session_unset();
            session_destroy();
            session_start();
            $this->log->info('User loggged off');
        }
    }

    /**
     * route() - decide what action to take based on URI request.
     */
    public function route()
    {
        if (is_dir($this->uri[0])) {  // requesting a directory?
            $this->log->error('ROUTE: 403 Action Forbidden');
            $this->error404('No spelunking allowed');
        }

        if ($this->uri[0] == '') { // The Homepage
            $this->uri[0] = 'home';
        }

        $this->log->debug('ROUTE: action: uri[0]: '.$this->uri[0]);

        $actions = $this->getActions();

        if ($this->isAdmin()) {
            foreach ($this->getAdminActions() as $name => $actionable) {
                $actions[$name] = $actionable;
            }
        }

        if (isset($actions[$this->uri[0]])) {
            switch ($actions[$this->uri[0]]['parser']) {
              case 'php':
                  $this->action = $actions[$this->uri[0]]['file'];
                  if (!is_file($this->action)) {
                      $this->log->error('ROUTE: Missing action');
                      $this->error404('Attempted actionless');
                  }
                  if (!is_readable($this->action)) {
                      $this->log->error('ROUTE: Unreadable action');
                      $this->error404('The pages of the book are blank');
                  }
                  $this->log->debug('ROUTE:   include '.$this->action);
                  include $this->action;
                  return;
              case 'md':
                  $this->doMarkdown($actions[$this->uri[0]]['file']);
                  return;
              default:
                  $this->log->error('ROUTE: No Parser Found');
                  $this->error404('No Way Out');
                  break;
            } // end switch on parser
        } //end if action set

        if ($this->uri[0] == 'home') { // missing the Home Page!
            $this->default_homepage();
            return;
        }

        $this->log->error('ROUTE: Action not found.  uri[0]='.$this->uri[0]);
        $this->error404('This is not the action you are looking for');

    } // end function route()

    /**
     * checks if request is for the virtual web directory "web/"
     * and serve the appropriate module file.
     */
    public function virtualWebDirectory()
    {
        if (!preg_match('/^\/'.'web'.'\//', $this->pathInfo)) {
            return; // not a virtual web directory request
        }
        $test = explode('/', $this->pathInfo);
        if (sizeof($test) < 3 || $test[2] == '') { // empty request
            $this->error404('Virtual Nothingness Found');
        }
        $trash = array_shift($test); // take off top level
        $trash = array_shift($test); // take off virtual web directory
        $req = implode('/', $test); // the virtual web request
        $mod = AttogramFS::getAllSubdirectories($this->modulesDirectory, 'public');
        $file = false;
        foreach ($mod as $m) {
            $test_file = $m.'/'.$req;
            if (!is_readable($test_file) || is_dir($test_file)) {
                continue;
            }
            $file = $test_file; // found file -- cascade set the file
        }
        if (!$file) {
            $this->error404('Virtually Nothing Found');
        }
        $this->do_cache_headers($file);
        $mime_type = AttogramFS::get_mime_type($file);
        if ($mime_type) {
            header('Content-Type:'.$mime_type.'; charset=utf-8');
            $result = readfile($file); // send file to browser
            if (!$result) {
                $this->log->error('virtualWebDirectory: can not read file: '.htmlentities($file));
                $this->error404('Virtually unreadable');
            }
            exit;
        }
        if (!(include($file))) { // include native PHP file
            $this->log->error('virtualWebDirectory: can not include file: '.htmlentities($file));
            $this->error404('Virtually unincludeable');
        }
        exit;
    } // end function virtualWebDirectory()

    /**
     * send HTTP cache headers.
     *
     * @param string $file
     */
    public function do_cache_headers($file)
    {
        if (!$lastmod = filemtime($file)) {
            $lastmod = time();
        }
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', $lastmod).' GMT');
        header('Etag: '.$lastmod);
        $server_if_mod = @strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
        $server_if_none = trim($_SERVER['HTTP_IF_NONE_MATCH']);
        if ($server_if_mod == $lastmod || $server_if_none == $lastmod) {
            header('HTTP/1.1 304 Not Modified');
            exit;
        }
    } // end function do_cache_headers()

    /**
     * Do requests for exception files: sitemap.xml, robots.txt.
     */
    public function exceptionFiles()
    {
        switch ($this->pathInfo) {
            case '/robots.txt':
                header('Content-Type: text/plain; charset=utf-8');
                echo 'Sitemap: '.$this->getSiteUrl().'/sitemap.xml';
                exit;
            case '/sitemap.xml':
                $site = $this->getSiteUrl().'/';
                $sitemap = '<?xml version="1.0" encoding="UTF-8"?>'
                .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
                .'<url><loc>'.$site.'</loc></url>';
                foreach (array_keys($this->getActions()) as $action) {
                    if ($action == 'home' || $action == 'user') {
                        continue;
                    }
                    $sitemap .= '<url><loc>'.$site.$action.'/</loc></url>';
                }
                $sitemap .= '</urlset>';
                header('Content-Type: text/xml; charset=utf-8');
                echo $sitemap;
                exit;
        }
    }

    /**
     * get HTML from a markdown file
     *
     * @param string $file The markdown file to parse
     *
     * @return string      HTML fragment or false
     */
    public function getMarkdown($file)
    {
        if (!AttogramFS::isReadableFile($file, '.md')) {
            $this->log->error('GET_MARKDOWN: can not read file: '.$this->webDisplay($file));
            return false;
        }
        if (!class_exists('Parsedown')) {
            $this->log->error('GET_MARKDOWN: can not find parser');
            return false;
        }
        $page = @file_get_contents($file);
        if ($page === false) {
            $this->log->error('GET_MARKDOWN: can not get file contents: '.$this->webDisplay($file));
            return false;
        }
        $content = \Parsedown::instance()->text($page);
        if (!$content) {
            $this->log->error('GET_MARKDOWN: parse failed on file: '.$this->webDisplay($file));
            return false;
        }
        return $content;
    } // end function getMarkdown

    /**
     * display a Markdown document, with standard page header and footer.
     *
     * @param string $file The markdown file to load
     * @param string $title (optional) Page title
     */
    public function doMarkdown($file, $title = '')
    {
        $this->log->debug('DO_MARKDOWN: '.$file);
        if (!$title) {
            $title = 'MARKDOWN';
        }
        // TODO dev - $title input, and default to 1st line of file
        // $title = trim( strtok($page, "\n") );
        // get first line of file, use as page title

        $this->pageHeader($title);
        echo '<div class="container">'.$this->getMarkdown($file).'</div>';
        $this->pageFooter();
    }

    /**
     * getSiteUrl().
     *
     * @return string
     */
    public function getSiteUrl()
    {
        return $this->request->getSchemeAndHttpHost().$this->path;
    }

    /**
     * getActions() - create list of all pages from the actions directory.
     *
     * @return array
     */
    public function getActions()
    {
        if (is_array($this->actions)) {
            return $this->actions;
        }
        $dirs = AttogramFS::getAllSubdirectories($this->modulesDirectory, 'actions');
        if (!$dirs) {
            $this->log->debug('getActions: No module actions found');
        }
        $this->actions = array();
        foreach ($dirs as $d) {
            foreach ($this->getActionables($d) as $name => $actionable) {
                $this->actions[$name] = $actionable;
            }
        }
        asort($this->actions);
        $this->log->debug('getActions: ', array_keys($this->actions));
        return $this->actions;
    } // end function getActions()

    /**
     * getAdminActions() - create list of all admin pages from the admin directory.
     *
     * @return array
     */
    public function getAdminActions()
    {
        if (is_array($this->adminActions)) {
            return $this->adminActions;
        }
        $dirs = AttogramFS::getAllSubdirectories($this->modulesDirectory, 'admin_actions');
        if (!$dirs) {
            $this->log->debug('getAdminActions: No module admin actions found');
        }
        $this->adminActions = array();
        foreach ($dirs as $d) {
            foreach ($this->getActionables($d) as $name => $actionable) {
                $this->adminActions[$name] = $actionable;
            }
        }
        asort($this->adminActions);
        $this->log->debug('getAdminActions: ', array_keys($this->adminActions));
        return $this->adminActions;
    } // end function getAdminActions()

    /**
     * getActionables - create list of all useable action files from a directory.
     *
     * @return array
     */
    public function getActionables($dir)
    {
        $result = array();
        if (!is_readable($dir)) {
            $this->log->error('GET_ACTIONABLES: directory not readable: '.$dir);
            return $result;
        }
        foreach (array_diff(scandir($dir), AttogramFS::get_skip_files()) as $afile) {
            $file = $dir.'/'.$afile;
            if (AttogramFS::isReadableFile($file, '.php')) { // PHP files
                $result[ str_replace('.php', '', $afile) ] = array('file' => $file, 'parser' => 'php');
            } elseif (AttogramFS::isReadableFile($file, '.md')) { // Markdown files
                $result[ str_replace('.md', '', $afile) ] = array('file' => $file, 'parser' => 'md');
            }
        }
        return $result;
    }

    /**
     * isAdmin() - is access from an admin IP?
     *
     * @return bool
     */
    public function isAdmin()
    {
        if (isset($this->isAdmin) && is_bool($this->isAdmin)) {
            return $this->isAdmin;
        }
        if (isset($_GET['noadmin'])) {
            $this->isAdmin = false;
            $this->log->debug('isAdmin false - noadmin override');
            return false;
        }
        if (!isset($this->admins) || !is_array($this->admins)) {
            $this->isAdmin = false;
            $this->log->error('isAdmin false - missing $this->admins  array');
            return false;
        }
        if (is_object($this->request)) {
            $cip = $this->request->getClientIp();
        } else {
            $cip = $_SERVER['REMOTE_ADDR'];
        }
        if (@in_array($cip, $this->admins)) {
            $this->isAdmin = true;
            $this->log->debug('isAdmin true '.$cip);
            return true;
        }
        $this->isAdmin = false;
        $this->log->debug('isAdmin false '.$cip);
        return false;
    }

    /**
     * pageHeader() - the web page header.
     *
     * @param string $title The web page title
     */
    public function pageHeader($title = '')
    {
        $file = $this->templates['header'];
        if (AttogramFS::isReadableFile($file, '.php')) {
            include $file;
            $this->log->debug('pageHeader, title: '.$title);
            return;
        }
        // Default page header
        echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8">'
        .'<meta name="viewport" content="width=device-width, initial-scale=1">'
        .'<title>'.$title.'</title></head><body>';
        $this->log->error('missing pageHeader '.$file.' - using default header');
    }

    /**
     * pageFooter() - the web page footer.
     */
    public function pageFooter()
    {
        $file = $this->templates['footer'];
        if (AttogramFS::isReadableFile($file, '.php')) {
            include $file;
            $this->log->debug('pageFooter');
            return;
        }
        // Default page footer
        echo '<hr /><p>Powered by <a href="'.$this->projectRepository.'">Attogram v'.ATTOGRAM_VERSION.'</a></p>';
        echo '</body></html>';
        $this->log->error('missing pageFooter '.$file.' - using default footer');
    }

    /**
     * Show the default home page.
     */
    public function default_homepage()
    {
        $this->log->error('using default_homepage');
        $this->pageHeader('Home');
        echo '<div class="container">'
        .'<h1>Welcome to the Attogram Framework <small>v'.self::ATTOGRAM_VERSION.'</small></h1>'
        .'<p>To replace this page, create a file named '
        .'<code>home.php</code> or <code>home.md</code> '
        .' in any <code>modules/*/actions/</code> directory</p>'
        .'<p>Public Actions:<ul>';
        if (!$this->getActions()) {
            echo '<li><em>No actions yet</em></li>';
        }
        foreach ($this->getActions() as $name => $val) {
            echo '<li><a href="'.$this->path.'/'.urlencode($name).'/">'.htmlentities($name).'</a></li>';
        }
        echo '</ul><p>';
        if ($this->isAdmin()) {
            echo '<p>Admin Actions:<ul>';
            if (!$this->getAdminActions()) {
                echo '<li><em>No admin actions yet</em></li>';
            }
            foreach ($this->getAdminActions() as $name => $val) {
                echo '<li><a href="'.$this->path.'/'.urlencode($name).'/">'.htmlentities($name).'</a></li>';
            }
            echo '</ul></p>';
        }
        echo '</div>';
        $this->pageFooter();
    }

    /**
     * error404() - display a 404 error page to user and exit.
     */
    public function error404($error = '')
    {
        //$this->event->error('404 Not Found: uri: [' . implode(', ', $this->uri) . '] error: ' . $error);
        header('HTTP/1.0 404 Not Found');
        if (AttogramFS::isReadableFile($this->templates['fof'], '.php')) {
            include $this->templates['fof'];
            exit;
        }
        // Default 404 page
        $this->log->error('ERROR404: 404 template not found');
        $this->pageHeader('404 Not Found');
        echo '<div class="container"><h1>404 Not Found</h1>';
        if ($error) {
            echo '<p>'.htmlentities($error).'</p>';
        }
        echo '</div>';
        $this->pageFooter();
        exit;
    }

    /**
     * clean a string for web display.
     *
     * @param string $string  The string to clean
     *
     * @return string  The cleaned string, or false
     */
    public function webDisplay($string)
    {
        if (!is_string($string)) {
            return false;
        }
        return htmlentities($string, ENT_COMPAT, 'UTF-8');
    }

} // END of class attogram
