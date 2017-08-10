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
 
class fcpayoneiframe extends oxUBase {

    /**
     * Helper object for dealing with different shop versions
     * @var object
     */
    protected $_oFcpoHelper = null;

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'fcpayoneiframe.tpl';
    
    /**
     * Order object
     * @var object
     */
    protected $_oOrder = null;
    
    
    /**
     * Class constructor, sets all required parameters for requests.
     * 
     * @return null
     */
    public function __construct() {
        parent::__construct();
        $this->_oFcpoHelper = oxNew('fcpohelper');
    }    
    
    
    
    /**
     * The conbtroller renderer
     * 
     * @param void
     * @return string
     */
    public function render() {
        $iCurrVersion = $this->_oFcpoHelper->fcpoGetIntShopVersion();
        if($iCurrVersion >= 4500 && $iCurrVersion < 4700) {
            $this->_sThisTemplate = 'page/checkout/fcpayoneiframe.tpl';
        }
        return parent::render();
    }
    
    
    /**
     * Returns a factory instance of given object
     * 
     * @param string $sName
     * @return object oxOrder
     */
    public function getFactoryObject($sName) {
        return oxNew($sName);
    }
    
    
    /**
     * Returns the order object
     * 
     * @param void
     * @return object
     */
    public function getOrder() {
        if($this->_oOrder === null) {
            $sOrderId = $this->_oFcpoHelper->fcpoGetSessionVariable('sess_challenge' );
            if($sOrderId) {
                $oOrder = $this->_oFcpoHelper->getFactoryObject('oxOrder');
                if($oOrder->load($sOrderId)) {
                    $this->_oOrder = $oOrder;
                }
            }
        }
        return $this->_oOrder;
    }
    
    
    /**
     * Returns iframe url or redirects directly to it
     * 
     * @param void
     * @return mixed
     */
    public function getIframeUrl() {
        $sIframeUrl = $this->_oFcpoHelper->fcpoGetSessionVariable('fcpoRedirectUrl');
        if($sIframeUrl) {
            return $sIframeUrl;
        } else {
            /* Maybe needed for future payment-methods
            $oOrder = $this->getOrder();
            if($oOrder) {
                return $oOrder->fcHandleAuthorization(true);
            }
            */
            $oConfig    = $this->getConfig();
            $oUtils     = $this->_oFcpoHelper->fcpoGetUtils();
            $oUtils->redirect($oConfig->getShopCurrentURL().'&cl=payment');
        }
    }
    
    
    /**
     * Get the height of the iframe
     * 
     * @param void
     * @return string
     */
    public function getIframeHeight() {
        $sPaymentId = $this->getPaymentType();
        switch ($sPaymentId) {
            case 'fcpocreditcard_iframe':
                $sHeight = 700;
                break;
        }
        return $sHeight;
    }
    

    /**
     * Get the width of the iframe
     * 
     * @param void
     * @return string
     */
    public function getIframeWidth() {
        $sPaymentId = $this->getPaymentType();
        switch ($sPaymentId) {
            case 'fcpocreditcard_iframe':
                $sWidth = 360;
                break;
        }
        return $sWidth;
    }
    
    
    /**
     * Get the style of the iframe
     * 
     * @param void
     * @return string
     */
    public function getIframeStyle() {
        $sPaymentId = $this->getPaymentType();
        switch ($sPaymentId) {
            case 'fcpocreditcard_iframe':
                $sStyle = "border:0;margin-top:20px;";
                break;
        }
        return $sStyle;
    }
    
    
    /**
     * Get the header of iframe
     * 
     * @param void
     * @return string
     */
    public function getIframeHeader() {
        $sPaymentId = $this->getPaymentType();
        switch ($sPaymentId) {
            case 'fcpocreditcard_iframe':
                $sHeader = $this->_oFcpoHelper->fcpoGetLang()->translateString('FCPO_CC_IFRAME_HEADER');
                break;
        }
        return $sHeader;
    }
    
    
    /**
     * Get text of iframe
     * 
     * @param void
     * @return mixed
     */
    public function getIframeText() {
        $sPaymentId = $this->getPaymentType();
        switch ($sPaymentId) {
            case 'fcpocreditcard_iframe':
                $sText = false;
                break;
        }
        return $sText;
    }
    
    
    /**
     * Get payment type
     * 
     * @param void
     * @return string
     */
    public function getPaymentType() {
        $oOrder = $this->getOrder();
        if($oOrder && !empty($oOrder->oxorder__oxpaymenttype->value)) {
            $sPaymentId = $oOrder->oxorder__oxpaymenttype->value;
        } else {
            $sPaymentId = $this->_oFcpoHelper->fcpoGetSessionVariable('paymentid');
        }
        return $sPaymentId;
    }
    
}