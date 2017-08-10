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
class fcPayOneBasketView extends fcPayOneBasketView_parent {

    /**
     * Helper object for dealing with different shop versions
     * @var object
     */
    protected $_oFcpoHelper = null;

    /**
     * Path where paypal logos can be found
     * @var string
     */
    protected $_sPayPalExpressLogoPath = 'modules/fcPayOne/out/img/';

    /**
     * Paypal Express picture
     * @var string
     */
    protected $_sPayPalExpressPic = null;

    /**
     * init object construction
     * 
     * @return null
     */
    public function __construct() {
        parent::__construct();
        $this->_oFcpoHelper = oxNew('fcpohelper');
    }

    /**
     * Returns wether paypal express is active or not
     * 
     * @param void
     * @return boolean
     */
    protected function _fcpoIsPayPalExpressActive() {
        $oBasket = $this->_oFcpoHelper->getFactoryObject('oxBasket');
        return $oBasket->fcpoIsPayPalExpressActive();
    }

    /**
     * Public getter for paypal express picture
     * 
     * @param void
     * @return string
     */
    public function fcpoGetPayPalExpressPic() {
        if ($this->_sPayPalExpressPic === null) {
            $this->_sPayPalExpressPic = false;
            if ($this->_fcpoIsPayPalExpressActive()) {
                $this->_sPayPalExpressPic = $this->_fcpoGetPayPalExpressPic();
            }
        }

        return $this->_sPayPalExpressPic;
    }
    
    /**
     * Finally fetches needed values and set attribute value
     * 
     * @param void
     * @return mixed
     */
    protected function _fcpoGetPayPalExpressPic() {
        $sPayPalExpressPic = false;
        $oBasket = $this->_oFcpoHelper->getFactoryObject('oxBasket');
        $sPic = $oBasket->fcpoGetPayPalExpressPic();

        $sPaypalExpressLogoPath = getShopBasePath() . $this->_sPayPalExpressLogoPath . $sPic;
        $blLogoPathExists = $this->_oFcpoHelper->fcpoFileExists($sPaypalExpressLogoPath);

        if ($blLogoPathExists) {
            $oConfig = $this->getConfig();
            $sShopURL = $oConfig->getCurrentShopUrl(false);
            $sPayPalExpressPic = $sShopURL . $this->_sPayPalExpressLogoPath . $sPic;
        }
        
        return $sPayPalExpressPic;
    }    

    /**
     * Method will return false or redirect to paypal express if used
     * 
     * @param void
     * @return boolean
     */
    public function fcpoUsePayPalExpress() {
        $oRequest = $this->_oFcpoHelper->getFactoryObject('fcporequest');
        $aOutput = $oRequest->sendRequestGenericPayment();

        if ($aOutput['status'] == 'ERROR') {
            $this->_iLastErrorNo = $aOutput['errorcode'];
            $this->_sLastError = $aOutput['customermessage'];
            return false;
        } elseif ($aOutput['status'] == 'REDIRECT') {
            $this->_oFcpoHelper->fcpoSetSessionVariable('fcpoWorkorderId', $aOutput['workorderid']);
            $oUtils = $this->_oFcpoHelper->fcpoGetUtils();
            $oUtils->redirect($aOutput['redirecturl']);
        }
    }

}
