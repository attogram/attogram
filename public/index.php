<?php
// Attogram Framework - Guru Meditation Loader - v0.5.2

namespace Attogram;

$guru = new GuruMeditationLoader(
    // $configFile
    'config.php',
    // $vendorDownload
    'https://github.com/attogram/attogram-vendor/archive/master.zip',
    // $requiredClasses
    array(
        '\Attogram\Attogram', // The Attogram Framework
        '\Symfony\Component\HttpFoundation\Request', // HTTP Request Object
        '\Parsedown', // Markdown Parser
        '\Psr\Log\NullLogger', // PSR-3 Null Logger Object
        '\Monolog\Formatter\LineFormatter', // Monolog Line Formatter
        '\Monolog\Handler\BufferHandler', // Monolog Buffer Handler
        '\Monolog\Handler\StreamHandler', // Monolog Stream Handle
        '\Monolog\Logger', // Monolog PSR-3 logger
    ),
    // $requiredInterfaces
    array(
        '\Psr\Log\LoggerInterface', // PSR-3 Logger Interface
        '\Attogram\AttogramDatabaseInterface' // Attogram Database Interface
    )
);

/** ************************************************************************* */
class GuruMeditationLoader
{

    /** @var array List of debug messages */
    public $debugLog;

    /** @var string The Main Configuration File */
    public $configFile;

    /** @var array List of configuration settings */
    public $config;

    /** @var string URL to download vendor directory */
    public $vendorDownload;

    /** @var array List of required classes */
    public $requiredClasses;

    /** @var array List of required Interfaces */
    public $requiredInterfaces;

    /** @var string The composer autoloader.php file */
    public $autoloader;

    /**
     * start the Guru Meditation Loader
     * @param string $configFile
     * @param string $vendorDownload
     * @param array $requiredClasses
     * @param array $requiredInterfaces
     */
    public function __construct(
        $configFile,
        $vendorDownload,
        array $requiredClasses,
        array $requiredInterfaces
    ) {
        $this->errorSetup();
        $this->debug('START Guru Meditation Loader');
        $this->configFile         = $configFile;
        $this->vendorDownload     = $vendorDownload;
        $this->requiredClasses    = $requiredClasses;
        $this->requiredInterfaces = $requiredInterfaces;
        $this->meditate();            // load the main Attogram configuration
        $this->expandConsciousness(); // run the composer vendor autoloader
        $this->focusMind();           // include Attogram project classes
        $this->focusInnerEye();       // include modules includes
        $this->innerAwareness();      // check for required classes
        $this->innerEmptiness();      // check for required interfaces
        $this->meditateDeeper();      // load the modules configurations
        $this->tranquility();         // Load The Attogram Framework

    } // end function __construct()

    /**
     * load the main Attogram configuration
     * @see GuruMeditationLoader::configFile
     * @see GuruMeditationLoader::config
     */
    public function meditate()
    {
        // Set default configuration
        $this->config = array();
        $this->config['attogramDirectory']  = '..'.DIRECTORY_SEPARATOR;
        $this->config['autoloader']         = '..'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
        $this->config['modulesDirectory']   = '..'.DIRECTORY_SEPARATOR.'modules';
        $this->config['templatesDirectory'] = '..'.DIRECTORY_SEPARATOR.'templates';
        $this->config['debug']              = false;
        $this->config['siteName']           = 'Attogram Framework';
        $this->config['admins']             = array('127.0.0.1', '::1');

        if (!is_file($this->configFile)) {
            $this->debug('meditate: NOT FOUND: ConfigFile: '.$this->configFile);
            $this->logDefaultConfiguration();
            return;
        }
        if (!is_readable($this->configFile)) {
            $this->debug('meditate: NOT READABLE: ConfigFile: '.$this->configFile);
            $this->logDefaultConfiguration();
            return;
        }
        if (!(include($this->configFile))) {
            $this->debug('meditate: INCLUDE FAILED: ConfigFile: '.$this->configFile);
            $this->logDefaultConfiguration();
            return;
        }
        $this->debug('meditate: LOADED OK: ConfigFile: '.$this->configFile);

        if (!isset($config)) {
            $this->debug('meditation: NOT FOUND: no config variable in ConfigFile');
            $this->logDefaultConfiguration();
            return;
        }
        if (!is_array($config)) {
            $this->debug('meditation: NOT FOUND: no config array in ConfigFile');
            $this->logDefaultConfiguration();
            return;
        }
        // Override default settings with ConfigFile configuration
        foreach ($config as $configName => $configValue) {
            $this->config[$configName] = $configValue;
        }
        $this->debug('meditate: CONFIG OK: '.print_r($this->config, true));
    } // end function meditate()

    public function logDefaultConfiguration()
    {
        $this->debug('DEFAULT CONFIG: '.print_r($this->config, true));
    }
    /**
     * run the vendor autoloader
     */
    public function expandConsciousness()
    {
        if (isset($this->config['autoloader'])
            && is_file($this->config['autoloader'])
            && is_readable($this->config['autoloader'])
        ) {
            $included = (include($this->config['autoloader']));
            if (!$included) {
                $this->guruMeditationError(
                    'Autoloader file exists, but include failed: '.$this->config['autoloader']
                );
            }
            $this->debug('expandConsciousness: OK: '.$this->config['autoloader']);
            return;
        }
        $this->guruMeditationError(
            'autoloader file not found: '.$this->config['autoloader'],
            'Possibile Fixes:'
            .'<br /><br />- Is the path to the autoloader wrong?  Edit <strong>'
            .$this->configFile
            .'</strong> and set <strong>$config[\'autoloader\']</strong>'
            .'<br /><br />- Was <a href="http://getcomposer.org/">composer</a> not run yet?'
            .' Run <strong>composer install</strong>'
            .'<br /><br />- Can\'t run composer? <a href="'.$this->vendorDownload
            .'"><strong>download the vendor zip file</strong></a> and install manually'
        );
    } // end function expandConsciousness()

    /**
     * include Attogram project classes
     */
    public function focusMind()
    {
        $projectClassesDir = $this->config['attogramDirectory'].'Attogram'.DIRECTORY_SEPARATOR;
        if (!is_dir($projectClassesDir)) {
            $this->guruMeditationError(
                'Missing project directory: '.$projectClassesDir
            );
        }
        if (!is_readable($projectClassesDir)) {
            $this->guruMeditationError(
                'Project directory is unreadable: '.$projectClassesDir
            );
        }
        $result = array();
        foreach (array_diff(scandir($projectClassesDir), array('.', '..')) as $file) {
            if (!(include_once($projectClassesDir.$file))) {
                $this->guruMeditationError(
                    'Failed to include project file: '.$projectClassesDir.$file
                );
                $result[] = $projectClassesDir.$file;
            }
        }
        $this->debug('focusMind: OK: '.implode(', ', $result));
    } // end function focusMind()

    public function focusInnerEye()
    {
        $counts = Attogram::loadModuleSubdirectories(
            $this->config['modulesDirectory'],
            'includes'
        );
        $this->debug('focusInnerEye: OK: '.implode(', ', $counts));
    }

    public function innerAwareness()
    {
        $missing = array();
        foreach ($this->requiredClasses as $c) {
            if (!class_exists($c)) {
                $missing[] = $c;
                $this->debug('innerAwareness: Required Class NOT FOUND: '.$c);
            }
            $result[] = $c;
        }
        if (!$missing && isset($result)) {
            $this->debug('innerAwareness: OK: '.implode(', ', $result));
            return;
        }
        $this->guruMeditationError('Required Class Missing: '.implode(', ', $missing));
    } // end function innerAwareness()

    public function innerEmptiness()
    {
        $missing = array();
        foreach ($this->requiredInterfaces as $c) {
            if (!interface_exists($c)) {
                $missing[] = $c;
                $this->debug('innerEmptiness: Required Inteface NOT FOUND: '.$c);
            }
            $result[] = $c;
        }
        if (!$missing && isset($result)) {
            $this->debug('innerEmptiness: OK: '.implode(', ', $result));
            return;
        }
        $this->guruMeditationError('Required Interface Missing: '.implode(', ', $missing));
    } // end function innerEmptiness()

    /**
     * load module configuration files
     */
    public function meditateDeeper()
    {
        global $config; // for loading of $config variable from within the module config files
        $configDirs = Attogram::getAllSubdirectories(
            $this->config['modulesDirectory'],
            'configs'
        );
        if (!$configDirs) {
            $this->debug('meditateDeeper: NOT FOUND: no module configs directories');
            return;
        }
        foreach ($configDirs as $dir) {
            //$this->debug('meditateDeeper: dir: ' . $dir);
            Attogram::includeAllPhpFilesInDirectory($dir);
        }
        if (!$config) {
            $this->debug('meditateDeeper: NOT FOUND: no config in module configs directories');
            return;
        }
        foreach ($config as $configName => $configValue) {
            $this->config[$configName] = $configValue;
        }
        $this->debug('meditateDepper: Module Config OK: ' . print_r($config, true));
    }

    public function tranquility()
    {
        // Speed things up! gz compession
        if (ob_start('ob_gzhandler')) {
            $this->debug('tranquility: ob_gzhandler active');
        }

        // Create the Request object
        $request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

        // Create the Debug Logger
        if ((isset($this->config['debug'])   // debug is true...
            && is_bool($this->config['debug'])
            && $this->config['debug'])
            ||
            ($request->query->has('debug')   // admin debug url override ?debug
            && isset($this->config['admins'])
            && is_array($this->config['admins'])
            && in_array($request->getClientIp(), $this->config['admins']))
        ) {
            $log = new \Monolog\Logger('debug');
            $streamHandler = new \Monolog\Handler\StreamHandler('php://output');
            $format = '<p class="text-danger squished">%datetime%|%level_name%: %message% %context%</p>'; // %extra%
            $dateformat = 'Y-m-d|H:i:s:u';
            $streamHandler->setFormatter(new \Monolog\Formatter\LineFormatter($format, $dateformat));
            $log->pushHandler(new \Monolog\Handler\BufferHandler($streamHandler));
            // $log->pushHandler( new \Monolog\Handler\BrowserConsoleHandler ); // dev
        } else {
            $log = new \Psr\Log\NullLogger();
        }
        // Save guru startup log to the Debug logger
        if (isset($this->configLog) && is_array($this->configLog)) {
            foreach ($this->configLog as $message) {
                $log->debug($message);
            }
        }

        // Create database and event objects
        if (class_exists('\Attogram\SqliteDatabase')) { // if database module installed...
            $database = new SqliteDatabase( // init the database, sans-connection
                $this->config['databaseName'],
                $this->config['modulesDirectory'],
                $log
            );
            $event = new \Monolog\Logger('event'); // Setup the Event Logger
            $event->pushHandler(new \Attogram\EventLogger($database));
        }
        if (!isset($database) || !$database) {
            $database = new NullDatabase();
            $event = new \Psr\Log\NullLogger();
        }

        new Attogram(     // Start the Attogram Framework!
            $log,         // The Debug Logger Object
            $event,       // The Event Logger Object
            $database,    // The Attogram Database Object
            $request,     // The Request Object
            $this->config // Configuration for this installation
        );

    } // end function tranquility()

    /**
     * Log a debug message
     * @param string $msg  The debug message
     */
    public function debug($msg)
    {
        $this->configLog[] = $msg;
    }

    public function guruMeditationError($error = '', $fix = '')
    {
        echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8">'
        .'<meta name="viewport" content="width=device-width, initial-scale=1">'
        .'<title>Guru Meditation Error</title>'
        .'<style>'
        .' body { margin:0 0 0 30px; font-size:22px; font-family:"Helvetica Neue",Helvetica,Arial,sans-serif; }'
        .' a { text-decoration:none; }'
        .' .icon { font-size:60px; vertical-align:middle; padding:0px; margin:10px; }'
        .' .err { color:red; }'
        .' .fix { font-size:18px; color:black;  }'
        .' .log { font-size:15px; color:#333366; }'
        .'</style></head><body>'
        .'<p><a href=""><span class="icon">😢</span></a> Guru Meditation Error</p>';
        if ($error) {
            echo '<p class="err"><a href=""><span class="icon">💔</span></a> '.$error.'</p>';
        }
        if ($fix) {
            echo '<p class="fix"><a href=""><span class="icon">🔧</span></a> '.$fix.'</p>';
        }
        if (isset($_GET['debug']) && isset($this->configLog)) {
            echo '<p class="log">🕑 '.gmdate('Y-m-d H:i:s').' UTC<br />💭 ';
            echo implode('<br />💭 ', $this->configLog);
        }
        echo '</body></html>';
        exit; // Exit everything
    } // end function guruMeditationError()

    /**
     * Catch any errors.
     */
    public function guruMeditationErrorHandler(
        $level,
        $message,
        $file = '',
        $line = '',
        $context = array()
    ) {
        switch ($level) {
            case 1:
                $this->debug("E_ERROR: file:$file line:$line $message");
                break;
            case 2:
                $this->debug("E_WARNING: file:$file line:$line $message");
                return;
            case 4:
                $this->debug("E_PARSE: file:$file line:$line $message");
                return;
            case 8:
                $this->debug("E_NOTICE: file:$file line:$line $message");
                return;
            case 16:
                $this->debug("E_CORE_ERROR: file:$file line:$line $message");
                break;
            case 32:
                $this->debug("E_CORE_WARNING: file:$file line:$line $message");
                return;
            case 64:
                $this->debug("E_COMPILE_ERROR: file:$file line:$line $message");
                break;
            case 128:
                $this->debug("E_COMPILE_WARNING: file:$file line:$line $message");
                return;
            case 256:
                $this->debug("E_USER_ERROR: file:$file line:$line $message");
                break;
            case 512:
                $this->debug("E_USER_WARNING: file:$file line:$line $message");
                return;
            case 1024:
                $this->debug("E_USER_NOTICE: file:$file line:$line $message");
                return;
            case 2048:
                $this->debug("E_STRICT: file:$file line:$line $message");
                return;
            case 4096:
                $this->debug("E_RECOVERABLE_EROR: file:$file line:$line $message");
                return;
            case 8192:
                $this->debug("E_DEPECIATED: file:$file line:$line $message");
                return;
            case 16384:
                $this->debug("E_USER_DEPECIATED: file:$file line:$line $message");
                return;
            case 30719:
                $this->debug("E_ALL: file:$file line:$line $message");
                break;
          default:
              $this->debug("E_UNKNOWN: file:$file line:$line $message");
              break;
        }

        $this->guruMeditationError(
            "Sadness $level: $message"
            . ((isset($file) && $file) ? "<pre>File: $file</pre>" : '')
            . ((isset($line) && $line) ? "<pre>Line: $line</pre>" : '')
            . (isset($context['project_name']) ? '<pre>Context: ' . $context['project_name'] . '</pre>' : '')
        );
    }

    /**
     * Catch any fatal errors at shutdown.
     */
    public function guruMeditationShutdown()
    {
        $last = error_get_last();
        switch ($last['type']) {
            case E_ERROR:
                $this->guruMeditationError(
                    'Shutdown due to Fatal Error:<br />'.str_replace("\n", '<br />', $last['message'])
                );
                // shutdown
        }
    }

    /**
     * setup error and shutdown handling
     */
    public function errorSetup()
    {
        error_reporting(E_ALL); // display all errors
        ini_set('display_errors', E_ALL); // display all errors
        set_error_handler(array($this, 'guruMeditationErrorHandler'));
        register_shutdown_function(array($this, 'guruMeditationShutdown'));
    }
} // end class GuruMeditationLoader
