<?php // Attogram Framework - Events log v0.1.1

namespace Attogram;

list( $limit, $offset ) = $this->db->get_set_limit_and_offset(
  $default_limit  = 1000,
  $default_offset = 0,
  $max_limit      = 10000,
  $min_limit      = 10
);

$sql = 'SELECT * FROM event ORDER BY id DESC LIMIT ' . $limit;
if( $offset ) {
  $sql .= ' OFFSET ' . $offset;
}

$e = $this->db->query($sql);

$this->page_header('⌚ Event Log');

print '<div class="container"><h1 class="squished">⌚ Event Log</h1>';

print $this->db->pager(
  $this->db->get_table_count('event'),
  $limit,
  $offset,
  $prepend_query_string = ''
);

foreach( $e as $v ) {
  $vm = explode( ' ', $v['message'] );
  $datetime = ltrim( $vm[0], '[' ) . ' ' . rtrim( $vm[1], ']');
  $type = rtrim( $vm[2], ':' );
  $type = preg_replace('/^event\./', '', $type);
  $trash = array_shift($vm); $trash = array_shift($vm); $trash = array_shift($vm);
  $message = implode(' ', $vm);
  $message = rtrim($message); $message = rtrim($message, '[..]'); $message = rtrim($message);
  $message = rtrim($message); $message = rtrim($message, '[..]'); $message = rtrim($message);

  print '<div class="row" style="border:1px solid #ccc;">'
  . '<div class="col-sm-2"><small>' . $datetime . '</small></div>'
  . '<div class="col-sm-1"><small>' . $type . '</small></div>'
  . '<div class="col-sm-9">' . $this->web_display($message) . '</div>'
  . '</div>';

}

print '</div>';
$this->page_footer();
