<?php // Attogram Framework - User Module - attogram_user class v0.0.4

namespace Attogram;

class attogram_user
{

  /**
   * login() - login a user into the system
   * @param obj $log - PSR-3 compliant logger object
   * @param obj $db - The attogram database object
   * @return boolean
   */
  public static function login( $log, $db ) {
    if( !isset($_POST['u']) || !isset($_POST['p']) || !$_POST['u'] || !$_POST['p'] ) {
      $log->error('LOGIN: Please enter username and password');
      return FALSE;
    }
    $user = $db->query(
      'SELECT id, username, level, email FROM user WHERE username = :u AND password = :p',
      $bind=array(':u'=>$_POST['u'],':p'=>$_POST['p']) );

    if( $db->db->errorCode() != '00000' ) { // query failed
      $log->error('LOGIN: Login system offline');
      return FALSE;
    }
    if( !$user ) { // no user, or wrong password
      $log->error('LOGIN: Invalid login');
      return FALSE;
    }
    if( !sizeof($user) == 1 ) { // corrupt data
      $log->error('LOGIN: Invalid login');
      return FALSE;
    }
    $user = $user[0];
    $_SESSION['attogram_id'] = $user['id'];
    $_SESSION['attogram_username'] = $user['username'];
    $_SESSION['attogram_level'] = $user['level'];
    $_SESSION['attogram_email'] = $user['email'];
    if( !$db->queryb(
      "UPDATE user SET last_login = datetime('now'), last_host = :last_host WHERE id = :id",
      $bind = array(':id'=>$user['id'], ':last_host'=>$_SERVER['REMOTE_ADDR'])
      ) ) {
        $log->error('LOGIN: can not updated last login info');
    }
    $log->debug('User Logged in');
    return TRUE;
  }

  /**
   * is_logged_in() - is a user logged into the system?
   * @return bool
   */
  public static function is_logged_in( ) {
    if( isset($_SESSION['attogram_id']) && $_SESSION['attogram_id'] && isset($_SESSION['attogram_username']) && $_SESSION['attogram_username']) {
      return TRUE;
    }
    return FALSE;
  }

} // end class attogram_user
