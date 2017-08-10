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
 
class fcpayone_boni_main extends fcpayone_admindetails {

    /**
     * Current class template name
     *
     * @var string
     */
    protected $_sThisTemplate = 'fcpayone_boni_main.tpl';

    /**
     * Definitions of multilang files
     * @var array
     */
    protected $_aMultiLangFields = array(
        'sFCPOApprovalText',
        'sFCPODenialText',
    );
    

    /**
     * Loads payment protection configuration and passes them to Smarty engine, returns
     * name of template file "fcpayone_boni_main.tpl".
     *
     * @return string
     */
    public function render() {
        $sReturn = parent::render();
        $oLang = $this->_oFcpoHelper->fcpoGetLang();

        $iLang = $this->_oFcpoHelper->fcpoGetRequestParameter("subjlang");
        if (empty($iLang)) {
            $iLang = '0';
        }

        $this->_aViewData["subjlang"] = $iLang;

        $oConfig = $this->getConfig();
        $sShopId = $oConfig->getShopId();

        $aConfigs = $this->_oFcpoConfigExport->fcpoGetConfig($sShopId, $iLang);

        $this->_aViewData["confbools"] = $aConfigs['bools'];
        $this->_aViewData["confstrs"] = $aConfigs['strs'];
        $this->_aViewData['sHelpURL'] = $this->_oFcpoHelper->fcpoGetHelpUrl();

        return $sReturn;
    }

    /**
     * Saves changed configuration parameters.
     *
     * @return mixed
     */
    public function save() {
        $oConfig = $this->_oFcpoHelper->fcpoGetConfig();

        $iLang = $this->_oFcpoHelper->fcpoGetRequestParameter("subjlang");
        if (empty($iLang)) {
            $iLang = '0';
        }

        $aConfBools = $this->_oFcpoHelper->fcpoGetRequestParameter("confbools");
        $aConfStrs = $this->_oFcpoHelper->fcpoGetRequestParameter("confstrs");
        if (is_array($aConfBools)) {
            foreach ($aConfBools as $sVarName => $sVarVal) {
                $oConfig->saveShopConfVar("bool", $sVarName, $sVarVal);
            }
        }

        if (is_array($aConfStrs)) {
            foreach ($aConfStrs as $sVarName => $sVarVal) {
                if (array_search($sVarName, $this->_aMultiLangFields) !== false) {
                    $sVarName = $sVarName . '_' . $iLang;
                }
                $oConfig->saveShopConfVar("str", $sVarName, $sVarVal);
            }
        }
    }
}
