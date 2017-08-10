<?php
/**
 * This file is part of OXID eSales PayPal module.
 *
 * OXID eSales PayPal module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales PayPal module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales PayPal module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 */

/**
 * Class for splitting user name.
 *
 * @package core
 */
class oePayPalFullName
{
    private $_sFirstName = '';
    private $_sLastName = '';

    /**
     * User first name and second name.
     *
     * @param string $sFullName
     */
    public function __construct($sFullName)
    {
        $this->_split($sFullName);
    }

    /**
     * Return user first name.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->_sFirstName;
    }

    /**
     * Return user second name.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->_sLastName;
    }

    /**
     * Split user full name to first name and second name.
     *
     * @param string $sFullName
     */
    protected function _split($sFullName)
    {
        $aNames = explode(" ", trim($sFullName), 2);

        $this->_sFirstName = trim($aNames[0]);
        $this->_sLastName = trim($aNames[1]);
    }
}
