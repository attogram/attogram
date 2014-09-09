<?php
// Attogram - plugin - test

class plugin_test { 

  function is_active() { return false; } 

  function __construct($attogram='') { $this->attogram = $attogram; }

  function hook($hook) { 
    switch($hook) { 
      default: print '<pre>plugin_test:hook(' . $hook . ')</pre>'; break;
    } 
  } 

} // END of class plugin_test
