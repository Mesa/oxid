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
 
class fcPayOneThankyouView extends fcPayOneThankyouView_parent {
    
    
    /**
     * Helper object for dealing with different shop versions
     * @var object
     */
    protected $_oFcpoHelper = null;
    
    /**
     * Instance of oxdb
     * @var object
     */
    protected $_oFcpoDb = null;

    /**
     * Mandate pdf url
     * @var string
     */
    protected $_sMandatePdfUrl = null;
    
    /**
     * Html for Barzahlen
     * @var string
     */
    protected $_sBarzahlenHtml = null;
    
    
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
     * Returns generated mandate pdf url and deletes it from session afterwards
     * 
     * @param void
     * @return string
     */
    public function fcpoGetMandatePdfUrl() {
        $sPdfUrl    = false;
        $oConfig    = $this->getConfig();
        $oOrder     = $this->getOrder();


        if($oOrder->oxorder__oxpaymenttype->value == 'fcpodebitnote' && $oConfig->getConfigParam('blFCPOMandateDownload')) {
            $sMandateIdentification = false;
            $sMode = false;

            $aMandate = $this->_oFcpoHelper->fcpoGetSessionVariable('fcpoMandate');
            if($aMandate && array_key_exists('mandate_identification', $aMandate) !== false) {
                $sMandateIdentification = $aMandate['mandate_identification'];
            }
            if($aMandate && array_key_exists('mode', $aMandate) !== false) {
                $sMode = $aMandate['mode'];
            }

            if($sMandateIdentification && $aMandate['mandate_status'] == 'active') {
                $oPayment = oxNew('oxPayment');
                $oPayment->fcpoAddMandateToDb($oOrder->getId(),$sMandateIdentification);
                $sPdfUrl = $oConfig->getShopUrl()."modules/fcPayOne/download.php?id=".$oOrder->getId();
            } elseif($sMandateIdentification && $sMode && $oOrder) {
                $oPORequest = $this->_oFcpoHelper->getFactoryObject('fcporequest');
                $sPdfUrl = $oPORequest->sendRequestGetFile($oOrder->getId(), $sMandateIdentification, $sMode);
            }

            $oUser = $this->getUser();
            if(!$oUser || !$oUser->oxuser__oxpassword->value) {
                $sPdfUrl .= '&uid='.$this->_oFcpoHelper->fcpoGetSessionVariable('sFcpoUserId');
            }
        }
        $this->_sMandatePdfUrl = $sPdfUrl;
        $this->_oFcpoHelper->fcpoDeleteSessionVariable('fcpoMandate');
        
        return $this->_sMandatePdfUrl;
    }
    
    
    /**
     * Method checks for an appointment error
     * 
     * @param void
     * @return bool
     */
    public function fcpoIsAppointedError() {
        $blReturn   = false;
        $oOrder     = $this->getOrder();
        
        if($oOrder->isPayOnePaymentType()) {
            if($oOrder->oxorder__oxfolder->value == 'ORDERFOLDER_PROBLEMS' && $oOrder->oxorder__oxtransstatus->value == 'ERROR')  {
                $blReturn = true;
            }
        }
        
        return $blReturn;
    }
    
    
    /**
     * Sets userid into session berfore triggering the parent method
     * 
     * @param void
     * @return string
     */
    public function render() {
        $oUser = $this->getUser();
        if($oUser) {
            $this->_oFcpoHelper->fcpoSetSessionVariable('sFcpoUserId', $oUser->getId());
        }
        
        $sReturn = parent::render();
        
        return $sReturn;
    }
    
    
    /**
     * Returns the html of barzahlen instructions
     * 
     * @param void
     * @return mixed
     */
    public function fcpoGetBarzahlenHtml() {
        if ( $this->_sBarzahlenHtml === null ) {
            $this->_sBarzahlenHtml = $this->_oFcpoHelper->fcpoGetSessionVariable('sFcpoBarzahlenHtml');
            // delete this from session after we have the result for one time displaying
            $this->_oFcpoHelper->fcpoDeleteSessionVariable('sFcpoBarzahlenHtml');
        }
        
        return $this->_sBarzahlenHtml;
    }
    
}