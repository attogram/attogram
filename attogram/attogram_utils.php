<?php // Attogram Framework - attogram_utils v0.0.1

namespace Attogram;

/**
 * attogram_utils class
 */
class attogram_utils
{

  public $start_time, $debug, $log, $skip_files, $project_github, $project_packagist;

  function __construct() {
    $this->start_time = microtime(1);
    $this->debug = FALSE;
    $this->log = new Logger(); // logger for startup tasks
    $this->skip_files = array('.','..','.htaccess');
    $this->project_github = 'https://github.com/attogram/attogram';
    $this->project_packagist = 'https://packagist.org/packages/attogram/attogram-framework';
  }

  /**
   * set_config() - set a system configuration variable
   * @param string $var_name     The name of the variable
   * @param string $config_val   The setting for the variable
   * @param string $default_val  The default setting for the variable, if $config_val is empty
   * @return void
   */
  function set_config( $var_name, $config_val='', $default_val ) {
    if( $config_val ) {
      $this->{$var_name} = $config_val;
    } else {
      $this->{$var_name} = $default_val;
    }
    $this->log->debug('set_config: ' . $var_name . ' = ' . print_r($this->{$var_name},1));
  }

  /**
   * get_all_subdirectories()
   * @param string $dir The directory to search within (ie: modules directory)
   * @param string name The name of the subdirectories to find
   * @return array
   */
  function get_all_subdirectories( $dir, $name ) {
    if( !isset($dir) || !$dir || !is_string($dir)) {
      $this->log->error('get_all_subdirectories: UNDEFINED dir:' . print_r($dir,1));
      return array();
    }
    if( !is_dir($dir) || !is_readable($dir) ) {
      $this->log->error('get_all_subdirectories: UNREADABLE dir=' . $dir);
      return array();
    }
    $r = array();
    foreach( array_diff(scandir($dir), $this->skip_files) as $d ) {
      $md = $dir . '/' . $d;
      if( !is_dir($md) ) { continue; }
      $md .= '/' . $name;
      if( !is_dir($md) || !is_readable($md) ) { continue; }
      $r[] = $md;
    }
    return $r;
  } // end function get_all_subdirectories()

  /**
   * include_all_php_files_in_directory()
   * @param string $dir The directory to search
   * @return void
   */
  function include_all_php_files_in_directory( $dir ) {
    if( !is_dir($dir) || !is_readable($dir)) {
      $this->log->error('include_all_php_files_in_directory: Directory not found: ' . $dir);
      return;
    }
    //$this->log->debug('include_all_php_files_in_directory: dir = ' . $dir);
    foreach( array_diff(scandir($dir), $this->skip_files) as $f ) {
      $ff = $dir . '/' . $f;
      if( $this->is_readable_file($ff,'.php') ) {
        $this->log->debug('include: ' . $ff);
        include_once($ff);
      } else {
        $this->log->error('include_all_php_files_in_directory: can not include: ' . $ff);
      }
    }
  } // end function include_all_php_files_in_directory()

  /**
   * is_readable_file() - Tests if is a file exist, is readable, and is of a certain type.
   * @param string $file The name of the file to test
   * @param string $type (optional) The file extension to allow. Defaults to '.php'
   * @return bool
   */
  function is_readable_file( $file=FALSE, $type='.php' ) {
    if( !$file || !is_file($file) || !is_readable($file) ) {
      return FALSE;
    }
    if( !$type || $type == '' || !is_string($type) ) { // error
      return FALSE;
    }
    if( preg_match('/' . $type . '$/',$file) ) {
      return TRUE;
    }
    return FALSE;
  }

} // end class attogram_utils
