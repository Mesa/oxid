<?php
/** 
 * PAYONE OXID Connector is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PAYONE OXID Connector is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with PAYONE OXID Connector.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.payone.de
 * @copyright (C) Payone GmbH
 * @version   OXID eShop CE
 */
 
class fcpayone_admindetails extends oxAdminDetails {
    
    /**
     * Helper object for dealing with different shop versions
     * @var object
     */
    protected $_oFcpoHelper = null;
    
    /**
     * Centralized Database instance
     * @var object
     */
    protected $_oFcpoDb = null;
    
    /**
     * fcpoconfigexport instance
     * @var object
     */
    protected $_oFcpoConfigExport = null;
    
    /**
     * fcpopaypal instance
     * @var object
     */
    protected $_oFcpoPayPal = null;
    /**
     * fcpopaypal instance
     * @var object
     */
    protected $_oFcpoKlarna = null;
    /**
     * fcpomapping instance
     * @var object
     */
    protected $_oFcpoMapping = null;
    /**
     * fcpoforwarding instance
     * @var object
     */
    protected $_oFcpoForwarding = null;
    

    /**
     * Init needed data
     */
    public function __construct() {
        parent::__construct();
        $this->_oFcpoHelper = oxNew('fcpohelper');
        $this->_oFcpoDb     = oxDb::getDb();
        $this->_oFcpoConfigExport = oxNew('fcpoconfigexport');
        $this->_oFcpoPayPal = oxNew('fcpopaypal');
        $this->_oFcpoKlarna = oxNew('fcpoklarna');
        $this->_oFcpoMapping = oxNew('fcpomapping');
        $this->_oFcpoErrorMapping = oxNew('fcpoerrormapping');
        $this->_oFcpoForwarding = oxNew('fcpoforwarding');
        $this->_oFcpoRatePay = oxNew('fcporatepay');
    }
    
    /**
     * Returns factory instance of given classname
     * 
     * @param string $sClassName
     * @return object
     */
    public function fcpoGetInstance($sClassName) {
        return oxNew($sClassName);
    }
    
}
