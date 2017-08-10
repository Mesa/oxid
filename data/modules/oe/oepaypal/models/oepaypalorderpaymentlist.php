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
 * PayPal order payment list class
 */
class oePayPalOrderPaymentList extends oePayPalList
{
    /**
     * Data base gateway
     *
     * @var oePayPalPayPalDbGateway
     */
    protected $_oDbGateway = null;

    /**
     * @var string|null
     */
    protected $_sOrderId = null;

    /**
     * Sets order id.
     *
     * @param string $sOrderId
     */
    public function setOrderId($sOrderId)
    {
        $this->_sOrderId = $sOrderId;
    }

    /**
     * Returns order id.
     *
     * @return null|string
     */
    public function getOrderId()
    {
        return $this->_sOrderId;
    }

    /**
     * Returns oePayPalPayPalDbGateway or creates and sets it if it was not set.
     *
     * @return oePayPalPayPalDbGateway
     */
    protected function _getDbGateway()
    {
        if (is_null($this->_oDbGateway)) {
            $this->_setDbGateway(oxNew('oePayPalOrderPaymentDbGateway'));
        }

        return $this->_oDbGateway;
    }

    /**
     * Set model database gateway.
     *
     * @param object $oDbGateway
     */
    protected function _setDbGateway($oDbGateway)
    {
        $this->_oDbGateway = $oDbGateway;
    }

    /**
     * Selects and loads order payment history.
     *
     * @param string $sOrderId Order id.
     */
    public function load($sOrderId)
    {
        $this->setOrderId($sOrderId);

        $aPayments = array();
        $aPaymentsData = $this->_getDbGateway()->getList($this->getOrderId());
        if (is_array($aPaymentsData) && count($aPaymentsData)) {
            $aPayments = array();
            foreach ($aPaymentsData as $aData) {
                $oPayment = oxNew('oePayPalOrderPayment');
                $oPayment->setData($aData);
                $aPayments[] = $oPayment;
            }
        }

        $this->setArray($aPayments);
    }

    /**
     * Check if list has payment with defined status.
     *
     * @param string $sStatus Payment status.
     *
     * @return bool
     */
    protected function _hasPaymentWithStatus($sStatus)
    {
        $blHasStatus = false;
        $aPayments = $this->getArray();

        foreach ($aPayments as $oPayment) {
            if ($sStatus == $oPayment->getStatus()) {
                $blHasStatus = true;
                break;
            }
        }

        return $blHasStatus;
    }

    /**
     * Check if list has pending payment.
     *
     * @return bool
     */
    public function hasPendingPayment()
    {
        return $this->_hasPaymentWithStatus('Pending');
    }

    /**
     * Check if list has failed payment.
     *
     * @return bool
     */
    public function hasFailedPayment()
    {
        return $this->_hasPaymentWithStatus('Failed');
    }

    /**
     * Returns not yet captured (remaining) order sum.
     *
     * @param oePayPalOrderPayment $oPayment order payment
     *
     * @return oePayPalOrderPayment
     */
    public function addPayment(oePayPalOrderPayment $oPayment)
    {
        //order payment info
        if ($this->getOrderId()) {
            $oPayment->setOrderId($this->getOrderId());
            $oPayment->save();
        }

        $this->load($this->getOrderId());

        return $oPayment;
    }
}
