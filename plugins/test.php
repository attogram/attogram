<?php
// Attogram - plugin - test

namespace Attogram;

class plugin_test {

  function is_active() { return false; }

  function __construct($attogram='') { }

  function hook($hook) {
    print '<pre>plugin_test:hook(' . $hook . ')</pre>';
  }

} // END of class plugin_test