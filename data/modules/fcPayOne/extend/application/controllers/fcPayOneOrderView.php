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
 
class fcPayOneOrderView extends fcPayOneOrderView_parent {
    
    /**
     * Helper object for dealing with different shop versions
     * @var object
     */
    protected $_oFcpoHelper = null;

    /**
     * Database instance
     * @var object
     */
    protected $_oFcpoDb = null;

    /**
     * Boolean of option "blConfirmAGB" error
     * @var bool
     */
    protected $_blFcpoConfirmMandateError = null;
    
    
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
     * Extends oxid standard method execute()
     * Check if debitnote mandate was accepted
     * 
     * Checks for order rules confirmation ("ord_agb", "ord_custinfo" form values)(if no
     * rules agreed - returns to order view), loads basket contents (plus applied
     * price/amount discount if available - checks for stock, checks user data (if no
     * data is set - returns to user login page). Stores order info to database
     * (oxorder::finalizeOrder()). According to sum for items automatically assigns user to
     * special user group ( oxuser::onOrderExecute(); if this option is not disabled in
     * admin). Finally you will be redirected to next page (order::_getNextStep()).
     *
     * @return string
     */
    public function execute() {
        $sFcpoMandateCheckbox = $this->_oFcpoHelper->fcpoGetRequestParameter('fcpoMandateCheckbox');
        
        $blConfirmMandateError = ((!$sFcpoMandateCheckbox || $sFcpoMandateCheckbox == 'false') && $this->_fcpoMandateAcceptanceNeeded());
        
        if ($blConfirmMandateError) {
            $this->_blFcpoConfirmMandateError = 1;
            return;
        }
        
        return parent::execute();
    }
    
    
    /**
     * Handles paypal express
     * 
     * @param void
     * @return string
     */
    public function fcpoHandlePayPalExpress() {
        try {
            $this->_handlePayPalExpressCall();
        } 
        catch (oxException $oExcp) {
            $oUtilsView = $this->_oFcpoHelper->fcpoGetUtilsView();
            $oUtilsView->addErrorToDisplay($oExcp);
            return "basket";
        }
    }
    
    
    /**
     * Checks if user already exists
     * 
     * @param string $sEmail
     * @return mixed
     */
    protected function _fcpoDoesUserAlreadyExist($sEmail) {
        $oOrder = $this->_oFcpoHelper->getFactoryObject('oxOrder');
        return $oOrder->fcpoDoesUserAlreadyExist($sEmail);
    }
    
    
    /**
     * Get userid by given username
     * 
     * @param string $sUserName
     * @return string
     */
    public function _fcpoGetIdByUserName($sUserName) {
        $oOrder = $this->_oFcpoHelper->getFactoryObject('oxOrder');
        return $oOrder->fcpoGetIdByUserName($sUserName);
    }
    
    
    /**
     * Get CountryID by countrycode
     * 
     * @param string $sCode
     * @return string
     */
    protected function _fcpoGetIdByCode($sCode) {
        $oOrder = $this->_oFcpoHelper->getFactoryObject('oxOrder');
        return (string)$oOrder->fcpoGetIdByCode($sCode);
    }
    
    
    /**
     * Returns salutation of customer in the expected form
     * 
     * @param string $sFirstname
     * @return string
     */
    protected function _fcpoGetSal($sFirstname) {
        $oOrder = $this->_oFcpoHelper->getFactoryObject('oxOrder');
        $sSal = $oOrder->fcpoGetSalByFirstName($sFirstname);
        $sSal = (!$sSal) ? 'MR' : $sSal;
        
        if($sSal == 'Herr') {
            $sSal = 'MR';
        } 
        elseif($sSal == 'Frau') {
            $sSal = 'MRS';
        }
        
        return $sSal;
    }
    
    
    /**
     * Create paypal user by API response
     * 
     * @param array $aResponse
     * @return object
     */
    protected function _fcpoCreatePayPalUser($aResponse) {
        $oUser = $this->_oFcpoHelper->getFactoryObject("oxUser");

        $sUserId = $this->_fcpoGetIdByUserName($aResponse['add_paydata[email]']);
        if ($sUserId) {
            $oUser->load($sUserId);
        }

        list($sStreet, $sStreetNr) = $this->_fcpoSplitAddress($aResponse['add_paydata[shipping_street]']);
        $sAddInfo = '';
        if(array_key_exists('add_paydata[shipping_addressaddition]', $aResponse)) {
            $sAddInfo = $aResponse['add_paydata[shipping_addressaddition]'];
        }
        
        $oUser->oxuser__oxactive = new oxField(1);
        $oUser->oxuser__oxusername = new oxField($aResponse['add_paydata[email]']);
        $oUser->oxuser__oxfname = new oxField($aResponse['add_paydata[shipping_firstname]']);
        $oUser->oxuser__oxlname = new oxField($aResponse['add_paydata[shipping_lastname]']);
        $oUser->oxuser__oxfon = new oxField('');
        $oUser->oxuser__oxsal = new oxField($this->_fcpoGetSal($aResponse['add_paydata[shipping_firstname]']));
        $oUser->oxuser__oxcompany = new oxField('');
        $oUser->oxuser__oxstreet = new oxField($sStreet);
        $oUser->oxuser__oxstreetnr = new oxField($sStreetNr);
        $oUser->oxuser__oxaddinfo = new oxField($sAddInfo);
        $oUser->oxuser__oxcity = new oxField($aResponse['add_paydata[shipping_city]']);
        $oUser->oxuser__oxzip = new oxField($aResponse['add_paydata[shipping_zip]']);
        $oUser->oxuser__oxcountryid = new oxField($this->_fcpoGetIdByCode($aResponse['add_paydata[shipping_country]']));        
        $oUser->oxuser__oxstateid = new oxField('');

        if ($oUser->save()) {
            $oUser->addToGroup("oxidnotyetordered");
            $oUser->fcpoUnsetGroups();
        }
        return $oUser;
    }
    
    
    /**
     * Compares user object and api response for validating user is the same
     * 
     * @param object $oUser
     * @param array $aResponse
     * @return bool
     */
    protected function _fcpoIsSamePayPalUser($oUser, $aResponse) {
        
        $blIsSamePayPalUser = (
            $oUser->oxuser__oxfname->value == $aResponse['add_paydata[shipping_firstname]'] ||
            $oUser->oxuser__oxlname->value == $aResponse['add_paydata[shipping_lastname]'] ||
            $oUser->oxuser__oxcity->value == $aResponse['add_paydata[shipping_city]'] ||
            stripos($aResponse['add_paydata[shipping_street]'], $oUser->oxuser__oxstreet->value) !== false
        );
        
        return $blIsSamePayPalUser;
    }
    
    
    /**
     * Handles user related things corresponding to API-Response
     * 
     * @param array $aResponse
     * @return object
     */
    protected function _fcpoHandleUser($aResponse) {
        $sEmail = $aResponse['add_paydata[email]'];
        
        $oCurrentUser = $this->getUser();
        if ($oCurrentUser) {
            $sEmail = $oCurrentUser->oxuser__oxusername->value;
        }

        if ($sUserId = $this->_fcpoDoesUserAlreadyExist($sEmail)) {
            $oUser = $this->_oFcpoHelper->getFactoryObject("oxUser");
            $oUser->load($sUserId);

            if (!$oCurrentUser) {
                if (!$this->_fcpoIsSamePayPalUser($oUser, $aResponse)) {
                    $this->_fcpoThrowException('FCPO_PAYPALEXPRESS_USER_SECURITY_ERROR');
                }
            } elseif (!$this->_fcpoIsSamePayPalUser($oUser, $aResponse)) {
                // user is logged in, but paypal address is different from the oxid-address, so create a new one
                $this->_fcpoCreatePayPalDelAddress($aResponse, $sUserId);
            } else {
                // remove custom shipping address ID from session
                $this->_oFcpoHelper->fcpoDeleteSessionVariable('deladrid');
            }
        } else {
            $oUser = $this->_fcpoCreatePayPalUser($aResponse);
        }
        
        $this->_oFcpoHelper->fcpoSetSessionVariable('usr', $oUser->getId());
        $this->setUser($oUser);

        return $oUser;
    }
    
    
    /**
     * Method throws an exception with given message
     * 
     * @param string $sMessage
     * @return void
     */
    protected function _fcpoThrowException($sMessage) {
        // user is not logged in and the address is different
        $oEx = oxNew('oxException');
        $oEx->setMessage($sMessage);
        throw $oEx;
    }
    
    
    /**
     * Handles the paypal express call
     * 
     * @param void
     * @return void
     */
    protected function _handlePayPalExpressCall() {
        $sWorkorderId = $this->_oFcpoHelper->fcpoGetSessionVariable('fcpoWorkorderId');
        if($sWorkorderId) {
            $oRequest   = $this->_oFcpoHelper->getFactoryObject('fcporequest');
            $aOutput    = $oRequest->sendRequestGenericPayment($sWorkorderId);
            $oUser = $this->_fcpoHandleUser($aOutput);
            if($oUser) {
                $oSession = $this->_oFcpoHelper->fcpoGetSession();
                $oBasket = $oSession->getBasket();
                $oBasket->setBasketUser($oUser);

                // setting PayPal as current active payment
                $this->_oFcpoHelper->fcpoSetSessionVariable('paymentid', "fcpopaypal_express");
                $oBasket->setPayment("fcpopaypal_express");
                
                $sActShipSet = $this->_oFcpoHelper->fcpoGetRequestParameter('sShipSet');
                if (!$sActShipSet) {
                    $sActShipSet = $this->_oFcpoHelper->fcpoGetSessionVariable('sShipSet');
                }

                // load sets, active set, and active set payment list
                $oDelSets = $this->_oFcpoHelper->getFactoryObject("oxdeliverysetlist");
                list($aAllSets, $sActShipSet, $aPaymentList) = $oDelSets->getDeliverySetData($sActShipSet, $this->getUser(), $oBasket);
                
                $oBasket->setShipping($sActShipSet);
                $oBasket->onUpdate();
                $oBasket->calculateBasket(true);
            }
        }
    }
    
    
    /**
     * Check if mandate acceptance is needed
     *
     * @return bool
     */
    protected function _fcpoMandateAcceptanceNeeded() {
        $aMandate = $this->_oFcpoHelper->fcpoGetSessionVariable('fcpoMandate');
        if($aMandate && array_key_exists('mandate_status', $aMandate) !== false && $aMandate['mandate_status'] == 'pending') {
            if(array_key_exists('mandate_text', $aMandate) !== false) {
                return true;
            }
        }
        return false;
    }
    
    
    /**
     * Template variable getter. Return if the debitnote mandate was not accepted and thus an error has to be shown.
     *
     * @return bool
     */
    public function fcpoIsMandateError() {
        return $this->_blFcpoConfirmMandateError;
    }
    
    
    /**
     * Extends oxid standard method _validateTermsAndConditions()
     * Validates whether necessary terms and conditions checkboxes were checked.
     *
     * @return bool
     */
    protected function _validateTermsAndConditions() {
        if (parent::_validateTermsAndConditions() === true) {
            return true;
        }
        
        $blValid = true;
        $oConfig = $this->_oFcpoHelper->fcpoGetConfig();

        if ($oConfig->getConfigParam('blConfirmAGB') && !$this->_oFcpoHelper->fcpoGetRequestParameter('ord_agb')) {
            $blValid = false;
        }

        if ($oConfig->getConfigParam('blEnableIntangibleProdAgreement')) {
            $oBasket = $this->getBasket();

            $blDownloadableProductsAgreement = $this->_oFcpoHelper->fcpoGetRequestParameter('fcdpa');
            if ($blValid && $oBasket->hasArticlesWithDownloadableAgreement() && !$blDownloadableProductsAgreement) {
                $blValid = false;
            }

            $blServiceProductsAgreement = $oConfig->getRequestParameter('fcspa');
            if ($blValid && $oBasket->hasArticlesWithIntangibleAgreement() && !$blServiceProductsAgreement) {
                $blValid = false;
            }
        }

        return $blValid;
    }

    
    /**
     * Splits street and number from concatenated combofield
     * 
     * @param string $sPayPalStreet
     * @return array
     */
    protected function _fcpoSplitAddress($sPayPalStreet) {
        $sStreetNr = '';
        $aSplit = explode(' ', $sPayPalStreet);
        if(is_array($aSplit) && count($aSplit) == 2) {
            $sPayPalStreet = $aSplit[0];
            $sStreetNr = $aSplit[1];
        }
        
        return array($sPayPalStreet, $sStreetNr);
    }
    
    
    /**
     * Creating paypal delivery address
     * 
     * @param array $aResponse
     * @param string $sUserId
     * @return void
     */
    protected function _fcpoCreatePayPalDelAddress($aResponse, $sUserId) {
        if ($sAddressId = $this->_fcpoGetExistingPayPalAddressId($aResponse)) {
            $this->_oFcpoHelper->fcpoSetSessionVariable("deladrid", $sAddressId);
        } 
        else {
            list($sStreet, $sStreetNr) = $this->_fcpoSplitAddress($aResponse['add_paydata[shipping_street]']);
            
            $sAddInfo = '';
            if(array_key_exists('add_paydata[shipping_addressaddition]', $aResponse)) {
                $sAddInfo = $aResponse['add_paydata[shipping_addressaddition]'];
            }
            
            $oAddress = oxNew("oxAddress");            
            $oAddress->oxaddress__oxuserid = new oxField($sUserId);
            $oAddress->oxaddress__oxfname = new oxField($aResponse['add_paydata[shipping_firstname]']);
            $oAddress->oxaddress__oxlname = new oxField($aResponse['add_paydata[shipping_lastname]']);
            $oAddress->oxaddress__oxstreet = new oxField($sStreet);
            $oAddress->oxaddress__oxstreetnr = new oxField($sStreetNr);
            $oAddress->oxaddress__oxaddinfo = new oxField($sAddInfo);
            $oAddress->oxaddress__oxcity = new oxField($aResponse['add_paydata[shipping_city]']);
            $oAddress->oxaddress__oxzip = new oxField($aResponse['add_paydata[shipping_zip]']);
            $oAddress->oxaddress__oxcountryid = new oxField($this->_fcpoGetIdByCode($aResponse['add_paydata[shipping_country]']));
            $oAddress->oxaddress__oxstateid = new oxField('');
            $oAddress->oxaddress__oxfon = new oxField('');
            $oAddress->oxaddress__oxsal = new oxField($this->_fcpoGetSal($aResponse['add_paydata[shipping_firstname]']));
            $oAddress->save();

            $this->_oFcpoHelper->fcpoSetSessionVariable("deladrid", $oAddress->getId());
        }
    }
    
    
    /**
     * Searches an existing addressid by extracting response of payone
     * 
     * @param array $aResponse
     * @return mixed
     */
    protected function _fcpoGetExistingPayPalAddressId($aResponse) {
        list($sStreet, $sStreetNr) = $this->_fcpoSplitAddress($aResponse['add_paydata[shipping_street]']);

        $oOrder = $this->_oFcpoHelper->getFactoryObject('oxOrder');
        $sAddressId = $oOrder->fcpoGetAddressIdByResponse($aResponse, $sStreet, $sStreetNr);

        $mReturn = ($sAddressId) ? $sAddressId : false;

        return $mReturn;
    }
    
    /* Maybe needed for future payment-methods
    protected function _getNextStep($iSuccess) {
        $sNextStep = parent::_getNextStep($iSuccess);

        // Check if Yapital was selected and if thankyou would be the next step
        $sPaymentId = $this->_oFcpoHelper->fcpoGetSessionVariable('paymentid');
        if (fcPayOnePayment::fcIsPayOneIframePaymentType($sPaymentId) && stripos($sNextStep, 'thankyou') !== false) {
            return 'fcpayoneiframe';
        }
        return $sNextStep;
    }*/
    
}
