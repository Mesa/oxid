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
 * Class OxpsPaymorrowPayment extends Payment
 *
 * @see Payment
 */
class OxpsPaymorrowPayment extends OxpsPaymorrowPayment_parent
{

    /**
     * Overridden parent method.
     * Additionally checks if Paymorrow request was successful.
     * It is needed for detecting cases, when JavaScript is disabled in user browser and no requests were sent.
     *
     * @return mixed
     */
    public function validatePayment()
    {
        // Remove payment methods initialization data from session of any is set
        $this->_unsetSessionInitData();

        $mReturn = $this->_OxpsPaymorrowPayment_validatePayment_parent();

        if ( $this->_isPaymorrowPayment( oxRegistry::getConfig()->getRequestParameter( 'paymentid' ) ) and
             $this->_isPaymentResponseSessionInvalid()
        ) {
            oxRegistry::getSession()->setVariable( 'payerror', 1 );

            return null;
        }

        return $mReturn;
    }


    /**
     * Delete session key with Paymorrow init data.
     * It is set on order confirmation errors, used to redirect user to the payment spe and passed to forms init.
     *
     * @codeCoverageIgnore
     */
    protected function _unsetSessionInitData()
    {
        oxRegistry::getSession()->deleteVariable( 'pm_init_data' );
    }

    /**
     * Load payment method by ID and check if it is mapped as active Paymorrow method.
     *
     * @param string $iId
     *
     * @return bool
     */
    protected function _isPaymorrowPayment( $iId )
    {
        /** @var OxpsPaymorrowOxPayment|oxPayment $oPayment */
        $oPayment = oxNew( 'oxPayment' );

        // Load selected payment method and check if it is Paymorrow
        return ( $oPayment->load( $iId ) and $oPayment->isPaymorrowActiveAndMapped() );
    }

    /**
     * Check payment response in session fot errors.
     *
     * @return bool True is response is invalid, false otherwise.
     */
    protected function _isPaymentResponseSessionInvalid()
    {
        // Get Paymorrow response from session
        $oSession           = oxRegistry::getSession();
        $aPaymorrowResponse = (array) $oSession->getVariable( 'pm_response' );

        // The response must exist and be valid
        return (
            !isset( $aPaymorrowResponse['order_status'], $aPaymorrowResponse['response_status'] ) or
            !in_array( $aPaymorrowResponse['order_status'], array('VALIDATED', 'ACCEPTED') ) or
            ( $aPaymorrowResponse['response_status'] !== 'OK' )
        );
    }


    /**
     * Parent `validatePayment` call.
     *
     * @codeCoverageIgnore
     *
     * @return mixed
     */
    protected function _OxpsPaymorrowPayment_validatePayment_parent()
    {
        return parent::validatePayment();
    }
}
