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
 * Class OxpsPaymorrowPrepareOrder
 */
class OxpsPaymorrowPrepareOrder extends oxUBase
{

    /**
     * Paymorrow function for Verifying Form Data against Paymorrow Services
     */
    public function prepareOrder()
    {
        /** @var OxpsPaymorrowRequestControllerProxy $oPmGateWay */
        $oPmGateWay = oxNew( 'OxpsPaymorrowRequestControllerProxy' );

        $oUtils = oxRegistry::getUtils();

        $oUtils->setHeader( "Content-Type: application/json" );
        $oUtils->showMessageAndExit( $oPmGateWay->prepareOrder( $_POST ) );
    }
}
