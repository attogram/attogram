<?php  // Attogram Framework - Database Module - sqlite_database class v0.1.0

namespace Attogram;

/**
 * Attogram Database Object Interface
 */
interface attogram_database
{

  /**
   * Query the database, return an array of results
   * @param  string $sql  The SQL query
   * @param  array  $bind (optional) Array of name/values to bind into the SQL query
   * @return array        An array of results
   */
  public function query( $sql, $bind = array() );

  /**
   * Query the database, return only true or false
   * @param  string $sql  The SQL query
   * @param  array  $bind (optional) Array of name/values to bind into the SQL query
   * @return bool         true on successful query, false on error
   */
  public function queryb( $sql, $bind = array() );

  /**
   * Initialize the database connection
   * @return bool true on successful initialization, false on error
   */
  public function init_db();

} // end interface attogram_database


/**
 * Attogram sqlite_database
 */
class sqlite_database implements attogram_database
{

  public $db_name;
  public $modules_directory;
  public $log;
  public $debug;
  public $db;

  /**
   * initialize database settings
   * @param string $db_name relative path to the SQLite database file
   * @param string $modules_directory relative path to the Attogram modules directory
   * @param object $log psr3 logger object
   * @param bool $debug (optional) defaults to false
   * @return void
   */
  public function __construct( $db_name, $modules_directory, $log, $debug = false )
  {
    $this->db_name = $db_name;
    $this->modules_directory = $modules_directory;
    $this->log = $log;
    $this->debug = $debug;
    $this->log->debug('START sqlite_database');
  }

  /**
   * Initialize the database connection
   * @return bool true on successful initialization, false on error
   */
  public function init_db()
  {
    if( is_object($this->db) && get_class($this->db) == 'PDO' ) {
      return true; // if PDO database object already set
    }
    if( !in_array('sqlite', \PDO::getAvailableDrivers() ) ) {
      $this->log->error('init_db: SQLite PDO driver not found');
      return false;
    }
    if( is_file( $this->db_name ) && !is_writeable( $this->db_name ) ) {
      $this->log->error('init_db: NOTICE: database file not writeable: ' . $this->db_name);
      // SELECT will work, UPDATE will not work
    }
    if( !is_file( $this->db_name ) ) {
      $this->log->debug('init_db: NOTICE: creating database file: ' . $this->db_name);
    }
    try {
      $this->db = new \PDO('sqlite:'. $this->db_name);
    } catch(\PDOException $e) {
      $this->log->error('init_db: error opening database: ' . $e->getMessage());
      return false;
    }
    $this->log->debug("init_db: Got SQLite database: $this->db_name");
    return true; // got database, into $this->db
  }

  /**
   * Query the database, return an array of results
   * @param  string $sql  The SQL query
   * @param  array  $bind (optional) Array of name/values to bind into the SQL query
   * @return array        An array of results
   */
  public function query( $sql, $bind = array() )
  {
    $this->log->debug('QUERY: backtrace=' . ( ($btr = debug_backtrace()) ? $btr[1]['function'] : '?' ) . ' sql=' . $sql);
    if( $bind ) {
      $this->log->debug('QUERY: bind=',$bind);
    }
    if( !$this->init_db() ) {
      $this->log->error('QUERY: Can not get database');
      return array();
    }
    $statement = $this->query_prepare($sql);
    if( !$statement ) {
      list($sqlstate, $error_code, $error_string) = @$this->db->errorInfo();
      $this->log->error("QUERY: prepare failed: $sqlstate:$error_code:$error_string");
      return array();
    }
    while( $x = each($bind) ) {
      $statement->bindParam( $x[0], $x[1] );
      // dev: Warning: PDOStatement::bindParam(): SQLSTATE[HY093]: Invalid parameter number: Columns/Parameters are 1-based
    }
    if( !$statement->execute() ) {
      $this->log->error('QUERY: Can not execute query');
      return array();
    }
    $r = $statement->fetchAll(\PDO::FETCH_ASSOC);
    if( !$r && $this->db->errorCode() != '00000') { // query failed
      $this->log->error('QUERY: Query failed');
      $r = array();
    }
    $this->log->debug('QUERY: result size=' . sizeof($r) );
    return $r;
  }

  /**
   * Query the database, return only true or false
   * @param  string $sql  The SQL query
   * @param  array  $bind (optional) Array of name/values to bind into the SQL query
   * @return bool         true on successful query, false on error
   */
  public function queryb( $sql, $bind = array() )
  {
    $this->log->debug('QUERYB: backtrace=' . ( ($btr = debug_backtrace()) ? $btr[1]['function'] : '?' ) . ' sql=' . $sql);
    if( $bind ) {
      $this->log->debug('QUERYB: bind=',$bind);
    }
    if( !$this->init_db() ) {
      $this->log->error('QUERYB: Can not get database');
      return false;
    }
    $statement = $this->query_prepare($sql);
    if( !$statement ) {
      list($sqlstate, $error_code, $error_string) = @$this->db->errorInfo();
      $this->log->error("QUERYB: prepare failed: $sqlstate:$error_code:$error_string");
      return false;
    }
    while( $x = each($bind) ) {
      $statement->bindParam($x[0], $x[1]);
    }
    if( !$statement->execute() ) {
      list($sqlstate, $error_code, $error_string) = @$this->db->errorInfo();
      $this->log->error("QUERYB: execute failed: $sqlstate:$error_code:$error_string");
      return false;
    }
    $this->log->debug('QUERYB true');
    return true;
   }

  /**
   * Prepare a SQL query
   * @param string $sql The SQL query to prepare
   * @return object|boolean
   */
  public function query_prepare( $sql )
  {
    $statement = $this->db->prepare($sql);
    if( $statement ) { return $statement; }
    list($sqlstate, $error_code, $error_string) = @$this->db->errorInfo();
    $this->log->error("QUERY_PREPARE: Can not prepare sql: $sqlstate:$error_code:$error_string");
    if( $sqlstate == 'HY000' && $error_code == '1' && preg_match('/^no such table/', $error_string) ) { // table not found
      $table = str_replace('no such table: ', '', $error_string); // get table name
      if( $this->create_table($table) ) { // create table
        $this->log->notice("QUERY_PREPARE: Created table: $table");
        $statement = $this->db->prepare($sql);
        if( $statement ) { return $statement; } // try again
        $this->log->error('QUERY_PREPARE: Still can not prepare sql');
        return false;
      } else {
        $this->log->error("QUERY_PREPARE: Can not create table: $table");
        return false;
      }
    }
  }

  /**
   * Get the table definitions from all the modules
   * @return boolean
   */
  public function get_tables()
  {
    if( isset($this->tables) && is_array($this->tables) ) {
      return true;
    }
    $dirs = attogram_fs::get_all_subdirectories( $this->modules_directory, 'tables');
    if( !$dirs ) {
      $this->log->debug('GET_TABLES: No module tables found');
      return false;
    }
    $this->tables = array();
    foreach( $dirs as $d ) {
      foreach( array_diff(scandir($d), attogram_fs::get_skip_files() ) as $f ) {
        $file = $d . '/' . $f;
        if( !is_file($file) || !is_readable($file) || !preg_match('/\.sql$/',$file) ) {
          continue; // .sql files only
        }
        $table_name = str_replace('.sql','',$f);
        $this->tables[$table_name] = file_get_contents($file);
        $this->log->debug('GET_TABLES: got table: ' . $table_name . ' from ' . $file);
      }
    }
    return true;
  }

  /**
   * Create a table in the active SQLite database
   * @param string $table The name of the table to create
   * @return boolean
   */
  public function create_table( $table='' )
  {
    $this->get_tables();
    if( !isset($this->tables[$table]) ) {
      $this->log->error("CREATE_TABLE: Unknown table: $table");
      return false;
    }
    if( !$this->queryb( $this->tables[$table] ) ) {
      $this->log->error("CREATE_TABLE: failed to create: $table");
      return false;
    }
    return true;
  }

  /**
   * tabler - HTML table with view of database table content, plus optional admin links
   *
   * @param  string $table         The table name
   * @param  string $name_singular The name of what we are editing, singular form
   * @param  string $name_plural   The name of what we are editing, plural form
   * @param  array  $col           Column Display Info - array of array('class'=>'...', 'title'=>'...', 'key'=>'...')
   * @param  string $sql           SQL query to view contents of table
   * @param  string $count_sql     SQL query to get total number of items in table
   * @param  string $public_link   URL to the public version of this view
   * @param  string $admin_link    URL to the admin version of this view
   * @param  bool   $show_edit     Show edit tools
   *
   * @return string                HTML fragment
   */
  public function tabler( $table, $name_singular, $name_plural, $public_link, $col, $sql, $admin_link, $show_edit )
  {

    $result = $this->query($sql);

    if( $show_edit ) {
      $admin_create = '../db-admin/?table=' . $table .'&amp;action=row_create';
      $admin_edit = '../db-admin/?table=' . $table . '&amp;action=row_editordelete&amp;type=edit&amp;pk='; // [#ID]
      $admin_delete = '../db-admin/?table=' . $table . '&amp;action=row_editordelete&amp;type=delete&amp;pk='; // [#ID]
    } else {
      $admin_create = $admin_edit = $admin_delete = '';
    }

    print '<div class="container"><p>'
    . '<strong>' . count($result) . '</strong> <a href="">' . $name_plural . '</a>';

    if( $show_edit ) {
      print ' &nbsp; &nbsp; &nbsp; ';
      if( $public_link ) {
        print '<a href="' . $public_link . '"><span class="glyphicon glyphicon-user"></span> view</a> &nbsp; &nbsp; &nbsp; ';
      }
      print '<a target="_db" href="' . $admin_create . '"><span class="glyphicon glyphicon-plus"></span> '
      . 'new ' . $name_singular . '</a>';
    } else {
      //if( $attogram->is_admin() ) {
      //  print ' &nbsp; &nbsp; &nbsp; '
      //  . '<a href="' . $admin_link . '"><span class="glyphicon glyphicon-wrench"></span> Admin</a>';
      //}
    }

    print '</p><table class="table table-bordered table-hover table-condensed"><colgroup>';

    foreach( $col as $c ) {
      print '<col class="' . $c['class'] . '">';
    }
    if( $show_edit ) {
      print '<col class="col-md-1">';
    }
    print '</colgroup><thead><tr class="active">';

    foreach( $col as $c ) {
      print '<th>' . $c['title'] . '</th>';
    }
    if( $show_edit ) {
      print '<th><nobr><small>'
      . 'edit <span class="glyphicon glyphicon-wrench" title="edit"></span>'
      . ' &nbsp; '
      . '<span class="glyphicon glyphicon-remove-circle" title="delete"></span> delete'
      . '</small></nobr></th>';
    }
    print '</tr></thead><tbody>';

    foreach( $result as $row ) {
      print '<tr>';
      foreach( $col as $c ) {
        print '<td>' . htmlentities($row[ $c['key'] ]) . '</td>';
      }
      if( $show_edit ) {
        print '<td> &nbsp; &nbsp; '
        . '<a target="_db" href="' . $admin_edit . '[' . $row['id'] . ']">'
        . '<span class="glyphicon glyphicon-wrench" title="edit"></span></a>'
        . ' &nbsp; &nbsp; '
        . '<a target="_db" href="' . $admin_delete . '[' . $row['id'] . ']">'
        . '<span class="glyphicon glyphicon-remove-circle" title="delete"></span></a>'
        . '</td>'
        ;
      }
      print '</tr>';
    }
    print '</tbody></table></div>';
  }

  /**
   * Show pagination links
   * @param  int    $count   The Total Resultset Count
   * @param  int    $limit   The # of results to list per page
   * @param  int    $offset  The item# to start the list
   * @return string          HTML fragment
   */
  public function pager( $count, $limit, $offset )
  {

    if( $limit > $count ) {
      $limit = $count;
    }
    if( $offset > $count ) {
      $offset = $count - $limit;
    }
    $start_count = $offset + 1;
    $end_count = $offset + $limit;
    if( $end_count > $count ) {
      $end_count = $count;
    }

    $r = "<p>Showing #$start_count to #$end_count of <code>$count</code> results</p>";

    if( $limit <= 0 ) {
      $total_pages = 0;
    } else {
      $total_pages = ceil( $count / $limit );
      if( $total_pages == 1 ) {
        $total_pages = 0;
      }
    }

    if( $total_pages ) {
      $r .= '<ul class="pagination squished">';
      $p_offset = 0;
      for( $x = 0; $x < $total_pages; $x++ ) {
        if( $start_count == $p_offset + 1 ) {
          $active = ' class="active"';
        } else {
          $active = '';
        }
        $r .= '<li' . $active . '><a href="?l=' . $limit . '&o=' . $p_offset . '">' . ($x+1) . '</a></li>';
        $p_offset += $limit;
      }
      $r .= '</ul>';
    }

    return '<div class="container">' . $r . '</div>';
  }

  /**
   * Get requested limit and offset from URI, and set real limit and offset
   * @param  int    $default_limit  (optional) The default limit, if not set. Defaults to 1000
   * @param  int    $default_offset (optional) The default offset, if not set. Defaults to 0
   * @return array                  Array of (limit,offset)
   */
  public function get_set_limit_and_offset( $default_limit = 1000, $default_offset = 0 )
  {
    if( isset($_GET['l']) && $_GET['l'] ) { // LIMIT
      $limit = (int)$_GET['l'];
      if( isset($_GET['o']) && $_GET['o'] ) { // OFFSET
        $offset = (int)$_GET['o'];
      } else {
        $offset = 0;
      }
    } else {
      $limit = $default_limit;
      $offset = $default_offset;
    }
    return array( $limit, $offset );
  }

} // END of class sqlite_database
