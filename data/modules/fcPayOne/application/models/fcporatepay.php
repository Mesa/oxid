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
class fcporatepay extends oxBase {
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
     * Init needed data
     */
    public function __construct() {
        parent::__construct();
        $this->_oFcpoHelper = oxNew('fcpohelper');
        $this->_oFcpoDb = oxDb::getDb();
    }
    
    /**
     * Add/Update RatePay profile
     * 
     * @param array $aRatepayData
     * @return void
     */
    public function fcpoInsertProfile($sOxid, $aRatePayData) {
        if (array_key_exists('delete', $aRatePayData) !== false) {
            $sQuery = "DELETE FROM fcporatepay WHERE oxid = " . oxDb::getDb()->quote($sOxid);
            $this->_oFcpoDb->Execute($sQuery);
        } else {
            $sQuery = " UPDATE
                            fcporatepay
                        SET
                            shopid = " . oxDb::getDb()->quote($aRatePayData['shopid']) . ",
                            currency = " . oxDb::getDb()->quote($aRatePayData['currency']) . ",
                            oxpaymentid = " . oxDb::getDb()->quote($aRatePayData['paymentid']) . "
                        WHERE
                            oxid = " . oxDb::getDb()->quote($sOxid);
            $this->_oFcpoDb->Execute($sQuery);
            $this->_fcpoUpdateRatePayProfile($sOxid);
        }
    }
    
    /**
     * Returns an array with RatePay profiles
     * 
     * @param string $sPaymentId (optional)
     * @return void
     */
    public function fcpoGetRatePayProfiles($sPaymentId = null) {
        $aReturn = array();
        
        $sFilterPaymentId = "";
        if (is_string($sPaymentId)) {
            $sFilterPaymentId = "WHERE OXPAYMENTID=".$this->_oFcpoDb->quote($sPaymentId);
        }
        
        $sQuery = "SELECT * FROM fcporatepay {$sFilterPaymentId}";
        $oRs = $this->_oFcpoDb->Execute($sQuery);
        $aFields = $this->fcpoGetFields();
        
        if ($oRs != false && $oRs->recordCount()) {
            while(!$oRs->EOF) {
                $sOxid = $oRs->fields[0];
                foreach ($oRs->fields as $iIndex=>$sValue) {
                    $aRow[$aFields[$iIndex]] = $sValue; 
                }
                $aReturn[$sOxid] = $aRow;
                $oRs->moveNext();
            }
        }
        
        return $aReturn;
    }
    
    
    /**
     * Add RatePay shop
     * 
     * @param void
     * @return void
     */
    public function fcpoAddRatePayProfile() {
        $oUtilsObject = $this->_oFcpoHelper->fcpoGetUtilsObject();
        $sNewOxid = $oUtilsObject->generateUId();
        $sQuery = "
            INSERT INTO fcporatepay 
            (
                `OXID`,
                `OXPAYMENTID`,
                `shopid`,
                `merchant_name`,
                `merchant_status`,
                `shop_name`,
                `name`,
                `currency`,
                `type`,
                `activation_status_elv`,
                `activation_status_installment`,
                `activation_status_invoice`,
                `activation_status_prepayment`,
                `amount_min_longrun`,
                `b2b_pq_full`,
                `b2b_pq_light`,
                `b2b_elv`,
                `b2b_installment`,
                `b2b_invoice`,
                `b2b_prepayment`,
                `country_code_billing`,
                `country_code_delivery`,
                `delivery_address_pq_full`,
                `delivery_address_pq_light`,
                `delivery_address_elv`,
                `delivery_address_installment`,
                `delivery_address_invoice`,
                `delivery_address_prepayment`,
                `device_fingerprint_snippet_id`,
                `eligibility_device_fingerprint`,
                `eligibility_ratepay_elv`,
                `eligibility_ratepay_installment`,
                `eligibility_ratepay_invoice`,
                `eligibility_ratepay_pq_full`,
                `eligibility_ratepay_pq_light`,
                `eligibility_ratepay_prepayment`,
                `interest_rate_merchant_towards_bank`,
                `interestrate_default`,
                `interestrate_max`,
                `interestrate_min`,
                `min_difference_dueday`,
                `month_allowed`,
                `month_longrun`,
                `month_number_max`,
                `month_number_min`,
                `payment_amount`,
                `payment_firstday`,
                `payment_lastrate`,
                `rate_min_longrun`,
                `rate_min_normal`,
                `service_charge`,
                `tx_limit_elv_max`,
                `tx_limit_elv_min`,
                `tx_limit_installment_max`,
                `tx_limit_installment_min`,
                `tx_limit_invoice_max`,
                `tx_limit_invoice_min`,
                `tx_limit_prepayment_max`,
                `txLimitPrepaymentMin`,
                `valid_payment_firstdays`
            ) 
            VALUES 
            (
                '".$sNewOxid."', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 
                '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 
                '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''
            )
        ";
        $this->_oFcpoDb->Execute($sQuery);
    }
    
    /**
     * Returns profiledata by id
     * 
     * @param type $sOxid
     * @return array
     */
    public function fcpoGetProfileData($sOxid) {
        $sQuery = "SELECT * FROM fcporatepay WHERE OXID=".$this->_oFcpoDb->quote($sOxid);
        $aResult = $this->_oFcpoDb->GetRow($sQuery);
        $aFields = $this->fcpoGetFields();
        $aReturn = array();
        
        foreach($aResult as $iIndex=>$sValue) {
            $aReturn[$aFields[$iIndex]] = $sValue;
        }
        
        return $aReturn;
    }
    
    /**
     * Helper method that returns field-names of ratepay-table
     * 
     * @param void
     * @return array
     */
    public function fcpoGetFields() {
        $sQuery = "SHOW FIELDS FROM fcporatepay";
        $oRs = $this->_oFcpoDb->Execute($sQuery);
        $aReturn = array();
        
        if ($oRs != false && $oRs->recordCount() > 0) {
            while(!$oRs->EOF) {
                $aReturn[] = $oRs->fields[0];
                $oRs->moveNext();
            }
        }
        
        return $aReturn;
    }
    
    /**
     * 
     * 
     * @param array $aMatchData
     * @return mixed boolean/array
     */
    public function fcpoMatchRatePayProfile($aMatchData) {
        $dBasketValue = $aMatchData['basketvalue'];
        $sPaymentId = $aMatchData['paymentid'];
        $sProfilePaymentActiveField = $this->_fcpoGetProfilePaymentActiveField($sPaymentId);
        $aRatePayProfiles = $this->fcpoGetRatePayProfiles();
        
        foreach ($aRatePayProfiles as $aRatePayProfile) {
            $dProfileBaketValueMin = $aRatePayProfile['tx_limit_invoice_min'];
        }

    }
    
    /**
     * 
     * @param string $sPaymentId
     * @return mixed
     */
    protected function _fcpoGetProfilePaymentActiveField($sPaymentId) {
        $aMap = array(
            'fcporp_bill' => 'activation_status_invoice',
        );
        
        $mReturn = false;
        if (isset($aMap[$sPaymentId])) {
            $mReturn = $aMap[$sPaymentId];
        }
        
        return $mReturn;
    }

    /**
     * Requests and updates payment information for given shop_id
     * 
     * @param $aRatePayData
     * @return void
     */
    protected function _fcpoUpdateRatePayProfile($sOxid) {
        $aRatePayData = $this->fcpoGetProfileData($sOxid);
        $oConfig = $this->getConfig();
        $oRequest = oxNew('fcporequest');
        $aResponse = $oRequest->sendRequestRatePayProfile($aRatePayData);
        if (isset($aResponse['status']) && $aResponse['status'] == 'OK') {
            $this->_fcpoUpdateRatePayProfileByResponse($sOxid, $aResponse);
        }
    }
    
    /**
     * Collects profile information and save it into profile
     * 
     * @param string $sOxid
     * @param array $aResponse
     * @return void
     */
    protected function _fcpoUpdateRatePayProfileByResponse($sOxid, $aResponse) {
        $sQuery = "
            UPDATE fcporatepay SET
                `merchant_name`=".$this->_oFcpoDb->quote($aResponse['add_paydata[merchant-name]']).",
                `merchant_status`=".$this->_oFcpoDb->quote($aResponse['add_paydata[merchant-status]']).",
                `shop_name`=".$this->_oFcpoDb->quote($aResponse['add_paydata[shop-name]']).",
                `name`=".$this->_oFcpoDb->quote($aResponse['add_paydata[name]']).",
                `type`=".$this->_oFcpoDb->quote($aResponse['add_paydata[type]']).",
                `activation_status_elv`=".$this->_oFcpoDb->quote($aResponse['add_paydata[activation-status-elv]']).",
                `activation_status_installment`=".$this->_oFcpoDb->quote($aResponse['add_paydata[activation-status-installment]']).",
                `activation_status_invoice`=".$this->_oFcpoDb->quote($aResponse['add_paydata[activation-status-invoice]']).",
                `activation_status_prepayment`=".$this->_oFcpoDb->quote($aResponse['add_paydata[activation-status-prepayment]']).",
                `amount_min_longrun`=".$this->_oFcpoDb->quote($aResponse['add_paydata[amount-min-longrun]']).",
                `b2b_pq_full`=".$this->_oFcpoDb->quote($aResponse['add_paydata[b2b-PQ-full]']).",
                `b2b_pq_light`=".$this->_oFcpoDb->quote($aResponse['add_paydata[b2b-PQ-light]']).",
                `b2b_elv`=".$this->_oFcpoDb->quote($aResponse['add_paydata[b2b-elv]']).",
                `b2b_installment`=".$this->_oFcpoDb->quote($aResponse['add_paydata[b2b-installment]']).",
                `b2b_invoice`=".$this->_oFcpoDb->quote($aResponse['add_paydata[b2b-invoice]']).",
                `b2b_prepayment`=".$this->_oFcpoDb->quote($aResponse['add_paydata[b2b-prepayment]']).",
                `country_code_billing`=".$this->_oFcpoDb->quote($aResponse['add_paydata[country-code-billing]']).",
                `country_code_delivery`=".$this->_oFcpoDb->quote($aResponse['add_paydata[country-code-delivery]']).",
                `delivery_address_pq_full`=".$this->_oFcpoDb->quote($aResponse['add_paydata[delivery-address-PQ-full]']).",
                `delivery_address_pq_light`=".$this->_oFcpoDb->quote($aResponse['add_paydata[delivery-address-PQ-light]']).",
                `delivery_address_elv`=".$this->_oFcpoDb->quote($aResponse['add_paydata[delivery-address-elv]']).",
                `delivery_address_installment`=".$this->_oFcpoDb->quote($aResponse['add_paydata[delivery-address-installment]']).",
                `delivery_address_invoice`=".$this->_oFcpoDb->quote($aResponse['add_paydata[delivery-address-invoice]']).",
                `delivery_address_prepayment`=".$this->_oFcpoDb->quote($aResponse['add_paydata[delivery-address-prepayment]']).",
                `device_fingerprint_snippet_id`=".$this->_oFcpoDb->quote($aResponse['add_paydata[device-fingerprint-snippet-id]']).",
                `eligibility_device_fingerprint`=".$this->_oFcpoDb->quote($aResponse['add_paydata[eligibility-device-fingerprint]']).",
                `eligibility_ratepay_elv`=".$this->_oFcpoDb->quote($aResponse['add_paydata[eligibility-ratepay-elv]']).",
                `eligibility_ratepay_installment`=".$this->_oFcpoDb->quote($aResponse['add_paydata[eligibility-ratepay-installment]']).",
                `eligibility_ratepay_invoice`=".$this->_oFcpoDb->quote($aResponse['add_paydata[eligibility-ratepay-invoice]']).",
                `eligibility_ratepay_pq_full`=".$this->_oFcpoDb->quote($aResponse['add_paydata[eligibility-ratepay-pq-full]']).",
                `eligibility_ratepay_pq_light`=".$this->_oFcpoDb->quote($aResponse['add_paydata[eligibility-ratepay-pq-light]']).",
                `eligibility_ratepay_prepayment`=".$this->_oFcpoDb->quote($aResponse['add_paydata[eligibility-ratepay-prepayment]']).",
                `interest_rate_merchant_towards_bank`=".$this->_oFcpoDb->quote($aResponse['add_paydata[interest-rate-merchant-towards-bank]']).",
                `interestrate_default`=".$this->_oFcpoDb->quote($aResponse['add_paydata[interestrate-default]']).",
                `interestrate_max`=".$this->_oFcpoDb->quote($aResponse['add_paydata[interestrate-max]']).",
                `interestrate_min`=".$this->_oFcpoDb->quote($aResponse['add_paydata[interestrate-min]']).",
                `min_difference_dueday`=".$this->_oFcpoDb->quote($aResponse['add_paydata[min-difference-dueday]']).",
                `month_allowed`=".$this->_oFcpoDb->quote($aResponse['add_paydata[month-allowed]']).",
                `month_longrun`=".$this->_oFcpoDb->quote($aResponse['add_paydata[month-longrun]']).",
                `month_number_max`=".$this->_oFcpoDb->quote($aResponse['add_paydata[month-number-max]']).",
                `month_number_min`=".$this->_oFcpoDb->quote($aResponse['add_paydata[month-number-min]']).",
                `payment_amount`=".$this->_oFcpoDb->quote($aResponse['add_paydata[payment-amount]']).",
                `payment_firstday`=".$this->_oFcpoDb->quote($aResponse['add_paydata[payment-firstday]']).",
                `payment_lastrate`=".$this->_oFcpoDb->quote($aResponse['add_paydata[payment-lastrate]']).",
                `rate_min_longrun`=".$this->_oFcpoDb->quote($aResponse['add_paydata[rate-min-longrun]']).",
                `rate_min_normal`=".$this->_oFcpoDb->quote($aResponse['add_paydata[rate-min-normal]']).",
                `service_charge`=".$this->_oFcpoDb->quote($aResponse['add_paydata[service-charge]']).",
                `tx_limit_elv_max`=".$this->_oFcpoDb->quote($aResponse['add_paydata[tx-limit-elv-max]']).",
                `tx_limit_elv_min`=".$this->_oFcpoDb->quote($aResponse['add_paydata[tx-limit-elv-min]']).",
                `tx_limit_installment_max`=".$this->_oFcpoDb->quote($aResponse['add_paydata[tx-limit-installment-max]']).",
                `tx_limit_installment_min`=".$this->_oFcpoDb->quote($aResponse['add_paydata[tx-limit-installment-min]']).",
                `tx_limit_invoice_max`=".$this->_oFcpoDb->quote($aResponse['add_paydata[tx-limit-invoice-max]']).",
                `tx_limit_invoice_min`=".$this->_oFcpoDb->quote($aResponse['add_paydata[tx-limit-invoice-min]']).",
                `tx_limit_prepayment_max`=".$this->_oFcpoDb->quote($aResponse['add_paydata[tx-limit-prepayment-max]']).",
                `txLimitPrepaymentMin`=".$this->_oFcpoDb->quote($aResponse['add_paydata[tx-limit-prepayment-min]']).",
                `valid_payment_firstdays`=".$this->_oFcpoDb->quote($aResponse['add_paydata[valid-payment-firstdays]'])."
            WHERE 
                OXID=".$this->_oFcpoDb->quote($sOxid)."
        ";
        
        $this->_oFcpoDb->Execute($sQuery);
    }
    
}
