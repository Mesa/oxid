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
 * Class OxpsPaymorrowInit.
 */
class OxpsPaymorrowInit extends oxWidget
{

    /**
     * Widget template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'oxpspaymorrowinit.tpl';


    /**
     * Get Paymorrow controller prepareOrder dispatcher.
     *
     * @return string
     */
    public function getPaymorrowControllerPrepareOrderProcessPaymentURL()
    {
        return $this->_getShopBaseLink() . 'cl=oxpspaymorrowprepareorder&fnc=prepareOrder';
    }

    /**
     * Get URL for Paymorrow dynamic JavaScript inclusion.
     *
     * @return string
     */
    public function getPaymorrowResourceControllerJavaScript()
    {
        return $this->_getShopBaseLink() . 'cl=oxpspaymorrowresource&fnc=getPaymorrowJavaScript';
    }

    /**
     * Get URL for Paymorrow dynamic CSS inclusion.
     *
     * @return string
     */
    public function getPaymorrowResourceControllerCSS()
    {
        return $this->_getShopBaseLink() . 'cl=oxpspaymorrowresource&fnc=getPaymorrowCSS';
    }

    /**
     * Returns a JavaScript with Paymorrow payment methods initialization functions wrapped in document.ready.
     *
     * @nice-to-have Use a template for JS generation rather than doing in within PHP.
     *
     * @return string
     */
    public function getPaymorrowJavaScriptPmInitFull()
    {
        $sPmPrintData              = $this->_getPaymorrowPrintData();
        $sPmControllerPrepareOrder = $this->getPaymorrowControllerPrepareOrderProcessPaymentURL();
        $sSelectedMethod           = $this->getSelectedPaymorrowMethod();

        $sSDD = sprintf(
            'pmInitFull("SDD", "pmsdd", "payment_oxiddebitnote", "dl_payment_sdd", "payment", %s, "%s", %s);',
            $sPmPrintData,
            $sPmControllerPrepareOrder,
            ( $sSelectedMethod == 'pm_sdd' ) ? 'true' : 'false'
        );

        $sInvoice = sprintf(
            'pmInitFull("INVOICE", "pminvoice", "payment_oxidinvoice", "dl_payment_invoice", "payment", %s, "%s", %s);',
            $sPmPrintData,
            $sPmControllerPrepareOrder,
            ( $sSelectedMethod == 'pm_invoice' ) ? 'true' : 'false'
        );

        return sprintf( '$(document).ready(function(){%s%s});', $sSDD, $sInvoice );
    }

    /**
     * Get a code of selected Paymorrow payment method.
     * First session is checked for what user have selected, then payment methods configuration is checked.
     * It applies only on payment methods linked to Paymorrow.
     *
     * @return string Paymorrow payment method code or empty string.
     */
    public function getSelectedPaymorrowMethod()
    {
        $sSessionPaymentId = (string) oxRegistry::getSession()->getVariable( 'paymentid' );

        /** @var OxpsPaymorrowOxPayment|oxPayment $oPayment */
        $oPayment = oxNew( 'OxpsPaymorrowOxPayment' );

        if ( empty( $sSessionPaymentId ) or !$oPayment->load( $sSessionPaymentId ) or
             !$oPayment->isPaymorrowActiveAndMapped()
        ) {
            $oPayment->loadPaymorrowDefault();
        }

        return (string) $oPayment->getPaymorrowPaymentType();
    }


    /**
     * Get a clean base URL of an active (sub-)shop suitable to pass to Paymorrow.
     *
     * @return string
     */
    protected function _getShopBaseLink()
    {
        return (string) str_replace('&amp;', '&', $this->getConfig()->getShopSecureHomeURL());
    }

    /**
     * Get Paymorrow payment forms initialization data in JSON format.
     * It calls OxpsPaymorrowEshopDataProvider -> printPmData.
     *
     * @return string
     */
    protected function _getPaymorrowPrintData()
    {
        /** @var OxpsOxid2Paymorrow $oOxidToPm */
        $oOxidToPm = oxNew( 'OxpsOxid2Paymorrow' );

        return $oOxidToPm->getPaymorrowPrintData();
    }
}
