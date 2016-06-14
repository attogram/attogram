<?php // Attogram Framework - attogram_fs class v0.0.5

namespace Attogram;

/**
 * Filesystem related functions
 */
class attogram_fs
{

  /**
   * Get list of all sub-subdirectories of a specific name:  $dir/[*]/$name
   * @param string $dir The directory to search within (ie: modules directory)
   * @param string name The name of the subdirectories to find
   * @return array
   */
  public static function get_all_subdirectories( $dir, $name )
  {
    if( !isset($dir) || !$dir || !is_string($dir) ) {
      return array();
    }
    if( !is_dir($dir) || !is_readable($dir) ) {
      return array();
    }
    $r = array();
    foreach( array_diff( scandir($dir), self::get_skip_files() ) as $d ) {
      $md = $dir . '/' . $d;
      if( !is_dir($md) ) {
        continue;
      }
      $md .= '/' . $name;
      if( !is_dir($md) || !is_readable($md) ) {
        continue;
      }
      $r[] = $md;
    }
    return $r;
  } // end function get_all_subdirectories()

  /**
   * include_once all php files in a specific directory
   * @param string $dir The directory to search
   * @return void
   */
  public static function include_all_php_files_in_directory( $dir )
  {
    if( !is_dir($dir) || !is_readable($dir) ) {
      return;
    }
    foreach( array_diff( scandir($dir), self::get_skip_files() ) as $f ) {
      $ff = $dir . '/' . $f;
      if( self::is_readable_file( $ff, '.php' ) ) {
        include_once($ff);
      }
    }
  } // end function include_all_php_files_in_directory()

  /**
   * Tests if is a file exist, is readable, and is of a certain type.
   * @param string $file The name of the file to test
   * @param string $type (optional) The file extension to allow. Defaults to '.php'
   * @return bool
   */
  public static function is_readable_file( $file = false, $type = '.php' )
  {
    if( !$file || !is_file($file) || !is_readable($file) ) {
      return false;
    }
    if( !$type || $type == '' || !is_string($type) ) { // input error
      return false;
    }
    if( preg_match( '/' . $type . '$/', $file ) ) {
      return true;
    }
    return false;
  }

  /**
   * get an array of filenames to skip
   * @return array
   */
  public static function get_skip_files()
  {
    return array( '.', '..', '.htaccess' );
  }

  /**
   * Examines each module for a subdirectory named 'configs'
   * and includes all *.php files from that directory
   * @param string $modules_directory
   * @return void
   */
  public function load_module_configs( $modules_directory )
  {
    global $config;
    $dirs = self::get_all_subdirectories( $modules_directory, 'configs' );
    if( !$dirs ) {
      return;
    }
    foreach( $dirs as $d ) {
      attogram_fs::include_all_php_files_in_directory( $d );
    }
  }

  /**
   * Examines each module for a subdirectory named 'includes'
   * and includes all *.php files from that directory
   * @param string $modules_directory
   * @return void
   */
  public function load_module_includes( $modules_directory )
  {
    $dirs = self::get_all_subdirectories( $modules_directory , 'includes');
    if( !$dirs ) {
      return;
    }
    foreach( $dirs as $d ) {
      self::include_all_php_files_in_directory( $d );
    }
  } // end function get_includes()

} // end class attogram_fs
