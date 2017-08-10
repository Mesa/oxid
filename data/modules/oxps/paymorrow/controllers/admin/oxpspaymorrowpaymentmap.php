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
 * Class OxpsPaymorrowPaymentMap.
 */
class OxpsPaymorrowPaymentMap extends oxAdminView
{

    /**
     * Current class template.
     *
     * @var string
     */
    protected $_sThisTemplate = 'paymorrow_paymentmap.tpl';

    /**
     * Names of payment model fields which were duplicated to the payment map tab.
     *
     * @var array
     */
    protected $_aDuplicatedPaymentFields = array(
        'oxaddsum'     => 'double',
        'oxaddsumtype' => 'string',
        'oxfromamount' => 'double',
        'oxtoamount'   => 'double',
        'oxchecked'    => 'integer',
        'oxsort'       => 'integer',
    );


    /**
     * Get an ID of current object.
     *
     * @return string
     */
    public function getPaymentObjectId()
    {
        return $this->getEditObjectId();
    }


    /**
     * Get current edited payment object.
     *
     * @return null|oxPayment
     */
    public function getPaymorrowEditValue()
    {
        $sOXID = $this->getPaymentObjectId();

        if ( $sOXID != "-1" && isset( $sOXID ) ) {

            /** @var oxPayment $oPayment */
            $oPayment = oxNew( "oxPayment" );
            $oPayment->loadInLang( $this->_iEditLang, $sOXID );

            $oOtherLang = $oPayment->getAvailableInLangs();
            if ( !isset( $oOtherLang[$this->_iEditLang] ) ) {

                $oPayment->loadInLang( key( $oOtherLang ), $sOXID );
            }

            return $oPayment;
        }

        return null;
    }


    /**
     * Save Payment methods mapping to database
     *
     * @return string|void
     */
    public function save()
    {
        $aParams = oxRegistry::getConfig()->getRequestParameter( "editval" );

        $sOXID = $aParams['oxpayments__oxid'];

        /** @var OxpsPaymorrowOxPayment|oxPayment $oxPayment */
        $oxPayment = oxNew( 'oxpayment' );
        $oxPayment->load( $sOXID );

        $blPaymorrowActive = (int) $aParams['oxpayments__oxpspaymorrowactive'];

        // Set duplicated payment fields values
        $oxPayment = $this->_setDuplicatedFields( $oxPayment, $aParams );

        $oxPayment->setPaymorrowActive( $blPaymorrowActive );

        $iPaymorrowPaymentMapTo = (int) $aParams['oxpayments__oxpspaymorrowmap'];

        if ( $oxPayment->setPaymorrowPaymentMap( $iPaymorrowPaymentMapTo ) ) {
            $oxPayment->save();
        }
    }


    /**
     * Set payment model duplicated fields values from array.
     * Makes sure, that field is in the array.
     * For amount type field validates if value is correct.
     *
     * @param OxpsPaymorrowOxPayment|oxPayment $oPayment
     * @param array                            $aParams
     *
     * @return OxpsPaymorrowOxPayment|oxPayment
     */
    protected function _setDuplicatedFields( $oPayment, $aParams )
    {
        $aDuplicatedFields = (array) $this->_aDuplicatedPaymentFields;

        foreach ( $aDuplicatedFields as $sFieldName => $sType ) {
            $sField = sprintf( '%s__%s', $oPayment->getCoreTableName(), $sFieldName );

            if ( !isset( $aParams[$sField] ) and ( $sFieldName != 'oxchecked' ) ) {
                continue;
            }

            $this->_setDuplicatedField( $oPayment, $sFieldName, $sType, $aParams, $sField );
        }

        return $oPayment;
    }

    /**
     * Check if field if valid and ok to set a value.
     *
     * @param OxpsPaymorrowOxPayment|oxPayment $oPayment
     * @param string                           $sFieldName
     * @param string                           $sType
     * @param array                            $aParams
     * @param string                           $sField
     *
     * @return bool
     */
    protected function _setDuplicatedField( $oPayment, $sFieldName, $sType, $aParams, $sField )
    {
        $mValue = $aParams[$sField];
        settype( $mValue, $sType );

        if ( ( $sFieldName == 'oxaddsumtype' ) and !in_array( $mValue, array('abs', '%') ) ) {
            return false;
        }

        $oPayment->$sField = new oxField( $mValue );

        return true;
    }
}
