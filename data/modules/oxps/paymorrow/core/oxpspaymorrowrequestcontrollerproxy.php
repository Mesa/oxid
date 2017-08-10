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
 * Class OxpsPaymorrowRequestControllerProxy.
 */
class OxpsPaymorrowRequestControllerProxy extends oxSuperCfg
{

    /**
     * Magic call method to redirect any call to Paymorrow gateway.
     *
     * @codeCoverageIgnore
     *
     * @param string $sMethodName
     * @param array  $aArguments
     *
     * @return string
     */
    public function __call( $sMethodName, $aArguments )
    {
        return $this->_getRequestController()->getGateway()->$sMethodName( (array) reset( $aArguments ) );
    }


    /**
     * Order preparation call.
     * Also perform configuration update on error code 900 received and calls the request again one time.
     *
     * @param array $aPostData
     * @param bool  $blSettingsUpdated If true, then settings update is not called again.
     *
     * @return string
     */
    public function prepareOrder( array $aPostData, $blSettingsUpdated = false )
    {
        // Update user profile with values user entered to Paymorrow form
        $this->_updateUserData( $aPostData );

        // Reset payment method in session and basket on its change
        $this->_resetPaymentMethod( $aPostData );

        // Send order preparation.verification request
        $aResponse = $this->_getRequestController()->pmVerify( $aPostData );
        oxRegistry::get( 'OxpsPaymorrowLogger' )->logWithType( $aPostData, 'Proxy-prepareOrderPOST' );

        /** @var OxpsPaymorrowResponseHandler $oResponseHandler */
        $oResponseHandler   = oxRegistry::get( 'OxpsPaymorrowResponseHandler' );
        $iResponseErrorCore = $oResponseHandler->getErrorCodeFromResponseData( $aResponse );

        // Check of response is an error with configuration update error code 900
        if ( ( $iResponseErrorCore === 900 ) and empty( $blSettingsUpdated ) ) {

            // Call module settings update in case error code 900 received
            oxRegistry::get( 'OxpsPaymorrowModule' )->updateSettings();

            // Call the request again
            return $this->prepareOrder( $aPostData, true );
        }

        return json_encode( $aResponse );
    }

    /**
     * Order confirmation call.
     *
     * @codeCoverageIgnore
     *
     * @return array Paymorrow response from curl validation
     */
    public function confirmOrder()
    {
        return $this->_getRequestController()->pmConfirm();
    }


    /**
     * Update user profile with data posted from Paymorrow form.
     *
     * @nice-to-have: Adjust unit tests to proxy class and make method protected.
     *
     * @param array $aPostData
     */
    public function _updateUserData( array $aPostData )
    {
        if ( $oUser = $this->_updateUserProfileData( $aPostData ) ) {
            $this->_updateUserActiveShippingAddress( $oUser, $aPostData );
        }
    }

    /**
     * Reset changed payment method selection in session for payment surcharge calculation to be valid.
     * If selected method is not the same as in session, it removes session data and adjusts basket calculation.
     *
     * @param array $aPostData
     */
    public function _resetPaymentMethod( array $aPostData )
    {
        $oSession = oxRegistry::getSession();

        if ( $oSession->getVariable( 'paymentid' ) and
             array_key_exists( 'paymentid', $aPostData ) and
             ( $oSession->getVariable( 'paymentid' ) != $aPostData['paymentid'] )
        ) {
            // Remove previous method from sessions
            $oSession->deleteVariable( 'paymentid' );

            // Adjust basket by removing payment surcharge and recalculating the basket
            $oBasket = $oSession->getBasket();
            $oBasket->setPayment();
            $oBasket->setCost( 'oxpayment' );
            $oBasket->calculateBasket( true );
        }
    }

    /**
     * Get built request controller.
     *
     * @return RequestController
     */
    protected function _getRequestController()
    {
        /** @var OxpsOxid2Paymorrow $oOxidToPm */
        $oOxidToPm = oxNew( 'OxpsOxid2Paymorrow' );

        return $oOxidToPm->getBuiltPaymorrowRequestController();
    }

    /**
     * Get valid, logged in user and update their profile data.
     *
     * @param array $aPostData
     *
     * @return bool|OxpsPaymorrowOxUser|oxUser User object if loaded, false otherwise.
     */
    protected function _updateUserProfileData( array $aPostData )
    {
        /** @var OxpsPaymorrowOxUser|oxUser $oUser */
        $oUser = $this->getUser();

        if ( empty( $oUser ) or !( $oUser instanceof oxUser ) or !$oUser->getId() ) {
            return false;
        }

        $oUser->mapToProfileDataAndUpdateUser( $aPostData );

        return $oUser;
    }

    /**
     * Get user active shipping address if it is used in the session and update it.
     *
     * @param OxpsPaymorrowOxUser|oxUser $oUser
     * @param array                      $aPostData
     *
     * @return bool
     */
    protected function _updateUserActiveShippingAddress( oxUser $oUser, array $aPostData )
    {
        /** @var oxAddress $oShippingAddress */
        $oShippingAddress      = $oUser->getSelectedAddress();
        $blShowShippingAddress = (bool) oxRegistry::getSession()->getVariable( 'blshowshipaddress' );

        if ( !$blShowShippingAddress or !( $oShippingAddress instanceof oxAddress ) or !$oShippingAddress->getId()
        ) {
            return false;
        }

        return $oUser->mapShippingDataAndUpdateAddress( $aPostData, $oShippingAddress );
    }
}
