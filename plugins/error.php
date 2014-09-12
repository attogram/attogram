<?php
// Attogram - plugin - error

class plugin_error { 

  var $attogram;
  
  function is_active() { return true; } 

  function __construct($attogram='') { $this->attogram = $attogram; } 

  function hook($hook) { 
    if( $hook == 'POST-ADMIN' ) { print '<br />plugin_error: ' . ($this->is_active() ? 'ACTIVE' : 'DISABLED'); return; }  
    if( !preg_match('/^ERROR/', $hook) ) { return; } 
    switch( $hook ) {
      case 'ERROR-QUERY':
        $ei = @$this->attogram->db->errorInfo();
        print '<pre>ERROR-QUERY: ' . @$this->attogram->db->errorCode() . ' ' . $ei[2] . '</pre>';
        break;
      default:
        print '<pre>' . $hook . '</pre>';
        break;
    }
  }

} // END of class plugin_error
