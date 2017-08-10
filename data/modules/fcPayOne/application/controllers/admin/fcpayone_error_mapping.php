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
 
class fcpayone_error_mapping extends fcpayone_admindetails {

    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'fcpayone_error_mapping.tpl';

    /**
     * Returns list of former configured errors
     * 
     * @param void
     * @return array
     */
    public function getMappings() {
        $aMappings = $this->_fcpoGetExistingMappings();
        $aMappings = $this->_fcpoAddNewMapping($aMappings);

        return $aMappings;
    }
    
    /**
     * Returns list of former configured iframe errors
     * 
     * @param void
     * @return array
     */
    public function getIframeMappings() {
        $aMappings = $this->_fcpoGetExistingIframeMappings();
        $aMappings = $this->_fcpoAddNewIframeMapping($aMappings);

        return $aMappings;
    }
    
    /**
     * Requests xml base file to fetch all existing error codes and default-messages
     * 
     * @param void
     * @return array
     */
    public function fcpoGetPayoneErrorMessages($sType='general') {
        $aErrorCodes = $this->_oFcpoErrorMapping->fcpoGetAvailableErrorCodes($sType);

        return $aErrorCodes;
    }
    
    /**
     * Returns list of language objects
     * 
     * @param void
     * @return array
     */
    public function getLanguages() {
        $oLang = $this->_oFcpoHelper->fcpoGetLang();
        $aLanguages = $oLang->getLanguageArray(null, true, true);
        
        return $aLanguages;
    }
    
    /**
     * Adds a new entry if flag has been set
     * 
     * @param array $aMappings
     * @return array
     */
    protected function _fcpoAddNewMapping($aMappings) {
        if ($this->_oFcpoHelper->fcpoGetRequestParameter('add')) {
            $oMapping = new stdClass();
            $oMapping->sOxid = 'new';
            $oMapping->sErrorCode = '';
            $oMapping->sMappedMessage = '';
            $oMapping->sLangId = '';
            $aMappings[] = $oMapping;
        }

        return $aMappings;
    }
    
    /**
     * Adds a new entry if flag has been set
     * 
     * @param array $aMappings
     * @return array
     */
    protected function _fcpoAddNewIframeMapping($aMappings) {
        if ($this->_oFcpoHelper->fcpoGetRequestParameter('addIframe')) {
            $oMapping = new stdClass();
            $oMapping->sOxid = 'new';
            $oMapping->sErrorCode = '';
            $oMapping->sMappedMessage = '';
            $oMapping->sLangId = '';
            $aMappings[] = $oMapping;
        }

        return $aMappings;
    }

    /**
     * Returns list of all mappings
     * 
     * @param void
     * @return array
     */
    protected function _fcpoGetExistingMappings() {
        $aExistingIframeMappings = $this->_oFcpoErrorMapping->fcpoGetExistingMappings();

        return $aExistingIframeMappings;
    }
    
    /**
     * Returns list of all mappings
     * 
     * @param void
     * @return array
     */
    protected function _fcpoGetExistingIframeMappings() {
        $aExistingErrorMappings = $this->_oFcpoErrorMapping->fcpoGetExistingMappings('iframe');

        return $aExistingErrorMappings;
    }

    /**
     * Updating settings into database
     * 
     * @param void
     * @return void
     */
    public function save() {
        $oMapping = $this->fcpoGetInstance('fcpoerrormapping');
        $aGeneralMappings = $this->_oFcpoHelper->fcpoGetRequestParameter("editval");
        if (is_array($aGeneralMappings) && count($aGeneralMappings) > 0) {
            $oMapping->fcpoUpdateMappings($aGeneralMappings, 'general');
        }
    }
    
    /**
     * Updating iFrame messages
     * 
     * @param void
     * @return void
     */
    public function saveIframe() {
        $oMapping = $this->fcpoGetInstance('fcpoerrormapping');
        $aIframeMappings = $this->_oFcpoHelper->fcpoGetRequestParameter("editval2");
        if (is_array($aIframeMappings) && count($aIframeMappings) > 0) {
            $oMapping->fcpoUpdateMappings($aIframeMappings, 'iframe');
        }
    }

}
