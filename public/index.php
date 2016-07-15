<?php
// Attogram Framework - Guru Meditation Loader - v0.4.4

namespace Attogram;

global $config;

// Default configuration
// Values may be overriden by ./public/config.php, and then ./modules/*/configs/*.php
$config['attogramDirectory'] = '../'; // with trailing slash
$config['autoloader'] = $config['attogramDirectory'].'vendor/autoload.php';
$config['modulesDirectory'] = $config['attogramDirectory'].'modules'; // without trailing slash
$config['templatesDirectory'] = $config['attogramDirectory'].'templates'; // without trailing slash
$config['debug'] = false;
$config['siteName'] = 'The Attogram Framework';
$config['admins'] = array('127.0.0.1', '::1');
$config['databaseName'] = $config['attogramDirectory'].'db/global';

// Load the Project
$guru = new GuruMeditationLoader(
    $config['siteName'], // $projectName
    './config.php', // $configFile
    $config['attogramDirectory'].'Attogram/', // $projectClasses
    $config['autoloader'], // $defaultAutoloader
    'https://github.com/attogram/attogram-vendor/archive/master.zip', // $vendorDownload
    array( // $requiredClasses
        '\Attogram\Attogram', // The Attogram Framework
        '\Symfony\Component\HttpFoundation\Request', // HTTP Request Object
        '\Parsedown', // Markdown Parser
        '\Psr\Log\NullLogger', // PSR-3 Null Logger Object
        '\Monolog\Formatter\LineFormatter', // Monolog Line Formatter
        '\Monolog\Handler\BufferHandler', // Monolog Buffer Handler
        '\Monolog\Handler\StreamHandler', // Monolog Stream Handle
        '\Monolog\Logger', // Monolog PSR-3 logger
    ),
    array( // $requiredInterfaces
        '\Psr\Log\LoggerInterface', // PSR-3 Logger Interface
    )
);

/** ************************************************************************* */
class GuruMeditationLoader
{
    public $projectName;
    public $configFile;
    public $projectClasses;
    public $defaultAutoloader;
    public $vendorDownload;
    public $requiredClasses;
    public $requiredInterfaces;
    public $autoloader;

    /**
     * set the Guru vars.
     */
    public function __construct(
        $projectName,
        $configFile,
        $projectClasses,
        $defaultAutoloader,
        $vendorDownload,
        array $requiredClasses,
        array $requiredInterfaces
    ) {
        error_reporting(E_ALL); // display all errors
        ini_set('display_errors', E_ALL); // display all errors
        //error_reporting(E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR ); // dev - hide errors
        //ini_set('display_errors',  E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR ); // dev - hide errors
        set_error_handler(array($this, 'guruMeditationErrorHandler'));
        register_shutdown_function(array($this, 'guruMeditationShutdown'));
        $this->debug('START Guru Meditation Loader: '.$this->projectName);
        $this->projectName        = $projectName;
        $this->configFile         = $configFile;
        $this->projectClasses     = $projectClasses;
        $this->defaultAutoloader  = $defaultAutoloader;
        $this->vendorDownload     = $vendorDownload;
        $this->requiredClasses    = $requiredClasses;
        $this->requiredInterfaces = $requiredInterfaces;
        $this->meditate();            // load the Attogram configuration -- get config[ autoloader, modulesDirectory, debug ]
        $this->expandConsciousness(); // run the composer vendor autoloader
        $this->focusMind();           // include Attogram project classes
        $this->focusInnerEye();       // include modules includes
        $this->innerAwareness();      // check for required classes
        $this->innerEmptiness();      // check for required interfaces
        $this->meditateDeeper();      // load the modules configurations
        $this->tranquility();         // Load The Attogram Framework
    } // end function __construct()

    /**
     * Catch any errors.
     */
    public function guruMeditationErrorHandler($level, $message, $file = '', $line = '', $context = array())
    {
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
        }
    }

    /**
     * set the system configuration
     */
    public function meditate()
    {
        global $config;
        if (is_file($this->configFile)) {
            if (!is_readable($this->configFile)) {
                $this->guruMeditationError('Config file not readable: '.$this->configFile);
            }
            $included = (include($this->configFile));
            if (!$included) {
                $this->guruMeditationError('Config file exists, but include failed: '.$this->configFile);
            }
            $this->debug('meditate: OK: '.$this->configFile);
        } else {
            $this->debug('meditate: configFile is NOT a file');
        }
        if (!isset($config)) {
            $this->debug('meditate: $config NOT set');
            $config = array();
        }
        if (!is_array($config)) {
            $this->guruMeditationError('$config is not an array');
        }
        if (!isset($config['autoloader'])) {
            $config['autoloader'] = $this->defaultAutoloader;
        }
        $this->autoloader = $config['autoloader'];
    } // end function meditate()

    /**
     * load module configs.
     */
    public function meditateDeeper()
    {
        global $config;
        $count = Attogram::loadModuleSubdirectories($config['modulesDirectory'], 'configs');
        $this->debug('meditateDeeper: OK: ' . implode(', ', $count));
    }

    /**
     * run the vendor autoloader.
     */
    public function expandConsciousness()
    {
        if (isset($this->autoloader) && is_file($this->autoloader) && is_readable($this->autoloader)) {
            $included = (include($this->autoloader));
            if (!$included) {
                $this->guruMeditationError('Autoloader file exists, but include failed: '.$this->autoloader);
            }
            $this->debug('expandConsciousness: OK: '.$this->autoloader);
            return;
        }
        $this->guruMeditationError(
            'autoloader file not found: '.$this->autoloader,
            'Possibile Fixes:'
            .'<br /><br />- Is the path to the autoloader wrong?  Edit <strong>'.$this->configFile
            .'</strong> and check for <strong>$config[\'autoloader\']</strong>'
            .'<br /><br />- Was <a href="http://getcomposer.org/">composer</a> not run yet?  Run <strong>composer install</strong>'
            .'<br /><br />- Can\'t run composer? <a href="'.$this->vendorDownload
            .'"><strong>download the vendor zip file</strong></a> and install manually'
        );
    } // end function expandConsciousness()

    public function focusMind()
    {
        if (!is_dir($this->projectClasses)) {
            $this->guruMeditationError('Missing project directory: '.$this->projectClasses);
        }
        if (!is_readable($this->projectClasses)) {
            $this->guruMeditationError('Project directory is unreadable: '.$this->projectClasses);
        }
        foreach (array_diff(scandir($this->projectClasses), array('.', '..')) as $f) {
            $included = (include_once($this->projectClasses.$f));
            if (!$included) {
                $this->guruMeditationError('Failed to include project file: '.$this->projectClasses.$f);
                $result[] = $this->projectClasses.$f;
            }
        }
        if (isset($result)) {
            $this->debug('focusMind: OK: '.implode(', ', $result));
        }

    } // end function focusMind()

    public function focusInnerEye()
    {
        global $config;
        $counts = Attogram::loadModuleSubdirectories($config['modulesDirectory'], 'includes');
        $this->debug('focusInnerEye: OK: ' . implode(', ', $counts));
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
            $this->debug('innerEmptiness: OK: '.$c);
        }
        if (!$missing) {
            return;
        }
        $this->guruMeditationError('Required Interface Missing: '.implode(', ', $missing));
    } // end function innerEmptiness()

    public function tranquility()
    {
        global $config;
        // Speed things up! gz compession
        if (ob_start('ob_gzhandler')) {
            $this->debug('tranquility: ob_gzhandler active');
        }

        // Create the Request object
        $request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

        // Create the Debug Logger
        if (
                (
                    isset($config['debug'])   // debug is true...
                    && is_bool($config['debug'])
                    && $config['debug']
                ) || (
                    $request->query->has('debug')   // admin debug url override ?debug
                    && isset($config['admins'])
                    && is_array($config['admins'])
                    && in_array($request->getClientIp(), $config['admins'])
               )
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
        if (isset($config['GuruMeditationLoader']) && is_array($config['GuruMeditationLoader'])) {
            foreach ($config['GuruMeditationLoader'] as $g) {
                $log->debug($g);
            }
        }

        // Create database and event objects
        if (class_exists('\Attogram\SqliteDatabase')) { // if database module installed...
            $database = new SqliteDatabase($config['databaseName'], $config['modulesDirectory'], $log);  // init the database, sans-connection
            $event = new \Monolog\Logger('event'); // Setup the Event Logger
            $event->pushHandler(new \Attogram\EventLogger($database));
        }
        if (!isset($database) || !$database) {
            $database = new NullDatabase();
            $event = new \Psr\Log\NullLogger();
        }

        new Attogram(   // Start the Attogram Framework!
            $log,       // The Debug Logger Object
            $event,     // The Event Logger Object
            $database,  // The Attogram Database Object
            $request    // The Request Object
        );

    } // end function tranquility()

    public function debug($msg)
    {
        global $config;
        $config['GuruMeditationLoader'][] = $msg;
    }

    public function guruMeditationError($error = '', $fix = '')
    {
        global $config;
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
        if (isset($_GET['debug']) && isset($config['GuruMeditationLoader'])) {
            echo '<p class="log">🕑 '.gmdate('Y-m-d H:i:s').' UTC<br />💭 ';
            echo implode('<br />💭 ', $config['GuruMeditationLoader']);
        }
        echo '</body></html>';
        exit;
    } // end function guruMeditationError()

} // end class GuruMeditationLoader
