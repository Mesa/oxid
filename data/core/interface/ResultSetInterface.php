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

namespace OxidEsales\Eshop\Core\Database\Adapter;

/**
 * Interface ResultSetInterface
 */
interface ResultSetInterface extends \Traversable, \Countable
{

    /**
    * Closes the cursor, enabling the statement to be executed again.
    *
    * @return boolean Returns true on success or false on failure.
    */
    public function close();

    /**
     * Returns an array containing all of the result set rows
     *
     * @return array
     */
    public function fetchAll();

    /**
     * Returns the next row from a result set.
     *
     * @return mixed The return value of this function on success depends on the fetch type.
     *               In all cases, FALSE is returned on failure.
     */
    public function fetchRow();

    /**
     * Returns the number of columns in the result set
     *
     * @return integer Returns the number of columns in the result set represented by the PDOStatement object.
     */
    public function fieldCount();

    /**
     * Returns the number of rows affected by the last DELETE, INSERT, or UPDATE statement.
     *
     * @deprecated since v5.3.0 (2016-06-16) This method will be removed in v6.0.
     *
     * @return integer Number of rows
     */
    public function recordCount();

    /**
     * Returns field name from select query
     *
     * @param string $field
     *
     * @deprecated since v5.3.0 (2016-06-16) This method will be removed in v6.0.
     *
     * @return string Field name
     */
    public function fields($field);

    /**
     * Returns next record
     *
     * @deprecated since v5.3.0 (2016-06-16) This method will be removed in v6.0.
     */
    public function moveNext();

    /**
     * Move to the first row in the record set. Many databases do NOT support this.
     *
     * @deprecated since v5.3.0 (2016-06-16) This method will be removed in v6.0.
     *
     * @return true or false
     */
    public function moveFirst();

    /**
     * Returns the Last Record
     *
     * @deprecated since v5.3.0 (2016-06-16) This method will be removed in v6.0.
     */
    public function moveLast();

    /**
     * Random access to a specific row in the record set. Some databases do not support
     * access to previous rows in the databases (no scrolling backwards).
     *
     * @param integer $rowNumber The row to move to (0-based)
     *
     * @deprecated since v5.3.0 (2016-06-16) This method will be removed in v6.0.
     *
     * @return true if there still rows available, or false if there are no more rows (EOF).
     */
    public function move($rowNumber = 0);

    /**
     * Perform Seek to specific row
     *
     * @param $row
     *
     * @deprecated since v5.3.0 (2016-06-16) This method will be removed in v6.0.
     *
     * @return mixed
     */
    public function _seek($row);

    /**
     * Fills field array with first database element when query initially executed
     *
     * @deprecated since v5.3.0 (2016-06-16) This method will be removed in v6.0.
     *
     * @access private
     */
    public function _fetch();

    /**
     * Check to see if last record reached
     *
     * @deprecated since v5.3.0 (2016-06-16) This method will be removed in v6.0.
     *
     * @access public
     */
    public function EOF();

    /**
     * Returns All Records in an array
     *
     * @access public
     *
     * @deprecated since v5.3.0 (2016-06-16)     This method will be removed in v6.0. Use self::fetchAll() to retrieve
     *                                           all rows or self::fetchRow to retrieve a single row
     *             
     * @param  integer $nRows The number of rows to return. -1 means every row.
     */
    public function getArray($nRows = -1);

    /**
     * Returns All Records in an array
     *
     * @access public
     *
     * @deprecated since v5.3.0 (2016-06-16)     This method will be removed in v6.0. Use self::fetchAll() to retrieve all rows
     *                                          or self::fetchRow to retrieve a single row
     *             
     * @param  integer $nRows The number of rows to return. -1 means every row.
     */
    public function getRows($nRows = -1);

    /**
     * Returns All Records in an array
     *
     * @deprecated since v5.3.0 (2016-06-16)     This method will be removed in v6.0. Use self::fetchAll() to retrieve all rows
     *                                          or self::fetchRow to retrieve a single row
     *             
     * @param  integer $nRows The number of rows to return. -1 means every row.
     */
    public function getAll($nRows = -1);

    /**
     * Fetch field information for a table.
     *
     * @param int $fieldOffset
     *
     * @deprecated since v5.3.0 (2016-06-16) This method will be removed in v6.0.
     *
     * @return object containing the name, type and max_length
     */
    public function fetchField($fieldOffset = -1);
}
