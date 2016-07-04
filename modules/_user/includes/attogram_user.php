<?php // Attogram Framework - User Module - attogram_user class v0.1.0

namespace Attogram;

/**
 * Attogram User Object Interface
 */
interface attogram_user_interface {

  public static function login( $PSR_3_logger_object, $database_object );

  public static function logout();

  public static function is_logged_in();

}

/**
 * A very simple user system, with very minimal security.
 * Not recommended for production environment
 */
class attogram_user implements attogram_user_interface
{

  /**
   * login a user into the system
   * @param  obj   $log  PSR-3 compliant logger object
   * @param  obj   $db   The attogram database object
   * @return bool
   */
  public static function login( $log, $db )
  {
    if( !isset($_POST['u']) || !isset($_POST['p']) || !$_POST['u'] || !$_POST['p'] ) {
      $log->error('LOGIN: missing username or password');
      return false;
    }
    $user = $db->query(
      'SELECT id, username, level, email FROM user WHERE username = :u AND password = :p',
      $bind=array(':u'=>$_POST['u'],':p'=>$_POST['p']) );

    if( $db->db->errorCode() != '00000' ) { // query failed
      $log->error('LOGIN: Login system offline');
      return false;
    }
    if( !$user ) { // no user, or wrong password
      $log->error('LOGIN: Invalid login');
      return false;
    }
    if( !sizeof($user) == 1 ) { // corrupt data
      $log->error('LOGIN: Login system error');
      return false;
    }
    $user = $user[0];
    $_SESSION['attogram_id'] = $user['id'];
    $_SESSION['attogram_username'] = $user['username'];
    $_SESSION['attogram_level'] = $user['level'];
    $_SESSION['attogram_email'] = $user['email'];
    $log->debug('LOGIN: User Logged in');
    return true;
  }

  /**
   * logout a user
   */
  public static function logout()
  {
    session_unset();
    session_destroy();
    session_start();
  }

  /**
   * is a user logged into the system?
   * @return bool
   */
  public static function is_logged_in( )
  {
    if( isset($_SESSION['attogram_id']) && $_SESSION['attogram_id'] && isset($_SESSION['attogram_username']) && $_SESSION['attogram_username']) {
      return true;
    }
    return false;
  }

} // end class attogram_user
