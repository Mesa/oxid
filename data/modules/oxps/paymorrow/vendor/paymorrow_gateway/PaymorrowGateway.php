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

require_once 'CertificateGenerator.php';

class PaymorrowGateway
{
    const PM_PREFIX = "pm_";

    private $pmClient;

    private $endPointUrl;

    private $responseHandler;

    private $eshopDataProvider;

    public function prepareOrder($data)
    {
        $this->validateInputParameters($data);

        $eshopData = null;
        if (!is_null($this->eshopDataProvider)) {
            $eshopData = $this->eshopDataProvider->collectEshopData();
        }

        $data = $this->mergeEshopDataWithRequestData($eshopData, $data);
		$orderTransactionName = 'pm_order_transaction_id' . $data['paymentMethod_name'];
		
        if (isset($_SESSION[$orderTransactionName])) {
            $data['pm_order_transaction_id'] = $_SESSION[$orderTransactionName];
            $data['prepareOrder_requestType'] = 'UPDATED';
        } else {
            unset($data['pm_order_transaction_id']);
            $data['prepareOrder_requestType'] = 'INITIAL';
        }

        $this->pmClient->setEndPoint($this->endPointUrl . 'prepareOrder');

        $responseData = $this->sendRequest($data);

        if (!is_null($this->responseHandler)) {
            if ($this->isResponseOK($responseData)) {
                $this->responseHandler->handlePrepareOrderResponseOK($responseData);
            } else {
                $this->responseHandler->handlePrepareOrderResponseError($responseData);
            }
        }

        $_SESSION['pm_response'] = $responseData;
        if (isset($responseData['pm_order_transaction_id'])) {
            $_SESSION[$orderTransactionName] = $responseData['pm_order_transaction_id'];
        }
		
		if ($this->isResponseDeclined($responseData)) {
			if (isset($_SESSION[$orderTransactionName])) {
				unset($_SESSION[$orderTransactionName]);
			}
		}		

        if (isset($responseData['order_id'])) {
            $_SESSION['order_id'] = $responseData['order_id'];
        }

        return $this->prepareResponseData($responseData);
    }

    public function confirmOrder()
    {
		// prepare data for order confirmation
        $requestData = array();
		if (!is_null($this->eshopDataProvider)) {
            $requestData = $this->eshopDataProvider->collectConfirmData();
        }

		$orderTransactionName = 'pm_order_transaction_id' . $requestData['paymentMethod_name'];

        $this->pmClient->setEndPoint($this->endPointUrl . 'confirmOrder');
        $responseData = $this->pmClient->sendRequest($requestData);

		if ($this->isResponseDeclined($responseData)) {
			if (isset($_SESSION[$orderTransactionName])) {
				unset($_SESSION[$orderTransactionName]);
			}
		}
		
        if (!is_null($this->responseHandler)) {
            if ($this->isResponseOK($responseData)) {
                $this->responseHandler->handleConfirmOrderResponseOK($responseData);
            } else {
                $this->responseHandler->handleConfirmOrderResponseError($responseData);
            }
        }

        return $responseData;
    }

    /**
     * Generates private key and public certificate, submits certificate to paymorrow service.
     *
     * @param $certData
     * @param $requestData
     * @return mixed
     * Array:
     * <ul>
     *  <li>'privateKey' => generated private key</li>
     *  <li>'merchantCertificate' => generated certificate</li>
     *  <li>'merchantCertificateConfirmationPdf' => PDF confirmation letter returned from service request</li>
     *  <li>'keyId' => identifier of your (merchant's) certificate for communication with Paymorrow</li>
     *  <li>'timestamp' => date and time when the certificate was succesfully uploaded</li>
     *  <li>'paymorrowCertificate' => Paymorrow certificate returned from service request</li>
     * </ul>
     */
    public function submitCertificate($certData, $requestData)
    {
        // generate private key and public certificate
        $gen = new CertificateGenerator();
        $merchantGeneratedKeys = $gen->generateCertificate($certData);
        $privateKey = $merchantGeneratedKeys['privateKey'];
        $merchantCertificate = $merchantGeneratedKeys['certificate'];

        if (!is_null($this->eshopDataProvider)) {
            $commonData = $this->eshopDataProvider->collectCommonData();
            $requestData = array_merge($commonData, $requestData);
        }

        $this->pmClient->setEndPoint($this->endPointUrl . 'submitCertificate');
        $this->pmClient->setPrivateKeyBytes($privateKey);

        $requestData["merchantCertificate"] = base64_encode($merchantGeneratedKeys['certificate']);;

        $response =  $this->pmClient->sendRequest($requestData);

        if (!is_null($this->responseHandler)) {
            $this->responseHandler->handleSubmitCertificateResponse($response);
        }

        $response['privateKey'] = $privateKey;
        $response['merchantCertificate'] = $merchantCertificate;
        $response['paymorrowCertificate'] = base64_decode($response['paymorrowCertificate']);

        return $response;
    }

    private function mergeEshopDataWithRequestData($eshopData, $data)
    {
        $outputData = array();

        if (!is_null($eshopData)) {
            foreach ($eshopData as $key => $value) {
                $outputData[$key] = $value;
            }
        }

        if (!is_null($data)) {
            foreach ($data as $key => $value) {
                if (startsWith($key, "pm_")) {
                    $outputData[substr($key, 3)] = $value;
                }
            }
        }

        return $outputData;
    }

    /* OXPS Patch - Start */protected/* OXPS Patch - End */ function prepareResponseData($data)
    {
		// select right parameters which can be used in payment method setup (use clientData)
		$filteredData = $this->filterClientData($data);
        
        // add PM_PREFIX to data keys
        $outputData = array();

        if (!is_null($filteredData)) {
            foreach ($filteredData as $key => $value) {
                $newKey = PaymorrowGateway::PM_PREFIX . $key;
                $outputData[$newKey] = $value;
            }
        }

        return $outputData;
    }

    private function validateInputParameters($data)
    {
        // here you can validate request parameters
    }


    /**
     * @param mixed $pmClient
     */
    public function setPmClient($pmClient)
    {
        $this->pmClient = $pmClient;
    }

	/**
     * 
     */
    public function getPmClient()
    {
        return $this->pmClient;
    }

    public function setEndPointUrl($endPointUrl)
    {
        $this->endPointUrl = $endPointUrl;
    }
	
    public function getEndPointUrl()
    {
        return $this->endPointUrl;
    }	

    /**
     * @param $responseData
     * @return bool
     */
    public function isResponseOK($responseData)
    {
        return isset($responseData['response_status'])
        && $responseData['response_status'] === 'OK'
        && ((isset($responseData['order_status']) && ($responseData['order_status'] === 'ACCEPTED'
                || $responseData['order_status'] === 'VALIDATED'
                || $responseData['order_status'] === 'ACCEPTED_CONFIRMED')));
    }

    /**
     * @param $responseData
     * @return bool
     */
    public function isResponseDeclined($responseData)
    {
        return isset($responseData['response_status'])
        && $responseData['response_status'] === 'OK'
        && ((isset($responseData['order_status']) && ($responseData['order_status'] === 'DECLINED'
                || $responseData['order_status'] === 'DECLINED_FINAL')));
    }


    private function sendRequest($data)
    {
        return $this->pmClient->sendRequest($data);
    }

    /**
     * @param mixed $responseHandler
     */
    public function setResponseHandler($responseHandler)
    {
        $this->responseHandler = $responseHandler;
    }

    /**
     * @param mixed $eshopDataProvider
     */
    public function setEshopDataProvider($eshopDataProvider)
    {
        $this->eshopDataProvider = $eshopDataProvider;
    }

    /**
     * @param $data
     * @return new array containing only those fields from $data that are enumerated in $data['clientData']
     */
    private function filterClientData($data)
    {
        $result = array();
        if (isset($data['clientData'])) {
            $fields = explode(',', $data['clientData']);
            foreach ($fields as $field) {
                if (!empty($field)) {
                    if (isset($data[$field])) {
                        $result[$field] = $data[$field];
                    }
                }
            }
        } else {
            // when error appears - sent it
            $result = $data;
        }
        return $result;
    }
	/**
	 * @param $address array with following mandatory fields : firstName, lastName, street, houseNo, zip
	 * @return string hash of the address
	 */
	public static function addressHash($address)
	{
		$s = $address['lastName'];
		$s .= $address['street'];
		$s .= $address['houseNo'];
		$s .= $address['zip'];
		return md5($s);
	}

	/**
	* address1 should be shipping address from customer profile
	* address2 should be shipping address from paymorrow (customer_shippingAddress_* from prepareOrder response)
	* @param $address1 array with following mandatory fields : firstName, lastName, street, city, zip; optional fields are: houseNo
	* @param $address2 array with following mandatory fields : firstName, lastName, street, city, zip; optional fields are: houseNo
	* @return boolean
	*/
	public static function isSimilarAddress($address1, $address2) {
        $zip1 = $address1['zip'];
        $zip2 = $address2['zip'];
        $zip1 = str_replace(' ', '', $zip1);
        $zip2 = str_replace(' ', '', $zip2);
        $part1 = substr($zip1, 0, 2);
        $part2 = substr($zip2, 0, 2);
        return $part1 === $part2;
    }
}


function startsWith($haystack, $needle)
{
    return $needle === "" || strpos($haystack, $needle) === 0;
}

function endsWith($haystack, $needle)
{
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}

function copyData($array, $name)
{
    $res = NULL;
    if (isset($array[$name])) {
        $res = $array[$name];
    }

    return $res;
}