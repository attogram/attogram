<?php
// Attogram - plugin - timer

namespace Attogram;

class plugin_timer {
 
  var $start;

  function is_active() { return true; }

  function __construct($attogram='') { }

  function hook($hook) { 
    switch($hook) { 
      case 'PRE-INIT': $this->start_timer(); break;
      case 'POST-FOOTER': $this->end_timer(); break;
    } 
  } 

  function start_timer() {
    $this->start = microtime(1);
  }
 
  function end_timer() {
    $end = microtime(1);
    $diff = round( $end - $this->start, 18);
    print 'Page generated in ' . round($diff,3) . ' seconds';
  }

} // END of class plugin_timer