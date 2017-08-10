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
 
class fcpoTransactionStatus extends oxBase {

    /**
     * Helper object for dealing with different shop versions
     * @var object
     */
    protected $_oFcpoHelper = null;

    /**
     * Instance of oxid database
     * @var object
     */
    protected $_oFcpoDb = null;

    /**
     * Object core table name
     *
     * @var string
     */
    protected $_sCoreTbl = 'fcpotransactionstatus';

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'fcpotransactionstatus';

    /**
     * Class constructor
     *
     * @return null
     */
    public function __construct() {
        parent::__construct();
        $this->init('fcpotransactionstatus');
        $this->_oFcpoHelper = oxNew('fcpohelper');
        $this->_oFcpoDb = oxDb::getDb();
    }

    /**
     * Get translated description text of the transaction action
     * 
     * @return string
     */
    public function getAction() {
        $oLang = $this->_oFcpoHelper->fcpoGetLang();
        $sAction = $this->fcpotransactionstatus__fcpo_txaction->value;
        $dReceivable = (double) $this->fcpotransactionstatus__fcpo_txreceivable->value;
        $dBalance = (double) $this->fcpotransactionstatus__fcpo_balance->value;

        if ($sAction == 'paid' && ($dReceivable + $dBalance) < 0) {
            $sAction = 'overpaid';
        }

        return $oLang->translateString('fcpo_action_' . $sAction, null, true);
    }

    /**
     * Get translated for payment type of transaction
     * 
     * @return string
     */
    public function getClearingtype() {
        $oLang = $this->_oFcpoHelper->fcpoGetLang();
        $sTxid = $this->fcpotransactionstatus__fcpo_txid->value;
        $sClearingType = $this->fcpotransactionstatus__fcpo_clearingtype->value;
        $oOrder = $this->_fcpoGetOrderByTxid($sTxid);
        $sPaymentType = $oOrder->oxorder__oxpaymenttype->value;
        $sTransSuffix = ($sClearingType == 'fnc') ? $sPaymentType : $sClearingType;
        $sReturn = $oLang->translateString('fcpo_clearingtype_' . $sTransSuffix, null, true);

        return $sReturn;
    }

    /**
     * Get total order sum of connected order
     * 
     * @return double
     */
    public function getCaptureAmount() {
        $sTxid = $this->fcpotransactionstatus__fcpo_txid->value;
        $oOrder = $this->_fcpoGetOrderByTxid($sTxid);
        return $oOrder->oxorder__oxtotalordersum;
    }

    /**
     * Get name of creditcard abbreviation
     * 
     * @return string
     */
    public function getCardtype() {
        $aMatchMap = array(
            'V' => 'Visa',
            'M' => 'Mastercard',
            'A' => 'Amex',
            'D' => 'Diners',
            'J' => 'JCB',
            'O' => 'Maestro International',
            'U' => 'Maestro UK',
            'C' => 'Discover',
            'B' => 'Carte Bleue',
        );

        $sCardType = $this->fcpotransactionstatus__fcpo_cardtype->value;
        $sReturn = (isset($aMatchMap[$sCardType])) ? $aMatchMap[$sCardType] : $sCardType;

        return $sReturn;
    }

    /**
     * Get translated name of the payment action by currenct receivable money amount
     * 
     * @param double $dReceivable receivable amount
     * 
     * @return string
     */
    public function getDisplayNameReceivable($dReceivable) {
        $oLang = $this->_oFcpoHelper->fcpoGetLang();
        $sLangAppointed = $this->_fcpoGetLangIdent($dReceivable, 'fcpo_receivable_appointed1', 'fcpo_receivable_appointed2');
        $sLangReminder = $this->_fcpoGetLangIdent($dReceivable, 'fcpo_receivable_reminder', '');
        $sLangDebit = $this->_fcpoGetLangIdent($dReceivable, 'fcpo_receivable_debit1', 'fcpo_receivable_debit2');

        $aMatchMap = array(
            'cancelation' => 'fcpo_receivable_cancelation',
            'appointed' => $sLangAppointed,
            'capture' => 'fcpo_receivable_capture',
            'refund' => $sLangDebit,
            'debit' => $sLangDebit,
            'reminder' => $sLangReminder,
        );

        $sTxAction = $this->fcpotransactionstatus__fcpo_txaction->value;
        $sLangIdent = $this->_fcpoGetMapAction($sTxAction, $aMatchMap, 'FCPO_RECEIVABLE');
        $sTranslatedString = $oLang->translateString($sLangIdent, null, true);

        return $sTranslatedString;
    }

    /**
     * Get translated name of the payment action by payed money amount
     * 
     * @param double $dPayment payed amount
     * 
     * @return string
     */
    public function getDisplayNamePayment($dPayment) {
        $oLang = $this->_oFcpoHelper->fcpoGetLang();

        $sLangCapture = $this->_fcpoGetLangIdent($dPayment, 'fcpo_payment_capture1', 'fcpo_payment_capture2');
        $sLangPaid = $this->_fcpoGetLangIdent($dPayment, 'fcpo_payment_paid1', 'fcpo_payment_paid2');
        $sLangUnderpaid = $this->_fcpoGetLangIdent($dPayment, 'fcpo_payment_underpaid1', 'fcpo_payment_underpaid2');
        $sLangDebit = $this->_fcpoGetLangIdent($dPayment, 'fcpo_payment_debit1', 'fcpo_payment_debit2');

        $aMatchMap = array(
            'capture' => $sLangCapture,
            'cancelation' => $sLangPaid,
            'paid' => $sLangPaid,
            'underpaid' => $sLangUnderpaid,
            'refund' => $sLangDebit,
            'debit' => $sLangDebit,
            'transfer' => 'fcpo_payment_transfer',
        );

        $sTxAction = $this->fcpotransactionstatus__fcpo_txaction->value;
        $sLangIdent = $this->_fcpoGetMapAction($sTxAction, $aMatchMap, 'fcpo_payment');
        $sTranslatedString = $oLang->translateString($sLangIdent, null, true);

        return $sTranslatedString;
    }

    /**
     * This method decides if given option1 or 2 will be used by checking if given value
     * 
     * @param double $dValue
     * @param string $sOption1
     * @param string $sOption2
     * @return string
     */
    protected function _fcpoGetLangIdent($dValue, $sOption1, $sOption2) {
        $sReturn = ($dValue > 0) ? $sOption1 : $sOption2;

        return $sReturn;
    }

    /**
     * Returns a certain action of a given map
     * 
     * @param string $sTxAction
     * @param array $aMatchMap
     * @return string
     */
    protected function _fcpoGetMapAction($sTxAction, $aMatchMap, $sDefault) {
        $sReturn = (isset($aMatchMap[$sTxAction])) ? $aMatchMap[$sTxAction] : $sDefault;

        return $sReturn;
    }

    /**
     * Returns order object by txid
     * 
     * @param string $sTxid
     * @return object
     */
    protected function _fcpoGetOrderByTxid($sTxid) {
        $sOxid = $this->_oFcpoDb->GetOne("SELECT oxid FROM oxorder WHERE fcpotxid = '{$sTxid}'");
        $oOrder = $this->_oFcpoHelper->getFactoryObject('oxorder');
        $oOrder->load($sOxid);

        return $oOrder;
    }

}
