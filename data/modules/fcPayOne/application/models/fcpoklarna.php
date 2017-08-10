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
class fcpoklarna extends oxBase {

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
     * Returns stored store ids
     * 
     * @param void
     * @return array
     */
    public function fcpoGetStoreIds() {
        $aStoreIds = array();
        $sQuery = "SELECT oxid, fcpo_storeid FROM fcpoklarnastoreids ORDER BY oxid ASC";
        $oResult = $this->_oFcpoDb->Execute($sQuery);
        if ($oResult != false && $oResult->recordCount() > 0) {
            while (!$oResult->EOF) {
                $aStoreIds[$oResult->fields['oxid']] = $oResult->fields['fcpo_storeid'];
                $oResult->moveNext();
            }
        }

        return $aStoreIds;
    }

    /**
     * Add/Update klarna campaigns into database
     * 
     * @param array $aCampaigns
     * @return void
     */
    public function fcpoInsertCampaigns($aCampaigns) {
        if (is_array($aCampaigns) && count($aCampaigns) > 0) {
            foreach ($aCampaigns as $iId => $aCampaignData) {
                if (array_key_exists('delete', $aCampaignData) !== false) {
                    $sQuery = "DELETE FROM fcpoklarnacampaigns WHERE oxid = " . oxDb::getDb()->quote($iId);
                } else {
                    $sQuery = " UPDATE
                                    fcpoklarnacampaigns
                                SET
                                    fcpo_campaign_code = " . oxDb::getDb()->quote($aCampaignData['code']) . ",
                                    fcpo_campaign_title = " . oxDb::getDb()->quote($aCampaignData['title']) . ",
                                    fcpo_campaign_language = " . oxDb::getDb()->quote(serialize($aCampaignData['language'])) . ",
                                    fcpo_campaign_currency = " . oxDb::getDb()->quote(serialize($aCampaignData['currency'])) . "
                                WHERE
                                    oxid = " . oxDb::getDb()->quote($iId);
                }
                $this->_oFcpoDb->Execute($sQuery);
            }
        }
    }

    /**
     * Add/Update klarna storeid into database
     * 
     * @param array $aStoreIds
     * @return void
     */
    public function fcpoInsertStoreIds($aStoreIds) {
        if (is_array($aStoreIds) && count($aStoreIds) > 0) {
            foreach ($aStoreIds as $iId => $aStoreIdData) {

                if (array_key_exists('delete', $aStoreIdData) !== false) {
                    $sQuery = "DELETE FROM fcpopayment2country WHERE fcpo_paymentid = 'KLV' AND fcpo_type = " . oxDb::getDb()->quote($iId);
                    $this->_oFcpoDb->Execute($sQuery);
                    $sQuery = "DELETE FROM fcpoklarnastoreids WHERE oxid = " . oxDb::getDb()->quote($iId);
                } else {
                    $sQuery = "UPDATE fcpoklarnastoreids SET fcpo_storeid = " . oxDb::getDb()->quote($aStoreIdData['id']) . " WHERE oxid = " . oxDb::getDb()->quote($iId);
                }
                $this->_oFcpoDb->Execute($sQuery);
            }
        }
    }

    /**
     * Add Klarna store id
     * 
     * @param void
     * @return void
     */
    public function fcpoAddKlarnaStoreId() {
        $sQuery = "INSERT INTO fcpoklarnastoreids (fcpo_storeid) VALUES ('')";
        $this->_oFcpoDb->Execute($sQuery);
    }

    /**
     * Add Klarna campaign id
     * 
     * @param void
     * @return void
     */
    public function fcpoAddKlarnaCampaign() {
        $sQuery = "INSERT INTO fcpoklarnacampaigns (fcpo_campaign_code, fcpo_campaign_title) VALUES ('', '')";
        $this->_oFcpoDb->Execute($sQuery);
    }

}
