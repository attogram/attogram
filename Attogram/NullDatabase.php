<?php
// Attogram Framework - NullDatabase class 0.1.4

namespace Attogram;

/**
 * Attogram Null Database
 */
class NullDatabase implements AttogramDatabaseInterface
{

    /**
     * Query the database, return an array of results
     * @param  string $sql  The SQL query
     * @param  array  $bind (optional) Array of name/values to bind into the SQL query
     * @return array        An array of results
     */
    public function query($sql, array $bind = array())
    {
        return false;
    }

    /**
     * Query the database, return only true or false
     * @param  string $sql  The SQL query
     * @param  array  $bind (optional) Array of name/values to bind into the SQL query
     * @return bool         true on successful query, false on error
     */
    public function queryb($sql, array $bind = array())
    {
        return false;
    }

    /**
     * Initialize the database connection
     * @return bool true on successful initialization, false on error
     */
    public function initDB()
    {
        return false;
    }

    /**
     * Get the count of entries in a table
     * @param string $table    The table name
     * @param string $idField  (optional) The id field used for counting
     * @param string $where    (optional) The SQL WHERE clause to add
     * @return int             The number of entries
     */
    public function getTableCount($table, $idField = 'id', $where = '')
    {
        return 0;
    }

    /**
     * Create a table in the active SQLite database
     * @param string $table The name of the table to create
     * @return boolean
     */
    public function createTable($table)
    {
        return false;
    }

    /**
     * tabler - HTML table with view of database table content, plus optional admin links
     * @param  string $table         The table name
     * @param  string $tableId      The name of the table ID field (or equivilant )
     * @param  string $nameSingular The name of what we are editing, singular form
     * @param  string $namePlural   The name of what we are editing, plural form
     * @param  array  $col           Column Display Info - array of array('class'=>'..', 'title'=>'..', 'key'=>'..')
     * @param  string $sql           SQL query to view contents of table
     * @param  string $countSql     SQL query to get total number of items in table
     * @param  string $publicLink   URL to the public version of this view
     * @param  string $adminLink    URL to the admin version of this view
     * @param  bool   $showEdit     Show edit tools
     * @param  int    $perPage      (optional) The number of results to show per page. Defaults to 50
     * @return string                HTML fragment
     */
    public function tabler(
        $table,
        $tableId,
        $nameSingular,
        $namePlural,
        $publicLink,
        array $col,
        $sql,
        $adminLink,
        $showEdit,
        $perPage
    ) {
        print 'tabler disabled';
    }

    /**
     * Show pagination links
     * @param  int    $count   The Total Resultset Count
     * @param  int    $limit   The # of results to list per page
     * @param  int    $offset  The item # to start the list
     * @param  string $preQS  (optional) URL Query String to prepend to pagination links,
    *                                    pairs of  name=value&name=value&...
     * @return string          HTML fragment
     */
    public function pager($count, $limit, $offset, $preQS = '')
    {
        print 'pager disabled';
    }

    /**
     * Get requested Query limit and offset from HTTP GET variables,
     * error check, and then return valid limit and offset
     * @param  int    $defaultLimit  (optional) The default limit, if not set.   Defaults to 1000
     * @param  int    $defaultOffset (optional) The default offset, if not set.  Defaults to 0
     * @param  int    $maxLimit      (optional) The maximum allowed limit value. Defaults to 5000
     * @param  int    $minLimit      (optional) The minimum allowed limit value. Defaults to 100
     * @return array                  Array of (limit,offset)
     */
    public function getSetLimitAndOffset(
        $defaultLimit = 1000,
        $defaultOffset = 0,
        $maxLimit = 5000,
        $minLimit = 100
    ) {
        return array(0,0);
    }
} // end class NullDatabase
