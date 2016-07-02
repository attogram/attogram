<?php // Attogram Framework - Events log v0.0.3

namespace Attogram;

$this->page_header('Event Log');
print '<div class="container"><h1>Event Log</h1>';
print '<p>last 1000 events:</p>';

$e = $this->db->query('SELECT * FROM event ORDER BY id DESC LIMIT 1000');

foreach( $e as $v ) {
  $vm = explode( ' ', $v['message'] );
  $datetime = ltrim( $vm[0], '[' ) . ' ' . rtrim( $vm[1], ']');
  $type = rtrim( $vm[2], ':' );
  $type = preg_replace('/^event\./', '', $type);
  $trash = array_shift($vm); $trash = array_shift($vm); $trash = array_shift($vm);
  $message = implode(' ', $vm);
  $message = rtrim($message); $message = rtrim($message, '[..]'); $message = rtrim($message);
  $message = rtrim($message); $message = rtrim($message, '[..]'); $message = rtrim($message);

  print '
  <div class="row" style="border:1px solid #ccc;">
   <div class="col-sm-2"><small>' . $datetime . '</small></div>
   <div class="col-sm-1"><small>' . $type . '</small></div>
   <div class="col-sm-9">' . $this->web_display($message) . '</div>
  </div>
  ';

}

print '</div>';
$this->page_footer();
