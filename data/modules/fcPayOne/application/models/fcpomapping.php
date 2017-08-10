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
 
class fcpomapping extends oxBase {

    /**
     * Helper object for dealing with different shop versions
     * @var object
     */
    protected $_oFcpoHelper = null;

    /**
     * Centralized Database instance
     * @var object
     */
    protected $_oFcpoDb = null;

    /**
     * Init needed data
     */
    public function __construct() {
        parent::__construct();
        $this->_oFcpoHelper = oxNew('fcpohelper');
        $this->_oFcpoDb = oxDb::getDb();
    }

    /**
     * Requests database for existing mappings and returns an array of mapping objects
     * 
     * @param void
     * @return array
     */
    public function fcpoGetExistingMappings() {
        $aMappings = array();
        $oDb = $this->_oFcpoHelper->fcpoGetDb(true);

        $sQuery = "SELECT oxid, fcpo_paymentid, fcpo_payonestatus, fcpo_folder FROM fcpostatusmapping ORDER BY oxid ASC";
        $oResult = $oDb->Execute($sQuery);
        if ($oResult != false && $oResult->recordCount() > 0) {
            while (!$oResult->EOF) {
                // collect data
                $sOxid = $oResult->fields['oxid'];
                $sPaymentId = $oResult->fields['fcpo_paymentid'];
                $sPayoneStatus = $oResult->fields['fcpo_payonestatus'];
                $sFolder = $oResult->fields['fcpo_folder'];

                // create object
                $oMapping = new stdClass();
                $oMapping->sOxid = $sOxid;
                $oMapping->sPaymentType = $sPaymentId;
                $oMapping->sPayoneStatusId = $sPayoneStatus;
                $oMapping->sShopStatusId = $sFolder;
                $aMappings[] = $oMapping;
                $oResult->moveNext();
            }
        }

        return $aMappings;
    }

    /**
     * Updates current set of mappings into database
     * 
     * @param array $aMappings
     * @return void
     */
    public function fcpoUpdateMappings($aMappings) {
        $oDb = $this->_oFcpoHelper->fcpoGetDb();
        // iterate through mappings
        foreach ($aMappings as $sMappingId => $aData) {
            $sQuery = $this->_fcpoGetQuery($sMappingId, $aData);
            $oDb->Execute($sQuery);
        }
    }

    /**
     * Returns the matching query for updating/adding data
     * 
     * @param string $sMappingId
     * @param array $aData
     * @return string
     */
    protected function _fcpoGetQuery($sMappingId, $aData) {
        // quote values from outer space
        if (array_key_exists('delete', $aData) !== false) {
            $oDb = $this->_oFcpoHelper->fcpoGetDb();
            $sOxid = $oDb->quote($sMappingId);
            $sQuery = "DELETE FROM fcpostatusmapping WHERE oxid = {$sOxid}";
        } else {
            $sQuery = $this->_fcpoGetUpdateQuery($sMappingId, $aData);
        }

        return $sQuery;
    }

    /**
     * Returns wether an insert or update query, depending on data
     * 
     * @param string $sMappingId
     * @param array $aData
     * @return string
     */
    protected function _fcpoGetUpdateQuery($sMappingId, $aData) {
        $blValidNewEntry = $this->_fcpoIsValidNewEntry($sMappingId, $aData['sPaymentType'], $aData['sPayoneStatus'], $aData['sShopStatus']);

        $sOxid = $this->_oFcpoDb->quote($sMappingId);
        $sPaymentId = $this->_oFcpoDb->quote($aData['sPaymentType']);
        $sPayoneStatus = $this->_oFcpoDb->quote($aData['sPayoneStatus']);
        $sFolder = $this->_oFcpoDb->quote($aData['sShopStatus']);

        if ($blValidNewEntry) {
            $sQuery = " INSERT INTO fcpostatusmapping (
                            fcpo_paymentid,     fcpo_payonestatus,  fcpo_folder
                        ) VALUES (
                            {$sPaymentId},    {$sPayoneStatus}, {$sFolder}
                        )";
        } else {
            $sQuery = " UPDATE fcpostatusmapping
                        SET
                            fcpo_paymentid = {$sPaymentId},
                            fcpo_payonestatus = {$sPayoneStatus},
                            fcpo_folder = {$sFolder}
                        WHERE
                            oxid = {$sOxid}";
        }

        return $sQuery;
    }

    /**
     * Checks if current entry is new and complete
     * 
     * @param type $sMappingId
     * @param type $sPaymentId
     * @param type $sPayoneStatus
     * @param type $sFolder
     * @return bool
     */
    protected function _fcpoIsValidNewEntry($sMappingId, $sPaymentId, $sPayoneStatus, $sFolder) {
        $blComplete = (!empty($sPayoneStatus) || !empty($sPaymentId) || !empty($sFolder));
        $blValid = ($sMappingId == 'new' && $blComplete) ? true : false;

        return $blValid;
    }

}
