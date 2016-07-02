<?php // Attogram Framework - Events log v0.0.1

namespace Attogram;

$this->page_header('Event Log');
print '<div class="container">';

$e = $this->db->query('SELECT * FROM event ORDER BY time DESC');

foreach( $e as $v ) {
  print '<pre>' . print_r( $v, 1 ) . '</pre>';
}

print '</div>';
$this->page_footer();
