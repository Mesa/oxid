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
 
class fcpoforwarding extends oxBase {

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
     * Returns an array of currently existing forwardings as an array with standard objects
     * 
     * @param void
     * @return array
     */
    public function fcpoGetExistingForwardings() {
        $aForwardings = array();
        $oDb = $this->_oFcpoHelper->fcpoGetDb(true);

        $sQuery = "SELECT oxid, fcpo_payonestatus, fcpo_url, fcpo_timeout FROM fcpostatusforwarding ORDER BY oxid ASC";
        $oResult = $oDb->Execute($sQuery);

        if ($oResult != false && $oResult->recordCount() > 0) {
            while (!$oResult->EOF) {
                // collect values
                $sOxid = $oResult->fields['oxid'];
                $sStatus = $oResult->fields['fcpo_payonestatus'];
                $sUrl = $oResult->fields['fcpo_url'];
                $sTimeout = $oResult->fields['fcpo_timeout'];
                // build object
                $oForwarding = new stdClass();
                $oForwarding->sOxid = $sOxid;
                $oForwarding->sPayoneStatusId = $sStatus;
                $oForwarding->sForwardingUrl = $sUrl;
                $oForwarding->iForwardingTimeout = $sTimeout;
                $aForwardings[] = $oForwarding;
                $oResult->moveNext();
            }
        }

        return $aForwardings;
    }

    /**
     * 
     * 
     * @param array $aForwardings
     * @return void
     */
    public function fcpoUpdateForwardings($aForwardings) {
        $oDb = $this->_oFcpoHelper->fcpoGetDb();
        // iterate through forwardings
        foreach ($aForwardings as $sForwardingId => $aData) {
            $sQuery = $this->_fcpoGetQuery($sForwardingId, $aData);
            $oDb->Execute($sQuery);
        }
    }

    /**
     * Returns the matching query for updating/adding data
     * 
     * @param string $sForwardingId
     * @param array $aData
     * @return string
     */
    protected function _fcpoGetQuery($sForwardingId, $aData) {
        $oDb = oxDb::getDb();
        // quote values from outer space
        $sOxid = $oDb->quote($sForwardingId);
        $sPayoneStatus = $oDb->quote($aData['sPayoneStatus']);
        $sUrl = $oDb->quote($aData['sForwardingUrl']);
        $iTimeout = $oDb->quote($aData['iForwardingTimeout']);


        if (array_key_exists('delete', $aData) !== false) {
            $sQuery = "DELETE FROM fcpostatusforwarding WHERE oxid = {$sOxid}";
        } else {
            $sQuery = $this->_fcpoGetUpdateQuery($sForwardingId, $sPayoneStatus, $sUrl, $iTimeout, $sOxid);
        }

        return $sQuery;
    }

    /**
     * Returns wether an insert or update query, depending on data
     * 
     * @param string $sForwardingId
     * @param string $sPayoneStatus
     * @param string $sUrl
     * @param string $iTimeout
     * @return string
     */
    protected function _fcpoGetUpdateQuery($sForwardingId, $sPayoneStatus, $sUrl, $iTimeout, $sOxid) {
        $blValidNewEntry = $this->_fcpoIsValidNewEntry($sForwardingId, $sPayoneStatus, $sUrl, $iTimeout);
        
        if ($blValidNewEntry) {
            $oUtilsObject = $this->_oFcpoHelper->fcpoGetUtilsObject();
            $sOxid = $oUtilsObject->generateUID();
            $sQuery = " INSERT INTO fcpostatusforwarding (
                                fcpo_payonestatus,  fcpo_url,   fcpo_timeout
                            ) VALUES (
                                {$sPayoneStatus}, {$sUrl},  {$iTimeout}
                            )";
        } else {
            $sQuery = " UPDATE fcpostatusforwarding
                            SET
                                fcpo_payonestatus = {$sPayoneStatus},
                                fcpo_url = {$sUrl},
                                fcpo_timeout = {$iTimeout}
                            WHERE
                                oxid = {$sOxid}";
        }

        return $sQuery;
    }

    /**
     * Checks if current entry is new and complete
     * 
     * @param type $sForwardingId
     * @param type $sPayoneStatus
     * @param type $sUrl
     * @param type $iTimeout
     * @return bool
     */
    protected function _fcpoIsValidNewEntry($sForwardingId, $sPayoneStatus, $sUrl, $iTimeout) {
        $blComplete = (!empty($sPayoneStatus) || !empty($sUrl) || !empty($iTimeout));
        $blValid = ($sForwardingId == 'new' && $blComplete) ? true : false;

        return $blValid;
    }

}
