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
 * Class OxpsPaymorrowOrder extends order
 *
 * @see order
 */
class OxpsPaymorrowOrder extends OxpsPaymorrowOrder_parent
{

    /**
     * Overridden method - checks type of payment
     *
     * @return mixed
     */
    public function render()
    {
        $this->_setPaymorrowTypeOfPayment();
        $this->_checkForErrorsToRedirect();

        return $this->_OxpsPaymorrowOrder_render_parent();
    }


    /**
     * Overrides `oxpayments__oxdesc` in 4th Order step
     * under line "Type of Payment" and sets it to Paymorrow: Invoice/Direct Debit
     */
    protected function _setPaymorrowTypeOfPayment()
    {
        /** @var OxpsPaymorrowOxPayment $oxPayment */
        $oxPayment = $this->getPayment();

        if ( ( $oxPayment instanceof oxPayment ) and $oxPayment->isPaymorrowActiveAndMapped() ) {
            $oLang = oxRegistry::getLang();

            if ( $oxPayment->getPaymorrowPaymentType() == 'pm_invoice' ) {
                $oxPayment->oxpayments__oxdesc = new oxField( $oLang->translateString(
                    'PAYMORROW_PAYMENT_METHOD_NAME_INVOICE'
                ) );
            } elseif ( $oxPayment->getPaymorrowPaymentType() == 'pm_sdd' ) {
                $oxPayment->oxpayments__oxdesc = new oxField( $oLang->translateString(
                    'PAYMORROW_PAYMENT_METHOD_NAME_DIRECT_DEBIT'
                ) );
            }

            $this->_oPayment = $oxPayment;
        }
    }

    /**
     * Overridden for Paymorrow Services validation
     *
     * @param $iSuccess
     *
     * @return string
     */
    protected function _getNextStep( $iSuccess )
    {
        /** @var OxpsPaymorrowResponseHandler $oPmResponseHandler */
        $oPmResponseHandler = oxRegistry::get( 'OxpsPaymorrowResponseHandler' );

        /**
         * If after validating confirmOrder against Paymorrow services there are errors,
         * return to order step and render error message
         */
        if ( $oPmResponseHandler->hasErrors() ) {
            $iErrorCode = $oPmResponseHandler->getErrorCode();

            /** @var OxpsPaymorrowErrorHandler $oPmErrorHandler */
            $oPmErrorHandler = oxNew( 'OxpsPaymorrowErrorHandler' );

            $oPmErrorHandler->redirectWithError( $iErrorCode );
        }

        /**
         * At this step assuming everything went fine
         * we need to delete SESSION variables created by Paymorrow
         * to avoid any incompatibilities if user has decided to
         * order again
         *
         * @doc: oxid_js_plugin.doc - 7.1 Controlling of customer’s browser session storage
         *
         * sess_challenge is deleted by `ThankYou` controller
         */
        $oSession = oxRegistry::getSession();
        $oSession->deleteVariable( 'pmVerify' );
        $oSession->deleteVariable( 'pm_response' );
        $oSession->deleteVariable( oxRegistry::get( 'OxpsPaymorrowModule' )->getPaymentTransactionId( true ) );
        $oSession->deleteVariable( 'pm_order_transaction_id' );
        $oSession->deleteVariable( 'pm_order_transaction_idINVOICE' );
        $oSession->deleteVariable( 'pm_order_transaction_idSDD' );

        // Set payment method error instead of shipping method error
        if ( $iSuccess == 4 ) {
            $iSuccess = 5;
        }

        return $this->_OxpsPaymorrowOrder_getNextStep_parent( $iSuccess );
    }

    /**
     * Check session for init error and redirect to payment step is any found.
     * The error or order declination entries are set during order confirmation.
     * This data is used on payment step ho inform user and handle payment forms and it is always unset there.
     *
     * @codeCoverageIgnore
     */
    protected function _checkForErrorsToRedirect()
    {
        if ( oxRegistry::getSession()->getVariable( 'pm_init_data' ) ) {
            $sUrl = oxRegistry::getConfig()->getShopCurrentUrl() . "cl=payment";
            $sUrl = oxRegistry::get( "oxUtilsUrl" )->processUrl( $sUrl );

            oxRegistry::getUtils()->redirect( $sUrl, false );
        }
    }


    /**
     * Calls parent _getNextStep method
     *
     * @codeCoverageIgnore
     *
     * @param integer $iSuccess
     */
    protected function _OxpsPaymorrowOrder_getNextStep_parent( $iSuccess )
    {
        return parent::_getNextStep( $iSuccess );
    }

    /**
     * Calls parent render() method
     *
     * @codeCoverageIgnore
     *
     * @return mixed
     */
    protected function _OxpsPaymorrowOrder_render_parent()
    {
        return parent::render();
    }
}
