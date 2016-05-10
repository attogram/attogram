<?php
// Attogram - plugin - error

namespace Attogram;

class plugin_error {

  var $attogram;

  function is_active() { return true; }

  function __construct($attogram='') { $this->attogram = $attogram; } 

  function hook($hook) {
    if( !preg_match('/^ERROR/', $hook) ) { return; } // only do ERROR hooks
    print '<pre>' . $hook . ': ' . @$this->attogram->error . '</pre>';
    if( is_object($this->attogram->sqlite_database->db) ) {
      $ei = @$this->attogram->sqlite_database->db->errorInfo();
      if( isset($ei[0]) && $ei[0] != '0000') {
        print '<pre>' . $hook . ': SQLSTATE:' . @$ei[0]
        . ' code:' . @$ei[1] . ' message:' . @$ei[2] . '</pre>';
      }
    }
  }

} // END of class plugin_error