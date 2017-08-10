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
 * Class OxpsPaymorrowSettings.
 * Implements Paymorrow settings getters and additional methods.
 */
class OxpsPaymorrowSettings extends OxpsPaymorrowModule
{

    /**
     * Class instance for registry access.
     *
     * @var OxpsPaymorrowSettings
     */
    private static $_instance = null;

    /**
     * Valid Paymorrow settings for storing in oxConfig table
     *
     * @var array
     */
    private $_aValidPaymorrowSettings = array(
        'SandboxMode',
        'MerchantId',
        'MerchantIdTest',
        'PrivateKey',
        'PrivateKeyTest',
        'PublicKey',
        'PublicKeyTest',
        'PaymorrowKey',
        'PaymorrowKeyTest',
        'EndpointUrlTest',
        'EndpointUrlProd',
        'LoggingEnabled',
        'ResourcePath',
        'ResourcePathTest',
        'OperationMode',
        'OperationModeTest',
        'UpdateAddresses',
        'UpdatePhones',
    );


    /**
     * Get valid Paymorrow module settings
     * used for saving and retrieving.
     *
     * @return array
     */
    public function getValidSettings()
    {
        return $this->_aValidPaymorrowSettings;
    }

    /**
     * Get end point URL setting.
     * It verifies if it is Test or Live mode to return corresponding value.
     *
     * @return string|null
     */
    public function getEndPointURL()
    {
        return $this->isSandboxMode() ? $this->getTestEndPointURL() : $this->getProductionEndPointURL();
    }

    /**
     * Get end point URL setting for live mode.
     *
     * @return mixed|null
     */
    public function getProductionEndPointURL()
    {
        return $this->getSetting( 'EndpointUrlProd' );
    }

    /**
     * Get end point URL setting for test mode.
     *
     * @return mixed|null
     */
    public function getTestEndPointURL()
    {
        return $this->getSetting( 'EndpointUrlTest' );
    }

    /**
     * Get a signature compiled of OXID shop: Oxid + Version + Paymorrow module version
     *
     * @return string
     */
    public function getMpiSignature()
    {
        /** @var  $oPmModule oxModule|OxpsPaymorrowModule */
        $oPmModule = oxRegistry::get( 'OxpsPaymorrowModule' );

        return sprintf(
            'Oxid-%s_%s',
            $this->getConfig()->getVersion(), // Shop Version
            $oPmModule->getPaymorrowModuleVersion() // Paymorrow Module version
        );
    }

    /**
     * Get Merchant Id which must be set in OXID Backend.
     * It verifies if it is Test or Live mode to return corresponding value.
     *
     * @return string|null
     */
    public function getMerchantId()
    {
        return $this->isSandboxMode() ? $this->getSetting( 'MerchantIdTest' ) : $this->getSetting( 'MerchantId' );
    }

    /**
     * Get merchants active private key.
     *
     * @return mixed|null
     */
    public function getPrivateKey()
    {
        return $this->_getKey( 'Private' );
    }

    /**
     * Get merchants active public key (certificate).
     *
     * @return mixed|null
     */
    public function getPublicKey()
    {
        return $this->_getKey( 'Public' );
    }

    /**
     * Get Paymorrow active public key (certificate).
     *
     * @return mixed|null
     */
    public function getPaymorrowKey()
    {
        return $this->_getKey( 'Paymorrow' );
    }

    /**
     * Check if test more is enabled in module settings.
     *
     * @return bool
     */
    public function isSandboxMode()
    {
        return (bool) $this->getSetting( 'SandboxMode' );
    }

    /**
     * Get Paymorrow resource path used for JavaScript / CSS inclusion
     * It verifies if it is Test or Live mode to return corresponding value.
     *
     * @return string
     */
    public function getPaymorrowResourcePath()
    {
        return $this->isSandboxMode() ? $this->getSetting( 'ResourcePathTest' ) : $this->getSetting( 'ResourcePath' );
    }

    /**
     * Get Paymorrow operation mode.
     * It verifies if it is Test or Live mode to return corresponding value.
     *
     * @return string
     */
    public function getPaymorrowOperationMode()
    {
        return $this->isSandboxMode() ? $this->getSetting( 'OperationModeTest' ) : $this->getSetting( 'OperationMode' );
    }

    /**
     * Check if logging is enabled in module settings.
     *
     * @return bool
     */
    public function isLoggingEnabled()
    {
        return (bool) $this->getSetting( 'LoggingEnabled' );
    }

    /**
     * Check if user address(es) should be updated with normalized Paymorrow data.
     *
     * @return bool
     */
    public function isAddressesUpdateEnabled()
    {
        return (bool) $this->getSetting( 'UpdateAddresses' );
    }

    /**
     * Check if user phone number(s) should be updated with normalized Paymorrow data.
     *
     * @return bool
     */
    public function isPhonesUpdateEnabled()
    {
        return (bool) $this->getSetting( 'UpdatePhones' );
    }

    /**
     * Get Paymorrow settings from OXID DB oxConfig table.
     *
     * @return array
     */
    public function getSettings()
    {
        $aValidSettings = $this->getValidSettings();
        $aSettings      = array();

        foreach ( $aValidSettings as $sSettingName ) {
            $aSettings[$sSettingName] = $this->getSetting( $sSettingName );
        }

        return $aSettings;
    }

    /**
     * Get specific Paymorrow setting.
     *
     * @param string $sSettingName
     * @param bool   $sSettingName
     *
     * @return mixed|null
     */
    public function getSetting( $sSettingName, $blUseModulePrefix = true )
    {
        return in_array( $sSettingName, $this->_aValidPaymorrowSettings )
            ? $this->_OxpsPaymorrowSettings_getSetting_parent( $sSettingName, true )
            : null;
    }


    /**
     * Get public or private merchant key or public Paymorrow key setting value.
     * It verifies if it is Test or Live mode to return corresponding value.
     * Additionally a base64 decoding is performed.
     *
     * @param string $sKeyType True for public key, false for private key.
     *
     * @return mixed
     */
    protected function _getKey( $sKeyType )
    {
        $sSettingName = (string) $sKeyType;
        $sSettingName .= 'Key';

        if ( $this->isSandboxMode() ) {
            $sSettingName .= 'Test';
        }

        $sEncodedKey = (string) $this->getSetting( $sSettingName );

        return !empty( $sEncodedKey ) ? base64_decode( $sEncodedKey ) : '';
    }

    /**
     * Parent `getSetting` call.
     *
     * @codeCoverageIgnore
     *
     * @param string $sSettingName
     * @param bool   $blUseModulePrefix
     *
     * @return mixed
     */
    protected function _OxpsPaymorrowSettings_getSetting_parent( $sSettingName, $blUseModulePrefix = true )
    {
        return parent::getSetting( $sSettingName, $blUseModulePrefix );
    }
}
