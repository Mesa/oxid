<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

require_once __DIR__ . DIRECTORY_SEPARATOR . 'ResultSetInterface.php';

/**
 * The database connection interface specifies how a database connection should look and act.
 */
interface DatabaseInterface
{

    /**
     * The default fetch mode as implemented by the database driver, in Doctrine this is usually FETCH_MODE_BOTH
     *
     * @deprecated since 6.0 (2016-04-19); This constant is confusing as the shop uses a different default fetch mode.
     */
    const FETCH_MODE_DEFAULT = 0;

    /**
     * Fetch the query result into an array with integer keys.
     * This is the default fetch mode as it is set by OXID eShop on opening a database connection.
     */
    const FETCH_MODE_NUM = 1;

    /** Fetch the query result into an array with string keys */
    const FETCH_MODE_ASSOC = 2;

    /** Fetch the query result into a mixed array with both integer and string keys */
    const FETCH_MODE_BOTH = 3;

    /**
     * Force database master connection.
     *
     * Hint: this method is here to have an easier update path. It will be implemented in the OXID eShop version 6.0.
     */
    public function forceMasterConnection();

    /**
     * Set the fetch mode of an open database connection.
     *
     * After the connection has been opened, this method may be used to set the fetch mode to any of the valid fetch
     * modes as defined in DatabaseInterface::FETCH_MODE_*
     *
     * NOTE: This implies, that it is not safe to make any assumptions about the current fetch mode of the connection.
     *
     * @param int $fetchMode See DatabaseInterface::FETCH_MODE_* for valid values
     */
    public function setFetchMode($fetchMode);

    /**
     * Get the first value of the first row of the result set of a given sql SELECT or SHOW statement.
     * Returns false for any other statement.
     *
     * NOTE: Although you might pass any SELECT or SHOW statement to this method, try to limit the result of the
     * statement to one single row, as the rest of the rows is simply discarded.
     *
     * @param string $query          The sql SELECT or SHOW statement.
     * @param array  $parameters     Array of parameters for the given sql statement.
     * @param bool   $executeOnSlave Execute this statement on the slave database. Only evaluated in a master-slave setup.
     *                               This parameter is deprecated since v5.3.0 (2016-06-17). Different solution in 6.0.
     *
     * @return string|false          Returns a string for SELECT or SHOW statements and FALSE for any other statement.
     */
    public function getOne($query, $parameters = array(), $executeOnSlave = true);

    /**
     * Get an array with the values of the first row of a given sql SELECT or SHOW statement .
     * Returns an empty array for any other statement.
     *
     * The keys of the array may be numeric, strings or both, depending on the FETCH_MODE_* of the connection.
     * Set the desired fetch mode with DatabaseInterface::setFetchMode() before calling this method.
     *
     * NOTE: Although you might pass any SELECT or SHOW statement to this method, try to limit the result of the
     * statement to one single row, as the rest of the rows is simply discarded.
     *
     * IMPORTANT:
     * You are strongly encouraged to use prepared statements like this:
     * $result = DatabaseInterfaceImplementation::getDb->getOne(
     *   'SELECT `id` FROM `mytable` WHERE `id` = ? LIMIT 0, 1',
     *   array($id1)
     * );
     * If you will not use prepared statements, you MUST quote variables the values with quote(), otherwise you create a
     * SQL injection vulnerability.
     *
     *
     * @param string $sqlSelect      The sql select statement we want to execute.
     * @param array  $parameters     Array of parameters, for the given sql statement.
     * @param bool   $executeOnSlave Execute this statement on the slave database. Only evaluated in a master-slave setup.
     *                               This parameter is deprecated since v5.3.0 (2016-06-17). Different solution in 6.0.
     *
     * @return array
     */
    public function getRow($sqlSelect, $parameters = array(), $executeOnSlave = true);

    /**
     * Return the first column of all rows of the results of a given sql SELECT or SHOW statement as an numeric array.
     * Throws an exception for any other statement.
     *
     * IMPORTANT:
     * You are strongly encouraged to use prepared statements like this:
     * $result = DatabaseInterfaceImplementation::getDb->getRow(
     *   'SELECT * FROM `mytable` WHERE `id` = ? LIMIT 0, 1',
     *   array($id1)
     * );
     * If you will not use prepared statements, you MUST quote variables the values with quote(), otherwise you create a
     * SQL injection vulnerability.
     *
     * @param string $sqlSelect      The sql select statement
     * @param array  $parameters     The parameters array.
     * @param bool   $executeOnSlave Execute this statement on the slave database. Only evaluated in a master-slave setup.
     *                               This parameter is deprecated since v5.3.0 (2016-06-17). Different solution in 6.0.
     *
     * @return array The values of the first column of a corresponding sql query.
     */
    public function getCol($sqlSelect, $parameters = array(), $executeOnSlave = true);

    /**
     * Get an multi-dimensional array of arrays with the values of the all rows of a given sql SELECT or SHOW statement.
     * Returns an empty array for any other statement.
     *
     * The keys of the first level array are numeric.
     * The keys of the second level arrays may be numeric, strings or both, depending on the FETCH_MODE_* of the connection.
     * Set the desired fetch mode with DatabaseInterface::setFetchMode() before calling this method.
     *
     * IMPORTANT:
     * You are strongly encouraged to use prepared statements like this:
     * $result = DatabaseInterfaceImplementation::getDb->getAll(
     *   'SELECT * FROM `mytable` WHERE `id` = ? OR `id` = ? LIMIT 0, 1',
     *   array($id1, $id2)
     * );
     * If you will not use prepared statements, you MUST quote variables the values with quote(), otherwise you create a
     * SQL injection vulnerability.
     *
     * @param string $query          If parameters are given, the "?" in the string will be replaced by the values in the array
     * @param array  $parameters     Array of parameters, for the given sql statement.
     * @param bool   $executeOnSlave Execute this statement on the slave database. Only evaluated in a master-slave setup.
     *                               This parameter is deprecated since v5.3.0 (2016-06-17). Different solution in 6.0.
     *
     * @see DatabaseInterface::setFetchMode()
     * @see Doctrine::$fetchMode
     *
     * @return array
     */
    public function getAll($query, $parameters = array(), $executeOnSlave = true);

    /**
     * Return the results of a given sql SELECT or SHOW statement as a ResultSet.
     * Throws an exception for any other statement.
     *
     * The values of first row of the result may be via resultSet's fields property.
     * This property is an array, which keys may be numeric, strings or both, depending on the FETCH_MODE_* of the connection.
     * All further rows can be accessed via the specific methods of ResultSet.
     *
     * IMPORTANT:
     * You are strongly encouraged to use prepared statements like this:
     * $resultSet = DatabaseInterfaceImplementation::getDb->select(
     *   'SELECT * FROM `mytable` WHERE `id` = ? OR `id` = ?',
     *   array($id1, $id2)
     * );
     * If you will not use prepared statements, you MUST quote variables the values with quote(), otherwise you create a
     * SQL injection vulnerability.
     *
     * @param string $sqlSelect      The sql select statement
     * @param array  $parameters     The parameters array.
     * @param bool   $executeOnSlave Execute this statement on the slave database. Only evaluated in a master-slave setup.
     *                               This parameter is deprecated since v5.3.0 (2016-06-17). Different solution in 6.0.
     * @throws Exception The exception, that can occur while executing the sql statement.
     *
     * @return object   The result of the given query. @deprecated since v5.3.0 (2016-06-16) This method will return an
     *                  instance of ResultSetInterface in v6.0.
     */
    public function select($sqlSelect, $parameters = array(), $executeOnSlave = true);

    /**
     * Return the results of a given sql SELECT or SHOW statement limited by a LIMIT clause as a ResultSet.
     * Throws an exception for any other statement.
     *
     * The values of first row of the result may be via resultSet's fields property.
     * This property is an array, which keys may be numeric, strings or both, depending on the FETCH_MODE_* of the connection.
     * All further rows can be accessed via the specific methods of ResultSet.
     *
     * IMPORTANT:
     * You are strongly encouraged to use prepared statements like this:
     * $resultSet = DatabaseInterfaceImplementation::getDb->selectLimit(
     *   'SELECT * FROM `mytable` WHERE `id` = ? OR `id` = ?',
     *   $rowCount,
     *   $offset,
     *   array($id1, $id2)
     * );
     * If you will not use prepared statements, you MUST quote variables the values with quote(), otherwise you create a
     * SQL injection vulnerability.
     *
     * @param string $sqlSelect      The sql select statement
     * @param int    $rowCount       Maximum number of rows to return
     * @param int    $offset         Offset of the first row to return.
     *                               The current default value of -1 is @deprecated since v5.3.3 (2017-03-28).
     *                               The default value in V6.0 is zero.
     * @param array  $parameters     The parameters array.
     * @param bool   $executeOnSlave Execute this statement on the slave database. Only evaluated in a master-slave setup.
     *                               This parameter is deprecated since v5.3.0 (2016-06-17). Different solution in 6.0.
     *
     * @throws Exception The exception, that can occur while executing the sql statement.
     *
     * @return object   The result of the given query. @deprecated since v5.3.0 (2016-06-16) This method will return an
     *                  instance of ResultSetInterface in v6.0.
     */
    public function selectLimit($sqlSelect, $rowCount = -1, $offset = -1, $parameters = array(), $executeOnSlave = true);

    /**
     * Execute non read statements like INSERT, UPDATE, DELETE and return the number of rows affected by the statement.
     *
     * Execute read statements like SELECT or SHOW and return the results as a ResultSet.
     * (This behavior is deprecated since v5.3.0 (2016-06-06) This method has to be used EXCLUSIVELY for non read
     * statements in v6.0)
     *
     * IMPORTANT:
     * You are strongly encouraged to use prepared statements like this:
     * $resultSet = DatabaseInterfaceImplementation::getDb->execute(
     *   'DELETE * FROM `mytable` WHERE `id` = ? OR `id` = ?',
     *   array($id1, $id2)
     * );
     * If you will not use prepared statements, you MUST quote variables the values with quote(), otherwise you create a
     * SQL injection vulnerability.
     *
     * @param string $query      The sql statement we want to execute.
     * @param array  $parameters The parameters array.
     *
     * @return object @deprecated since v5.3.0 (2016-06-06) This method will return an integer as the number of rows
     *                affected by the statement for non read statements. An exception will be thrown, if a read statement
     *                is passed to this function.
     */
    public function execute($query, $parameters = array());

    /**
     * Quote a string or a numeric value in a way, that it might be used as a value in a sql statement.
     * Returns false for values that cannot be quoted.
     *
     * NOTE: It is not safe to use the return value of this function in a query. There will be no risk of SQL injection,
     * but when the statement is executed and the value could not have been quoted, a DatabaseException is thrown.
     * You are strongly encouraged to always use prepared statements instead of quoting the values on your own.
     * E.g. use
     * $resultSet = DatabaseInterfaceImplementation::getDb->select(
     *   'SELECT * FROM `mytable` WHERE `id` = ? OR `id` = ?',
     *   array($id1, $id2)
     * );
     * instead of
     * $resultSet = DatabaseInterfaceImplementation::getDb->select(
     *  'SELECT * FROM `mytable` WHERE `id` = ' . DatabaseInterfaceImplementation::getDb->quote($id1) . ' OR `id` = ' . DatabaseInterfaceImplementation::getDb->quote($id1)
     * );
     *
     * @param mixed $value The string or numeric value to be quoted.
     *
     * @return false|string The given string or numeric value converted to a string surrounded by single quotes or set to false, if the value could not have been quoted.
     */
    public function quote($value);

    /**
     * Quote every value in a given array in a way, that it might be used as a value in a sql statement and return the
     * result as a new array. Numeric values will be converted to strings which quotes.
     * The keys and their order of the returned array will be the same as of the input array.
     *
     * NOTE: It is not safe to use the return value of this function in a query. There will be no risk of SQL injection,
     * but when the statement is executed and the value could not have been quoted, a DatabaseException is thrown.
     * You are strongly encouraged to always use prepared statements instead of quoting the values on your own.
     *
     * @param array $array The strings to quote as an array.
     *
     * @return array Array with all string and numeric values quoted with single quotes or set to false, if the value could not have been quoted.
     */
    public function quoteArray($array);

    /**
     * Return the meta data for the columns of a table.
     *
     * @param string $table The name of the table.
     *
     * @return array The meta information about the columns.
     */
    public function metaColumns($table);

    /**
     * Start a database transaction.
     *
     * @throws Exception
     */
    public function startTransaction();

    /**
     * Commit a database transaction.
     *
     * @throws Exception
     */
    public function commitTransaction();

    /**
     * RollBack a database transaction.
     *
     * @throws Exception
     */
    public function rollbackTransaction();

    /**
     * Set the transaction isolation level.
     * Allowed values 'READ UNCOMMITTED', 'READ COMMITTED', 'REPEATABLE READ' and 'SERIALIZABLE'.
     *
     * NOTE: Currently the transaction isolation level is set on the database session and not globally.
     * Setting the transaction isolation level globally requires root privileges in MySQL an this application should not
     * be executed with root privileges.
     * If you need to set the transaction isolation level globally, ask your database administrator to do so,
     *
     * @param string $level The transaction isolation level
     *
     * @throws Exception
     */
    public function setTransactionIsolationLevel($level);
}
