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
 * Class OxpsPaymorrowErrorHandler.
 */
class OxpsPaymorrowErrorHandler extends OxpsPaymorrowModule
{

    /**
     * List of public error codes.
     *
     * @var array
     */
    protected $_aPublicErrors = array(

        // Payment form submission/validation/other errors
        3000 => 'GENERAL_ERROR',
    );


    /**
     * Get human readable error message by error code.
     *
     * @param integer $iErrorCode
     *
     * @return string
     */
    public function getErrorByCode( $iErrorCode )
    {
        return array_key_exists( $iErrorCode, $this->_aPublicErrors )
            ? $this->translateError( $this->_aPublicErrors[$iErrorCode] )
            : $this->translateError( $this->_aPublicErrors[3000] ); // If exact error not exist throw general
    }

    /**
     * Redirect user to given controller and shows an error.
     * In case of 'RELOAD_CONFIGURATION_REQUIRED' error, update module settings and redirect.
     *
     * @codeCoverageIgnore
     *
     * @param        $iErrorCode
     * @param string $sController
     */
    public function redirectWithError( $iErrorCode, $sController = 'order' )
    {
        $sErrorMessage = $this->getErrorByCode( $iErrorCode );

        // Set error
        $oEx = oxNew( 'oxExceptionToDisplay' );
        $oEx->setMessage( $sErrorMessage );
        oxRegistry::get( "oxUtilsView" )->addErrorToDisplay( $oEx, false );

        // Redirect (refresh page)
        $sUrl = $this->getConfig()->getShopCurrentUrl() . "cl=" . $sController;
        $sUrl = oxRegistry::get( "oxUtilsUrl" )->processUrl( $sUrl );
        oxRegistry::getUtils()->redirect( $sUrl );

        return;
    }

    /**
     * Translate Paymorrow errors.
     * Alias for module `translate` method.
     *
     * @param string $sError
     *
     * @return string
     */
    public function translateError( $sError )
    {
        return $this->translate( $sError );
    }
}
