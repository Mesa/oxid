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
 * Class OxpsPaymorrowOxBasketItem extends oxBasketItem
 *
 * @see oxBasketItem
 */
class OxpsPaymorrowOxBasketItem extends OxpsPaymorrowOxBasketItem_parent
{

    const PAYMORROW_LINE_ITEM_PREFIX = "lineItem_%d_";


    /**
     * Paymorrow Line Item prefix builder
     *
     * @param $iLineItemCount
     *
     * @return string
     */
    public static function getPaymorrowBasketSummaryLineItemPrefix( $iLineItemCount )
    {
        return sprintf( self::PAYMORROW_LINE_ITEM_PREFIX, $iLineItemCount );
    }

    /**
     * Get related article number.
     *
     * @return string
     */
    public function getProductNumber()
    {
        /** @var $this OxpsPaymorrowOxBasketItem|oxBasketItem */

        /** @var oxArticle $oArticle */
        $oArticle = $this->getArticle();

        return isset( $oArticle->oxarticles__oxartnum->value ) ? (string) $oArticle->oxarticles__oxartnum->value : '';
    }

    /**
     * Compiles summary data array of basket item for Paymorrow.
     *
     * @param int $iLineItemCount
     *
     * @return array
     */
    public function getPaymorrowBasketItemSummary( $iLineItemCount )
    {
        /** @var OxpsPaymorrowOxBasketItem|oxBasketItem $this */

        $sPaymorrowLineItemPrefix = self::getPaymorrowBasketSummaryLineItemPrefix( $iLineItemCount );

        return array(
            $sPaymorrowLineItemPrefix . 'quantity'       => (double) $this->getAmount(),
            $sPaymorrowLineItemPrefix . 'articleId'      => $this->_toUtf( $this->getProductNumber() ),
            $sPaymorrowLineItemPrefix . 'name'           => $this->_toUtf( $this->getTitle(), 50 ),
            $sPaymorrowLineItemPrefix . 'type'           => 'GOODS',
            $sPaymorrowLineItemPrefix . 'unitPriceGross' => (double) $this->getUnitPrice()->getBruttoPrice(),
            $sPaymorrowLineItemPrefix . 'grossAmount'    => (double) $this->getPrice()->getBruttoPrice(),
            $sPaymorrowLineItemPrefix . 'vatAmount'      => (double) $this->getPrice()->getVatValue(),
            $sPaymorrowLineItemPrefix . 'vatRate'        => (double) $this->getVatPercent(),
        );
    }

    /**
     * Alias for encoding casting method.
     *
     * @codeCoverageIgnore
     * @see OxpsPaymorrowEshopDataProvider::toUtf
     *
     * @param string   $sString
     * @param null|int $mLimitLength
     *
     * @return string
     */
    protected function _toUtf( $sString, $mLimitLength = null )
    {
        return OxpsPaymorrowEshopDataProvider::toUtf( $sString, $mLimitLength );
    }
}
