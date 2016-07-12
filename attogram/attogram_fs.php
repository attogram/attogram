<?php
// Attogram Framework - AttogramFS class v0.1.5

namespace Attogram;

/**
 * Filesystem related functions.
 */
class AttogramFS
{
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
        foreach (array_diff(scandir($dir), self::get_skip_files()) as $d) {
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
    public static function include_all_php_files_in_directory($dir)
    {
        $included = array();
        if (!is_readable($dir)) {
            return $included;
        }
        foreach (array_diff(scandir($dir), self::get_skip_files()) as $f) {
            $ff = $dir.'/'.$f;
            if (!self::isReadableFile($ff, '.php')) {
                continue;
            }
            if ((include($ff))) {
                $included[] = $ff;
            }
        }
        return $included;
    } // end function include_all_php_files_in_directory()

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
    public static function get_skip_files()
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
    public static function load_module_subdirectories($modulesDirectory, $subdirectory)
    {
        global $config;
        $included = array();
        $dirs = self::getAllSubdirectories($modulesDirectory, $subdirectory);
        if (!$dirs) {
            return $included;
        }
        foreach ($dirs as $d) {
            $inc = self::include_all_php_files_in_directory($d);
            $included = array_merge($included, $inc);
        }
        return $included;
    } // end function load_module_subdirectories()

    /**
     * get the mime type of a file.
     *
     * @param string $file  The file to examine
     *
     * @return string       The mime type, or false
     */
    public static function get_mime_type($file)
    {
        $mimeType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file);
        switch (pathinfo($file, PATHINFO_EXTENSION)) { // https://bugs.php.net/bug.php?id=53035
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

} // end class AttogramFS
