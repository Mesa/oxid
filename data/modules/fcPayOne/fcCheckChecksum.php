<?php

class fcCheckChecksum
{

    protected $_sModuleId = null;
    protected $_sModuleName = null;
    protected $_sModuleVersion = null;
    protected $_blGotModuleInfo = null;
    protected $_sShopSystem = null;
    protected $_sVersionCheckUrl = 'http://version.fatchip.de/fcVerifyChecksum.php';
    
    protected function _getBasePath() {
        return dirname(__FILE__).'/';
    }
    
    protected function _getShopBasePath() {
        if($this->_sShopSystem == 'oxid') {
            return $this->_getBasePath().'../../';
        } elseif($this->_sShopSystem == 'magento2') {
            return $this->_getBasePath().'../../../../';
        } else {
            return $this->_getBasePath();
        }
    }

    protected function _handleMetadata($sFilePath) {
        include $sFilePath;
        if(isset($aModule)) {
            if(isset($aModule['id'])) {
                $this->_sModuleId = $aModule['id'];
            }
            if(isset($aModule['title'])) {
                $this->_sModuleName = $aModule['title'];
            }
            if(isset($aModule['version'])) {
                $this->_sModuleVersion = $aModule['version'];
            }
            $this->_sShopSystem = 'oxid';
            $this->_blGotModuleInfo = true;
        }
    }
    
    protected function _handleComposerJson($sFilePath) {
        $sFile = file_get_contents($sFilePath);
        if(!empty($sFile)) {
            $aFile = json_decode($sFile, true);
            if(isset($aFile['name'])) {
                $this->_sModuleId = preg_replace('#[^A-Za-z0-9]#', '_', $aFile['name']);
                $this->_sModuleName = $aFile['name'];
            }
            if(isset($aFile['version'])) {
                $this->_sModuleVersion = $aFile['version'];
            }
            $this->_sShopSystem = 'magento2';
            $this->_blGotModuleInfo = true;
        }
    }
    
    protected function _getFilesToCheck() {
        $aFiles = array();
        if(file_exists($this->_getBasePath().'metadata.php')) {
            $this->_handleMetadata($this->_getBasePath().'metadata.php');
        }
        if(file_exists($this->_getBasePath().'composer.json')) {
            $this->_handleComposerJson($this->_getBasePath().'composer.json');
        }
        if($this->_blGotModuleInfo === true) {
            $sRequestUrl = $this->_sVersionCheckUrl.'?module='.$this->_sModuleId.'&version='.$this->_sModuleVersion;
            $sResponse = file_get_contents($sRequestUrl);
            if($sResponse) {
                $aFiles = json_decode($sResponse);
            }
        }
        return $aFiles;
    }
    
    protected function _checkFiles($aFiles) {
        $aChecksums = array();
        foreach ($aFiles as $sFilePath) {
            $sFullFilePath = $this->_getShopBasePath().$sFilePath;
            if(file_exists($sFullFilePath)) {
                $aChecksums[md5($sFilePath)] = md5_file($sFullFilePath);
            }
        }
        return $aChecksums;
    }
    
    protected function _getCheckResults($aChecksums) {
        $oCurl = curl_init();
        curl_setopt($oCurl, CURLOPT_URL, $this->_sVersionCheckUrl);
        curl_setopt($oCurl, CURLOPT_HEADER, false);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($oCurl, CURLOPT_POST, true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, array(
            'checkdata' => json_encode($aChecksums),    // you'll have to change the name, here, I suppose
            'module' => $this->_sModuleId,
            'version' => $this->_sModuleVersion,
        ));
        $sResult = curl_exec($oCurl);
        curl_close($oCurl);
        
        return $sResult;
    }
    
    public function checkChecksumXml($blOutput = false) {
        if(ini_get('allow_url_fopen') == 0) {
            die("Cant verify checksums, allow_url_fopen is not activated on customer-server!");
        } elseif(!function_exists('curl_init')) {
            die("Cant verify checksums, curl is not activated on customer-server!");
        }

        $aFiles = $this->_getFilesToCheck();
        $aChecksums = $this->_checkFiles($aFiles);
        $sResult = $this->_getCheckResults($aChecksums);
        if($blOutput === true) {
            if($sResult == 'correct') {
                echo $sResult;
            } else {
                $aErrors = json_decode(stripslashes($sResult));
                if(is_null($aErrors)) {
                    $aErrors = json_decode($sResult);
                }
                if(is_array($aErrors)) {
                    foreach ($aErrors as $sError) {
                        echo $sError.'<br>';
                    }
                }
            }
        }
        return $sResult;
    }

}

if(!isset($blOutput) || $blOutput == true) {
    $oScript = new fcCheckChecksum();
    $oScript->checkChecksumXml(true);
}
