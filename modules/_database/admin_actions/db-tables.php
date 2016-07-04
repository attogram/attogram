<?php // Attogram Framework - Database Tables v0.0.2

namespace Attogram;

$title = 'Database Tables';
$this->page_header($title );
print '<div class="container"><h1 class="squished">' . $title . '</h1><hr />';

if( !$this->db->get_tables() || !$this->db->tables ) {
  print 'ERROR: no table definitions found.</div>';
  $this->page_footer();
  exit;
}

foreach( $this->db->tables AS $table_name => $table_definition ) {
  $count = $this->db->get_table_count( $table_name );
  print '<p>'
  . "<strong>$table_name</strong> table: <code>$count</code> entries"
  . '<textarea class="form-control" rows="8">' . $table_definition . '</textarea>'
  . '</p>';
}

print '</div>';
$this->page_footer();
