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
 
class fcPayOneBasket extends fcPayOneBasket_parent {
    
    /**
     * Helper object for dealing with different shop versions
     * @var object
     */
    protected $_oFcpoHelper = null;
    
    /**
     * Helper object for dealing database
     * @var object
     */
    protected $_oFcpoDb = null;
    
    
    /**
     * init object construction
     * 
     * @return null
     */
    public function __construct() {
        parent::__construct();
        $this->_oFcpoHelper = oxNew('fcpohelper');
        $this->_oFcpoDb     = oxDb::getDb();
    }
    
    
    /**
     * Returns wether paypal express is active or not
     * 
     * @return bool
     */
    public function fcpoIsPayPalExpressActive() {
        $sQuery = "SELECT oxactive FROM oxpayments WHERE oxid = 'fcpopaypal_express'";
        return (bool)$this->_oFcpoDb->GetOne($sQuery);
    }
    
    
    /**
     * Returns pic that is configured in database
     * 
     * @param void
     * @return string
     */
    public function fcpoGetPayPalExpressPic() {
        $oLang = $this->_oFcpoHelper->fcpoGetLang();
        $iLangId = $oLang->getBaseLanguage();
        $sQuery = "SELECT fcpo_logo FROM fcpopayoneexpresslogos WHERE fcpo_logo != '' AND fcpo_langid = '{$iLangId}' ORDER BY fcpo_default DESC";
        $sPic   = $this->_oFcpoDb->GetOne($sQuery);
        
        return $sPic;
    }
    
}
