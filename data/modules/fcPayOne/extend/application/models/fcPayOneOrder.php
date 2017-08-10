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
class fcPayOneOrder extends fcPayOneOrder_parent {

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
     * Array with all reponse paramaters from the API order request
     *
     * @var array
     */
    protected $_aResponse = null;

    /**
     * Flag for redirecting after save
     * @var bool
     */
    protected $_blIsRedirectAfterSave = null;

    /**
     * Variable for flagging payment as payone payment
     * @var bool
     */
    protected $_blIsPayonePayment = false;

    /**
     * Appointed error
     * 
     * @var bool
     */
    protected $_blFcPayoneAppointedError = false;

    /**
     * List of Payment IDs which need to save workorderid
     * @var array
     */
    protected $_aPaymentsWorkorderIdSave = array('fcpopo_bill', 'fcpopo_debitnote', 'fcpopo_installment');

    /**
     * List of Payment IDs which are foreseen for saving clearing reference
     * @var array
     */
    protected $_aPaymentsClearingReferenceSave = array('fcporp_bill', 'fcpopo_bill', 'fcpopo_debitnote', 'fcpopo_installment');

    /**
     * List of Payment IDs which are foreseen for saving external shopid
     * @var array
     */
    protected $_aPaymentsProfileIdentSave = array('fcporp_bill');

    /**
     * init object construction
     * 
     * @return null
     */
    public function __construct() {
        parent::__construct();
        $this->_oFcpoHelper = oxNew('fcpohelper');
        $this->_oFcpoDb = oxDb::getDb();
    }

    /**
     * Checks if the selected payment method for this order is a PAYONE payment method
     *
     * @param string $sPaymenttype payment id. Default is null
     * 
     * @return bool
     */
    public function isPayOnePaymentType($sPaymenttype = null) {
        if (!$sPaymenttype) {
            $sPaymenttype = $this->oxorder__oxpaymenttype->value;
        }
        return $this->_fcpoIsPayonePaymentType($sPaymenttype);
    }

    /**
     * Method validates if given payment-type is an payone iframe payment
     * 
     * @param string $sPaymenttype
     * @return bool
     */
    public function isPayOneIframePayment($sPaymenttype = null) {
        if (!$sPaymenttype) {
            $sPaymenttype = $this->oxorder__oxpaymenttype->value;
        }
        return $this->_fcpoIsPayonePaymentType($sPaymenttype, true);
    }

    /**
     * Checks if user already exists
     * 
     * @param string $sEmail
     * @return mixed
     */
    public function fcpoDoesUserAlreadyExist($sEmail) {
        $sQuery = "SELECT oxid FROM oxuser WHERE oxusername = " . oxDb::getDb()->quote($sEmail) . " AND oxpassword != ''";
        $sUserId = $this->_oFcpoDb->GetOne($sQuery);
        $mReturn = ($sUserId) ? $sUserId : false;

        return $mReturn;
    }

    /**
     * Returns user id by given username
     * 
     * @param string $sUserName
     * @return type
     */
    public function fcpoGetIdByUserName($sUserName) {
        $oConfig = $this->_oFcpoHelper->fcpoGetConfig();
        $sQuery = "SELECT oxid FROM oxuser WHERE oxusername = " . oxDb::getDb()->quote($sUserName);

        if (!$oConfig->getConfigParam('blMallUsers')) {
            $sQuery .= " AND oxshopid = '{$oConfig->getShopId()}'";
        }

        $sReturn = $this->_oFcpoDb->GetOne($sQuery);

        return $sReturn;
    }

    /**
     * Returns countryid by given countrycode
     * 
     * @param string $sCode
     * @return mixed
     */
    public function fcpoGetIdByCode($sCode) {
        $sQuery = "SELECT oxid FROM oxcountry WHERE oxisoalpha2 = " . oxDb::getDb()->quote($sCode);
        return $this->_oFcpoDb->GetOne($sQuery);
    }

    /**
     * Returns salutation stored in database by firstname
     * 
     * @param string $sFirstname
     * @return string
     */
    public function fcpoGetSalByFirstName($sFirstname) {
        $sQuery = "SELECT oxsal FROM oxuser WHERE oxfname = " . oxDb::getDb()->quote($sFirstname) . " AND oxsal != '' LIMIT 1";
        $sSal = $this->_oFcpoDb->GetOne($sQuery);

        return $sSal;
    }

    /**
     * Checks address database for receiving a address matching to response
     * 
     * @param array $aResponse
     * @return mixed
     */
    public function fcpoGetAddressIdByResponse($aResponse, $sStreet, $sStreetNr) {
        $sQuery = " SELECT
                        oxid
                    FROM
                        oxaddress
                    WHERE
                        oxfname = {$this->_oFcpoDb->quote($aResponse['add_paydata[shipping_firstname]'])} AND
                        oxlname = {$this->_oFcpoDb->quote($aResponse['add_paydata[shipping_lastname]'])} AND
                        oxstreet = {$this->_oFcpoDb->quote($sStreet)} AND
                        oxstreetnr = {$this->_oFcpoDb->quote($sStreetNr)} AND
                        oxcity = {$this->_oFcpoDb->quote($aResponse['add_paydata[shipping_city]'])} AND
                        oxzip = {$this->_oFcpoDb->quote($aResponse['add_paydata[shipping_zip]'])} AND
                        oxcountryid = {$this->_oFcpoDb->quote($this->fcpoGetIdByCode($aResponse['add_paydata[shipping_country]']))}";

        return $this->_oFcpoDb->GetOne($sQuery);
    }

    /**
     * Removes MSIE(\s)?(\S)*(\s) from browser agent information
     *
     * @param string $sAgent browser user agent idenfitier
     *
     * @return string
     */
    protected function _fcProcessUserAgentInfo($sAgent) {
        if ($sAgent) {
            $sAgent = getStr()->preg_replace("/MSIE(\s)?(\S)*(\s)/", "", (string) $sAgent);
        }
        return $sAgent;
    }

    /**
     * Compares the HTTP user agent before and after the redirect payment method.
     * If HTTP user agent is diffenrent it checks if the remote tokens match.
     * If so, the current user agent is updated in the user session.
     * 
     * @return null
     */
    protected function _fcpoCheckUserAgent() {
        $oUtils = $this->_oFcpoHelper->fcpoGetUtilsServer();

        $sAgent = $oUtils->getServerVar('HTTP_USER_AGENT');
        $sExistingAgent = $this->_oFcpoHelper->fcpoGetSessionVariable('sessionagent');
        $sAgent = $this->_fcProcessUserAgentInfo($sAgent);
        $sExistingAgent = $this->_fcProcessUserAgentInfo($sExistingAgent);

        if ($this->_fcGetCurrentVersion() >= 4310 && $sAgent && $sAgent !== $sExistingAgent) {
            $oSession = $this->_oFcpoHelper->fcpoGetSession();
            $sInputToken = $this->_oFcpoHelper->fcpoGetRequestParameter('rtoken');
            $sToken = $oSession->getRemoteAccessToken(false);
            $blValid = $this->_fcpoValidateToken($sInputToken, $sToken);
            if ($blValid === true) {
                $this->_oFcpoHelper->fcpoGetSessionVariable("sessionagent", $oUtils->getServerVar('HTTP_USER_AGENT'));
            }
        }
    }

    /**
     * Compares tokens and returns if they are valid
     * 
     * @param string $param
     * @return bool
     */
    protected function _fcpoValidateToken($sInputToken, $sToken) {
        $blTokenEqual = !(bool) strcmp($sInputToken, $sToken);
        $blValid = $sInputToken && $blTokenEqual;

        return $blValid;
    }

    /**
     * Get current version number as 4 digit integer e.g. Oxid 4.5.9 is 4590
     * 
     * @return integer
     */
    protected function _fcGetCurrentVersion() {
        return $this->_oFcpoHelper->fcpoGetIntShopVersion();
    }

    /**
     * Returns true if this request is the return to the shop from a payment provider where the user has been redirected to
     * 
     * @return bool
     */
    protected function _isRedirectAfterSave() {
        if ($this->_blIsRedirectAfterSave === null) {
            $this->_blIsRedirectAfterSave = false;
            $oSession = $this->_oFcpoHelper->fcpoGetSession();
            $oBasket = $oSession->getBasket();
            $sPaymentId = $oBasket->getPaymentId();

            $blUseRedirectAfterSave = (
                    $this->_oFcpoHelper->fcpoGetRequestParameter('fcposuccess') &&
                    $this->_oFcpoHelper->fcpoGetRequestParameter('refnr') &&
                    (
                    $this->_oFcpoHelper->fcpoGetSessionVariable('fcpoTxid') ||
                    $sPaymentId == 'fcpocreditcard_iframe'
                    )
                    );

            if ($blUseRedirectAfterSave) {
                $this->_blIsRedirectAfterSave = true;
            }
        }
        return $this->_blIsRedirectAfterSave;
    }

    /**
     * Overrides standard oxid finalizeOrder method
     * 
     * Order checking, processing and saving method.
     * Before saving performed checking if order is still not executed (checks in
     * database oxorder table for order with know ID), if yes - returns error code 3,
     * if not - loads payment data, assigns all info from basket to new oxorder object
     * and saves full order with error status. Then executes payment. On failure -
     * deletes order and returns error code 2. On success - saves order (oxorder::save()),
     * removes article from wishlist (oxorder::_updateWishlist()), updates voucher data
     * (oxorder::_markVouchers()). Finally sends order confirmation email to customer
     * (oxemail::SendOrderEMailToUser()) and shop owner (oxemail::SendOrderEMailToOwner()).
     * If this is order racalculation, skipping payment execution, marking vouchers as used
     * and sending order by email to shop owner and user
     * Mailing status (1 if OK, 0 on error) is returned.
     *
     * @param oxBasket $oBasket              Shopping basket object
     * @param object   $oUser                Current user object
     * @param bool     $blRecalculatingOrder Order recalculation
     *
     * @return integer
     * @extend finalizeOrder
     */
    public function finalizeOrder(oxBasket $oBasket, $oUser, $blRecalculatingOrder = false) {
        // Use standard method if payment type does not belong to PAYONE
        if ($this->isPayOnePaymentType($oBasket->getPaymentId()) === false) {
            return parent::finalizeOrder($oBasket, $oUser, $blRecalculatingOrder);
        }

        $blSaveAfterRedirect = $this->_isRedirectAfterSave();

        $mRet = $this->_fcpoEarlyValidation($blSaveAfterRedirect, $oBasket, $oUser, $blRecalculatingOrder);
        if ($mRet !== null) {
            return $mRet;
        }

        // copies user info
        $this->_setUser($oUser);

        // copies basket info if no basket injection or presave order is inactive
        $this->_fcpoHandleBasket($blSaveAfterRedirect, $oBasket);

        // payment information
        $oUserPayment = $this->_setPayment($oBasket->getPaymentId());

        // set folder information, if order is new
        // #M575 in recalcualting order case folder must be the same as it was
        if (!$blRecalculatingOrder) {
            $this->_setFolder();
        }

        $mRet = $this->_fcpoExecutePayment($blSaveAfterRedirect, $oBasket, $oUserPayment, $blRecalculatingOrder);
        if ($mRet !== null) {
            return $mRet;
        }

        //saving all order data to DB
        $this->save();

        $this->_fcpoSaveAfterRedirect($blSaveAfterRedirect);


        $blRet = $this->_fcpoHandleTsProtection($blRecalculatingOrder, $oBasket);
        if ($blRet !== true) {
            return $blRet;
        }


        // deleting remark info only when order is finished
        $this->_oFcpoHelper->fcpoDeleteSessionVariable('ordrem');
        $this->_oFcpoHelper->fcpoDeleteSessionVariable('stsprotection');

        //#4005: Order creation time is not updated when order processing is complete
        if (method_exists($this, '_updateOrderDate') && !$blRecalculatingOrder) {
            $this->_updateOrderDate();
        }

        $this->_fcpoSetOrderStatus();

        // store orderid
        $oBasket->setOrderId($this->getId());

        // updating wish lists
        $this->_updateWishlist($oBasket->getContents(), $oUser);

        // updating users notice list
        $this->_updateNoticeList($oBasket->getContents(), $oUser);

        // marking vouchers as used and sets them to $this->_aVoucherList (will be used in order email)
        // skipping this action in case of order recalculation
        $this->_fcpoMarkVouchers($blRecalculatingOrder, $oUser, $oBasket);


        $this->_oFcpoHelper->fcpoDeleteSessionVariable('fcpoordernotchecked');
        $this->_oFcpoHelper->fcpoDeleteSessionVariable('fcpoWorkorderId');

        // send order by email to shop owner and current user
        // skipping this action in case of order recalculation
        $iRet = $this->_fcpoFinishOrder($blRecalculatingOrder, $oUser, $oBasket, $oUserPayment);

        return $iRet;
    }

    /**
     * Triggers steps to execute payment
     * 
     * @param bool $blSaveAfterRedirect
     * @param oxBasket $oBasket
     * @param oxUserPayment $oUserPayment
     * @return mixed
     */
    protected function _fcpoExecutePayment($blSaveAfterRedirect, $oBasket, $oUserPayment, $blRecalculatingOrder) {
        if ($blSaveAfterRedirect === true) {
            $sRefNrCheckResult = $this->_fcpoCheckRefNr();
            $sTxid = $this->_oFcpoHelper->fcpoGetSessionVariable('fcpoTxid');

            if ($sRefNrCheckResult != '') {
                return $sRefNrCheckResult;
            }
            $this->_fcpoProcessOrder($oBasket, $sTxid);
        } else {
            if (!$blRecalculatingOrder) {
                $blRet = $this->_executePayment($oBasket, $oUserPayment);
                if ($blRet !== true) {
                    return $blRet;
                }
            }
        }

        return null;
    }

    /**
     * Handles basket loading into order
     * 
     * @param bool $blSaveAfterRedirect
     * @param oxBasket $oBasket
     * @return void
     */
    protected function _fcpoHandleBasket($blSaveAfterRedirect, $oBasket) {
        $sGetChallenge = $this->_oFcpoHelper->fcpoGetSessionVariable('sess_challenge');
        $oConfig = $this->getConfig();
        $blFCPOPresaveOrder = $oConfig->getConfigParam('blFCPOPresaveOrder');
        if ($blFCPOPresaveOrder === false || $blSaveAfterRedirect === false) {
            $this->_loadFromBasket($oBasket);
        } else {
            $this->load($sGetChallenge);
        }
    }

    /**
     * 
     * 
     * @param bool $blSaveAfterRedirected
     * @param oxBasket $oBasket
     * @param oxUser $oUser
     * @return mixed
     */
    protected function _fcpoEarlyValidation($blSaveAfterRedirect, $oBasket, $oUser, $blRecalculatingOrder) {
        // check if this order is already stored
        $sGetChallenge = $this->_oFcpoHelper->fcpoGetSessionVariable('sess_challenge');
        if ($blSaveAfterRedirect === false && $this->_checkOrderExist($sGetChallenge)) {
            $oUtils = $this->_oFcpoHelper->fcpoGetUtils();
            $oUtils->logger('BLOCKER');
            // we might use this later, this means that somebody klicked like mad on order button
            return self::ORDER_STATE_ORDEREXISTS;
        }

        // if not recalculating order, use sess_challenge id, else leave old order id
        if (!$blRecalculatingOrder) {
            // use this ID
            $this->setId($sGetChallenge);

            // validating various order/basket parameters before finalizing
            if (($iOrderState = $this->validateOrder($oBasket, $oUser))) {
                return $iOrderState;
            }
        }

        return null;
    }

    /**
     * Method checks and handle TS protection
     * 
     * @param bool $blRecalculatingOrder
     * @param oxBasket $oBasket
     * @return bool
     */
    protected function _fcpoHandleTsProtection($blRecalculatingOrder, $oBasket) {
        $blRet = true;
        if ($this->_fcGetCurrentVersion() >= 4399) {
            // executing TS protection
            if (!$blRecalculatingOrder && $oBasket->getTsProductId()) {
                $blRet = $this->_executeTsProtection($oBasket);
            }
        }

        return $blRet;
    }

    /**
     * Finishes order and returns state
     * 
     * @param bool $blRecalculatingOrder
     * @param oxUser $oUser
     * @param oxBasket $oBasket
     * @param oxUserPayment $oUserPayment
     */
    protected function _fcpoFinishOrder($blRecalculatingOrder, $oUser, $oBasket, $oUserPayment) {
        if (!$blRecalculatingOrder) {
            $iRet = $this->_sendOrderByEmail($oUser, $oBasket, $oUserPayment);
        } else {
            $iRet = self::ORDER_STATE_OK;
        }

        return $iRet;
    }

    /**
     * Mathod triggers saving after redirect if this option has been configured
     * 
     * @param bool $blSaveAfterRedirect
     * @return void
     */
    protected function _fcpoSaveAfterRedirect($blSaveAfterRedirect) {
        if ($blSaveAfterRedirect === true) {
            $sQuery = "UPDATE fcpotransactionstatus SET fcpo_ordernr = '{$this->oxorder__oxordernr->value}' WHERE fcpo_txid = '" . $this->_oFcpoHelper->fcpoGetSessionVariable('fcpoTxid') . "'";
            $this->_oFcpoDb->Execute($sQuery);
        }
    }

    /**
     * Sets order status depending on having an appointed error
     * 
     * @return void
     */
    protected function _fcpoSetOrderStatus() {
        if ($this->_fcpoGetAppointedError() === false) {
            // updating order trans status (success status)
            $this->_setOrderStatus('OK');
        } else {
            $this->_setOrderStatus('ERROR');
        }
    }

    /**
     * Method triggers marking vouchers if order hasn't been set for recalculation
     * 
     * @param bool $blRecalculatingOrder
     * @return void
     */
    protected function _fcpoMarkVouchers($blRecalculatingOrder, $oUser, $oBasket) {
        if (!$blRecalculatingOrder) {
            $this->_markVouchers($oBasket, $oUser);
        }
    }

    /**
     * Summed steps to process a payone order
     * 
     * @param object $oBasket
     * @param string $sTxid
     * @return void
     */
    protected function _fcpoProcessOrder($oBasket, $sTxid) {
        $this->_fcpoCheckTxid($oBasket);
        $iOrderNotChecked = $this->_oFcpoHelper->fcpoGetSessionVariable('fcpoordernotchecked');
        if (!$iOrderNotChecked || $iOrderNotChecked != 1) {
            $iOrderNotChecked = 0;
        }
        $this->_fcpoSaveOrderValues($sTxid, $iOrderNotChecked);
        $this->_fcpoCheckUserAgent();
    }

    /**
     * Saves payone specific orderlines
     * 
     * @param string $sTxid
     * @param int $iOrderNotChecked
     * @return void
     */
    protected function _fcpoSaveOrderValues($sTxid, $iOrderNotChecked) {
        $oConfig = $this->_oFcpoHelper->fcpoGetConfig();
        $blPresaveOrder = (bool) $oConfig->getConfigParam('blFCPOPresaveOrder');
        if ($blPresaveOrder === true) {
            $this->oxorder__oxordernr = new oxField($this->_oFcpoHelper->fcpoGetSessionVariable('fcpoOrderNr'), oxField::T_RAW);
        }
        $this->oxorder__fcpotxid = new oxField($sTxid, oxField::T_RAW);
        $this->oxorder__fcporefnr = new oxField($this->_oFcpoHelper->fcpoGetRequestParameter('refnr'), oxField::T_RAW);
        $this->oxorder__fcpoauthmode = new oxField($this->_oFcpoHelper->fcpoGetSessionVariable('fcpoAuthMode'), oxField::T_RAW);
        $this->oxorder__fcpomode = new oxField($this->_oFcpoHelper->fcpoGetSessionVariable('fcpoMode'), oxField::T_RAW);
        $this->oxorder__fcpoordernotchecked = new oxField($iOrderNotChecked, oxField::T_RAW);
        $sWorkorderId = $this->_oFcpoHelper->fcpoGetSessionVariable('payolution_workorderid');
        if ($sWorkorderId) {
            $this->oxorder__fcpoworkorderid = new oxField($sWorkorderId, oxField::T_RAW);
        }
        $this->_oFcpoDb->Execute("UPDATE fcporefnr SET fcpo_txid = '" . $sTxid . "' WHERE fcpo_refnr = '" . $this->_oFcpoHelper->fcpoGetRequestParameter('refnr') . "'");
        $this->_oFcpoHelper->fcpoDeleteSessionVariable('fcpoOrderNr');
        $this->_oFcpoHelper->fcpoDeleteSessionVariable('fcpoTxid');
        $this->_oFcpoHelper->fcpoDeleteSessionVariable('fcpoRefNr');
        $this->_oFcpoHelper->fcpoDeleteSessionVariable('fcpoAuthMode');
        $this->_oFcpoHelper->fcpoDeleteSessionVariable('fcpoRedirectUrl');
    }

    /**
     * Check Txid against transactionstatus table and set resulting order values
     * 
     * @param object $oBasket
     * @return boolean
     */
    protected function _fcpoCheckTxid($oBasket) {
        $blAppointedError = false;
        $sTxid = $this->_oFcpoHelper->fcpoGetSessionVariable('fcpoTxid');

        if ($sTxid) {
            $sQuery = "SELECT oxid FROM fcpotransactionstatus WHERE FCPO_TXACTION = 'appointed' AND fcpo_txid = '" . $sTxid . "'";
            $sTestOxid = $this->_oFcpoDb->getOne($sQuery);
        } elseif ($oBasket->getPaymentId() == 'fcpocreditcard_iframe') {
            $sQuery = "SELECT fcpo_txid FROM fcpotransactionstatus WHERE FCPO_TXACTION = 'appointed' AND fcpo_reference = " . oxDb::getDb()->quote($this->_oFcpoHelper->fcpoGetRequestParameter('refnr')) . " LIMIT 1";
            $sTestOxid = $sTxid = $this->_oFcpoDb->getOne($sQuery);
        }
        if (!$sTestOxid) {
            $blAppointedError = true;
            $this->oxorder__oxfolder = new oxField('ORDERFOLDER_PROBLEMS', oxField::T_RAW);
            $oLang = $this->_oFcpoHelper->fcpoGetLang();
            $this->oxorder__oxremark->value .= $oLang->translateString('FCPO_REMARK_APPOINTED_MISSING');
        }
        $this->_fcpoSetAppointedError($blAppointedError);
        return $blAppointedError;
    }

    /**
     * Checks the reference number and returns a string in case of check failed
     * 
     * @param void
     * @return string
     */
    protected function _fcpoCheckRefNr() {
        $sReturn = "";
        $iSessRefNr = $this->_oFcpoHelper->fcpoGetSessionVariable('fcpoRefNr');

        if ($this->_oFcpoHelper->fcpoGetRequestParameter('refnr') != $iSessRefNr) {
            $this->_oFcpoHelper->fcpoDeleteSessionVariable('fcpoRefNr');
            $sReturn = $this->_oFcpoHelper->fcpoGetLang()->translateString('FCPO_MANIPULATION');
        }

        return $sReturn;
    }

    /**
     * Overrides standard oxid _insert method
     * 
     * Inserts order object information in DB. Returns true on success.
     *
     * @return bool
     */
    protected function _insert() {
        $oConfig = $this->getConfig();
        $sShopVersion = $oConfig->getVersion();
        if (version_compare($sShopVersion, '4.7.0', '<')) {
            return parent::_insert();
        }

        $oUtilsDate = $this->_oFcpoHelper->fcpoGetUtilsDate();

        //V #M525 orderdate must be the same as it was
        if (!$this->oxorder__oxorderdate->value) {
            $this->oxorder__oxorderdate = new oxField(date('Y-m-d H:i:s', $oUtilsDate->getTime()), oxField::T_RAW);
        } else {
            $this->oxorder__oxorderdate = new oxField($oUtilsDate->formatDBDate($this->oxorder__oxorderdate->value, true));
        }

        $this->oxorder__oxshopid = new oxField($oConfig->getShopId(), oxField::T_RAW);
        $this->oxorder__oxsenddate = new oxField($oUtilsDate->formatDBDate($this->oxorder__oxsenddate->value, true));

        if (( $blInsert = parent::_insert())) {
            // setting order number
            if (!$this->oxorder__oxordernr->value) {
                $blInsert = $this->_setNumber();
            } else {
                $oCounter = $this->_oFcpoHelper->getFactoryObject('oxCounter');
                $oCounter->update($this->_getCounterIdent(), $this->oxorder__oxordernr->value);
            }
        }
        return $blInsert;
    }

    /**
     * Overrides standard oxid save method
     * 
     * Save orderarticles only when not already existing
     * 
     * Updates/inserts order object and related info to DB
     *
     * @return null
     */
    public function save($blFinishingSave = true) {
        $oConfig = $this->_oFcpoHelper->fcpoGetConfig();
        $blPresaveOrder = (bool) $oConfig->getConfigParam('blFCPOPresaveOrder');
        if ($blPresaveOrder === false || $this->isPayOnePaymentType() === false) {
            return parent::save();
        }

        if ($this->oxorder__oxshopid->value === false) {
            $oShop = $oConfig->getActiveShop();
            $this->oxorder__oxshopid = new oxField($oShop->getId());
        }

        if (( $blSave = oxBase::save())) {
            $blSaveAfterRedirect = $this->_isRedirectAfterSave();

            // saving order articles
            $oOrderArticles = $this->getOrderArticles();
            if ($oOrderArticles && count($oOrderArticles) > 0) {
                foreach ($oOrderArticles as $oOrderArticle) {
                    $oOrderArticle->save($this, $blFinishingSave);
                }
            }
        }

        return $blSave;
    }

    /**
     * Checks based on the transaction status received by PAYONE whether
     * the capture request is available for this order at the moment.
     * 
     * @return bool
     */
    public function allowCapture() {
        $blReturn = true;
        if ($this->oxorder__fcpoauthmode->value == 'authorization') {
            $blReturn = false;
        }

        if ($blReturn) {
            $iCount = $this->_oFcpoDb->GetOne("SELECT COUNT(*) FROM fcpotransactionstatus WHERE fcpo_txid = '{$this->oxorder__fcpotxid->value}'");
            $blReturn = ($iCount == 0) ? false : true;
        }

        return $blReturn;
    }

    /**
     * Checks based on the transaction status received by PAYONE whether
     * the debit request is available for this order at the moment.
     * 
     * @return bool
     */
    public function allowDebit() {
        if ($this->oxorder__fcpoauthmode->value == 'authorization') {
            $blReturn = true;
        } else {
            $iCount = $this->_oFcpoDb->GetOne("SELECT COUNT(*) FROM fcpotransactionstatus WHERE fcpo_txid = '{$this->oxorder__fcpotxid->value}' AND fcpo_txaction = 'capture'");
            if ($iCount == 0) {
                $blReturn = false;
            }
        }
        return $blReturn;
    }

    /**
     * Checks based on the payment method whether
     * the settleaccount checkbox should be shown.
     * 
     * @return bool
     */
    public function allowAccountSettlement() {
        $blReturn = (
                $this->oxorder__oxpaymenttype->value == 'fcpopayadvance' ||
                $this->oxorder__oxpaymenttype->value == 'fcpoonlineueberweisung'
                );

        return $blReturn;
    }

    /**
     * Checks based on the selected payment method for this order whether
     * the users bank data has to be transferred for the debit request.
     * 
     * @return bool
     */
    public function debitNeedsBankData() {
        $blReturn = (
                $this->oxorder__oxpaymenttype->value == 'fcpoinvoice' ||
                $this->oxorder__oxpaymenttype->value == 'fcpopayadvance' ||
                $this->oxorder__oxpaymenttype->value == 'fcpocashondel' ||
                $this->oxorder__oxpaymenttype->value == 'fcpoonlineueberweisung'
                );

        return $blReturn;
    }

    /**
     * Checks based on the payment method whether
     * the detailed product list is needed.
     * 
     * @param void
     * @return bool
     */
    public function isDetailedProductInfoNeeded() {
        $blReturn = (
                $this->oxorder__oxpaymenttype->value == 'fcpobillsafe' ||
                $this->oxorder__oxpaymenttype->value == 'fcpoklarna' ||
                $this->oxorder__oxpaymenttype->value == 'fcpocreditcard_iframe'
                );

        return $blReturn;
    }

    /**
     * Get the current sequence number of the order
     * 
     * @return int
     */
    public function getSequenceNumber() {
        $iCount = $this->_oFcpoDb->GetOne("SELECT MAX(fcpo_sequencenumber) FROM fcpotransactionstatus WHERE fcpo_txid = '{$this->oxorder__fcpotxid->value}'");

        $iReturn = ($iCount === null) ? 0 : $iCount + 1;

        return $iReturn;
    }

    /**
     * Get the last transaction status the shop received from PAYONE
     * 
     * @return object
     */
    public function getLastStatus() {
        $sOxid = $this->_oFcpoDb->GetOne("SELECT * FROM fcpotransactionstatus WHERE fcpo_txid = '{$this->oxorder__fcpotxid->value}' ORDER BY fcpo_sequencenumber DESC, fcpo_timestamp DESC");
        if ($sOxid) {
            $oStatus = $this->_oFcpoHelper->getFactoryObject('fcpotransactionstatus');
            $oStatus->load($sOxid);
        }

        $mReturn = (isset($oStatus)) ? $oStatus : false;

        return $mReturn;
    }

    /**
     * Get the API log entry from the (pre)authorization request of this order
     * 
     * @return array
     */
    protected function getResponse() {
        if ($this->_aResponse === null) {
            $sOxidRequest = $this->_oFcpoDb->GetOne("SELECT oxid FROM fcporequestlog WHERE fcpo_refnr = '{$this->oxorder__fcporefnr->value}' AND (fcpo_requesttype = 'preauthorization' OR fcpo_requesttype = 'authorization')");
            if ($sOxidRequest) {
                $oRequestLog = $this->_oFcpoHelper->getFactoryObject('fcporequestlog');
                $oRequestLog->load($sOxidRequest);
                $aResponse = $oRequestLog->getResponseArray();
                if ($aResponse) {
                    $this->_aResponse = $aResponse;
                }
            }
        }
        return $this->_aResponse;
    }

    /**
     * Get a certain parameter out of the response array
     * 
     * @return string
     */
    protected function getResponseParameter($sParameter) {
        $aResponse = $this->getResponse();
        $mReturn = ($aResponse) ? $aResponse[$sParameter] : '';

        return $mReturn;
    }

    /**
     * Get the bankaccount holder of this order out of the response array
     * 
     * @return string
     */
    public function getFcpoBankaccountholder() {
        return $this->getResponseParameter('clearing_bankaccountholder');
    }

    /**
     * Get the bankname of this order out of the response array
     * 
     * @return string
     */
    public function getFcpoBankname() {
        return $this->getResponseParameter('clearing_bankname');
    }

    /**
     * Get the bankcode of this order out of the response array
     * 
     * @return string
     */
    public function getFcpoBankcode() {
        return $this->getResponseParameter('clearing_bankcode');
    }

    /**
     * Get the banknumber of this order out of the response array
     * 
     * @return string
     */
    public function getFcpoBanknumber() {
        return $this->getResponseParameter('clearing_bankaccount');
    }

    /**
     * Get the BIC code of this order out of the response array
     * 
     * @return string
     */
    public function getFcpoBiccode() {
        return $this->getResponseParameter('clearing_bankbic');
    }

    /**
     * Get the IBAN number of this order out of the response array
     * 
     * @return string
     */
    public function getFcpoIbannumber() {
        return $this->getResponseParameter('clearing_bankiban');
    }

    /**
     * Get the capturable amount left
     * Returns order sum if the was no capture before
     * Returns order sum minus prior captures if there were captures before
     * 
     * @return double
     */
    public function getFcpoCapturableAmount() {
        $oTransaction = $this->getLastStatus();
        $dReceivable = 0;
        if ($oTransaction !== false) {
            $dReceivable = $oTransaction->fcpotransactionstatus__fcpo_receivable->value;
        }
        return $this->oxorder__oxtotalordersum->value - $dReceivable;
    }

    /**
     * Function whitch cheks if article stock is valid.
     * If not displays error and returns false.
     *
     * @param object $oBasket basket object
     *
     * @throws oxOutOfStockException exception
     *
     * @return null
     */
    public function validateStock($oBasket) {
        $oConfig = $this->getConfig();
        $blReduceStockBefore = !(bool) $oConfig->getConfigParam('blFCPOReduceStock');
        $blCheckProduct = ($blReduceStockBefore && $this->_isRedirectAfterSave()) ? false : true;

        foreach ($oBasket->getContents() as $key => $oContent) {
            try {
                $oProd = $oContent->getArticle($blCheckProduct);
            } catch (oxNoArticleException $oEx) {
                $oBasket->removeItem($key);
                throw $oEx;
            } catch (oxArticleInputException $oEx) {
                $oBasket->removeItem($key);
                throw $oEx;
            }

            if ($blCheckProduct === true) {
                // check if its still available
                if (version_compare($oConfig->getVersion(), '4.3.0', '<')) {
                    $dArtStockAmount = $this->fcGetArtStockInBasket($oBasket, $oProd->getId(), $key);
                } else {
                    $dArtStockAmount = $oBasket->getArtStockInBasket($oProd->getId(), $key);
                }
                $iOnStock = $oProd->checkForStock($oContent->getAmount(), $dArtStockAmount);
                if ($iOnStock !== true) {
                    $oEx = oxNew('oxOutOfStockException');
                    $oEx->setMessage('EXCEPTION_OUTOFSTOCK_OUTOFSTOCK');
                    $oEx->setArticleNr($oProd->oxarticles__oxartnum->value);
                    $oEx->setProductId($oProd->getId());

                    if (!is_numeric($iOnStock)) {
                        $iOnStock = 0;
                    }
                    $oEx->setRemainingAmount($iOnStock);
                    throw $oEx;
                }
            }
        }
    }

    /**
     * Returns stock of article in basket, including bundle article
     *
     * @param object $oBasket       basket object
     * @param string $sArtId        article id
     * @param string $sExpiredArtId item id of updated article
     *
     * @return double
     */
    public function fcGetArtStockInBasket($oBasket, $sArtId, $sExpiredArtId = null) {
        $dArtStock = 0;

        $aContents = $oBasket->getContents();
        foreach ($aContents as $sItemKey => $oOrderArticle) {
            if ($oOrderArticle && ( $sExpiredArtId == null || $sExpiredArtId != $sItemKey )) {
                $oArticle = $oOrderArticle->getArticle(true);
                if ($oArticle->getId() == $sArtId) {
                    $dArtStock += $oOrderArticle->getAmount();
                }
            }
        }

        return $dArtStock;
    }

    /**
     * Returns mandate filename if existing for this order
     * 
     * @param void
     * @return mixed
     */
    public function fcpoGetMandateFilename() {
        $sOxid = $this->getId();
        $sQuery = "SELECT fcpo_filename FROM fcpopdfmandates WHERE oxorderid = '{$sOxid}'";
        $sFile = $this->_oFcpoDb->GetOne($sQuery);

        return $sFile;
    }

    /**
     * Returns transaction status of order
     * 
     * @param void
     * @return array
     */
    public function fcpoGetStatus() {
        $sQuery = "SELECT oxid FROM fcpotransactionstatus WHERE fcpo_txid = '{$this->oxorder__fcpotxid->value}' ORDER BY oxid ASC";
        $oRs = $this->_oFcpoDb->Execute($sQuery);

        $aStatus = array();
        if ($oRs != false && $oRs->recordCount() > 0) {
            while (!$oRs->EOF) {
                $oTransactionStatus = $this->_oFcpoHelper->getFactoryObject('fcpotransactionstatus');
                $sTransactionStatusOxid = (isset($oRs->fields[0])) ? $oRs->fields[0] : $oRs->fields['oxid'];
                $oTransactionStatus->load($sTransactionStatusOxid);
                $aStatus[] = $oTransactionStatus;
                $oRs->moveNext();
            }
        }

        return $aStatus;
    }

    /**
     * Method checks via current paymenttype is of payone paypal type
     * 
     * @param void
     * @return boolean
     */
    public function fcIsPayPalOrder() {
        $blReturn = false;
        if ($this->oxorder__oxpaymenttype->value == 'fcpopaypal' || $this->oxorder__oxpaymenttype->value == 'fcpopaypal_express') {
            $blReturn = true;
        }
        return $blReturn;
    }

    /**
     * Handle authorization of current order
     * 
     * @param type $blReturnRedirectUrl
     * @param type $oPayGateway
     * @return boolean
     */
    public function fcHandleAuthorization($blReturnRedirectUrl = false, $oPayGateway = null) {
        $oConfig = $this->getConfig();
        $aDynvalue = $this->_oFcpoHelper->fcpoGetSessionVariable('dynvalue');
        $aDynvalue = $aDynvalue ? $aDynvalue : $this->_oFcpoHelper->fcpoGetRequestParameter('dynvalue');

        $blPresaveOrder = (bool) $oConfig->getConfigParam('blFCPOPresaveOrder');
        if ($blPresaveOrder === true) {
            $sOrderNr = $this->_fcpoGetNextOrderNr();
            $this->oxorder__oxordernr = new oxField($sOrderNr, oxField::T_RAW);
        }

        $oPORequest = $this->_oFcpoHelper->getFactoryObject('fcporequest');
        $oPayment = $this->_oFcpoHelper->getFactoryObject('oxpayment');
        $oPayment->load($this->oxorder__oxpaymenttype->value);
        $sAuthorizationType = $oPayment->oxpayments__fcpoauthmode->value;

        $sRefNr = $oPORequest->getRefNr($this);
        $this->_oFcpoHelper->fcpoSetSessionVariable('fcpoRefNr', $sRefNr);

        $aResponse = $oPORequest->sendRequestAuthorization($sAuthorizationType, $this, $this->getOrderUser(), $aDynvalue, $sRefNr);
        $sMode = $oPayment->fcpoGetMode($aDynvalue);
        $mResult = $this->_fcpoHandleAuthorizationResponse($aResponse, $oPayGateway, $sRefNr, $sMode, $sAuthorizationType, $blReturnRedirectUrl);

        return $mResult;
    }

    /**
     * Returns new valid ordernr. Method depends on shop version
     * 
     * @param void
     * @return string
     */
    protected function _fcpoGetNextOrderNr() {
        $oConfig = $this->getConfig();
        $sShopVersion = $oConfig->getVersion();

        if (version_compare($sShopVersion, '4.6.0', '>=')) {
            $oCounter = oxNew('oxCounter');
            $sOrderNr = $oCounter->getNext($this->_getCounterIdent());
        } else {
            $sQuery = "SELECT MAX(oxordernr)+1 FROM oxorder LIMIT 1";
            $sOrderNr = $this->_oFcpoDb->GetOne($sQuery);
        }

        return $sOrderNr;
    }

    /**
     * Returns the numeric code which determines if order has not beeing checked
     * 
     * @param void
     * @return int 
     */
    protected function _fcpoGetOrderNotChecked() {
        $iOrderNotChecked = $this->_oFcpoHelper->fcpoGetSessionVariable('fcpoordernotchecked');
        if (!$iOrderNotChecked || $iOrderNotChecked != 1) {
            $iOrderNotChecked = 0;
        }

        return $iOrderNotChecked;
    }

    /**
     * Cares about validation of authorization request
     * 
     * @param array $aResponse
     * @param object $oPayGateway
     * @param string $sRefNr
     * @param string $sMode
     * @param string $sAuthorizationType
     * @param bool $blReturnRedirectUrl
     * @return boolean
     */
    protected function _fcpoHandleAuthorizationResponse($aResponse, $oPayGateway, $sRefNr, $sMode, $sAuthorizationType, $blReturnRedirectUrl) {
        $mReturn = false;

        if ($aResponse['status'] == 'ERROR') {
            $this->_fcpoHandleAuthorizationError($aResponse, $oPayGateway);
            $mReturn = false;
        } elseif ($aResponse['status'] == 'APPROVED') {
            $this->_fcpoHandleAuthorizationApproved($aResponse, $sRefNr, $sAuthorizationType, $sMode);
            $mReturn = true;
        } elseif ($aResponse['status'] == 'REDIRECT') {
            $mReturn = $this->_fcpoHandleAuthorizationRedirect($aResponse, $sRefNr, $sAuthorizationType, $sMode, $blReturnRedirectUrl);
        }

        return $mReturn;
    }

    /**
     * Handles case of redirect type authorization
     * 
     * @param array $aResponse
     * @param string $sRefNr
     * @param string $sAuthorizationType
     * @param string $sMode
     * @param bool $blReturnRedirectUrl
     * @return void
     */
    protected function _fcpoHandleAuthorizationRedirect($aResponse, $sRefNr, $sAuthorizationType, $sMode, $blReturnRedirectUrl) {
        $oConfig = $this->_oFcpoHelper->fcpoGetConfig();
        $oUtils = $this->_oFcpoHelper->fcpoGetUtils();
        $iOrderNotChecked = $this->_fcpoGetOrderNotChecked();

        $blPresaveOrder = (bool) $oConfig->getConfigParam('blFCPOPresaveOrder');
        if ($blPresaveOrder === true) {
            $this->save(false);
        }

        $this->_oFcpoHelper->fcpoSetSessionVariable('fcpoTxid', $aResponse['txid']);
        $this->_oFcpoHelper->fcpoSetSessionVariable('fcpoAuthMode', $sAuthorizationType);
        $this->_oFcpoHelper->fcpoSetSessionVariable('fcpoMode', $sMode);

        $this->oxorder__fcpotxid = new oxField($aResponse['txid'], oxField::T_RAW);
        $this->oxorder__fcporefnr = new oxField($sRefNr, oxField::T_RAW);
        $this->oxorder__fcpoauthmode = new oxField($sAuthorizationType, oxField::T_RAW);
        $this->oxorder__fcpomode = new oxField($sMode, oxField::T_RAW);
        $this->oxorder__fcpoordernotchecked = new oxField($iOrderNotChecked, oxField::T_RAW);

        if ($blPresaveOrder === true) {
            $this->oxorder__oxtransstatus = new oxField('INCOMPLETE');
            $this->oxorder__oxfolder = new oxField('ORDERFOLDER_PROBLEMS');
            $this->save(false);
            $this->_oFcpoHelper->fcpoSetSessionVariable('fcpoOrderNr', $this->oxorder__oxordernr->value);
            $this->_fcpoCheckReduceBefore();
        }

        if ($blReturnRedirectUrl === true) {
            return $aResponse['redirecturl'];
        } else {
            if ($this->isPayOneIframePayment()) {
                $this->_oFcpoHelper->fcpoSetSessionVariable('fcpoRedirectUrl', $aResponse['redirecturl']);
                $sRedirectUrl = $oConfig->getCurrentShopUrl() . 'index.php?cl=fcpayoneiframe';
            } else {
                $sRedirectUrl = $aResponse['redirecturl'];
            }
            $oUtils->redirect($sRedirectUrl);
        }
    }

    /**
     * Reduces stock of article before if its configured this way and a redirect payment has been used
     * 
     * @param void
     * @return void
     */
    protected function _fcpoCheckReduceBefore() {
        $oConfig = $this->getConfig();
        $sPaymentId = $this->oxorder__oxpaymenttype->value;
        $blReduceStockBefore = !(bool) $oConfig->getConfigParam('blFCPOReduceStock');
        $blIsRedirectPayment = fcPayOnePayment::fcIsPayOneRedirectType($sPaymentId);

        if ($blReduceStockBefore && $blIsRedirectPayment) {
            $aOrderArticles = $this->getOrderArticles();
            foreach ($aOrderArticles as $oOrderArticle) {
                $oOrderArticle->updateArticleStock($oOrderArticle->oxorderarticles__oxamount->value * (-1), $oConfig->getConfigParam('blAllowNegativeStock'));
            }
        }
    }

    /**
     * Handles case of approved authorization
     * 
     * @param array $aResponse
     * @param string $sRefNr
     * @param string $sAuthorizationType
     * @param string $sMode
     * @return void
     */
    protected function _fcpoHandleAuthorizationApproved($aResponse, $sRefNr, $sAuthorizationType, $sMode) {
        $iOrderNotChecked = $this->_fcpoGetOrderNotChecked();
        $sPaymentId = $this->oxorder__oxpaymenttype->value;

        $this->oxorder__fcpotxid = new oxField($aResponse['txid'], oxField::T_RAW);
        $this->oxorder__fcporefnr = new oxField($sRefNr, oxField::T_RAW);
        $this->oxorder__fcpoauthmode = new oxField($sAuthorizationType, oxField::T_RAW);
        $this->oxorder__fcpomode = new oxField($sMode, oxField::T_RAW);
        $this->oxorder__fcpoordernotchecked = new oxField($iOrderNotChecked, oxField::T_RAW);
        $this->_oFcpoDb->Execute("UPDATE fcporefnr SET fcpo_txid = '{$aResponse['txid']}' WHERE fcpo_refnr = '" . $sRefNr . "'");
        if ($sPaymentId == 'fcpobarzahlen' && isset($aResponse['add_paydata[instruction_notes]'])) {
            $sBarzahlenHtml = urldecode($aResponse['add_paydata[instruction_notes]']);
            $this->_oFcpoHelper->fcpoSetSessionVariable('sFcpoBarzahlenHtml', $sBarzahlenHtml);
        }

        $this->_fcpoSaveWorkorderId($sPaymentId, $aResponse);
        $this->_fcpoSaveClearingReference($sPaymentId, $aResponse);
        $this->_fcpoSaveProfileIdent($sPaymentId, $aResponse);
    }

    /**
     * For certain payments it's mandatory to save workorderid
     * 
     * @param string $sPaymentId
     * @param array $aResponse
     * @return void
     */
    protected function _fcpoSaveWorkorderId($sPaymentId, $aResponse) {
        if (in_array($sPaymentId, $this->_aPaymentsWorkorderIdSave)) {
            $sWorkorderId = (isset($aResponse['add_paydata[workorderid]'])) ? $aResponse['add_paydata[workorderid]'] : false; // 
            if ($sWorkorderId) {
                $this->oxorder__fcpoworkorderid = new oxField($sWorkorderId, oxField::T_RAW);
                $this->_oFcpoHelper->fcpoDeleteSessionVariable('payolution_workorderid');
            }
        }
    }

    /**
     * For certain payments it's mandatory to save clearing reference
     * 
     * @param string $sPaymentId
     * @param array $aResponse
     * @return void
     */
    protected function _fcpoSaveClearingReference($sPaymentId, $aResponse) {
        if (in_array($sPaymentId, $this->_aPaymentsClearingReferenceSave)) {
            $sClearingReference = (isset($aResponse['add_paydata[clearing_reference]'])) ? $aResponse['add_paydata[clearing_reference]'] : false;
            if ($sClearingReference) {
                $this->oxorder__fcpoclearingreference = new oxField($sClearingReference, oxField::T_RAW);
            }
        }
    }

    /**
     * For certain payments it's mandatory to save (external) shopid/userid (e. g- ratepay payments)
     * 
     * @param string $sPaymentId
     * @param array $aResponse
     * @return void
     */
    protected function _fcpoSaveProfileIdent($sPaymentId, $aResponse) {
        if (in_array($sPaymentId, $this->_aPaymentsProfileIdentSave)) {
            $sProfileIdent = (isset($aResponse['userid'])) ? $aResponse['userid'] : false;
            if ($sProfileIdent) {
                $this->oxorder__fcpoprofileident = new oxField($sProfileIdent, oxField::T_RAW);
            }
        }
    }

    /**
     * Handles case of Authorization error
     * 
     * @param array $aResponse
     * @param object $oPayGateway
     * @return void
     */
    protected function _fcpoHandleAuthorizationError($aResponse, $oPayGateway) {
        if ($oPayGateway) {
            $oPayGateway->fcSetLastErrorNr($aResponse['errorcode']);
            $oPayGateway->fcSetLastError($aResponse['customermessage']);
        }
    }

    /**
     * Returns wether given paymentid is of payone type
     * 
     * @param string $sId
     * @param bool $blIFrame
     * @return bool
     */
    protected function _fcpoIsPayonePaymentType($sId, $blIFrame = false) {
        if ($blIFrame) {
            $blReturn = fcPayOnePayment::fcIsPayOneIframePaymentType($sId);
        } else {
            $blReturn = fcPayOnePayment::fcIsPayOnePaymentType($sId);
        }

        return $blReturn;
    }

    /**
     * Returns true if appointed error occured
     * 
     * @return bool
     */
    protected function _fcpoGetAppointedError() {
        return $this->_blFcPayoneAppointedError;
    }

    /**
     * Sets appointed error 
     * 
     * @param bool $blError appointed error indicator
     * @return void
     */
    protected function _fcpoSetAppointedError($blError = false) {
        $this->_blFcPayoneAppointedError = $blError;
    }

}
