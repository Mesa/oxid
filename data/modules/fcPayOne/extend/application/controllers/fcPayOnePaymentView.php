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
class fcPayOnePaymentView extends fcPayOnePaymentView_parent {

    /**
     * Helper object for dealing with different shop versions
     * @var object
     */
    protected $_oFcpoHelper = null;

    /**
     * Helper object for dealing with different shop versions
     * @var object
     */
    protected $_oFcpoDb = null;

    /**
     * bill country id of the user object
     * @var string
     */
    protected $_sUserBillCountryId = null;

    /**
     * delivery country id if existant
     * @var string
     */
    protected $_sUserDelCountryId = null;

    /**
     * Contains the sub payment methods that are available for the user ( Visa, MC, etc. )
     * @var array
     */
    protected $_aCheckedSubPayments = array();

    /**
     * Array of isoalpha2-countries for which birthday is needed
     * @var array
     */
    protected $_aKlarnaBirthdayNeededCountries = array('DE', 'NL', 'AT');

    /**
     * Datacontainer for all cc payment meta data
     * @var array
     */
    protected $_aPaymentCCMetaData = array();

    /**
     * Base link for payolution agreement overlay
     * @var string
     */
    protected $_sPayolutionAgreementBaseLink = 'https://payment.payolution.com/payolution-payment/infoport/dataprivacydeclaration?mId=';

    /**
     * Mandate link for payolution debitnote 
     * @var string
     */
    protected $_sPayolutionSepaAgreement = 'https://payment.payolution.com/payolution-payment/infoport/sepa/mandate.pdf';

    /**
     * Holder for installment calculation data
     * @var array
     */
    protected $_aInstallmentCalculation = array();

    /**
     * Flag which indicates, that functionality is called from outside via ajax
     * @var bool
     */
    protected $_blIsPayolutionInstallmentAjax = null;

    /**
     * Params holder for payolution installment params
     * @var array
     */
    protected $_aAjaxPayolutionParams = array();

    /**
     * Contains profile which matched for ratepay invoice payment
     * @var string
     */
    protected $_aRatePayBillProfileIds = array('fcporp_bill' => null);

    /**
     * init object construction
     * 
     * @return null
     */
    public function __construct() {
        parent::__construct();
        $this->_oFcpoHelper = oxNew('fcpohelper');
        $this->_oFcpoDb = oxDb::getDb();
        $this->_blIsPayolutionInstallmentAjax = false;
    }

    /**
     * Extends oxid standard method init()
     * Executes parent method parent::init().
     *
     * @return null
     */
    public function init() {
        if ($this->_hasFilterDynDataMethod() === false) {
            $this->_filterDynData();
        }
        $oConfig = $this->_oFcpoHelper->fcpoGetConfig();

        $sOrderId = $this->_oFcpoHelper->fcpoGetSessionVariable('sess_challenge');
        $sType = $this->_oFcpoHelper->fcpoGetRequestParameter('type');
        $blPresaveOrder = (bool) $oConfig->getConfigParam('blFCPOPresaveOrder');
        $blReduceStockBefore = !(bool) $oConfig->getConfigParam('blFCPOReduceStock');
        if ($sOrderId && $blPresaveOrder && $blReduceStockBefore && ($sType == 'error' || $sType == 'cancel')) {
            $oOrder = $this->_oFcpoHelper->getFactoryObject('oxorder');
            $oOrder->load($sOrderId);
            if ($oOrder) {
                $oOrder->cancelOrder();
            }
            unset($oOrder);
        }
        $this->_oFcpoHelper->fcpoDeleteSessionVariable('sess_challenge');

        parent::init();
    }

    /**
     * Returns matched profile
     * 
     * @param string $sPaymentId
     * @return string
     */
    public function fcpoGetRatePayMatchedProfile($sPaymentId) {
        return $this->_aRatePayBillProfileIds[$sPaymentId];
    }

    /**
     * Returns matching notiication string if sofo is configured to show iban
     * 
     * @param void
     * @return string
     */
    public function fcpoGetSofoShowIban() {
        $oConfig = $this->getConfig();
        $blFCPOSofoShowIban = $oConfig->getConfigParam('blFCPOSofoShowIban');

        $sReturn = ($blFCPOSofoShowIban) ? 'true' : 'false';
        return $sReturn;
    }

    /**
     * Gets config parameter
     * 
     * @param string $sParam config parameter name
     * 
     * @return string
     */
    public function getConfigParam($sParam) {
        $oConfig = $this->getConfig();
        return $oConfig->getConfigParam($sParam);
    }

    /**
     * Get config parameter sFCPOMerchantID
     * 
     * @return string
     */
    public function getMerchantId() {
        return $this->getConfigParam('sFCPOMerchantID');
    }

    /**
     * Get config parameter sFCPOSubAccountID
     * 
     * @return string
     */
    public function getSubAccountId() {
        return $this->getConfigParam('sFCPOSubAccountID');
    }

    /**
     * Get config parameter sFCPOPortalID
     * 
     * @return string
     */
    public function getPortalId() {
        return $this->getConfigParam('sFCPOPortalID');
    }

    /**
     * Get config parameter sFCPOPortalKey
     * 
     * @return string
     */
    public function getPortalKey() {
        return $this->getConfigParam('sFCPOPortalKey');
    }

    /**
     * Get config parameter sFCPOPOSCheck
     * 
     * @return string
     */
    public function getChecktype() {
        return $this->getConfigParam('sFCPOPOSCheck');
    }

    /**
     * Method checks if current ratepay payment has a matching profile based on activity and basket values
     * 
     * @param string $sPaymentId
     * @return void
     */
    public function fcpoRatePayAllowed($sPaymentId) {
        $aMatchingRatePayProfile = $this->_fcpoGetMatchingProfile($sPaymentId);
        $blReturn = false;
        if (count($aMatchingRatePayProfile) > 0) {
            $blReturn = true;
        }

        if ($blReturn === true) {
            $this->_aRatePayBillProfileIds[$sPaymentId] = $aMatchingRatePayProfile['OXID'];
        }

        return $blReturn;
    }

    /**
     * Check if there are available sub payment types for the user
     * 
     * @param string $sType payment type PAYONE
     * 
     * @return bool
     */
    public function hasPaymentMethodAvailableSubTypes($sType) {
        $aSubtypes = array(
            'cc' => array(
                $this->getVisa(),
                $this->getMastercard(),
                $this->getAmex(),
                $this->getDiners(),
                $this->getJCB(),
                $this->getMaestroInternational(),
                $this->getMaestroUK(),
                $this->getDiscover(),
                $this->getCarteBleue(),
            ),
            'sb' => array(
                $this->getSofortUeberweisung(),
                $this->getGiropay(),
                $this->getPostFinanceEFinance(),
                $this->getPostFinanceCard(),
                $this->getIdeal(),
                $this->getP24(),
            ),
        );

        $blReturn = in_array(true, $aSubtypes[$sType]);

        return $blReturn;
    }

    /**
     * Return the default type for this payment-type for the mobile-theme
     * 
     * @return string
     */
    public function getDefaultOnlineUeberweisung() {
        if ($this->getSofortUeberweisung())
            return 'PNT';
        if ($this->getGiropay())
            return 'GPY';
        if ($this->getEPS())
            return 'EPS';
        if ($this->getPostFinanceEFinance())
            return 'PFF';
        if ($this->getPostFinanceCard())
            return 'PFC';
        if ($this->getIdeal())
            return 'IDL';
        if ($this->getP24())
            return 'P24';
        return '';
    }

    /**
     * Check if sub payment method Visa is available to the user
     * 
     * @return bool
     */
    public function getVisa() {
        return ($this->getConfigParam('blFCPOVisaActivated') && $this->isPaymentMethodAvailableToUser('V', 'cc'));
    }

    /**
     * Check if sub payment method Mastercard is available to the user
     * 
     * @return bool
     */
    public function getMastercard() {
        return ($this->getConfigParam('blFCPOMastercardActivated') && $this->isPaymentMethodAvailableToUser('M', 'cc'));
    }

    /**
     * Check if sub payment method Amex is available to the user
     * 
     * @return bool
     */
    public function getAmex() {
        return ($this->getConfigParam('blFCPOAmexActivated') && $this->isPaymentMethodAvailableToUser('A', 'cc'));
    }

    /**
     * Check if sub payment method Diners is available to the user
     * 
     * @return bool
     */
    public function getDiners() {
        return ($this->getConfigParam('blFCPODinersActivated') && $this->isPaymentMethodAvailableToUser('D', 'cc'));
    }

    /**
     * Check if sub payment method JCB is available to the user
     * 
     * @return bool
     */
    public function getJCB() {
        return ($this->getConfigParam('blFCPOJCBActivated') && $this->isPaymentMethodAvailableToUser('J', 'cc'));
    }

    /**
     * Check if sub payment method MaestroInternational is available to the user
     * 
     * @return bool
     */
    public function getMaestroInternational() {
        return ($this->getConfigParam('blFCPOMaestroIntActivated') && $this->isPaymentMethodAvailableToUser('O', 'cc'));
    }

    /**
     * Check if sub payment method MaestroUK is available to the user
     * 
     * @return bool
     */
    public function getMaestroUK() {
        return ($this->getConfigParam('blFCPOMaestroUKActivated') && $this->isPaymentMethodAvailableToUser('U', 'cc'));
    }

    /**
     * Check if sub payment method Discover is available to the user
     * 
     * @return bool
     */
    public function getDiscover() {
        return ($this->getConfigParam('blFCPODiscoverActivated') && $this->isPaymentMethodAvailableToUser('C', 'cc'));
    }

    /**
     * Check if sub payment method CarteBleue is available to the user
     * 
     * @return bool
     */
    public function getCarteBleue() {
        return ($this->getConfigParam('blFCPOCarteBleueActivated') && $this->isPaymentMethodAvailableToUser('B', 'cc'));
    }

    /**
     * Check if sub payment method SofortUeberweisung is available to the user
     * 
     * @return bool
     */
    public function getSofortUeberweisung() {
        return ($this->getConfigParam('blFCPOSofoActivated') && $this->isPaymentMethodAvailableToUser('PNT', 'sb'));
    }

    /**
     * Check if sub payment method Giropay is available to the user
     * 
     * @return bool
     */
    public function getGiropay() {
        return ($this->getConfigParam('blFCPOgiroActivated') && $this->isPaymentMethodAvailableToUser('GPY', 'sb'));
    }

    /**
     * Check if sub payment method EPS is available to the user
     * 
     * @return bool
     */
    public function getEPS() {
        return ($this->getConfigParam('blFCPOepsActivated') && $this->isPaymentMethodAvailableToUser('EPS', 'sb'));
    }

    /**
     * Check if sub payment method PostFinanceEFinance is available to the user
     * 
     * @return bool
     */
    public function getPostFinanceEFinance() {
        return ($this->getConfigParam('blFCPOPoFiEFActivated') && $this->isPaymentMethodAvailableToUser('PFF', 'sb'));
    }

    /**
     * Check if sub payment method PostFinanceCard is available to the user
     * 
     * @return bool
     */
    public function getPostFinanceCard() {
        return ($this->getConfigParam('blFCPOPoFiCaActivated') && $this->isPaymentMethodAvailableToUser('PFC', 'sb'));
    }

    /**
     * Check if sub payment method Ideal is available to the user
     * 
     * @return bool
     */
    public function getIdeal() {
        return ($this->getConfigParam('blFCPOiDealActivated') && $this->isPaymentMethodAvailableToUser('IDL', 'sb'));
    }

    /**
     * Check if sub payment method Ideal is available to the user
     * 
     * @return bool
     */
    public function getP24() {
        return ($this->getConfigParam('blFCPOP24Activated') && $this->isPaymentMethodAvailableToUser('P24', 'sb'));
    }

    /**
     * Get encoding of the shop
     * 
     * @return string
     */
    public function getEncoding() {
        $oConfig = $this->getConfig();
        if ($oConfig->isUtf()) {
            return 'UTF-8';
        }
        return 'ISO-8859-1';
    }

    /**
     * Get the basket brut price in the smallest unit of the currency
     * 
     * @return int
     */
    public function getAmount() {
        $oSession = $this->_oFcpoHelper->fcpoGetSession();
        $oBasket = $oSession->getBasket();
        $oPrice = $oBasket->getPrice();
        $dPrice = $oPrice->getBruttoPrice();

        return number_format($dPrice, 2, '.', '') * 100;
    }

    /**
     * Get the language the user is using in the shop
     * 
     * @return string
     */
    public function getTplLang() {
        $oLang = $this->_oFcpoHelper->fcpoGetLang();
        return $oLang->getLanguageAbbr();
    }

    /*
     * Return language id
     * 
     * @return int
     */

    public function fcGetLangId() {
        $oLang = $this->_oFcpoHelper->fcpoGetLang();
        $iLang = ( $iLang === null && isAdmin() ) ? $oLang->getTplLanguage() : $iLang;
        if (!isset($iLang)) {
            $iLang = $oLang->getBaseLanguage();
        }
        return $iLang;
    }

    /**
     * Get verification safety hash for creditcard payment method
     * 
     * @return string
     */
    public function getHashCC($sType = '') {
        $sHash = md5(
                $this->getSubAccountId() .
                $this->getEncoding() .
                $this->getMerchantId() .
                $this->_getOperationModeCC($sType) .
                $this->getPortalId() .
                'creditcardcheck' .
                'JSON' .
                'yes' .
                $this->getPortalKey()
        );
        return $sHash;
    }

    /**
     * Template getter for delivering all meta information to build input fields in foreach loop
     * 
     * @param void
     * @return array
     */
    public function fcpoGetCCPaymentMetaData() {
        $this->_aPaymentCCMetaData = array();
        $sPaymentId = 'fcpocreditcard';

        $oPayment = oxNew('oxpayment');
        $oPayment->load($sPaymentId);

        $this->_fcpoSetCCMetaData($oPayment, 'V', 'Visa');
        $this->_fcpoSetCCMetaData($oPayment, 'M', 'Mastercard');
        $this->_fcpoSetCCMetaData($oPayment, 'A', 'American Express');
        $this->_fcpoSetCCMetaData($oPayment, 'D', 'Diners Club');
        $this->_fcpoSetCCMetaData($oPayment, 'J', 'JCB');
        $this->_fcpoSetCCMetaData($oPayment, 'O', 'Maestro International');
        $this->_fcpoSetCCMetaData($oPayment, 'U', 'Maestro UK');
        $this->_fcpoSetCCMetaData($oPayment, 'C', 'Discover');
        $this->_fcpoSetCCMetaData($oPayment, 'B', 'Carte Bleue');

        return $this->_aPaymentCCMetaData;
    }

    /**
     * Template getter for delivering payment meta data of online payments
     * 
     * @param void
     * @return array
     */
    public function fcpoGetOnlinePaymentMetaData() {
        $aPaymentMetaData = array();

        if ($this->getSofortUeberweisung()) {
            $aPaymentMetaData[] = $this->_fcpoGetOnlinePaymentData('PNT');
        }
        if ($this->getGiropay()) {
            $aPaymentMetaData[] = $this->_fcpoGetOnlinePaymentData('GPY');
        }
        if ($this->getEPS()) {
            $aPaymentMetaData[] = $this->_fcpoGetOnlinePaymentData('EPS');
        }
        if ($this->getPostFinanceEFinance()) {
            $aPaymentMetaData[] = $this->_fcpoGetOnlinePaymentData('PFF');
        }
        if ($this->getPostFinanceCard()) {
            $aPaymentMetaData[] = $this->_fcpoGetOnlinePaymentData('PFC');
        }
        if ($this->getIdeal()) {
            $aPaymentMetaData[] = $this->_fcpoGetOnlinePaymentData('IDL');
        }
        if ($this->getP24()) {
            $aPaymentMetaData[] = $this->_fcpoGetOnlinePaymentData('P24');
        }

        return $aPaymentMetaData;
    }

    /**
     * Get verification safety hash for debitnote payment method with checktype parameter
     * 
     * @return string
     */
    public function getHashELVWithChecktype() {
        $sHash = md5(
                $this->getSubAccountId() .
                $this->getChecktype() .
                $this->getEncoding() .
                $this->getMerchantId() .
                $this->_getOperationModeELV() .
                $this->getPortalId() .
                'bankaccountcheck' .
                'JSON' .
                $this->getPortalKey()
        );
        return $sHash;
    }

    /**
     * Get verification safety hash for debitnote payment method without checktype parameter
     * 
     * @return string
     */
    public function getHashELVWithoutChecktype() {
        $sHash = md5(
                $this->getSubAccountId() .
                $this->getEncoding() .
                $this->getMerchantId() .
                $this->_getOperationModeELV() .
                $this->getPortalId() .
                'bankaccountcheck' .
                'JSON' .
                $this->getPortalKey()
        );
        return $sHash;
    }

    /**
     * Extends oxid standard method getPaymentList
     * Extends it with the creditworthiness check for the user
     * 
     * @return string
     * @extend  getPaymentList
     */
    public function getPaymentList() {
        $this->_oFcpoHelper->fcpoDeleteSessionVariable('fcpoordernotchecked');
        if ($this->_oPaymentList === null) {
            $oConfig = $this->getConfig();
            $oUser = $this->getUser();
            $blContinue = false;

            if ($oUser && $oConfig->getConfigParam('sFCPOBonicheckMoment') != 'after') {
                $blContinue = $oUser->checkAddressAndScore();
            } else {
                $blContinue = $oUser->checkAddressAndScore(true, false);
            }

            if ($blContinue === true) {
                parent::getPaymentList();
                $this->_fcpoCheckPaypalExpressRemoval();
            } else {
                $oUtils = $this->_oFcpoHelper->fcpoGetUtils();
                $oUtils->redirect($this->getConfig()->getShopHomeURL() . 'cl=user', false);
            }
        }
        return $this->_oPaymentList;
    }

    /**
     * Returns if CVC
     * 
     * @param void
     * @return bool
     */
    public function fcpoUseCVC() {
        $oConfig = $this->getConfig();
        $blUseCVC = $oConfig->getConfigParam('blFCPOCCUseCvc');

        return $blUseCVC;
    }

    /**
     * Returns if option for BIC is set mandatory as string to handle it with javascript checks
     * 
     * @param void
     * @return string
     */
    public function fcpoGetBICMandatory() {
        $oConfig = $this->getConfig();
        $blFCPODebitBICMandatory = $oConfig->getConfigParam('blFCPODebitBICMandatory');

        $sReturn = ($blFCPODebitBICMandatory) ? 'true' : 'false';

        return $sReturn;
    }

    /**
     * Returns creditcard type
     * 
     * @param void
     * @return mixed
     */
    public function fcpoGetCreditcardType() {
        $oConfig = $this->getConfig();
        return $oConfig->getConfigParam('sFCPOCCType');
    }

    /**
     * Extends oxid standard method validatePayment
     * Extends it with the creditworthiness check for the user
     * 
     * Validates oxidcreditcard and oxiddebitnote user payment data.
     * Returns null if problems on validating occured. If everything
     * is OK - returns "order" and redirects to payment confirmation
     * page.
     *
     * Session variables:
     * <b>paymentid</b>, <b>dynvalue</b>, <b>payerror</b>
     *
     * @return  mixed
     */
    public function validatePayment() {
        $oUser = $this->getUser();

        $sPaymentId = $this->_fcpoGetPaymentId();
        $this->_fcpoCheckKlarnaUpdateUser($sPaymentId);

        $mReturn = parent::validatePayment();
        
        $mReturn = $this->_processParentReturnValue($mReturn);
        $mReturn = $this->_fcpoProcessValidation($mReturn, $sPaymentId);

        return $mReturn;
    }

    /**
     * Template getter for previously calculated installments
     * 
     * @param void
     * @return array
     */
    public function fcpoGetInstallments() {
        return $this->_aInstallmentCalculation;
    }

    /**
     * Get configured operation mode ( live or test ) for creditcard
     * 
     * @param string $sType sub payment type PAYONE
     * 
     * @return string
     */
    protected function _getOperationModeCC($sType = '') {
        $oPayment = oxNew('oxpayment');
        $oPayment->load('fcpocreditcard');
        return $oPayment->fcpoGetOperationMode($sType);
    }

    /**
     * Will return first matching profile or empty array
     * 
     * @param string $sPaymentId
     * @return array
     */
    protected function _fcpoGetMatchingProfile($sPaymentId) {
        $aRatePayProfiles = $this->_fcpoFetchRatePayProfilesByPaymentType($sPaymentId);
        $aReturn = array();

        foreach ($aRatePayProfiles as $aCurrentRatePayProfile) {
            $sPaymentStringAddition = $this->_fcpoGetRatePayStringAdditionByPaymentId($sPaymentId);
            if ($sPaymentStringAddition) {
                $sProfileBasketMaxIndex = 'tx_limit_' . $sPaymentStringAddition . '_max';
                $sProfileBasketMinIndex = 'tx_limit_' . $sPaymentStringAddition . '_min';
                $sProfileActivationStatusIndex = 'activation_status_' . $sPaymentStringAddition;
                $dProfileBasketValueMax = (double) $aCurrentRatePayProfile[$sProfileBasketMaxIndex];
                $dProfileBasketValueMin = (double) $aCurrentRatePayProfile[$sProfileBasketMinIndex];
                $sProfileActivationStatus = $aCurrentRatePayProfile[$sProfileActivationStatusIndex];

                $aRatepayMatchData = array(
                    'basketvalue_max' => $dProfileBasketValueMax,
                    'basketvalue_min' => $dProfileBasketValueMin,
                    'activation_status' => $sProfileActivationStatus,
                );

                $blProfileMatches = $this->_fcpoCheckRatePayProfileMatch($aRatepayMatchData);
                if ($blProfileMatches) {
                    $aReturn = $aCurrentRatePayProfile;
                    break;
                }
            }
        }

        return $aReturn;
    }

    /**
     * Checks values for matching profile data
     * 
     * @param array $aRatepayMatchData
     * @return boolean
     */
    protected function _fcpoCheckRatePayProfileMatch($aRatepayMatchData) {
        $blReturn = false;
        if ($aRatepayMatchData['activation_status'] == '2') {
            $dBasketValue = $this->fcpoGetBasketSum();

            if ($dBasketValue <= $aRatepayMatchData['basketvalue_max'] && $dBasketValue >= $aRatepayMatchData['basketvalue_min']) {
                $blReturn = true;
            }
        }

        return $blReturn;
    }

    /**
     * Returns string part, that matches right profile values
     * 
     * @param string $sPaymentId
     * @return string
     */
    protected function _fcpoGetRatePayStringAdditionByPaymentId($sPaymentId) {
        $aMap = array(
            'fcporp_bill' => 'invoice',
        );

        $sReturn = '';
        if (isset($aMap[$sPaymentId])) {
            $sReturn = $aMap[$sPaymentId];
        }

        return $sReturn;
    }

    /**
     * Returns all profiles for given RatePay payment type
     * 
     * @param string $sPaymentId
     * @return array
     */
    protected function _fcpoFetchRatePayProfilesByPaymentType($sPaymentId) {
        $oRatePay = oxNew('fcporatepay');
        $aProfiles = $oRatePay->fcpoGetRatePayProfiles($sPaymentId);

        return $aProfiles;
    }

    /**
     * Checks whether the oxid version has the _filterDynData method
     * Oxid 4.2 and below dont have the _filterDynData method
     * 
     * @return bool
     */
    protected function _hasFilterDynDataMethod() {
        $iVersion = $this->_oFcpoHelper->fcpoGetIntShopVersion();
        $oConfig = $this->_oFcpoHelper->fcpoGetConfig();
        $sVersion = $oConfig->getVersion();
        $blReturn = (version_compare($sVersion, '4.3.0', '>=')) ? true : false;

        return $blReturn;
    }

    /**
     * Extends oxid standard method _filterDynData()
     * Unsets the PAYONE form-data fields containing creditcard data
     * 
     * Due to legal reasons probably you are not allowed to store or even handle credit card data.
     * In this case we just delete and forget all submited credit card data from this point.
     * Override this method if you actually want to process credit card data.
     *
     * Note: You should override this method as setting blStoreCreditCardInfo to true would
     *       force storing CC data on shop side (what most often is illegal).
     *
     * @return null
     * @extend _filterDynData
     */
    protected function _filterDynData() {
        if ($this->_hasFilterDynDataMethod() === true) {
            parent::_filterDynData();
        }

        $oConfig = $this->_oFcpoHelper->fcpoGetConfig();

        //in case we actually ARE allowed to store the data
        if ($oConfig->getConfigParam("blStoreCreditCardInfo"))
        //then do nothing
            return;

        $aDynData = $this->_oFcpoHelper->fcpoGetSessionVariable("dynvalue");

        if ($aDynData) {
            $aDynData["fcpo_kktype"] = null;
            $aDynData["fcpo_kknumber"] = null;
            $aDynData["fcpo_kkname"] = null;
            $aDynData["fcpo_kkmonth"] = null;
            $aDynData["fcpo_kkyear"] = null;
            $aDynData["fcpo_kkpruef"] = null;
            $aDynData["fcpo_kkcsn"] = null;
            $this->_oFcpoHelper->fcpoSetSessionVariable("dynvalue", $aDynData);
        }

        $aParameters = array(
            'fcpo_kktype',
            'fcpo_kknumber',
            'fcpo_kkname',
            'fcpo_kkmonth',
            'fcpo_kkyear',
            'fcpo_kkpruef',
            'fcpo_kkcsn'
        );

        foreach ($aParameters as $sParameter) {
            unset($_REQUEST['dynvalue'][$sParameter]);
            unset($_POST['dynvalue'][$sParameter]);
            unset($_GET['dynvalue'][$sParameter]);
        }
    }

    /**
     * Get, and set if needed, the bill country id of the user object
     * 
     * @return string
     */
    protected function getUserBillCountryId() {
        if ($this->_sUserBillCountryId === null) {
            $oUser = $this->getUser();
            $this->_sUserBillCountryId = $oUser->oxuser__oxcountryid->value;
        }
        return $this->_sUserBillCountryId;
    }

    /**
     * Get, and set if needed, the delivery country id if existant
     * 
     * @return string
     */
    protected function getUserDelCountryId() {
        if ($this->_sUserDelCountryId === null) {
            $oOrder = $this->_oFcpoHelper->getFactoryObject('oxorder');
            $oDelAddress = $oOrder->getDelAddressInfo();
            $sUserDelCountryId = false;
            if ($oDelAddress !== null) {
                $sUserDelCountryId = $oDelAddress->oxaddress__oxcountryid->value;
            }
            $this->_sUserDelCountryId = $sUserDelCountryId;
        }
        return $this->_sUserDelCountryId;
    }

    /**
     * Check if the user is allowed to use the given payment method
     * 
     * @param string $sSubPaymentId ID of the sub payment method ( Visa, MC, etc. )
     * @param string $sType payment type PAYONE
     * 
     * @return bool
     */
    protected function isPaymentMethodAvailableToUser($sSubPaymentId, $sType) {
        if (array_key_exists($sSubPaymentId . '_' . $sType, $this->_aCheckedSubPayments) === false) {
            $sUserBillCountryId = $this->getUserBillCountryId();
            $sUserDelCountryId = $this->getUserDelCountryId();
            $oPayment = oxNew('oxPayment');
            $this->_aCheckedSubPayments[$sSubPaymentId . '_' . $sType] = $oPayment->isPaymentMethodAvailableToUser($sSubPaymentId, $sType, $sUserBillCountryId, $sUserDelCountryId);
        }
        return $this->_aCheckedSubPayments[$sSubPaymentId . '_' . $sType];
    }

    /**
     * Sets cc meta payment data
     * 
     * @param oxPayment $oPayment
     * @param string $sBrandShortcut
     * @param string $sBrandName
     */
    protected function _fcpoSetCCMetaData($oPayment, $sBrandShortcut, $sBrandName) {
        $aActiveCCBrands = array(
            'V' => $this->getVisa(),
            'M' => $this->getMastercard(),
            'A' => $this->getAmex(),
            'D' => $this->getDiners(),
            'J' => $this->getJCB(),
            'O' => $this->getMaestroInternational(),
            'U' => $this->getMaestroUK(),
            'C' => $this->getDiscover(),
            'B' => $this->getCarteBleue(),
        );

        if ($aActiveCCBrands[$sBrandShortcut]) {
            $this->_aPaymentCCMetaData[] = $this->_fcpoGetCCPaymentMetaData($oPayment, $sBrandShortcut, $sBrandName);
        }
    }

    /**
     * Returns online payment meta data object for ident
     * 
     * @param string $sIdent
     * @return object
     */
    protected function _fcpoGetOnlinePaymentData($sIdent) {
        $aDynValue = $this->getDynValue();
        $blSelected = ( $aDynValue['fcpo_sotype'] == $sIdent ) ? true : false;

        $aCaptions = array(
            'PNT' => 'SOFORT &Uuml;berweisung',
            'GPY' => 'giropay',
            'EPS' => 'eps - Online-&Uuml;berweisung',
            'PFF' => 'PostFinance E-Finance',
            'PFC' => 'PostFinance Card',
            'IDL' => 'iDeal',
            'P24' => 'P24',
        );

        $sCaption = ($aCaptions[$sIdent]) ? $aCaptions[$sIdent] : '';

        $oPaymentMetaData = new stdClass();
        $oPaymentMetaData->sShortcut = $sIdent;
        $oPaymentMetaData->sCaption = $sCaption;
        $oPaymentMetaData->blSelected = $blSelected;

        return $oPaymentMetaData;
    }

    /**
     * Returns a payment meta data object for payment method and its payment-tag
     * 
     * @param object $oPayment
     * @param string $sPaymentTag
     * @return object
     */
    protected function _fcpoGetCCPaymentMetaData($oPayment, $sPaymentTag, $sPaymentName) {
        $sPaymentId = $oPayment->getId();
        $sHashNamePrefix = "fcpo_hashcc_";
        $OperationModeNamePrefix = "fcpo_mode_";
        $aDynValue = $this->getDynValue();
        $blSelected = ( $aDynValue['fcpo_kktype'] == $sPaymentTag ) ? true : false;

        $oPaymentMetaData = new stdClass();
        $oPaymentMetaData->sHashName = $sHashNamePrefix . $sPaymentTag;
        $oPaymentMetaData->sHashValue = $this->getHashCC($sPaymentTag);
        $oPaymentMetaData->sOperationModeName = $OperationModeNamePrefix . $sPaymentId . "_" . $sPaymentTag;
        $oPaymentMetaData->sOperationModeValue = $oPayment->fcpoGetOperationMode($sPaymentTag);
        $oPaymentMetaData->sPaymentTag = $sPaymentTag;
        $oPaymentMetaData->sPaymentName = $sPaymentName;
        $oPaymentMetaData->blSelected = $blSelected;

        return $oPaymentMetaData;
    }

    /**
     * Get configured operation mode ( live or test ) for debitnote payment method
     * 
     * @return string
     */
    protected function _getOperationModeELV() {
        $oPayment = $this->_oFcpoHelper->getFactoryObject('oxpayment');
        $oPayment->load('fcpodebitnote');
        return $oPayment->fcpoGetOperationMode();
    }

    /**
     * Checking if paypal express should be removed from payment list
     * 
     * @param void
     * @return void
     */
    protected function _fcpoCheckPaypalExpressRemoval() {
        //&& !$this->_oFcpoHelper->fcpoGetSessionVariable('fcpoWorkorderId')
        if (array_key_exists('fcpopaypal_express', $this->_oPaymentList) !== false) {
            unset($this->_oPaymentList['fcpopaypal_express']);
        }
    }

    /**
     * Update klarna user
     * 
     * @param void
     * @return void
     */
    protected function _fcpoKlarnaUpdateUser() {
        $oUser = $this->getUser();
        $blUserChanged = false;
        $aDynValue = $this->getDynValue();
        $sPaymentId = $this->_fcpoGetPaymentId();
        $sType = $this->_fcpoGetType($sPaymentId);

        $blUserChanged = $this->_fcpoCheckUpdateField($blUserChanged, $sType, $aDynValue, 'oxfon', 'fon', $oUser);
        $blUserChanged = $this->_fcpoCheckUpdateField($blUserChanged, $sType, $aDynValue, 'oxbirthdate', 'birthday', $oUser);
        $blUserChanged = $this->_fcpoCheckUpdateField($blUserChanged, $sType, $aDynValue, 'fcpopersonalid', 'personalid', $oUser);
        $blUserChanged = $this->_fcpoCheckUpdateField($blUserChanged, $sType, $aDynValue, 'oxsal', 'sal', $oUser);
        $blUserChanged = $this->_fcpoCheckUpdateField($blUserChanged, $sType, $aDynValue, 'oxaddinfo', 'addinfo', $oUser);

        if (array_key_exists('fcpo_' . $sType . '_del_addinfo', $aDynValue) !== false) {
            $sDeliveryAddressId = $oUser->getSelectedAddressId();
            if ($sDeliveryAddressId) {
                $oAddress = $this->_oFcpoHelper->getFactoryObject('oxaddress');
                if ($oAddress->load($sDeliveryAddressId)) {
                    $oAddress->oxaddress__oxaddinfo = new oxField($aDynValue['fcpo_' . $sType . '_del_addinfo'], oxField::T_RAW);
                    $oAddress->save();
                }
            }
        }

        if ($blUserChanged === true) {
            $oUser->save();
        }
    }

    /**
     * Adds new value to user object and return the changed status
     * 
     * @param boolean $blUserChanged
     * @param string $sType
     * @param array $aDynValue
     * @param string $sDbField
     * @param string $sDynValueField
     * @param oxUser $oUser
     * @return boolean
     */
    protected function _fcpoCheckUpdateField($blUserChanged, $sType, $aDynValue, $sDbField, $sDynValueField, $oUser) {
        $blAlreadyChanged = $blUserChanged;
        $sCompleteDynValueName = 'fcpo_' . $sType . '_' . $sDynValueField;

        if (array_key_exists($sCompleteDynValueName, $aDynValue) !== false) {
            $sObjAttribute = 'oxuser__' . $sDbField;

            $oUser->$sObjAttribute = new oxField($aDynValue[$sCompleteDynValueName], oxField::T_RAW);
            $blUserChanged = true;
        }

        if ($blAlreadyChanged === true) {
            $blReturn = $blAlreadyChanged;
        } else {
            $blReturn = $blUserChanged;
        }

        return $blReturn;
    }

    /**
     * Set payment type and process special case of klarna
     * 
     * @param void
     * @return string
     */
    protected function _fcpoGetType($sPaymentId) {
        $sType = 'klv';

        return $sType;
    }

    /**
     * Extension of validation, which takes care on specific payone payments
     * 
     * @param mixed $mReturn
     * @param string $sPaymentId
     * @return mixed
     */
    protected function _fcpoProcessValidation($mReturn, $sPaymentId) {
        if ($mReturn == 'order') { // success
            $this->_fcpoSetKlarnaCampaigns();

            $oPayment = $this->_oFcpoHelper->getFactoryObject('oxpayment');
            $oPayment->load($sPaymentId);

            $blContinue = $this->_fcpoCheckBoniMoment($oPayment);

            if ($blContinue !== true) {
                $this->_fcpoSetBoniErrorValues($sPaymentId);
                $mReturn = null;
            } else {
                $this->_fcpoSetMandateParams($oPayment);
            }

            $this->_fcCleanupSessionFragments($oPayment);

            $mReturn = $this->_fcpoPayolutionPreCheck($mReturn, $sPaymentId);
            if ($sPaymentId == 'fcporp_bill') {
                $mReturn = $this->_fcpoCheckRatePayBillMandatoryUserData($mReturn, $sPaymentId);
            }
        }

        return $mReturn;
    }

    /**
     * Method for transport of params that came via payolution installment params
     * 
     * @param array $aParams
     * @return void
     */
    public function setPayolutionAjaxParams($aParams) {
        $this->_aAjaxPayolutionParams = $aParams;
    }

    /**
     * Public method for payolution precheck which can be called via ajax wrapper
     * 
     * @param string $sPaymentId
     * @return mixed
     */
    public function fcpoPayolutionPreCheck($sPaymentId) {
        $oSession = $this->getSession();
        $oBasket = $oSession->getBasket();
        $oUser = $oBasket->getBasketUser();
        $this->_blIsPayolutionInstallmentAjax = true;

        return $this->_fcpoPayolutionPreCheck(true, $sPaymentId);
    }

    /**
     * Returns the sum of basket
     * 
     * @param void
     * @return decimal
     */
    public function fcpoGetBasketSum() {
        $oConfig = $this->getConfig();
        $sShopVersion = $oConfig->getVersion();
        $oSession = $this->getSession();
        $oBasket = $oSession->getBasket();
        $dBruttoSum = (version_compare($sShopVersion, '4.7.0', '>=')) ? $oBasket->getBruttoSum() : $oBasket->getProductsPrice()->getBruttoSum();
        $sBruttoSum = number_format($dBruttoSum, 2, ',', '.');

        return $sBruttoSum;
    }

    /**
     * Template getter which checks if requesting ustid is needed
     * 
     * @param void
     * @return bool
     */
    public function fcpoRatePayShowUstid() {
        $oConfig = $this->getConfig();
        $oUser = $this->getUser();
        $blB2b2Mode = ($oConfig->getConfigParam('blFCPORatePayB2BMode') && $oUser->oxuser__oxcompany->value != '');
        
        $blReturn = ($oUser->oxuser__oxustid->value == '' && $blB2b2Mode) ? true : false;

        return $blReturn;
    }

    /**
     * Template getter which checks if requesting birthdate is needed
     * 
     * @param void
     * @return bool
     */
    public function fcpoRatePayShowBirthdate() {
        $oConfig = $this->getConfig();
        $oUser = $this->getUser();
        $blShowUstid = $this->fcpoRatePayShowUstid();
        $blB2BMode = $oConfig->getConfigParam('blFCPORatePayB2BMode');
        
        $blReturn = ($oUser->oxuser__oxbirthdate->value == '0000-00-00' && !$blShowUstid) ? true : false;

        return $blReturn;
    }

    /**
     * Template getter which checks if requesting telephone number is needed
     * 
     * @param void
     * @return bool
     */
    public function fcpoRatePayShowFon() {
        $oConfig = $this->getConfig();
        $oUser = $this->getUser();
        $blShowUstid = $this->fcpoRatePayShowUstid();
        $blB2BMode = $oConfig->getConfigParam('blFCPORatePayB2BMode');

        $blReturn = ($oUser->oxuser__oxfon->value == '' && !$blShowUstid) ? true : false;

        return $blReturn;
    }

    /**
     * Checks if all mandatory data is available for using ratepay invoicing
     * 
     * @param mixed $mReturn
     * @param string $sPaymentId
     * @return mixed
     */
    protected function _fcpoCheckRatePayBillMandatoryUserData($mReturn, $sPaymentId) {
        $oConfig = $this->getConfig();
        $oLang = $this->_oFcpoHelper->fcpoGetLang();
        // mayme user entered values, save them so maybe check will be ok
        $this->_fcpoRatePaySaveRequestedValues($sPaymentId);
        
        $blB2b2Mode = $oConfig->getConfigParam('blFCPORatePayB2BMode');

        if ($blB2b2Mode) {
            $blShowUstid = $this->fcpoRatePayShowUstid();
            $mReturn = (!$blShowUstid) ? $mReturn : false;
            if (!$mReturn) {
                $sMessage = $oLang->translateString('FCPO_RATEPAY_NO_USTID');
                $this->_oFcpoHelper->fcpoSetSessionVariable('payerror', -20);
                $this->_oFcpoHelper->fcpoSetSessionVariable('payerrortext', $sMessage);
            }
        } else {
            $blShowFon = $this->fcpoRatePayShowFon();
            $blShowBirthdate = $this->fcpoRatePayShowBirthdate();
            $mReturn = (!$blShowBirthdate && !$blShowFon) ? $mReturn : false;
            if (!$mReturn) {
                $sMessage = $oLang->translateString('FCPO_RATEPAY_NO_SUFFICIENT_DATA');
                $this->_oFcpoHelper->fcpoSetSessionVariable('payerror', -20);
                $this->_oFcpoHelper->fcpoSetSessionVariable('payerrortext', $sMessage);
            }
        }

        return $mReturn;
    }

    /**
     * Perform payolution precheck
     * 
     * @param mixed $mReturn
     * @param string $sPaymentId
     * @return mixed
     */
    protected function _fcpoPayolutionPreCheck($mReturn, $sPaymentId) {
        // payolution pre check
        $oLang = $this->_oFcpoHelper->fcpoGetLang();
        $blPayolutionPayment = $this->_fcpoIsPayolution($sPaymentId);

        if ($blPayolutionPayment) {
            $this->_fcpoPayolutionSaveRequestedValues($sPaymentId);

            $blAgreedDataUsage = $this->_fcpoCheckAgreedDataUsage($sPaymentId);
            $blValidMandatoryUserData = $this->_fcpoCheckPayolutionMandatoryUserData($sPaymentId);

            if (!$blAgreedDataUsage) {
                $sMessage = $oLang->translateString('FCPO_PAYOLUTION_NOT_AGREED');
                $this->_oFcpoHelper->fcpoSetSessionVariable('payerror', -20);
                $this->_oFcpoHelper->fcpoSetSessionVariable('payerrortext', $sMessage);
                $mReturn = null;
            } else if (!$blValidMandatoryUserData) {
                $sMessage = $oLang->translateString('FCPO_PAYOLUTION_NO_USTID');
                $this->_oFcpoHelper->fcpoSetSessionVariable('payerror', -20);
                $this->_oFcpoHelper->fcpoSetSessionVariable('payerrortext', $sMessage);
                $mReturn = null;
            } else {
                $aBankData = ($sPaymentId == 'fcpopo_debitnote' || $sPaymentId == 'fcpopo_installment') ? $this->_fcpoGetPayolutionBankData($sPaymentId) : false;
                if ($aBankData) {
                    $blBankDataValid = $this->_fcpoValidateBankData($aBankData, $sPaymentId);
                    if (!$blBankDataValid) {
                        $this->_oFcpoHelper->fcpoSetSessionVariable('payerror', -20);
                        $this->_oFcpoHelper->fcpoSetSessionVariable('payerrortext', $oLang->translateString('FCPO_PAYOLUTION_BANKDATA_INCOMPLETE'));
                        $mReturn = null;
                    }

                    $blAgreedSepa = $this->_fcpoCheckSepaAgreed($sPaymentId);
                    if (!$blAgreedSepa && $blBankDataValid) {
                        $this->_oFcpoHelper->fcpoSetSessionVariable('payerror', -20);
                        $this->_oFcpoHelper->fcpoSetSessionVariable('payerrortext', $oLang->translateString('FCPO_PAYOLUTION_SEPA_NOT_AGREED'));
                        $mReturn = null;
                    }

                    if ($sPaymentId == 'fcpopo_installment') {
                        $sSelectedInstallmentIndex = $this->_fcpoGetPayolutionSelectedInstallmentIndex();
                        if (!$sSelectedInstallmentIndex) {
                            $this->_oFcpoHelper->fcpoSetSessionVariable('payerror', -20);
                            $this->_oFcpoHelper->fcpoSetSessionVariable('payerrortext', $oLang->translateString('FCPO_PAYOLUTION_NO_INSTALLMENT_SELECTED'));
                            $mReturn = null;
                        }
                    }
                } elseif (($sPaymentId == 'fcpopo_debitnote' || $sPaymentId == 'fcpopo_installment' ) && !$this->_blIsPayolutionInstallmentAjax) {
                    $sMessage = $oLang->translateString('FCPO_PAYOLUTION_BANKDATA_INCOMPLETE');
                    $this->_oFcpoHelper->fcpoSetSessionVariable('payerror', -20);
                    $this->_oFcpoHelper->fcpoSetSessionVariable('payerrortext', $sMessage);
                    $mReturn = null;
                }

                $blPreCheckValid = $this->_fcpoPerformPayolutionPreCheck($sPaymentId);
                if (!$blPreCheckValid) {
                    $sMessage = $oLang->translateString('FCPO_PAYOLUTION_PRECHECK_FAILED');
                    $this->_oFcpoHelper->fcpoSetSessionVariable('payerror', -20);
                    $this->_oFcpoHelper->fcpoSetSessionVariable('payerrortext', $sMessage);
                    $mReturn = null;
                }
            }
        }

        if ($this->_blIsPayolutionInstallmentAjax && $mReturn !== true) {
            return $sMessage;
        } else {
            return $mReturn;
        }
    }

    /**
     * Returns if mandatory data has been set or not
     * 
     * @param string $sPaymentId
     * @return bool
     */
    protected function _fcpoCheckPayolutionMandatoryUserData($sPaymentId) {
        $oConfig = $this->getConfig();
        $oUser = $this->getUser();
        $blB2BModeActive = $oConfig->getConfigParam('blFCPOPayolutionB2BMode');
        $blValidPaymentForCompanyData = in_array($sPaymentId, array('fcpopo_bill'));
        $blIsCompany = ($oUser->oxuser__oxcompany->value != '') ? true : false;

        $blReturn = true;
        if ($blIsCompany && $blB2BModeActive && $blValidPaymentForCompanyData) {
            $blReturn = ($oUser->oxuser__oxustid->value != '') ? true : false;
        }

        return $blReturn;
    }

    /**
     * Validates given Bankdata
     * 
     * @param array $aBankData
     * @return bool
     */
    protected function _fcpoValidateBankData($aBankData, $sPaymentId) {
        $blReturn = false;
        if ($sPaymentId == 'fcpopo_installment') {
            $blReturn = (
                    is_array($aBankData) &&
                    isset($aBankData['fcpo_payolution_installment_iban']) &&
                    isset($aBankData['fcpo_payolution_installment_bic']) &&
                    !empty($aBankData['fcpo_payolution_installment_iban']) &&
                    !empty($aBankData['fcpo_payolution_installment_bic']) &&
                    isset($aBankData['fcpo_payolution_installment_accountholder']) &&
                    !empty($aBankData['fcpo_payolution_installment_accountholder'])
                    );
        } else if ($sPaymentId == 'fcpopo_debitnote') {
            $blReturn = (
                    is_array($aBankData) &&
                    isset($aBankData['fcpo_payolution_debitnote_iban']) &&
                    isset($aBankData['fcpo_payolution_debitnote_bic']) &&
                    !empty($aBankData['fcpo_payolution_debitnote_iban']) &&
                    !empty($aBankData['fcpo_payolution_debitnote_bic']) &&
                    isset($aBankData['fcpo_payolution_debitnote_accountholder']) &&
                    !empty($aBankData['fcpo_payolution_debitnote_accountholder'])
                    );
        }

        return $blReturn;
    }

    /**
     * Reutrns possible given Bankdata
     * 
     * @param void
     * @return mixed
     */
    protected function _fcpoGetPayolutionBankData($sPaymentId) {
        $aParams = $this->_oFcpoHelper->fcpoGetRequestParameter('dynvalue');
        $aBankData = array();

        if (is_array($aParams) && count($aParams) > 0) {
            foreach ($aParams as $sKey => $sParam) {
                $aInstallmentAdditions = array(
                    'fcpopo_bill' => '',
                    'fcpopo_installment' => '_installment',
                    'fcpopo_debitnote' => '_debitnote',
                );

                $sInstallmentAddition = $aInstallmentAdditions[$sPaymentId];

                $aMap = array(
                    'fcpo_payolution' . $sInstallmentAddition . '_iban',
                    'fcpo_payolution' . $sInstallmentAddition . '_bic',
                    'fcpo_payolution' . $sInstallmentAddition . '_accountholder',
                );

                if (in_array($sKey, $aMap)) {
                    $aBankData[$sKey] = $sParam;
                }
            }
        }
        $aReturn = (count($aBankData) != 3) ? false : $aBankData;

        return $aReturn;
    }

    /**
     * Returns selected installment index
     * 
     */
    protected function _fcpoGetPayolutionSelectedInstallmentIndex() {
        $aParams = $this->_oFcpoHelper->fcpoGetRequestParameter('dynvalue');

        $mReturn = (isset($aParams['fcpo_payolution_installment_index'])) ? $aParams['fcpo_payolution_installment_index'] : false;

        return $mReturn;
    }

    /**
     * Checks if user confirmed agreement of data usage 
     * 
     * @param void
     * @return bool
     */
    protected function _fcpoCheckAgreedDataUsage($sPaymentId = 'fcpopo_bill') {
        $aParams = $this->_oFcpoHelper->fcpoGetRequestParameter('dynvalue');
        if ($this->_blIsPayolutionInstallmentAjax) {
            $aParams = $this->_aAjaxPayolutionParams;
        }

        $sPaymentIdPart = str_replace('fcpopo_', '', $sPaymentId);
        $blValidConditions = (isset($aParams['fcpo_payolution_' . $sPaymentIdPart . '_agreed']) && $aParams['fcpo_payolution_' . $sPaymentIdPart . '_agreed'] == 'agreed');

        $blReturn = false;
        if ($blValidConditions) {
            $blReturn = true;
        }

        return $blReturn;
    }

    /**
     * Checks if user confirmed agreement of data usage
     * 
     * @param string $sPaymentId
     * @return bool
     */
    protected function _fcpoCheckSepaAgreed($sPaymentId) {
        $aParams = $this->_oFcpoHelper->fcpoGetRequestParameter('dynvalue');
        $blReturn = false;
        if ($sPaymentId == 'fcpopo_installment') {
            $blReturn = true;
        } elseif ($sPaymentId == 'fcpopo_debitnote') {
            if (isset($aParams['fcpo_payolution_debitnote_sepa_agreed']) && $aParams['fcpo_payolution_debitnote_sepa_agreed'] == 'agreed') {
                $blReturn = true;
            }
        }

        return $blReturn;
    }

    /**
     * Check if values have been set via checkout payment process and save them
     * 
     * @param string $sPaymentId
     * @return void
     */
    protected function _fcpoRatePaySaveRequestedValues($sPaymentId) {
        $blSaveUser = false;
        $oSession = $this->_oFcpoHelper->fcpoGetSession();
        $oBasket = $oSession->getBasket();
        $oUser = $oBasket->getBasketUser();

        $aRequestedValues = $this->_oFcpoHelper->fcpoGetRequestParameter('dynvalue');
        $sCurrentBirthdate = $oUser->oxuser__oxbirthdate->value;
        $sRequestBirthdate = $aRequestedValues[$sPaymentId . '_birthdate_year'] . "-" .
                $aRequestedValues[$sPaymentId . '_birthdate_month'] . "-" .
                $aRequestedValues[$sPaymentId . '_birthdate_day'];

        $blRefreshBirthdate = ($sCurrentBirthdate != $sRequestBirthdate && $sRequestBirthdate != '0000-00-00' && $sRequestBirthdate != '--');
        if ($blRefreshBirthdate) {
            $oUser->oxuser__oxbirthdate = new oxField($sRequestBirthdate, oxField::T_RAW);
            $blSaveUser = true;
        }

        $blRefreshFon = (isset($aRequestedValues[$sPaymentId . '_fon']) && strlen($aRequestedValues[$sPaymentId . '_fon']) > 0);

        if ($blRefreshFon) {
            $oUser->oxuser__oxfon = new oxField($aRequestedValues[$sPaymentId . '_fon'], oxField::T_RAW);
            $blSaveUser = true;
        }
        
        $blRefreshUstid = (isset($aRequestedValues[$sPaymentId . '_ustid']) && strlen($aRequestedValues[$sPaymentId . '_ustid']) > 0);

        if ($blRefreshUstid) {
            $oUser->oxuser__oxustid = new oxField($aRequestedValues[$sPaymentId . '_ustid'], oxField::T_RAW);
            $blSaveUser = true;
        }

        $this->_oFcpoHelper->fcpoSetSessionVariable('ratepayprofileid', $aRequestedValues[$sPaymentId . '_profileid']);

        if ($blSaveUser) {
            $oUser->save();
        }
    }

    /**
     * Save requested values if there haven't been some before or they have changed
     * 
     * @param string $sPaymentId
     * @return void
     */
    protected function _fcpoPayolutionSaveRequestedValues($sPaymentId) {
        $oUser = false; // $this->getUser();
        if (!$oUser) {
            // try to fetch user from session
            $oSession = $this->_oFcpoHelper->fcpoGetSession();
            $oBasket = $oSession->getBasket();
            $oUser = $oBasket->getBasketUser();
        }

        $aRequestedValues = $this->_oFcpoHelper->fcpoGetRequestParameter('dynvalue');
        if ($this->_blIsPayolutionInstallmentAjax) {
            $aRequestedValues = $this->_aAjaxPayolutionParams;
        }
        $blSaveUser = false;
        $sFieldNameAddition = str_replace("fcpopo_", "", $sPaymentId);

        $blValidBirthdateData = $this->_fcpoValidateBirthdayData($sPaymentId, $aRequestedValues);
        if ($blValidBirthdateData) {
            $sCurrentBirthdate = $oUser->oxuser__oxbirthdate->value;
            $sRequestBirthdate = $aRequestedValues['fcpo_payolution_' . $sFieldNameAddition . '_birthdate_year'] . "-" . $aRequestedValues['fcpo_payolution_' . $sFieldNameAddition . '_birthdate_month'] . "-" . $aRequestedValues['fcpo_payolution_' . $sFieldNameAddition . '_birthdate_day'];
            $blRefreshBirthdate = ($sCurrentBirthdate != $sRequestBirthdate && $sRequestBirthdate != '0000-00-00' && $sRequestBirthdate != '--');
            if ($blRefreshBirthdate) {
                $oUser->oxuser__oxbirthdate = new oxField($sRequestBirthdate, oxField::T_RAW);
                $blSaveUser = true;
            }
        }

        $sRequestUstid = $this->_fcpoGetRequestedUstid($aRequestedValues, $sPaymentId);
        if ($sRequestUstid) {
            $sCurrentUstid = $oUser->oxuser__oxustid->value;

            $blRefreshUstid = ($sCurrentUstid != $sRequestUstid);
            if ($blRefreshUstid) {
                $oUser->oxuser__oxustid = new oxField($sRequestUstid, oxField::T_RAW);
                $blSaveUser = true;
            }
        }

        if ($blSaveUser) {
            $oUser->save();
        }
    }

    /**
     * Returns value for ustid depending on payment or false if this hasn't been set
     * 
     * @param string $aRequestedValues
     * @param string $sPaymentId
     * @return mixed string/boolean
     */
    protected function _fcpoGetRequestedUstid($aRequestedValues, $sPaymentId) {
        $sFieldNameAddition = str_replace("fcpopo_", "", $sPaymentId);

        $mReturn = false;
        if (isset($aRequestedValues['fcpo_payolution_' . $sFieldNameAddition . '_ustid'])) {
            $mReturn = $aRequestedValues['fcpo_payolution_' . $sFieldNameAddition . '_ustid'];
        }

        return $mReturn;
    }

    /**
     * Checks request data for valid birthday data
     * 
     * @param string $sPaymentId
     * @param array $aRequestedValues
     * @return boolean
     */
    protected function _fcpoValidateBirthdayData($sPaymentId, $aRequestedValues) {
        $sFieldNameAddition = str_replace("fcpopo_", "", $sPaymentId);
        $sBirthdateYear = $aRequestedValues['fcpo_payolution_' . $sFieldNameAddition . '_birthdate_year'];
        $blValidPayments = in_array($sPaymentId, array('fcpopo_bill', 'fcpopo_installment', 'fcpopo_debitnote'));
        $blValidRequestData = ((isset($sBirthdateYear) && !empty($sBirthdateYear)));

        $blReturn = false;
        if ($blValidPayments && $blValidRequestData) {
            $blReturn = true;
        }

        return $blReturn;
    }

    /**
     * Checks if given payment belongs to payone payolution
     * 
     * @param string $sPaymentId
     * @return bool
     */
    protected function _fcpoIsPayolution($sPaymentId) {
        $aPayolutionPayments = array(
            'fcpopo_bill',
            'fcpopo_debitnote',
            'fcpopo_installment',
        );

        return in_array($sPaymentId, $aPayolutionPayments);
    }

    /**
     * Ajax interface for triggering installment caclulation
     * 
     * @param void
     * @return void
     */
    public function fcpoPerformInstallmentCalculation() {
        $this->_fcpoPerformInstallmentCalculation('fcpopo_installment');
    }

    /**
     * Performs a pre check
     * 
     * @param string $sPaymentId
     * @return bool
     */
    protected function _fcpoPerformInstallmentCalculation($sPaymentId, $sWorkOrderId = null) {
        $oUser = $this->getUser();
        $aBankData = $this->_fcpoGetPayolutionBankData($sPaymentId);
        $oPORequest = $this->_oFcpoHelper->getFactoryObject('fcporequest');
        $aResponse = $oPORequest->sendRequestPayolutionInstallment($sPaymentId, $oUser, $aBankData, 'calculation', $sWorkOrderId);
        if ($aResponse['status'] == 'ERROR') {
            $this->_oFcpoHelper->fcpoSetSessionVariable('payerror', -20);
            $blReturn = false;
        } else if (is_array($aResponse) && array_key_exists('workorderid', $aResponse) !== false) {
            $this->_oFcpoHelper->fcpoSetSessionVariable('payolution_workorderid', $aResponse['workorderid']);
            $this->_fcpoSetInstallmentOptionsByResponse($aResponse);
            $blReturn = true;
        }

        return $blReturn;
    }

    /**
     * Fetches needed installment details from response and prepares data so it can be interpreted easier
     * 
     * @param array $aResponse
     * @return void
     */
    protected function _fcpoSetInstallmentOptionsByResponse($aResponse) {
        // cleanup before atempt
        $this->_aInstallmentCalculation = array();
        foreach ($aResponse as $sKey => $sValue) {
            // make sure we handle only add_paydata data and fetch installment_index
            if (substr($sKey, 0, 11) != 'add_paydata')
                continue;
            preg_match('/add_paydata\[PaymentDetails_([0-9]*)_/', $sKey, $aResultInstallmentIndex);
            if (!isset($aResultInstallmentIndex[1]) || !is_numeric($aResultInstallmentIndex[1]))
                continue;
            $iInstallmentIndex = $aResultInstallmentIndex[1];

            if (!isset($this->_aInstallmentCalculation[$iInstallmentIndex]['Duration'])) {
                $this->_aInstallmentCalculation[$iInstallmentIndex]['Duration'] = $aResponse['add_paydata[PaymentDetails_' . $iInstallmentIndex . '_Duration]'];
            }
            if (!isset($this->_aInstallmentCalculation[$iInstallmentIndex]['Currency'])) {
                $this->_aInstallmentCalculation[$iInstallmentIndex]['Currency'] = $aResponse['add_paydata[PaymentDetails_' . $iInstallmentIndex . '_Currency]'];
            }
            if (!isset($this->_aInstallmentCalculation[$iInstallmentIndex]['StandardCreditInformationUrl'])) {
                $this->_aInstallmentCalculation[$iInstallmentIndex]['StandardCreditInformationUrl'] = $aResponse['add_paydata[PaymentDetails_' . $iInstallmentIndex . '_StandardCreditInformationUrl]'];
            }
            if (!isset($this->_aInstallmentCalculation[$iInstallmentIndex]['Usage'])) {
                $this->_aInstallmentCalculation[$iInstallmentIndex]['Usage'] = $aResponse['add_paydata[PaymentDetails_' . $iInstallmentIndex . '_Usage]'];
            }
            if (!isset($this->_aInstallmentCalculation[$iInstallmentIndex]['EffectiveInterestRate'])) {
                $this->_aInstallmentCalculation[$iInstallmentIndex]['EffectiveInterestRate'] = $aResponse['add_paydata[PaymentDetails_' . $iInstallmentIndex . '_EffectiveInterestRate]'];
            }
            if (!isset($this->_aInstallmentCalculation[$iInstallmentIndex]['InterestRate'])) {
                $this->_aInstallmentCalculation[$iInstallmentIndex]['InterestRate'] = $aResponse['add_paydata[PaymentDetails_' . $iInstallmentIndex . '_InterestRate]'];
            }
            if (!isset($this->_aInstallmentCalculation[$iInstallmentIndex]['OriginalAmount'])) {
                $this->_aInstallmentCalculation[$iInstallmentIndex]['OriginalAmount'] = $aResponse['add_paydata[PaymentDetails_' . $iInstallmentIndex . '_OriginalAmount]'];
            }
            if (!isset($this->_aInstallmentCalculation[$iInstallmentIndex]['TotalAmount'])) {
                $this->_aInstallmentCalculation[$iInstallmentIndex]['TotalAmount'] = $aResponse['add_paydata[PaymentDetails_' . $iInstallmentIndex . '_TotalAmount]'];
            }
            if (!isset($this->_aInstallmentCalculation[$iInstallmentIndex]['MinimumInstallmentFee'])) {
                $this->_aInstallmentCalculation[$iInstallmentIndex]['MinimumInstallmentFee'] = $aResponse['add_paydata[PaymentDetails_' . $iInstallmentIndex . '_MinimumInstallmentFee]'];
            }

            // check search pattern to receive month of current installment detail 
            // add_paydata[PaymentDetails_<n>_Installment_<m>_Amount]
            preg_match('/add_paydata\[PaymentDetails_([0-9]*)_Installment_([0-9]*)_Amount\]/', $sKey, $aMonthResult);
            if (isset($aMonthResult[2]) && is_numeric($aMonthResult[2])) {
                $this->_aInstallmentCalculation[$iInstallmentIndex]['Amount'] = $aResponse['add_paydata[PaymentDetails_' . $iInstallmentIndex . '_Installment_' . $aMonthResult[2] . '_Amount]'];
                $this->_aInstallmentCalculation[$iInstallmentIndex]['Months'][$aMonthResult[2]]['Amount'] = $aResponse['add_paydata[PaymentDetails_' . $iInstallmentIndex . '_Installment_' . $aMonthResult[2] . '_Amount]'];
                $this->_aInstallmentCalculation[$iInstallmentIndex]['Months'][$aMonthResult[2]]['Due'] = $aResponse['add_paydata[PaymentDetails_' . $iInstallmentIndex . '_Installment_' . $aMonthResult[2] . '_Due]'];
                $this->_aInstallmentCalculation[$iInstallmentIndex]['Months'][$aMonthResult[2]]['Currency'] = $aResponse['add_paydata[PaymentDetails_' . $iInstallmentIndex . '_Currency]'];
                $this->_aInstallmentCalculation[$iInstallmentIndex]['Months'][$aMonthResult[2]]['DraftUrl'] = $aResponse['add_paydata[PaymentDetails_' . $iInstallmentIndex . '_StandardCreditInformationUrl]'];
                ksort($this->_aInstallmentCalculation[$iInstallmentIndex]['Months']);
            }
        }
        krsort($this->_aInstallmentCalculation);
    }

    /**
     * Performs a pre check
     * 
     * @param string $sPaymentId
     * @return bool
     */
    protected function _fcpoPerformPayolutionPreCheck($sPaymentId, $sWorkOrderId = null) {
        $blPreCheckNeeded = $this->_fcpoCheckIfPrecheckNeeded($sPaymentId);
        if ($blPreCheckNeeded) {
            $oUser = $this->getUser();
            if (!$oUser) {
                // try to fetch user from session
                $oSession = $this->getSession();
                $oBasket = $oSession->getBasket();
                $oUser = $oBasket->getBasketUser();
            }
            $oPORequest = $this->_oFcpoHelper->getFactoryObject('fcporequest');
            $aBankData = $this->_fcpoGetPayolutionBankData($sPaymentId);
            $sSelectedIndex = $this->_fcpoGetPayolutionSelectedInstallmentIndex();
            $aResponse = $oPORequest->sendRequestPayolutionPreCheck($sPaymentId, $oUser, $aBankData, $sWorkOrderId);
            if ($aResponse['status'] == 'ERROR') {
                $this->_oFcpoHelper->fcpoSetSessionVariable('payerror', -20);
                $blReturn = false;
            } else if (is_array($aResponse) && array_key_exists('workorderid', $aResponse) !== false) {
                $this->_oFcpoHelper->fcpoSetSessionVariable('payolution_workorderid', $aResponse['workorderid']);
                $this->_oFcpoHelper->fcpoSetSessionVariable('payolution_bankdata', $aBankData);
                $blReturn = true;
            }
        } else {
            // reperform calculation due security reasons so the user can't change duration by manipulating hidden fields
            $sWorkOrderId = $this->_oFcpoHelper->fcpoGetSessionVariable('payolution_workorderid');
            $blValidCalculation = $this->_fcpoPerformInstallmentCalculation($sPaymentId, $sWorkOrderId);
            if (!$blValidCalculation) {
                $this->_oFcpoHelper->fcpoSetSessionVariable('payerror', -20);
                $blReturn = false;
            } else {
                $sSelectedIndex = $this->_fcpoGetPayolutionSelectedInstallmentIndex();
                $sDuration = $this->_fcpoPayolutionFetchDuration($sSelectedIndex);
                $this->_oFcpoHelper->fcpoSetSessionVariable('payolution_installment_duration', $sDuration);
                $blReturn = true;
            }
        }

        return $blReturn;
    }

    /**
     * Precheck is not needed for payolution installment payment if its not coming via ajax
     * 
     * @param string $sPaymentId
     * @return bool
     */
    protected function _fcpoCheckIfPrecheckNeeded($sPaymentId) {
        $blReturn = true;
        $blCheckException = ($sPaymentId == 'fcpopo_installment' && !$this->_blIsPayolutionInstallmentAjax);
        if ($blCheckException) {
            $blReturn = false;
        }

        return $blReturn;
    }

    /**
     * Returns duration by given installment index after performing calculation
     * 
     * @param string $sSelectedIndex
     * @return mixed
     */
    protected function _fcpoPayolutionFetchDuration($sSelectedIndex) {
        $mReturn = false;
        if (isset($this->_aInstallmentCalculation[$sSelectedIndex]['Duration'])) {
            $mReturn = $this->_aInstallmentCalculation[$sSelectedIndex]['Duration'];
        }

        return $mReturn;
    }

    /**
     * Takes care of debit specific actions arround mandates
     * 
     * @param oxPayment $oPayment
     * @return void
     */
    protected function _fcpoSetMandateParams($oPayment) {
        $oConfig = $this->getConfig();
        if ($oPayment->getId() == 'fcpodebitnote' && $oConfig->getConfigParam('blFCPOMandateIssuance')) {
            $oUser = $this->getUser();
            $aDynValue = $this->_fcpoGetDynValues();

            $oPORequest = $this->_oFcpoHelper->getFactoryObject('fcporequest');
            $aResponse = $oPORequest->sendRequestManagemandate($oPayment->fcpoGetOperationMode(), $aDynValue, $oUser);

            $this->_fcpoHandleMandateResponse($aResponse);
        }
    }

    /**
     * Handles response for mandate request
     * 
     * @param array $aResponse
     * @return mixed
     */
    protected function _fcpoHandleMandateResponse($aResponse) {
        if ($aResponse['status'] == 'ERROR') {
            $this->_oFcpoHelper->fcpoSetSessionVariable('payerror', -20);
            $oLang = $this->_oFcpoHelper->fcpoGetLang();
            $this->_oFcpoHelper->fcpoSetSessionVariable('payerrortext', $oLang->translateString('FCPO_MANAGEMANDATE_ERROR'));
            return;
        } else if (is_array($aResponse) && array_key_exists('mandate_status', $aResponse) !== false) {
            $this->_oFcpoHelper->fcpoSetSessionVariable('fcpoMandate', $aResponse);
        }
    }

    /**
     * Takes care of error handling for case that boni check is negative
     * 
     * @param string $sPaymentId
     * @return void
     */
    protected function _fcpoSetBoniErrorValues($sPaymentId) {
        $oConfig = $this->_oFcpoHelper->fcpoGetConfig();
        $iLangId = $this->fcGetLangId();

        #$this->_oFcpoHelper->fcpoSetSessionVariable( 'payerror', $oPayment->getPaymentErrorNumber() );
        $this->_oFcpoHelper->fcpoSetSessionVariable('payerror', -20);
        $this->_oFcpoHelper->fcpoSetSessionVariable('payerrortext', $oConfig->getConfigParam('sFCPODenialText_' . $iLangId));

        //#1308C - delete paymentid from session, and save selected it just for view
        $this->_oFcpoHelper->fcpoDeleteSessionVariable('paymentid');
        if (!($sPaymentId = $this->_oFcpoHelper->fcpoGetRequestParameter('paymentid'))) {
            $sPaymentId = $this->_oFcpoHelper->fcpoGetSessionVariable('paymentid');
        }
        $this->_oFcpoHelper->fcpoSetSessionVariable('_selected_paymentid', $sPaymentId);
        $this->_oFcpoHelper->fcpoDeleteSessionVariable('stsprotection');

        if ($this->_fcGetCurrentVersion() >= 4400) {
            $oSession = $this->_oFcpoHelper->fcpoGetSession();
            $oBasket = $oSession->getBasket();
            $oBasket->setTsProductId(null);
        }
    }

    /**
     * Check configuration for boni check moment and triggers check if moment has been set to now
     * Method will return if checkout progress can be continued or not
     * 
     * @param object $oPayment
     * @return boolean
     */
    protected function _fcpoCheckBoniMoment($oPayment) {
        $oConfig = $this->getConfig();
        $blContinue = true;

        if ($oConfig->getConfigParam('sFCPOBonicheckMoment') == 'after') {
            $blContinue = $this->_fcpoCheckAddressAndScore($oPayment);
        }

        return $blContinue;
    }

    /**
     * Checks the address and boni values
     * 
     * @param bool $blApproval
     * @param bool $blBoniCheckNeeded
     * @param oxPayment $oPayment
     * @return boolean
     */
    protected function _fcpoCheckAddressAndScore($oPayment) {
        $oUser = $this->getUser();
        $sPaymentId = $oPayment->getId();
        $aApproval = $this->_oFcpoHelper->fcpoGetRequestParameter('fcpo_bonicheckapproved');
        $blApproval = $this->_fcpoValidateApproval($sPaymentId, $aApproval);
        $blBoniCheckNeeded = $oPayment->fcBoniCheckNeeded();

        if ($blBoniCheckNeeded === true && $blApproval === true) {
            $blContinue = $oUser->checkAddressAndScore(false);
            $blContinue = $this->_fcpoCheckUserBoni($blContinue, $oPayment);
        } else {
            $this->_fcpoSetNotChecked($blBoniCheckNeeded, $blApproval);
            $blContinue = true;
        }

        return $blContinue;
    }

    /**
     * Check if session flag fcpoordernotchecked will be set
     * 
     * @param bool $blBoniCheckNeeded
     * @param bool $blApproval
     * @return void
     */
    protected function _fcpoSetNotChecked($blBoniCheckNeeded, $blApproval) {
        if ($blBoniCheckNeeded === true && $blApproval === false) {
            $this->_oFcpoHelper->fcpoSetSessionVariable('fcpoordernotchecked', 1);
        }
    }

    /**
     * Compares user boni which could cause a denial on continuing process
     * 
     * @param boolean $blContinue
     * @param oxPayment $oPayment
     * @return boolean
     */
    protected function _fcpoCheckUserBoni($blContinue, $oPayment) {
        $oUser = $this->getUser();
        if ($oUser->oxuser__oxboni->value < $oPayment->oxpayments__oxfromboni->value) {
            $blContinue = false;
        }

        return $blContinue;
    }

    /**
     * Checks approval data to be valid and returns result
     * 
     * @param string $sPaymentId
     * @param array $aApproval
     * @return boolean
     */
    protected function _fcpoValidateApproval($sPaymentId, $aApproval) {
        $blApproval = true;
        if ($aApproval && array_key_exists($sPaymentId, $aApproval) && $aApproval[$sPaymentId] == 'false') {
            $blApproval = false;
        }

        return $blApproval;
    }

    /**
     * Sets needed session values if there is corresponding data
     * 
     * @param void
     * @return void
     */
    protected function _fcpoSetKlarnaCampaigns() {
        if ($this->_oFcpoHelper->fcpoGetRequestParameter('fcpo_klarna_campaign')) {
            $this->_oFcpoHelper->fcpoSetSessionVariable('fcpo_klarna_campaign', $this->_oFcpoHelper->fcpoGetRequestParameter('fcpo_klarna_campaign'));
        } else {
            $this->_oFcpoHelper->fcpoDeleteSessionVariable('fcpo_klarna_campaign');
        }
    }

    /**
     * Returns paymentid wether from request parameter or session
     * 
     * @param void
     * @return mixed
     */
    protected function _fcpoGetPaymentId() {
        $sPaymentId = $sPaymentId = $this->_oFcpoHelper->fcpoGetRequestParameter('paymentid');
        if (!$sPaymentId) {
            $sPaymentId = $this->_oFcpoHelper->fcpoGetSessionVariable('paymentid');
        }

        return $sPaymentId;
    }

    /**
     * Triggers updating user if klarna payment has been recognized
     * 
     * @param string $sPaymentId
     * @return void
     */
    protected function _fcpoCheckKlarnaUpdateUser($sPaymentId) {
        $oUser = $this->getUser();
        if ($oUser && ($sPaymentId == 'fcpoklarna')) {
            $this->_fcpoKlarnaUpdateUser();
        }
    }

    /**
     * Returns dynvalues wether from request or session
     * 
     * @param void
     * @return mixed
     */
    protected function _fcpoGetDynValues() {
        $aDynvalue = $this->_oFcpoHelper->fcpoGetRequestParameter('dynvalue');
        if (!$aDynvalue) {
            $aDynvalue = $this->_oFcpoHelper->fcpoGetSessionVariable('dynvalue');
        }

        return $aDynvalue;
    }

    /**
     * Hook for processing a return value
     * 
     * @param string $sReturn
     * @return string
     */
    protected function _processParentReturnValue($sReturn) {
        return $sReturn;
    }

    /**
     * Returns the cn
     * 
     * @param void
     * @return mixed
     */
    public function fcGetApprovalText() {
        $iLangId = $this->fcGetLangId();
        $oConfig = $this->getConfig();
        return $oConfig->getConfigParam('sFCPOApprovalText_' . $iLangId);
    }

    /**
     * Check if approval message should be displayed
     * 
     * @param void
     * @return bool
     */
    public function fcShowApprovalMessage() {
        $oConfig = $this->getConfig();
        $blReturn = ($oConfig->getConfigParam('sFCPOBonicheckMoment') == 'after') ? true : false;
        return $blReturn;
    }

    /**
     * Loads shop version and formats it in a certain way
     *
     * @param void
     * @return string
     */
    public function getIntegratorid() {
        return $this->_oFcpoHelper->fcpoGetIntegratorId();
    }

    /**
     * Loads shop edition and shop version and formats it in a certain way
     *
     * @param void
     * @return string
     */
    public function getIntegratorver() {
        return $this->_oFcpoHelper->fcpoGetIntegratorVersion();
    }

    /**
     * get PAYONE module version
     *
     * @param void
     * @return string
     */
    public function getIntegratorextver() {
        return $this->_oFcpoHelper->fcpoGetModuleVersion();
    }

    /**
     * Returns the Klarna confirmation text for the current bill country
     * 
     * @param void
     * @return string
     */
    public function fcpoGetConfirmationText() {
        $oPayment = $this->_oFcpoHelper->getFactoryObject('oxPayment');
        $sId = $oPayment->fcpoGetKlarnaStoreId();
        $sKlarnaLang = $this->_fcpoGetKlarnaLang();
        $oLang = $this->_oFcpoHelper->fcpoGetLang();
        $sConfirmText = $oLang->translateString('FCPO_KLV_CONFIRM');
        $sConfirmText = sprintf($sConfirmText, $sId, $sKlarnaLang, $sId, $sKlarnaLang);

        return $sConfirmText;
    }

    /**
     * Checks if telephone number
     * 
     * @param void
     * @return bool
     */
    public function fcpoKlarnaIsTelephoneNumberNeeded() {
        $oUser = $this->getUser();
        $blReturn = ($oUser->oxuser__oxfon->value) ? false : true;

        return $blReturn;
    }

    /**
     * Checks if birthday neeeded for klarna
     * 
     * @param void
     * @return bool
     */
    public function fcpoKlarnaIsBirthdayNeeded() {
        $oUser = $this->getUser();
        $oCountry = $oUser->getUserCountry();
        $sBirthdate = $oUser->oxuser__oxbirthdate->value;
        $sUserCountryIso2 = $oCountry->oxcountry__oxisoalpha2->value;
        $blNoBirthdaySet = (!$sBirthdate || $sBirthdate == '0000-00-00');
        $blInCountryList = in_array($sUserCountryIso2, $this->_aKlarnaBirthdayNeededCountries);
        $blBirthdayNeeded = (bool) ($blInCountryList && $blNoBirthdaySet);

        return $blBirthdayNeeded;
    }

    /**
     * Determine if address addition is needed
     * 
     * @param void
     * @return bool
     */
    public function fcpoKlarnaIsAddressAdditionNeeded() {
        $oUser = $this->getUser();
        $sBillCountryIso2 = strtolower($this->fcGetBillCountry());
        $blAdditionNeeded = ($sBillCountryIso2 == 'nl' && !$oUser->oxuser__oxaddinfo->value);

        return $blAdditionNeeded;
    }

    /**
     * Determine if delivery address addition is needed
     * 
     * @param void
     * @return boolean
     */
    public function fcpoKlarnaIsDelAddressAdditionNeeded() {
        $blReturn = false;
        $sBillCountryIso2 = strtolower($this->fcGetBillCountry());

        if ($sBillCountryIso2 == 'nl') {
            $oUser = $this->getUser();
            $sDeliveryAddressId = $oUser->getSelectedAddressId();
            if ($sDeliveryAddressId) {
                $oAddress = $this->_oFcpoHelper->getFactoryObject('oxaddress');
                $oAddress->load($sDeliveryAddressId);
                if ($oAddress && !$oAddress->oxaddress__oxaddinfo->value) {
                    $blReturn = true;
                }
            }
        }

        return $blReturn;
    }

    /**
     * Determine if gender is needed
     * 
     * @param void
     * @return bool
     */
    public function fcpoKlarnaIsGenderNeeded() {
        $sBillCountryIso2 = strtolower($this->fcGetBillCountry());
        $oUser = $this->getUser();
        $blValidCountry = (array_search($sBillCountryIso2, array('de', 'at', 'nl')) !== false);
        $blValidSalutation = !$oUser->oxuser__oxsal->value;
        $blIsValid = $blValidCountry && $blValidSalutation;
        $blReturn = ($blIsValid) ? true : false;

        return $blReturn;
    }

    /**
     * Determine if personal id is needed
     * 
     * @param void
     * @return bool
     */
    public function fcpoKlarnaIsPersonalIdNeeded() {
        $sBillCountryIso2 = strtolower($this->fcGetBillCountry());
        $oUser = $this->getUser();
        $blValidCountry = (array_search($sBillCountryIso2, array('dk', 'fi', 'no', 'se')) !== false);
        $blValidPersId = !$oUser->oxuser__fcpopersonalid->value;
        $blIsValid = $blValidCountry && $blValidPersId;
        $blReturn = ($blIsValid) ? true : false;

        return $blReturn;
    }

    /**
     * Determine if info is needed
     * 
     * @param void
     * @return bool
     */
    public function fcpoKlarnaInfoNeeded() {
        $blInfoNeeded = (
                $this->fcpoKlarnaIsTelephoneNumberNeeded() ||
                $this->fcpoKlarnaIsBirthdayNeeded() ||
                $this->fcpoKlarnaIsAddressAdditionNeeded() ||
                $this->fcpoKlarnaIsDelAddressAdditionNeeded() ||
                $this->fcpoKlarnaIsGenderNeeded() ||
                $this->fcpoKlarnaIsPersonalIdNeeded()
                );

        return $blInfoNeeded;
    }

    /**
     * Returns an array of configured debit countries
     * 
     * @param void
     * @return array
     */
    public function fcpoGetDebitCountries() {
        $aCountries = array();
        $oConfig = $this->_oFcpoHelper->fcpoGetConfig();
        $aFCPODebitCountries = $oConfig->getConfigParam('aFCPODebitCountries');

        if (is_array($aFCPODebitCountries) && count($aFCPODebitCountries)) {
            foreach ($aFCPODebitCountries as $sCountryId) {
                $oPayment = $this->_oFcpoHelper->getFactoryObject('oxPayment');
                $aCountries[$oPayment->fcpoGetCountryIsoAlphaById($sCountryId)] = $oPayment->fcpoGetCountryNameById($sCountryId);
            }
        }

        return $aCountries;
    }

    /**
     * Decides wether old debit fiels should show up
     * 
     * @param void
     * @return bool
     */
    public function fcpoShowOldDebitFields() {
        $oConfig = $this->_oFcpoHelper->fcpoGetConfig();
        $blReturn = (bool) $oConfig->getConfigParam('blFCPODebitOldGer');

        return $blReturn;
    }

    /**
     * Remove all session variables of non selected payment
     * 
     * @param object $oPayment
     * @return void
     */
    protected function _fcCleanupSessionFragments($oPayment) {
        $sPaymentId = $oPayment->getId();

        $aPayments2SessionVariables = array(
            'fcpodebitnote' => array('fcpoMandate'),
            'fcpobarzahlen' => array('sFcpoBarzahlenHtml'),
            'fcpoklarna' => array('fcpo_klarna_campaign'),
        );

        // remove own payment from list
        unset($aPayments2SessionVariables[$sPaymentId]);

        // iterate through the rest and delete session variables
        foreach ($aPayments2SessionVariables as $aSessionVariables) {
            foreach ($aSessionVariables as $sSessionVariable) {
                $this->_oFcpoHelper->fcpoDeleteSessionVariable($sSessionVariable);
            }
        }
    }

    /**
     * Get user payment by payment id with oxid bugfix for getting last payment
     *
     * @param oxUser $oUser        user object
     * @param string $sPaymentType payment type
     *
     * @return bool
     */
    protected function _fcGetPaymentByPaymentType($oUser = null, $sPaymentType = null) {
        $mReturn = false;
        if ($oUser && $sPaymentType != null) {
            $oPayment = $this->_oFcpoHelper->getFactoryObject('oxPayment');
            $sOxid = $oPayment->fcpoGetUserPaymentId($oUser->getId(), $sPaymentType);

            if ($sOxid) {
                $oUserPayment = $this->_oFcpoHelper->getFactoryObject('oxuserpayment');
                $oUserPayment->load($sOxid);
                $mReturn = $oUserPayment;
            }
        }

        return $oUserPayment;
    }

    /**
     * Assign debit note payment values to view data. Loads user debit note payment
     * if available and assigns payment data to $this->_aDynValue
     *
     * @param void
     * @return null
     */
    protected function _assignDebitNoteParams() {
        parent::_assignDebitNoteParams();
        if ((bool) $this->getConfigParam('sFCPOSaveBankdata') === true) {
            //such info available ?
            if ($oUserPayment = $this->_fcGetPaymentByPaymentType($this->getUser(), 'fcpodebitnote')) {
                $oUtils = $this->_oFcpoHelper->fcpoGetUtils();
                $aAddPaymentData = $oUtils->assignValuesFromText($oUserPayment->oxuserpayments__oxvalue->value);
                //checking if some of values is allready set in session - leave it
                foreach ($aAddPaymentData as $oData) {
                    if (!isset($this->_aDynValue[$oData->name]) || ( isset($this->_aDynValue[$oData->name]) && !$this->_aDynValue[$oData->name] )) {
                        $this->_aDynValue[$oData->name] = $oData->value;
                    }
                }
            }
        }
    }

    /**
     * Template variable getter. Returns dyn values
     *
     * @param void
     * @return array
     */
    public function getDynValue() {
        $aReturn = parent::getDynValue();
        if ((bool) $this->getConfigParam('sFCPOSaveBankdata') === true) {
            $aPaymentList = $this->getPaymentList();
            if (isset($aPaymentList['fcpodebitnote'])) {
                $this->_assignDebitNoteParams();
            }
        }
        return $this->_aDynValue;
    }

    /**
     * Return ISO2 code of bill country
     * 
     * @param void
     * @return string
     */
    public function fcGetBillCountry() {
        $sBillCountryId = $this->getUserBillCountryId();
        $oCountry = $this->_oFcpoHelper->getFactoryObject('oxcountry');
        $sReturn = ($oCountry->load($sBillCountryId)) ? $oCountry->oxcountry__oxisoalpha2->value : '';

        return $sReturn;
    }

    /**
     * Extends oxid standard method _setValues
     * Extends it with the approval checkbox in the longdesc property
     * 
     * Calculate payment cost for each payment. Sould be removed later
     *
     * @param array    &$aPaymentList payments array
     * @param oxBasket $oBasket       basket object
     *
     * @return null
     */
    protected function _setValues(& $aPaymentList, $oBasket = null) {
        parent::_setValues($aPaymentList, $oBasket);
        if (is_array($aPaymentList)) {
            foreach ($aPaymentList as $oPayment) {
                if ($this->fcIsPayOnePaymentType($oPayment->getId()) && $this->fcShowApprovalMessage() && $oPayment->fcBoniCheckNeeded()) {
                    $sApprovalLongdesc = '<br><table><tr><td><input type="hidden" name="fcpo_bonicheckapproved[' . $oPayment->getId() . ']" value="false"><input type="checkbox" name="fcpo_bonicheckapproved[' . $oPayment->getId() . ']" value="true" style="margin-bottom:0px;margin-right:10px;"></td><td>' . $this->fcGetApprovalText() . '</td></tr></table>';
                    $oPayment->oxpayments__oxlongdesc->value .= $sApprovalLongdesc;
                }
            }
        }
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
     * Extends oxid standard method _setDeprecatedValues
     * Extends it with the approval checkbox in the longdesc property
     * 
     * Calculate payment cost for each payment. Sould be removed later
     *
     * @param array    &$aPaymentList payments array
     * @param oxBasket $oBasket       basket object
     *
     * @return null
     */
    protected function _setDeprecatedValues(& $aPaymentList, $oBasket = null) {
        if ($this->_fcGetCurrentVersion() <= 4700) {
            parent::_setDeprecatedValues($aPaymentList, $oBasket);
            if (is_array($aPaymentList)) {
                $oLang = $this->_oFcpoHelper->fcpoGetLang();
                foreach ($aPaymentList as $oPayment) {
                    if ($this->fcIsPayOnePaymentType($oPayment->getId()) && $this->fcShowApprovalMessage() && $oPayment->fcBoniCheckNeeded()) {
                        $sApprovalLongdesc = '<br><table><tr><td><input type="hidden" name="fcpo_bonicheckapproved[' . $oPayment->getId() . ']" value="false"><input type="checkbox" name="fcpo_bonicheckapproved[' . $oPayment->getId() . ']" value="true" style="margin-bottom:0px;margin-right:10px;"></td><td>' . $this->fcGetApprovalText() . '</td></tr></table>';
                        $oPayment->oxpayments__oxlongdesc->value .= $sApprovalLongdesc;
                    }
                }
            }
        }
    }

    /**
     * Returns the Klarna lang abbreviation
     * 
     * @return string
     */
    protected function _fcpoGetKlarnaLang() {
        $sReturn = 'de_de';
        $sBillCountryIso2 = strtolower($this->fcGetBillCountry());
        if ($sBillCountryIso2) {
            $aKlarnaLangMap = array(
                'de' => 'de_de',
                'at' => 'de_at',
                'dk' => 'da_dk',
                'fi' => 'fi_fi',
                'nl' => 'nl_nl',
                'no' => 'nb_no',
                'se' => 'sv_se',
            );
            if (array_key_exists($sBillCountryIso2, $aKlarnaLangMap) !== false) {
                $sReturn = $aKlarnaLangMap[$sBillCountryIso2];
            }
        }
        return $sReturn;
    }

    /**
     * Returns wether payment is of type payone
     * 
     * @param string $sId
     * @return bool
     */
    public function fcIsPayOnePaymentType($sId) {
        $blIsPayonePaymentType = (bool) fcPayOnePayment::fcIsPayOnePaymentType($sId);
        return $blIsPayonePaymentType;
    }

    /**
     * Template getter for checking which kind of field should be shown
     * 
     * @param void
     * @return bool
     */
    public function fcpoShowB2B() {
        $oConfig = $this->getConfig();
        $oUser = $this->getUser();
        $blB2BModeActive = $oConfig->getConfigParam('blFCPOPayolutionB2BMode');

        if ($blB2BModeActive) {
            $blCompany = ($oUser->oxuser__oxcompany->value) ? true : false;
            $blReturn = $blCompany;
            // check if we already have ustid, then showing is not needed
            if ($blCompany) {
                $blReturn = ($oUser->oxuser__oxustid->value) ? false : true;
            }
        } else {
            $blReturn = false;
        }

        return $blReturn;
    }

    /**
     * Template getter for checking which kind of field should be shown
     * 
     * @param void
     * @return bool
     */
    public function fcpoShowB2C() {
        $oConfig = $this->getConfig();
        $oUser = $this->getUser();
        $blB2BIsShown = $this->fcpoShowB2B();
        $blReturn = !$blB2BIsShown;

        if ($blReturn) {
            $blReturn = ($oUser->oxuser__oxbirthdate->value != '0000-00-00') ? false : true;
        }

        return $blReturn;
    }

    /**
     * Template getter which delivers certain parts of birthdate
     * 
     * @param string $sPart (year,month,day)
     * @return string
     */
    public function fcpoGetBirthdayField($sPart) {
        $sBirthdate = $this->fcpoGetUserValue('oxbirthdate');
        $aBirthdateParts = explode('-', $sBirthdate);
        $aMap = array(
            'year' => 0,
            'month' => 1,
            'day' => 2,
        );

        $sReturn = '';
        if (isset($aBirthdateParts[$aMap[$sPart]])) {
            $sReturn = $aBirthdateParts[$aMap[$sPart]];
        }

        return $sReturn;
    }

    /**
     * Returns a value of user object or empty string if value nor available
     * 
     * @param string $sField
     * @return string
     */
    public function fcpoGetUserValue($sField) {
        $oUser = $this->getUser();
        $sUserField = 'oxuser__' . $sField;

        try {
            $sReturn = $oUser->$sUserField->value;
        } catch (Exception $ex) {
            $sReturn = '';
        }

        return $sReturn;
    }

    /**
     * Returns prepared link for displaying agreement as 
     * 
     * @param void
     * @return string
     */
    public function fcpoGetPayolutionAgreementLink() {
        $oConfig = $this->getConfig();
        $sCompanyName = $oConfig->getConfigParam('sFCPOPayolutionCompany');
        $sLink = $this->_sPayolutionAgreementBaseLink . base64_encode($sCompanyName);

        return $sLink;
    }

    /**
     * Template getter returns payolution sepa mandata
     * 
     * @return string
     */
    public function fcpoGetPayolutionSepaAgreementLink() {
        $oConfig = $this->getConfig();
        $sShopUrl = $oConfig->getShopUrl();
        $sLink = $sShopUrl . '/modules/fcPayOne/lib/fcpopopup_content.php?loadurl=' . $this->_sPayolutionSepaAgreement;

        return $sLink;
    }

    /**
     * Returns an array with range of given numbers as pad formatted string
     * 
     * @param int $iFrom
     * @param int $iTo
     * @param int $iPositions
     * @return array
     */
    protected function _fcpoGetNumericRange($iFrom, $iTo, $iPositions) {
        $aRange = array();

        for ($iCurrentNumber = $iFrom; $iCurrentNumber <= $iTo; $iCurrentNumber++) {
            $aRange[] = str_pad($iCurrentNumber, $iPositions, '0', STR_PAD_LEFT);
        }

        return $aRange;
    }

    /**
     * Template getter for returning an array with last hundred years
     * 
     * @param void
     * @return array
     */
    public function fcpoGetYearRange() {
        $iCurrentYear = (int) date('Y');
        $iHundredYearsAgo = $iCurrentYear - 100;

        $aRange = $this->_fcpoGetNumericRange($iHundredYearsAgo, $iCurrentYear, 4);
        $aReturn = array_reverse($aRange);

        return $aReturn;
    }

    /**
     * Returns an array of available months
     * 
     * @param void
     * @return array
     */
    public function fcpoGetMonthRange() {
        $aRange = $this->_fcpoGetNumericRange(1, 12, 2);
        return $aRange;
    }

    /**
     * Returns an array of available days
     * 
     * @param void
     * @return array
     */
    public function fcpoGetDayRange() {
        $aRange = $this->_fcpoGetNumericRange(1, 31, 2);
        return $aRange;
    }

}
