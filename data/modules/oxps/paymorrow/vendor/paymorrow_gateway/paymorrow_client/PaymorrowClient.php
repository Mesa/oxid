<?php
/**
 * This file is part of OXID eSales Paymorrow module.
 *
 * OXID eSales Paymorrow module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales eVAT module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales eVAT module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2015
 */

require_once('AbstractPaymorrowClient.php');

class PaymorrowClient extends AbstractPaymorrowClient
{
    private $privateKeyBytes;
    private $paymorrowPublicKey;

    /**
     * Signs request by merchant private key
     *
     * @param $requestString string urlencoded request string for webservice
     * @return string
     */
    public function signRequest($requestString)
    {
        if (!isset($this->privateKeyBytes)) {
            return array('client_error' => 'PRIVATE_KEY_NOT_SET');
        }


		$priv_key = $this->privateKeyBytes;
        $privkeyid = openssl_get_privatekey($priv_key);
        openssl_sign($requestString, $signature, $privkeyid);
        return bin2hex($signature);
    }

    /**
     * Verifies paymorrow response according to given paymorrow public key
     *
     * @param $responseString string response raw string
     * @param $signature string signature
     * @return boolean true if signature is ok, false otherwise
     */
    public function verifyResponse($responseString, $signature)
    {
        if (!isset($this->paymorrowPublicKey)) {
            return array('client_error' => 'PUBLIC_KEY_NOT_SET');
        }

		$fp = fopen($this->paymorrowPublicKey, "r");
        $pub_key = fread($fp, filesize($this->paymorrowPublicKey));
        fclose($fp);
        $pubkeyid = openssl_get_publickey($pub_key);
        $ok = openssl_verify($responseString, hex2bin($signature), $pubkeyid);
        error_log("openssl_verify:" . $ok);
        if($ok == 1){
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param mixed $privateKey
     */
    public function setPrivateKey($privateKey)
    {
        if (!empty($privateKey)) {
            $fp = fopen($privateKey, "r");
            $this->privateKeyBytes = fread($fp, filesize($privateKey));
            fclose($fp);
        }
    }

    /**
     * @param mixed $paymorrowPublicKey
     */
    public function setPaymorrowPublicKey($paymorrowPublicKey)
    {
        $this->paymorrowPublicKey = $paymorrowPublicKey;
    }

    /**
     * @return mixed
     */
    public function getPaymorrowPublicKey()
    {
        return $this->paymorrowPublicKey;
    }

    /**
     * @param mixed $privateKeyBytes
     */
    public function setPrivateKeyBytes($privateKeyBytes)
    {
        $this->privateKeyBytes = $privateKeyBytes;
    }

}

if (!function_exists('hex2bin')) {
    function hex2bin($data) {
        static $old;
        if ($old === null) {
            $old = version_compare(PHP_VERSION, '5.2', '<');
        }
        $isobj = false;
        if (is_scalar($data) || (($isobj = is_object($data)) && method_exists($data, '__toString'))) {
            if ($isobj && $old) {
                ob_start();
                echo $data;
                $data = ob_get_clean();
            }
            else {
                $data = (string) $data;
            }
        }
        else {
            trigger_error(__FUNCTION__.'() expects parameter 1 to be string, ' . gettype($data) . ' given', E_USER_WARNING);
            return;//null in this case
        }
        $len = strlen($data);
        if ($len % 2) {
            trigger_error(__FUNCTION__.'(): Hexadecimal input string must have an even length', E_USER_WARNING);
            return false;
        }
        if (strspn($data, '0123456789abcdefABCDEF') != $len) {
            trigger_error(__FUNCTION__.'(): Input string must be hexadecimal string', E_USER_WARNING);
            return false;
        }
        return pack('H*', $data);
    }
}
