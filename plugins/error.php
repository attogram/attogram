<?php
// Attogram - plugin - error

class plugin_error { 

  function is_active() { return true; } 

  function __construct($attogram='') { $this->attogram = $attogram; } 

  function hook($hook) { 
    if( $hook == 'POST-ADMIN' ) { print '<br />plugin_error: ' . ($this->is_active() ? 'ACTIVE' : 'DISABLED'); return; }  
    if( $hook == 'ERROR_QUERY' ) { 
      $ei = @$this->attogram->db->errorInfo();
      print '<pre>ERROR_QUERY: ' . @$this->attogram->db->errorCode() . ' ' . $ei[2] . '</pre>';
      return;
    } 
    if( !preg_match('/^ERROR/', $hook) ) { return; } 
    print '<pre>Attogram ERROR: ' . $hook . '</pre>';
  } 

} // END of class plugin_error
