<?php
/**
 * This file is part of OXID eSales Paymorrow module.
 *
 * OXID eSales Paymorrow module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales eVAT module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales eVAT module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2015
 */

/**
 * Class OxpsPaymorrowOxOrder extends oxOrder.
 *
 * @see oxOrder
 */
class OxpsPaymorrowOxOrder extends OxpsPaymorrowOxOrder_parent
{

    /**
     * Session key for order ID.
     */
    const PAYMORROW_ORDER_ID = 'order_id';


    /**
     * Save Paymorrow temporary order ID to session.
     *
     * @param string $sTempOrderId
     */
    public function savePaymorrowTemporaryOrderIdToSession( $sTempOrderId )
    {
        oxRegistry::getSession()->setVariable( self::PAYMORROW_ORDER_ID, $sTempOrderId );
    }

    /**
     * Get Paymorrow temporary order ID from session.
     *
     * @return array
     */
    public function getPaymorrowTemporaryOrderIdFromSession()
    {
        return oxRegistry::getSession()->getVariable( self::PAYMORROW_ORDER_ID );
    }

    /**
     * Overridden parent method.
     * Sends an update order Id call to set order number instead of order OXID.
     * Triggers for complete order payed with a Paymorrow payment method.
     *
     * @param oxBasket $oBasket
     * @param oxUser   $oUser
     * @param bool     $blRecalculatingOrder
     *
     * @return mixed
     */
    public function finalizeOrder( oxBasket $oBasket, $oUser, $blRecalculatingOrder = false )
    {
        $mReturn = $this->_OxpsPaymorrowOxOrder_finalizeOrder_parent( $oBasket, $oUser, $blRecalculatingOrder );

        if ( ( $this->oxorder__oxtransstatus->value == 'OK' ) and $this->_wasPaymorrowPaymentUsed( $oBasket ) ) {
            $aData = array(
                'pm_order_transaction_id' => oxRegistry::get( 'OxpsPaymorrowModule' )->getPaymentTransactionId(),
                'order_id'                => $this->getId(),
                'new_order_id'            => $this->getPaymorrowOrderNumber(),
            );

            oxRegistry::get( 'OxpsPaymorrowLogger' )->logWithType( $aData, 'finalizeOrder' );

            /** @var OxpsPaymorrowRequestControllerProxy $oPmRequestControllerProxy */
            $oPmRequestControllerProxy = oxNew( 'OxpsPaymorrowRequestControllerProxy' );
            $oPmRequestControllerProxy->updateOrderId( $aData );
        }

        return $mReturn;
    }

    /**
     * Get user payment instance related to the order.
     *
     * @return oxUserPayment
     */
    public function getPaymorrowOxUserPaymentReloaded()
    {
        $sUserPaymentId = $this->oxorder__oxpaymentid->value;

        /** @var oxUserPayment $oUserPayment */
        $oUserPayment = oxNew( 'oxUserPayment' );
        $oUserPayment->load( $sUserPaymentId );

        return $oUserPayment;
    }

    /**
     * Get order number.
     *
     * @return string
     */
    public function getPaymorrowOrderNumber()
    {
        return $this->oxorder__oxordernr->value;
    }

    /**
     * Get order creation date.
     *
     * @return string
     */
    public function getOrderDate()
    {
        if ( empty( $this->oxorder__oxorderdate->value ) ) {
            return '';
        }

        return date( 'Y-m-d', strtotime( $this->oxorder__oxorderdate->value ) );
    }

    /**
     * Get order payment method ID.
     *
     * @return string
     */
    public function getPaymentMethodId()
    {
        return (string) $this->oxorder__oxpaymenttype->value;
    }

    /**
     * Get order payment method title.
     *
     * @return string
     */
    public function getPaymentMethodName()
    {
        /** @var OxpsPaymorrowOxPayment|oxPayment $oPayment */
        $oPayment = oxNew( 'oxPayment' );

        if ( !$oPayment->load( $this->getPaymentMethodId() ) ) {
            return '';
        }

        return $oPayment->getTitle();
    }

    /**
     * Get order status.
     *
     * @return string
     */
    public function getOrderStatus()
    {
        return (string) $this->oxorder__oxtransstatus->value;
    }


    /**
     * Check is valid Paymorrow payment method was used in the checkout ir not.
     *
     * @param oxBasket $oBasket
     *
     * @return bool
     */
    protected function _wasPaymorrowPaymentUsed( oxBasket $oBasket )
    {
        /** @var OxpsPaymorrowOxPayment|oxPayment $oPayment */
        $oPayment = oxNew( 'OxpsPaymorrowOxPayment' );

        if ( $oPayment->load( $oBasket->getPaymentId() ) and $oPayment->isPaymorrowActiveAndMapped() ) {
            return true;
        }

        return false;
    }


    /**
     * Parent `finalizeOrder` function call.
     *
     * @codeCoverageIgnore
     *
     * @param oxBasket $oBasket
     * @param oxUser   $oUser
     * @param bool     $blRecalculatingOrder
     *
     * @return mixed
     */
    protected function _OxpsPaymorrowOxOrder_finalizeOrder_parent( oxBasket $oBasket, oxUser $oUser,
                                                                   $blRecalculatingOrder = false )
    {
        return parent::finalizeOrder( $oBasket, $oUser, $blRecalculatingOrder );
    }
}
