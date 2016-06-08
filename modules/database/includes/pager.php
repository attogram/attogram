<?php // Attogram Framework - Database Module - Pager v0.0.1

namespace Attogram;

/**
 * pager() - pagination
 * @param int $count
 * @param int $limit
 * @param int $offset
 * @return string  HTML fragment
 */
function pager( int $count, int $limit, int $offset ) {
  $r = '';

  $r .= "Showing $limit of $count results";
  if( $offset ) {
    $r .= ', starting at #' . ($offset+1);
  }

  $r .= '
<ul class="pagination">
 <li><a href="?l=' . $limit . '&o=0" class="active">1</a></li>
 <li><a href="?l=' . $limit . '&o=' . $limit . '" class="active">2</a></li>
 <li><a href="#">3</a></li>
 <li><a href="#">4</a></li>
 <li><a href="#">5</a></li>
</ul>';

  return "<p>$r</p>";
}
