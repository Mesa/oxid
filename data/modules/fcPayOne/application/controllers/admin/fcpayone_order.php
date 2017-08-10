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
 
class fcpayone_order extends fcpayone_admindetails {

    /**
     * Current class template name
     *
     * @var string
     */
    protected $_sThisTemplate = 'fcpayone_order.tpl';

    /**
     * Array with existing status of order
     *
     * @var array
     */
    protected $_aStatus = null;

    /**
     * Holds prefix of request message to be able to translate right
     * @var array
     */
    protected $_sResponsePrefix = null;

    /**
     * Holds a current response status 
     * @var array
     */
    protected $_aResponse = null;

    /**
     * Holds current status oxid
     * @var string
     */
    protected $_sStatusOxid = null;

    /**
     * Load PAYONE payment information for selected order, passes
     * it's data to Smarty engine and returns name of template file
     * "fcpayone_order.tpl".
     *
     * @return string
     */
    public function render() {
        parent::render();

        $oOrder = $this->_oFcpoHelper->getFactoryObject("oxorder");

        $sOxid = $this->_oFcpoHelper->fcpoGetRequestParameter("oxid");
        if ($sOxid != "-1" && isset($sOxid)) {
            // load object
            $oOrder->load($sOxid);
            $this->_aViewData["edit"] = $oOrder;
        }

        $this->_aViewData['sHelpURL'] = $this->_oFcpoHelper->fcpoGetHelpUrl();

        return $this->_sThisTemplate;
    }

    /**
     * Returns the current status oxid
     * 
     * @params void
     * @return mixed
     */
    public function fcpoGetStatusOxid() {
        if (!$this->_sStatusOxid) {
            $sStatusOxid = $this->_oFcpoHelper->fcpoGetRequestParameter("status_oxid");
            if ($sStatusOxid) {
                $this->_sStatusOxid = $sStatusOxid;
            } else {
                $this->_sStatusOxid = '-1';
            }
        }


        return $this->_sStatusOxid;
    }

    /**
     * Returns current status object if given
     * 
     * @params void
     * @return mixed
     */
    public function fcpoGetCurrentStatus() {
        $oReturn = false;
        $sStatusOxid = $this->fcpoGetStatusOxid();
        $sOrderOxid = $this->_oFcpoHelper->fcpoGetRequestParameter('oxid');

        if ($sStatusOxid && $sOrderOxid) {
            $oOrder = $this->fcpoGetInstance('oxOrder');
            $oTransactionStatus = $this->fcpoGetInstance('fcpotransactionstatus');

            $oOrder->load($sOrderOxid);
            $oTransactionStatus->load($sStatusOxid);
            if ($oOrder->oxorder__fcpotxid->value == $oTransactionStatus->fcpotransactionstatus__fcpo_txid->value) {
                $oReturn = $oTransactionStatus;
            } else {
                $this->_sStatusOxid = '-1';
            }
        }

        return $oReturn;
    }

    /**
     * Get all transaction status for the given order
     *
     * @param void
     * @return array
     */
    public function getStatus() {
        if (!$this->_aStatus) {
            $this->_aStatus = array();
            $sOxid = $this->_oFcpoHelper->fcpoGetRequestParameter("oxid");
            if ($sOxid != "-1" && isset($sOxid)) {
                $oOrder = $this->_oFcpoHelper->getFactoryObject('oxorder');
                $oOrder->load($sOxid);
            }

            if ($oOrder) {
                $this->_aStatus = $oOrder->fcpoGetStatus();
            }
        }
        return $this->_aStatus;
    }

    /**
     * Triggers capture request to PAYONE API and displays the result
     *
     * @param void
     * @return null
     */
    public function capture() {
        $sOxid = $this->_oFcpoHelper->fcpoGetRequestParameter("oxid");
        if ($sOxid != "-1" && isset($sOxid)) {
            $oOrder = $this->_oFcpoHelper->getFactoryObject("oxorder");
            $oOrder->load($sOxid);

            $blSettleAccount = $this->_oFcpoHelper->fcpoGetRequestParameter("capture_settleaccount");
            $blSettleAccount = ($blSettleAccount === null) ? true : (bool) $blSettleAccount;

            $oPORequest = $this->_oFcpoHelper->getFactoryObject('fcporequest');

            $sAmount = $this->_oFcpoHelper->fcpoGetRequestParameter('capture_amount');
            if ($sAmount !== null) {
                $dAmount = str_replace(',', '.', $sAmount);
                $oResponse = $oPORequest->sendRequestCapture($oOrder, $dAmount, $blSettleAccount);
            } elseif ($aPositions = $this->_oFcpoHelper->fcpoGetRequestParameter('capture_positions')) {
                foreach ($aPositions as $sOrderArtKey => $aOrderArt) {
                    if ($aOrderArt['capture'] == '0') {
                        unset($aPositions[$sOrderArtKey]);
                    }
                }
                $oResponse = $oPORequest->sendRequestCapture($oOrder, $dAmount, $blSettleAccount, $aPositions);
            }

            $this->_sResponsePrefix = 'FCPO_CAPTURE_';
            $this->_aResponse = $oResponse;
        }
    }

    /**
     * Triggers debit request to PAYONE API and displays the result
     *
     * @param void
     * @return null
     */
    public function debit() {
        $sOxid = $this->_oFcpoHelper->fcpoGetRequestParameter("oxid");
        if ($sOxid != "-1" && isset($sOxid)) {
            $oOrder = $this->_oFcpoHelper->getFactoryObject("oxorder");
            $oOrder->load($sOxid);

            $sBankCountry = $this->_oFcpoHelper->fcpoGetRequestParameter('debit_bankcountry');
            $sBankAccount = $this->_oFcpoHelper->fcpoGetRequestParameter('debit_bankaccount');
            $sBankCode = $this->_oFcpoHelper->fcpoGetRequestParameter('debit_bankcode');
            $sBankaccountholder = $this->_oFcpoHelper->fcpoGetRequestParameter('debit_bankaccountholder');
            $sAmount = $this->_oFcpoHelper->fcpoGetRequestParameter('debit_amount');

            $oPORequest = $this->_oFcpoHelper->getFactoryObject('fcporequest');
            if ($sAmount !== null) {
                $dAmount = str_replace(',', '.', $sAmount);

                // amount for credit entry has to be negative
                if ((double) $dAmount > 0) {
                    $dAmount = (double) $dAmount * -1;
                }

                if ($dAmount && $dAmount < 0) {
                    $oResponse = $oPORequest->sendRequestDebit($oOrder, $dAmount, $sBankCountry, $sBankAccount, $sBankCode, $sBankaccountholder);
                }
            } elseif ($aPositions = $this->_oFcpoHelper->fcpoGetRequestParameter('capture_positions')) {
                foreach ($aPositions as $sOrderArtKey => $aOrderArt) {
                    if ($aOrderArt['debit'] == '0') {
                        unset($aPositions[$sOrderArtKey]);
                    }
                }
                $oResponse = $oPORequest->sendRequestDebit($oOrder, $dAmount, $sBankCountry, $sBankAccount, $sBankCode, $sBankaccountholder, $aPositions);
            }

            $this->_sResponsePrefix = 'FCPO_DEBIT_';
            $this->_aResponse = $oResponse;
        }
    }

    /**
     * Gets the url of mandate pdf
     * 
     * @param void
     * @return string
     */
    public function fcpoGetMandatePdfUrl() {
        $sPdfUrl = '';
        $sOxid = $this->_oFcpoHelper->fcpoGetRequestParameter("oxid");

        if ($sOxid != "-1" && isset($sOxid)) {
            $oOrder = $this->_oFcpoHelper->getFactoryObject("oxorder");
            $oConfig = $this->_oFcpoHelper->fcpoGetConfig();
            $oOrder->load($sOxid);

            $sPdfUrl = false;

            $blFCPOMandateDownload = $oConfig->getConfigParam('blFCPOMandateDownload');

            if ($oOrder->oxorder__oxpaymenttype->value == 'fcpodebitnote' && $blFCPOMandateDownload) {
                $sFile = $oOrder->fcpoGetMandateFilename();
                if ($sFile) {
                    $sPdfUrl = $this->getViewConfig()->getSelfLink() . 'cl=fcpayone_order&amp;fnc=download&amp;oxid=' . $sOxid;
                }
            }
        }
        return $sPdfUrl;
    }

    /**
     * Trigger redownloading the mandate
     * 
     * @param string $sMandateFilename
     * @return void
     */
    protected function _redownloadMandate($sMandateFilename) {
        $sOxid = $this->_oFcpoHelper->fcpoGetRequestParameter("oxid");
        if ($sOxid != "-1" && isset($sOxid)) {
            $oOrder = $this->_oFcpoHelper->getFactoryObject("oxOrder");
            $blLoaded = $oOrder->load($sOxid);
            if ($blLoaded) {
                $sMandateIdentification = str_replace('.pdf', '', $sMandateFilename);

                $oPORequest = $this->_oFcpoHelper->getFactoryObject('fcporequest');
                $oPORequest->sendRequestGetFile($oOrder->getId(), $sMandateIdentification, $oOrder->oxorder__fcpomode->value);
            }
        }
    }

    /**
     * The download of mandate
     * 
     * @param bool $blUnitTest
     * @return void
     */
    public function download($blUnitTest = false) {
        $sOxid = $this->_oFcpoHelper->fcpoGetRequestParameter("oxid");
        $oConfig = $this->_oFcpoHelper->fcpoGetConfig();
        $blFCPOMandateDownload = $oConfig->getConfigParam('blFCPOMandateDownload');

        if ($blFCPOMandateDownload) {
            $oOrder = $this->fcpoGetInstance("oxOrder");
            $sFilename = $oOrder->fcpoGetMandateFilename();

            if ($sFilename) {
                $sPath = getShopBasePath() . 'modules/fcPayOne/mandates/' . $sFilename;

                if (!$this->_oFcpoHelper->fcpoFileExists($sPath)) {
                    $this->_redownloadMandate($sFilename);
                }

                if ($this->_oFcpoHelper->fcpoFileExists($sPath)) {
                    if (!$blUnitTest)
                        header("Content-Type: application/pdf");
                    if (!$blUnitTest)
                        header("Content-Disposition: attachment; filename=\"{$sFilename}\"");
                    if (!$blUnitTest)
                        readfile($sPath);
                }
            }
        }

        if (!$blUnitTest)
            exit();
    }

    /**
     * Returns request message if there is a relevant one
     * 
     * @param void
     * @return string
     */
    public function fcpoGetRequestMessage() {
        $sReturn = "";

        if ($this->_aResponse && is_array($this->_aResponse) && $this->_sResponsePrefix) {
            $oLang = $this->_oFcpoHelper->fcpoGetLang();
            if ($this->_aResponse['status'] == 'APPROVED') {
                $sReturn = '<span style="color: green;">' . $oLang->translateString($this->_sResponsePrefix . 'APPROVED', null, true) . '</span>';
            } else if ($this->_aResponse['status'] == 'ERROR') {
                $sReturn = '<span style="color: red;">' . $oLang->translateString($this->_sResponsePrefix . 'ERROR', null, true) . $this->_aResponse['errormessage'] . '</span>';
            }
        }

        return $sReturn;
    }

}
