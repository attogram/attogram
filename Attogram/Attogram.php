<?php
// Attogram Framework - Attogram class v0.4.9

namespace Attogram;

/**
 * Attogram Framework.
 *
 * The Attogram Framework provides developers a PHP skeleton starter site with
 * content modules, file-based URL routing, IP-protected backend, user system,
 * integrated SQLite database with web admin, Markdown parser, jQuery and Bootstrap.
 *
 * The Attogram Framework is Dual Licensed under the MIT License (MIT)
 * or the GNU General Public License version 3 (GPL-3.0+), at your choosing.
 *
 * @license (MIT or GPL-3.0+)
 * @copyright 2016 Attogram Framework Developers https://github.com/attogram/attogram
 */
class Attogram
{
    const ATTOGRAM_VERSION = '0.7.8-pre';

    public $startTime;          // (float) microsecond time of awakening
    public $log;                // (object) Debug Log - PSR-3 Logger object
    public $event;              // (object) Event Log - PSR-3 Logger object
    public $database;           // (object) The Attogram Database Object
    public $request;            // (object) Symfony HttpFoundation Request object
    public $path;               // (string) Relative URL path to this installation
    public $projectRepository;  // (string) URL to Attogram Framework GitHub Project
    public $attogramDirectory;  // (string) path to this installation
    public $modulesDirectory;   // (string) path to the modules directory
    public $templatesDirectory; // (string) path to the templates directory
    public $templates;          // (array) list of templates
    public $siteName;           // (string) The Site Name
    public $depth;              // (array) Allowed depth settings
    public $noEndSlash;         // (array) actions to NOT force slash at end
    public $uri;                // (array) The Current URI
    public $actions;            // (array) memory variable for $this->getActions()
    public $action;             // (string) The Current Action name
    public $admins;             // (array) Administrator IP addresses
    public $isAdmin;            // (boolean) memory variable for $this->isAdmin()
    public $adminActions;       // (array) memory variable for $this->getAdminActions()

    /**
     * @param obj  $log      Debug PSR-3 logger object, interface: \Psr\Log\LoggerInterface
     * @param obj  $event    Event PSR-3 logger object, interface: \Psr\Log\LoggerInterface
     * @param obj  $database Attogram Database object, interface: \Attogram\AttogramDatabase
     * @param obj  $request  Request object, \Symfony\Component\HttpFoundation\Request
     * @param array $configuration  (optional) List of configuration values
     */
    public function __construct(
        \Psr\Log\LoggerInterface $log,
        \Psr\Log\LoggerInterface $event,
        \Attogram\AttogramDatabase $database,
        \Symfony\Component\HttpFoundation\Request $request,
        array $configuration = array()
    ) {

        global $config; // The Global Configuration Array // TODO TMP DEV

        $this->startTime = microtime(1);
        $this->log = $log;
        $this->log->debug('START The Attogram Framework v'.self::ATTOGRAM_VERSION);
        $this->event = $event;
        $this->database = $database;

        $this->request = $request;
        $this->path = $this->request->getBasePath();
        $this->log->debug('host: '.$this->request->getHost().' IP: '.$this->request->getClientIp());

        $this->config = $configuration;

        if( is_array($config) ) {  // TODO TMP DEV - global $config use?
          $this->config = $config;
        }
        $this->log->debug('CONFIG:', $this->config);

        $this->projectRepository = 'https://github.com/attogram/attogram';
        $this->awaken(); // set the configuration
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


        $this->remember('admins', @$this->config['admins'], array('127.0.0.1', '::1')); // The Site Administrator IP addresses
        $this->remember('attogramDirectory', @$this->config['attogramDirectory'], '../');
        $this->remember('modulesDirectory', @$this->config['modulesDirectory'], $this->attogramDirectory.'modules');
        $this->remember('templatesDirectory', @$this->config['templatesDirectory'], $this->attogramDirectory.'templates');
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
        $this->remember('siteName', @$this->config['siteName'], 'Attogram Framework <small>v'.self::ATTOGRAM_VERSION.'</small>');
        $this->remember('noEndSlash', @$this->config['noEndSlash'], array());
        $this->remember('depth', @$this->config['depth'], array()); // Depth settings
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
        $dirs = $this->getAllSubdirectories($this->modulesDirectory, 'templates');
        if (!$dirs) {
            $this->log->debug('setModuleTemplates: no module templates found');
            return;
        }
        foreach ($dirs as $moduleDir) {
            foreach (array_diff(scandir($moduleDir), $this->getSkipFiles()) as $mfile) {
                $file = "$moduleDir/$mfile";
                if ($this->isReadableFile($file, '.php')) {
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
     * set uri array.
     */
    public function setUri()
    {
        $this->uri = explode('/', $this->request->getPathInfo());
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
        if (!preg_match('/\/$/', $this->request->getPathInfo())) { // No, there is no slash at end of current url
            if (!in_array($this->uri[0], $this->noEndSlash)) {
                // This action IS NOT excepted from force slash at end
                $url = str_replace($this->request->getPathInfo(), $this->request->getPathInfo().'/', $this->request->getRequestUri());
                header('HTTP/1.1 301 Moved Permanently');
                header('Location: '.$url);  // Force Trailing Slash
                exit;
            }
            return;
        }
        // Yes, there is a slash at end of current url
        if (in_array($this->uri[0], $this->noEndSlash)) {
            // This action IS excepted from force slash at end
            $url = str_replace($this->request->getPathInfo(), rtrim($this->request->getPathInfo(), ' /'), $this->request->getRequestUri());
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
        if ($this->request->query->has('logoff')) {
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
                  $this->log->debug('ROUTE: include '.$this->action);
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
            $this->defaultHomepage();
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
        if (!preg_match('/^\/'.'web'.'\//', $this->request->getPathInfo())) {
            return; // not a virtual web directory request
        }
        $test = explode('/', $this->request->getPathInfo());
        if (sizeof($test) < 3 || $test[2] == '') { // empty request
            $this->error404('Virtual Nothingness Found');
        }
        $trash = array_shift($test); // take off top level
        $trash = array_shift($test); // take off virtual web directory
        $req = implode('/', $test); // the virtual web request
        $modulesDirectories = $this->getAllSubdirectories($this->modulesDirectory, 'public');
        $file = false;
        foreach ($modulesDirectories as $moduleDirectory) {
            $testFile = $moduleDirectory.'/'.$req;
            if (!is_readable($testFile) || is_dir($testFile)) {
                continue;
            }
            $file = $testFile; // found file -- cascade set the file
        }
        if (!$file) {
            $this->error404('Virtually Nothing Found');
        }
        $this->doCacheHeaders($file);
        $mimeType = $this->getMimeType($file);
        if ($mimeType) {
            header('Content-Type:'.$mimeType.'; charset=utf-8');
            $result = readfile($file); // send file to browser
            if (!$result) {
                $this->log->error('virtualWebDirectory: can not read file: '.htmlentities($file));
                $this->error404('Virtually unreadable');
            }
            exit;
        }
        if (!(include($file))) { // include native PHP or HTML file
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
    public function doCacheHeaders($file)
    {
        if (!$lastmod = filemtime($file)) {
            $lastmod = time();
        }
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', $lastmod).' GMT');
        header('Etag: '.$lastmod);
        $serverIfMod = @strtotime($this->request->server->get('HTTP_IF_MODIFIED_SINCE'));
        $serverIfNone = trim($this->request->server->get('HTTP_IF_NONE_MATCH'));
        if ($serverIfMod == $lastmod || $serverIfNone == $lastmod) {
            header('HTTP/1.1 304 Not Modified');
            exit;
        }
    } // end function doCacheHeaders()

    /**
     * Do requests for exception files: sitemap.xml, robots.txt.
     */
    public function exceptionFiles()
    {
        switch ($this->request->getPathInfo()) {
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
        if (!$this->isReadableFile($file, '.md')) {
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
        $dirs = $this->getAllSubdirectories($this->modulesDirectory, 'actions');
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
        $dirs = $this->getAllSubdirectories($this->modulesDirectory, 'admin_actions');
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
     * @param string $dir The directory to scan
     * @return array List of actions
     */
    public function getActionables($dir)
    {
        $result = array();
        if (!is_readable($dir)) {
            $this->log->error('GET_ACTIONABLES: directory not readable: '.$dir);
            return $result;
        }
        foreach (array_diff(scandir($dir), $this->getSkipFiles()) as $afile) {
            $file = $dir.'/'.$afile;
            if ($this->isReadableFile($file, '.php')) { // PHP files
                $result[str_replace('.php', '', $afile)] = array('file' => $file, 'parser' => 'php');
                continue;
            }
            if ($this->isReadableFile($file, '.md')) { // Markdown files
                $result[str_replace('.md', '', $afile)] = array('file' => $file, 'parser' => 'md');
                continue;
            }
            if ($this->isReadableFile($file, '.html')) { // HTML files
                $result[str_replace('.html', '', $afile)] = array('file' => $file, 'parser' => 'php');
                continue;
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
        if ($this->request->query->has('noadmin')) {
            $this->isAdmin = false;
            $this->log->debug('isAdmin false - noadmin override');
            return false;
        }
        if (!isset($this->admins) || !is_array($this->admins)) {
            $this->isAdmin = false;
            $this->log->error('isAdmin false - missing $this->admins  array');
            return false;
        }
        $cip = $this->request->getClientIp();

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
        if ($this->isReadableFile($file, '.php')) {
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
        if ($this->isReadableFile($file, '.php')) {
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
    public function defaultHomepage()
    {
        $this->log->error('using defaultHomepage');
        $this->pageHeader('Home');
        echo '<div class="container">'
        .'<h1>Welcome to the Attogram Framework <small>v'.self::ATTOGRAM_VERSION.'</small></h1>'
        .'<p>Public pages:<ul>';
        if (!$this->getActions()) {
            echo '<li><em>None yet</em></li>';
        }
        foreach ($this->getActions() as $name => $val) {
            echo '<li><a href="'.$this->path.'/'.urlencode($name).'/">'.htmlentities($name).'</a></li>';
        }
        echo '</ul><p>';
        if ($this->isAdmin()) {
            echo '<p>Admin pages:<ul>';
            if (!$this->getAdminActions()) {
                echo '<li><em>None yet</em></li>';
            }
            foreach ($this->getAdminActions() as $name => $val) {
                echo '<li><a href="'.$this->path.'/'.urlencode($name).'/">'.htmlentities($name).'</a></li>';
            }
            echo '</ul></p>';
        }

        echo '<p><br /><hr />To replace this home page:<ul>'
            .'<li>Goto the top level of your <a href="' . $this->projectRepository . '">Attogram Framework</a> installation</li>'
            .'<li>Create a new module directory: <code>modules/mymodule/</code></li>'
            .'<li>Create an actions directory within the module: <code>modules/mymodule/actions/</code></li>'
            .'<li>Create a <strong>home</strong> action, in PHP, Markdown, or HTML:<ul>'
                . '<li><code>modules/mymodule/actions/home.php</code></li>'
                . '<li>or <code>modules/mymodule/actions/home.md</code></li>'
                . '<li>or <code>modules/mymodule/actions/home.html</code></li>'
            .'</ul></li>'
            .'</ul></p>'
            .'</div>';
        $this->pageFooter();
    }

    /**
     * error404() - display a 404 error page to user and exit.
     */
    public function error404($error = '')
    {
        //$this->event->error('404 Not Found: uri: [' . implode(', ', $this->uri) . '] error: ' . $error);
        header('HTTP/1.0 404 Not Found');
        if ($this->isReadableFile($this->templates['fof'], '.php')) {
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


    // Attogram Filesystem

    /**
     * Get list of all sub-subdirectories of a specific name:  $dir/[*]/$name.
     *
     * @param string $dir  The directory to search within (ie: modules directory)
     * @param string $name The name of the subdirectories to find
     *
     * @return array       List of the directories found
     */
    public static function getAllSubdirectories($dir, $name)
    {
        if (!isset($dir) || !$dir || !is_string($dir) || !is_readable($dir)) {
            return array();
        }
        $result = array();
        foreach (array_diff(scandir($dir), self::getSkipFiles()) as $d) {
            $md = $dir.'/'.$d;
            if (!is_readable($md)) {
                continue;
            }
            $md .= '/'.$name;
            if (!is_readable($md)) {
                continue;
            }
            $result[] = $md;
        }
        return $result;
    } // end function getAllSubdirectories()

    /**
     * Include all php files in a specific directory.
     *
     * @param  string $dir The directory to search
     *
     * @return array       List of the files successfully included
     */
    public static function includeAllPhpFilesInDirectory($dir)
    {
        $included = array();
        if (!is_readable($dir)) {
            return $included;
        }
        foreach (array_diff(scandir($dir), self::getSkipFiles()) as $f) {
            $ff = $dir.'/'.$f;
            if (!self::isReadableFile($ff, '.php')) {
                continue;
            }
            if ((include($ff))) {
                $included[] = $ff;
            }
        }
        return $included;
    } // end function includeAllPhpFilesInDirectory()

    /**
     * Tests if is a file exist, is readable, and is of a certain type.
     *
     * @param  string $file The name of the file to test
     * @param  string $type (optional) The file extension to allow. Defaults to '.php'
     *
     * @return bool
     */
    public static function isReadableFile($file = false, $type = '.php')
    {
        if (!$file || !$type || $type == '' || !is_string($type) || !is_string($file) || !is_readable($file)) {
            return false;
        }
        if (preg_match('/'.$type.'$/', $file)) {
            return true;
        }

        return false;
    }

    /**
     * get an array of filenames to skip.
     *
     * @return array
     */
    public static function getSkipFiles()
    {
        return array('.', '..', '.htaccess');
    }

    /**
     * Examines each module for a named subdirectory, then includes all *.php files from that directory.
     *
     * @param string $modulesDirectory
     *
     * @return array List of the files successfully loaded
     */
    public static function loadModuleSubdirectories($modulesDirectory, $subdirectory)
    {
        $included = array();
        $dirs = self::getAllSubdirectories($modulesDirectory, $subdirectory);
        if (!$dirs) {
            return $included;
        }
        foreach ($dirs as $dir) {
            $inc = self::includeAllPhpFilesInDirectory($dir);
            $included = array_merge($included, $inc);
        }
        return $included;
    } // end function loadModuleSubdirectories()

    /**
     * get the mime type of a file.
     *
     * @param string $file  The file to examine
     *
     * @return string       The mime type, or false
     */
    public static function getMimeType($file)
    {
        $mimeType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file);
        switch (pathinfo($file, PATHINFO_EXTENSION)) { // https://bugs.php.net/bug.php?id=53035
            case 'html':
                $mimeType = 'text/html';
                break;
            case 'css':
                $mimeType = 'text/css';
                break;
            case 'js':
                $mimeType = 'application/javascript';
                break;
            case 'xml':
                $mimeType = 'text/xml';
                break;
            case 'php':
                $mimeType = false; // do not do content type header, not needed for native php
                break;
            case 'eot':
                $mimeType = 'application/vnd.ms-fontobject';
                break;
            case 'svg':
                $mimeType = 'image/svg+xml';
                break;
            case 'ttf':
                $mimeType = 'application/font-sfnt';
                break;
            case 'woff':
                $mimeType = 'application/font-woff';
                break;
            case 'woff2':
                $mimeType = 'application/font-woff2';
                break;
        }
        return $mimeType;
    }

} // END of class attogram
