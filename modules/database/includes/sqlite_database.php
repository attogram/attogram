<?php  // Attogram Framework - Database Module - sqlite_database class v0.0.2

namespace Attogram;

/**
 * Attogram sqlite_database
 */
class sqlite_database extends attogram_utils
{

  public $db_name, $modules_directory, $db;

  /**
   * __construct() - initialize database settings
   *
   * @param string $db_name relative path to the SQLite database file
   * @param string $modules_directory relative path to the Attogram modules directory
   * @param object $log psr3 logger object
   * @param bool $debug (optional) defaults to FALSE
   *
   * @return void
   */
  function __construct( $db_name, $modules_directory, $log, $debug=FALSE ) {
    parent::__construct( $log );
    $this->debug = $debug;
    $this->modules_directory = $modules_directory;
    $this->db_name = $db_name;
  }

  /**
   * get_db() - Get the SQLite database object
   *
   * @return boolean
   */
  function get_db() {
    if( is_object($this->db) && get_class($this->db) == 'PDO' ) {
      return TRUE; // if PDO database object already set
    }
    if( !in_array('sqlite', \PDO::getAvailableDrivers() ) ) {
      $this->log->error('GET_DB: SQLite PDO driver not found');
      return FALSE;
    }
    if( is_file( $this->db_name ) && !is_writeable( $this->db_name ) ) {
      $this->log->error('GET_DB: NOTICE: database file not writeable: ' . $this->db_name);
      // SELECT will work, UPDATE will not work
    }
    if( !is_file( $this->db_name ) ) {
      $this->log->debug('GET_DB: NOTICE: creating database file: ' . $this->db_name);
    }
    try {
      $this->db = new \PDO('sqlite:'. $this->db_name);
    } catch(PDOException $e) {
      $this->log->error('GET_DB: error opening database');
      return FALSE;
    }
    $this->log->debug("GET_DB: Got SQLite database: $this->db_name");
    return TRUE; // got database, into $this->db
  }

  /**
   * query() - Query the database, return an array of results
   *
   * @param string $sql The SQL query
   * @param array $bind Optional, Array of values to bind into the SQL query
   *
   * @return array
   */
  function query( $sql, $bind=array() ) {
    $this->log->debug('QUERY: backtrace=' . ( ($btr = debug_backtrace()) ? $btr[1]['function'] : '?' ) . ' sql=' . $sql);
    if( $bind ) {
      $this->log->debug('QUERY: bind=',$bind);
    }
    if( !$this->get_db() ) {
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
      // possible:  Warning: PDOStatement::bindParam(): SQLSTATE[HY093]: Invalid parameter number: Columns/Parameters are 1-based
    }
    //$this->log->debug('QUERY: bound:', $bind);
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
   * queryb() - Query the database, return only TRUE or FALSE
   *
   * @param string $sql The SQL query
   * @param array $bind Optional, Array of values to bind into the SQL query
   *
   * @return boolean
   */
  function queryb( $sql, $bind=array() ) {
    $this->log->debug('QUERYB: backtrace=' . ( ($btr = debug_backtrace()) ? $btr[1]['function'] : '?' ) . ' sql=' . $sql);
    if( $bind ) {
      $this->log->debug('QUERYB: bind=',$bind);
    }
    if( !$this->get_db() ) {
      $this->log->error('QUERYB: Can not get database');
      return FALSE;
    }
    $statement = $this->query_prepare($sql);
    if( !$statement ) {
      list($sqlstate, $error_code, $error_string) = @$this->db->errorInfo();
      $this->log->error("QUERYB: prepare failed: $sqlstate:$error_code:$error_string");
      return FALSE;
    }
    while( $x = each($bind) ) {
      $statement->bindParam($x[0], $x[1]);
    }
    //$this->log->debug('QUERYB: bound:', $bind);
    if( !$statement->execute() ) {
      list($sqlstate, $error_code, $error_string) = @$this->db->errorInfo();
      $this->log->error("QUERYB: execute failed: $sqlstate:$error_code:$error_string");
      return FALSE;
    }
    $this->log->debug('QUERYB TRUE');
    return TRUE;
   }

  /**
   * query_prepare()
   *
   * @param string $sql The SQL query to prepare
   *
   * @return object|boolean
   */
  function query_prepare( $sql ) {
    //$this->log->debug("QUERY_PREPARE: $sql");
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
        return FALSE;
      } else {
        $this->log->error("QUERY_PREPARE: Can not create table: $table");
        return FALSE;
      }
    }
  }

  /**
   * get_tables()
   *
   * @return boolean
   */
  function get_tables() {
    if( isset($this->tables) && is_array($this->tables) ) {
      return TRUE;
    }
    $dirs = $this->get_all_subdirectories( $this->modules_directory, 'tables');
    //$this->log->debug('GET_TABLES', $dirs);
    if( !$dirs ) {
      $this->log->debug('GET_TABLES: No module tables found');
      return FALSE;
    }
    $this->tables = array();
    foreach( $dirs as $d ) {
      //$this->log->debug('GET_TABLES: d='. $d);
      foreach( array_diff(scandir($d), $this->skip_files) as $f ) {
        $file = $d . '/' . $f;
        if( !is_file($file) || !is_readable($file) || !preg_match('/\.sql$/',$file) ) {
          continue; // .sql files only
        }
        $table_name = str_replace('.sql','',$f);
        $this->tables[$table_name] = file_get_contents($file);
        $this->log->debug('GET_TABLES: got table: ' . $table_name . ' from ' . $file);
      }
    }
    return TRUE;
  }

  /**
   * create_table() - Create a table in the active SQLite database
   *
   * @param string $table The name of the table to create
   *
   * @return boolean
   */
  function create_table( $table='' ) {
    $this->get_tables();
    if( !isset($this->tables[$table]) ) {
      $this->log->error("CREATE_TABLE: Unknown table: $table");
      return FALSE;
    }
    if( !$this->queryb( $this->tables[$table] ) ) {
      $this->log->error("CREATE_TABLE: failed to create: $table");
      return FALSE;
    }
    return TRUE;
  }

} // END of class sqlite_database
