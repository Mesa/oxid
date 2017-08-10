<?php

/** 
 * PAYONE OXID Connector is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PAYONE OXID Connector is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with PAYONE OXID Connector.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.payone.de
 * @copyright (C) Payone GmbH
 * @version   OXID eShop CE
 */
 
class fcPayOneBasketitem extends fcPayOneBasketitem_parent {
    
    /**
     * Helper object for dealing with different shop versions
     * @var object
     */
    protected $_oFcpoHelper = null;

    /**
     * init object construction
     * 
     * @return null
     */
    public function __construct() {
        parent::__construct();
        $this->_oFcpoHelper = oxNew('fcpohelper');
    }
    

    /**
     * Overrides standard oxid getArticle method
     * 
     * Retrieves the article .Throws an execption if article does not exist,
     * is not buyable or visible.
     *
     * @param bool   $blCheckProduct       checks if product is buyable and visible
     * @param string $sProductId           product id
     * @param bool   $blDisableLazyLoading disable lazy loading
     *
     * @throws oxArticleException, oxNoArticleException exception
     *
     * @return oxarticle
     */
    public function getArticle( $blCheckProduct = true, $sProductId = null, $blDisableLazyLoading = false ) {
        $oConfig = $this->_oFcpoHelper->fcpoGetConfig();
        $blReduceStockBefore    = !(bool)$oConfig->getConfigParam('blFCPOReduceStock');
        $blSuccess              = $this->_oFcpoHelper->fcpoGetRequestParameter('fcposuccess');
        $sRefNr                 = $this->_oFcpoHelper->fcpoGetRequestParameter('refnr');
        $blCheckProduct         = !($blReduceStockBefore && $blSuccess && $sRefNr);
        
        try {
            $blReturn = $this->_fcpoParentGetArticle($blCheckProduct, $sProductId, $blDisableLazyLoading);//
        } 
        catch (oxArticleException $exc) {
            throw $exc;
        }
        return $blReturn;
    }
    
    
    protected function _fcpoParentGetArticle($blCheckProduct, $sProductId, $blDisableLazyLoading) {
        return parent::getArticle($blCheckProduct, $sProductId, $blDisableLazyLoading);
    }

}