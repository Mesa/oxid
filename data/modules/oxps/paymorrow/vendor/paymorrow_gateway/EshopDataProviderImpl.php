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

namespace Paymorrow;

require_once('EshopDataProvider.php');

class EshopDataProviderImpl implements EshopDataProvider {

    private $merchantId = "oxid_ws";
    private $mpiSignature = "Oxid123";
    private $requestLanguageCode = "de";
    private static $requestId = 0;
    private static $orderId = 0;

    public function collectCommonData()
    {
        $data = array();

        $data['merchantId'] = $this->merchantId;
        $data['mpiSignature'] = $this->mpiSignature;
        $data['request_languageCode'] = $this->requestLanguageCode;
        $data['request_id'] = self::$requestId++;

        return $data;
    }

    public function collectEshopData()
    {
        // TODO fill with real merchant & order data
        $data = $this->collectCommonData();

        $data['addressEditing_disabled'] = "N";

        $data['order_id'] = $this->getOrderId();
        $data['source'] = "PAYMORROW_GATEWAY_JS";
        $data['operationMode'] = "VALIDATE";

        // order data
        $data['order_grossAmount'] = "19.87";
        $data['order_vatAmount'] = "3.17";
        $data['order_currency'] = "EUR";

        $data['lineItem_1_quantity'] = "1";
        $data['lineItem_1_articleId'] = "a-0001";
        $data['lineItem_1_name'] = "mp3 player";
        $data['lineItem_1_type'] = "GOODS";
        $data['lineItem_1_category'] = "ELECTRONICS";
        $data['lineItem_1_unitPriceGross'] = "11.90";
        $data['lineItem_1_grossAmount'] = "11.90";
        $data['lineItem_1_vatAmount'] = "1.9";
        $data['lineItem_1_vatRate'] = "19.00";

        $data['lineItem_2_quantity'] = "1";
        $data['lineItem_2_articleId'] = "a-0002";
        $data['lineItem_2_name'] = "CD player";
        $data['lineItem_2_type'] = "GOODS";
        $data['lineItem_2_category'] = "ELECTRONICS";
        $data['lineItem_2_unitPriceGross'] = "7.97";
        $data['lineItem_2_grossAmount'] = "7.97";
        $data['lineItem_2_vatAmount'] = "1.27";
        $data['lineItem_2_vatRate'] = "19.00";

        $data['customer_id'] = "oxid_cust";
        $data['customer_title'] = "Mr.";
//        $data['customer_gender'] = "MALE";
        $data['customer_firstName'] = "Paul";
        $data['customer_lastName'] = "Novymedik";
        //$data['customer_phoneNumber'] = "033/2332343";
        //$data['customer_mobileNumber'] = "49 0170 002 198";
        $data['customer_email'] = "pmtst@gmx.de";
        //$data['customer_dateOfBirth'] = "1968-02-15";
        $data['customer_billingAddress_street'] = "Neuwerkstrasse";
        $data['customer_billingAddress_houseNo'] = "11";
        $data['customer_billingAddress_postalCode'] = "99084";
        $data['customer_billingAddress_city'] = "Erfurt - Altstadt";
        $data['customer_billingAddress_country'] = "DE";

        $data['device_checkId'] = session_id();
        $data['client_browser_session_id'] = session_id();
        $data['client_cookies_id'] = "cookies";
        $data['client_ipAddress'] = $_SERVER['REMOTE_ADDR'];
        $data['client_browser_header'] = $this->getBrowserHeaders();

        return $data;
    }

    public function collectConfirmData() {
        $data = $this->collectCommonData();

        $data['pm_order_transaction_id'] = $_SESSION['pm_order_transaction_id'];
        $data['order_id'] = $_SESSION['order_id'];

        return $data;
    }

    /*
        print string which contains all needed data for Paymorrow JS
    */
    public function printPmData()
    {
        $data = $this->collectEshopData();

        // TODO build string with real shop data
        $sessionId = $this->findInArray($data, 'client_browser_session_id', NULL);
        $cookieId = "some_cookie_id";
        $langcode = $this->findInArray($data, 'request_languageCode', NULL);
        $clientIp = $this->findInArray($data, 'client_ipAddress', NULL);

        $firstName = $this->findInArray($data, 'customer_firstName', NULL);
        $lastName = $this->findInArray($data, 'customer_lastName', NULL);

        $phone = $this->findInArray($data, 'customer_phoneNumber', NULL);
        $mobile = $this->findInArray($data, 'customer_mobileNumber', NULL);
        $dob = $this->findInArray($data, 'customer_dateOfBirth', NULL);
        $gender = $this->findInArray($data, 'customer_gender', NULL);
        $email = $this->findInArray($data, 'customer_email', NULL);
        $orderAmount = $this->findInArray($data, 'order_grossAmount', NULL);
        $currencyCode = $this->findInArray($data, 'order_currency', NULL);

        $billingStreet = $this->findInArray($data, 'customer_billingAddress_street', NULL);
        $billingHouseNo = $this->findInArray($data, 'customer_billingAddress_houseNo', NULL);
        $billingLocality = $this->findInArray($data, 'customer_billingAddress_city', NULL);
        $billingPostalCode = $this->findInArray($data, 'customer_billingAddress_postalCode', NULL);
        $billingCountryCode = $this->findInArray($data, 'customer_billingAddress_country', NULL);

        $shippingStreet = $this->findInArray($data, 'customer_shippingAddress_street', $billingStreet);
        $shippingHouseNo = $this->findInArray($data, 'customer_shippingAddress_houseNo', $billingHouseNo);
        $shippingLocality = $this->findInArray($data, 'customer_shippingAddress_city', $billingLocality);
        $shippingPostalCode = $this->findInArray($data, 'customer_shippingAddress_postalCode', $billingPostalCode);
        $shippingCountryCode = $this->findInArray($data, 'customer_shippingAddress_country', $billingCountryCode);


        $arr = array(
            'phone' => $phone,
            'mobile' => $mobile,
            'session_id' => $sessionId,
            'cookie_id' => $cookieId,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'dob' => $dob,
            'gender' => $gender,
            'email' => $email,
            'street' => $billingStreet,
            'houseNumber' => $billingHouseNo,
            'locality' => $billingLocality,
            'postalCode' => $billingPostalCode,
            'country' => $billingCountryCode,
            'shippingStreet' => $shippingStreet,
            'shippingHouseNumber' => $shippingHouseNo,
            'shippingLocality' => $shippingLocality,
            'shippingPostalCode' => $shippingPostalCode,
            'shippingCountry' => $shippingCountryCode,
            'orderAmount' => $orderAmount,
            'currencyCode' => $currencyCode,
            'langcode' => $langcode,
            'client_ip' => $clientIp
        );

        echo json_encode($arr);
    }

    private function findInArray($arr, $key, $default)
    {
        $val = $default;

        if (array_key_exists($key, $arr)) {
            $val = $arr[$key];
        }

        return $val;
    }

    private function getOrderId() {
        // returns unique order id
        srand(time());
        $val = rand();

        return "oxid-" . $val;
    }

    private function getBrowserHeaders() {
        $headers = apache_request_headers();
        $headerStr = '';
        foreach ($headers as $header => $value) {
            $headerStr = $headerStr . " $header: $value\n";
        }

        $headerBase64 = base64_encode($headerStr);

        return $headerBase64;
    }
}
