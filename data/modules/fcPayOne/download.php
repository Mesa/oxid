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
 
set_time_limit(0);
ini_set ('memory_limit', '1024M');
ini_set ('log_errors', 1);
ini_set ('error_log', 'error.log');

if(file_exists(dirname(__FILE__)."/../../bootstrap.php")) {
    require_once dirname(__FILE__) . "/../../bootstrap.php";
} else {
	if (!function_exists('getShopBasePath')) {
		/**
		 * Returns shop base path.
		 *
		 * @return string
		 */
		function getShopBasePath()
		{
			return dirname(__FILE__).'/../../';
		}
	}

	set_include_path(get_include_path() . PATH_SEPARATOR . getShopBasePath());

	/**
	 * Returns true.
	 *
	 * @return bool
	 */
	if ( !function_exists( 'isAdmin' )) {
		function isAdmin()
		{
			return false;
		}
	}

	error_reporting( E_ALL ^ E_NOTICE );

	// custom functions file
	require getShopBasePath() . 'modules/functions.php';

	// Generic utility method file
	require_once getShopBasePath() . 'core/oxfunctions.php';
}

/**
 * Description of fcPayOneMandateDownload
 *
 * @author Robert
 */
class fcPayOneMandateDownload extends oxUBase {
    
    
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
    

    protected function _redownloadMandate($sMandateFilename, $sOrderId, $sMode) {
        $sMandateIdentification = str_replace('.pdf', '', $sMandateFilename);

        $oPORequest = oxNew('fcporequest');
        $oPORequest->sendRequestGetFile($sOrderId, $sMandateIdentification, $sMode);
    }
    
    public function render() {
        parent::render();

        $sOrderId = $this->_oFcpoHelper->fcpoGetRequestParameter('id');
        $sUserId = $this->_oFcpoHelper->fcpoGetRequestParameter('uid');
        $sPath = false;
        
        $oUser = $this->getUser();
        if($oUser) {
            $sUserId = $oUser->getId();
        }
        
        if($sUserId) {
            if($sOrderId) {
                $sQuery = " SELECT 
                                a.fcpo_filename,
                                b.oxid,
                                b.fcpomode
                            FROM 
                                fcpopdfmandates AS a
                            INNER JOIN
                                oxorder AS b ON a.oxorderid = b.oxid
                            WHERE
                                b.oxid = '".mysql_real_escape_string($sOrderId)."' AND
                                b.oxuserid = '{$sUserId}'
                            LIMIT 1";
            } else {
                $sQuery = " SELECT 
                                a.fcpo_filename,
                                b.oxid,
                                b.fcpomode
                            FROM 
                                fcpopdfmandates AS a
                            INNER JOIN
                                oxorder AS b ON a.oxorderid = b.oxid
                            WHERE
                                b.oxuserid = '{$sUserId}'
                            ORDER BY
                                b.oxorderdate DESC
                            LIMIT 1";
            }
            $oResult = oxDb::getDb()->Execute($sQuery);
            if ($oResult != false && $oResult->recordCount() > 0) {
                $sFilename = $oResult->fields[0];
                $sOrderId = $oResult->fields[1];
                $sMode = $oResult->fields[2];
            }
            if($sFilename) {
                $sPath = getShopBasePath().'modules/fcPayOne/mandates/'.$sFilename;
            }
        }
        
        if($sPath === false) {
            echo 'Permission denied!';
        } else {
            if(!file_exists($sPath)) {
                $this->_redownloadMandate($sFilename, $sOrderId, $sMode);
            }
            if(file_exists($sPath)) {
                header("Content-Type: application/pdf");
                header("Content-Disposition: attachment; filename=\"{$sFilename}\"");
                readfile($sPath);
            } else {
                echo 'Error: File not found!';
            }
        }        
        exit();
    }
    
}

$oDownload = oxNew('fcPayOneMandateDownload');
$oDownload->render();
