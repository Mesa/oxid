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
 
class fcpayone_status_mapping extends fcpayone_admindetails {

    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'fcpayone_status_mapping.tpl';

    /**
     * Returns list of former configured mappings
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
     * Adds a new entry if flag has been set
     * 
     * @param array $aMappings
     * @return array
     */
    protected function _fcpoAddNewMapping($aMappings) {
        if ($this->_oFcpoHelper->fcpoGetRequestParameter('add')) {
            $oMapping = new stdClass();
            $oMapping->sOxid = 'new';
            $oMapping->sPaymentType = '';
            $oMapping->sPayoneStatusId = '';
            $oMapping->sShopStatusId = '';
            $aMappings[] = $oMapping;
        }

        return $aMappings;
    }

    /**
     * Requests database for existing mappings and returns an array of mapping objects
     * 
     * @param void
     * @return array
     */
    protected function _fcpoGetExistingMappings() {
        $aExistingStatusMappings = $this->_oFcpoMapping->fcpoGetExistingMappings();

        return $aExistingStatusMappings;
    }

    /**
     * Returns a list of payment types
     * 
     * @param void
     * @return array
     */
    public function getPaymentTypeList() {
        $oPayment = oxNew('oxPayment');
        $aPaymentTypes = $oPayment->fcpoGetPayonePaymentTypes();

        return $aPaymentTypes;
    }

    /**
     * Returns a list of payone status list
     * 
     * @param void
     * @return array
     */
    public function getPayoneStatusList() {
        $aPayoneStatusList = $this->_oFcpoHelper->fcpoGetPayoneStatusList();

        $aNewList = array();
        foreach ($aPayoneStatusList as $sStatusId) {
            $oStatus = new stdClass();
            $oStatus->sId = $sStatusId;
            $oStatus->sTitle = $this->_oFcpoHelper->fcpoGetLang()->translateString('fcpo_status_' . $sStatusId, null, true);
            $aNewList[] = $oStatus;
        }

        return $aNewList;
    }

    /**
     * Returns a list of shop states
     * 
     * @param void
     * @return array
     */
    public function getShopStatusList() {
        $aFolders = $this->getConfig()->getConfigParam('aOrderfolder');
        return $aFolders;
    }

    /**
     * Updating settings into database
     * 
     * @param void
     * @return void
     */
    public function save() {
        $oMapping = $this->fcpoGetInstance('fcpomapping');
        $aMappings = $this->_oFcpoHelper->fcpoGetRequestParameter("editval");
        if (is_array($aMappings) && count($aMappings) > 0) {
            $oMapping->fcpoUpdateMappings($aMappings);
        }
    }

}
