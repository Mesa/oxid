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

use Paymorrow\PaymorrowGateway;

require_once '../paymorrow_client/PaymorrowClient.php';
require_once '../PaymorrowGateway.php';
require_once '../PaymorrowWsResponseHandler.php';
require_once '../EshopDataProvider.php';

class PaymorrowGatewayTest extends PHPUnit_Framework_TestCase
{

    private $gateway;
    private $pmClientMock;
    private $responseHandlerMock;
    private $eshopDataProviderMock;

    protected function setUp()
    {
        $this->gateway = new PaymorrowGateway();

        $this->pmClientMock = $this->getMock('PaymorrowClient');
        $this->gateway->setPmClient($this->pmClientMock);
        $this->gateway->setEndPointUrl(null);

        $this->responseHandlerMock = $this->getMock('PaymorrowWsResponseHandler',
            array('handlePrepareOrderResponseOK',
                'handlePrepareOrderResponseError',
                'handleConfirmOrderResponseOK',
                'handleConfirmOrderResponseError'));
        $this->gateway->setResponseHandler($this->responseHandlerMock);

        $this->eshopDataProviderMock = $this->getMock("EshopDataProvider",
            array('collectEshopData'));
    }

    public function testCopyData()
    {
        $array = array();

        $value = "value";
        $key = "key";
        $array[$key] = $value;

        $result = \Paymorrow\copyData($array, $key);
        $this->assertEquals($result, $value);
    }

    public function testCopyData_notSet()
    {
        $result = \Paymorrow\copyData(array(), "key");
        $this->assertEquals($result, null, "Result should be null.");
    }

    public function testStartsWith()
    {
        $this->assertTrue(\Paymorrow\startsWith("Hamburger", "Hamburg"));
        $this->assertFalse(\Paymorrow\startsWith("Hamburg", "Hamburger"));
        $this->assertTrue(\Paymorrow\startsWith("text", ""));
        $this->assertTrue(\Paymorrow\startsWith("", ""));
        $this->assertFalse(\Paymorrow\endsWith("", "any pattern"));
        $this->assertFalse(\Paymorrow\startsWith("A text", "text"));
    }

    public function testEndsWith()
    {
        $this->assertFalse(\Paymorrow\endsWith("aaa", "aaaa"));
        $this->assertTrue(\Paymorrow\endsWith("aaa", "aaa"));
        $this->assertTrue(\Paymorrow\endsWith("text", ""));
        $this->assertTrue(\Paymorrow\endsWith("", ""));
        $this->assertFalse(\Paymorrow\endsWith("", "any pattern"));
        $this->assertTrue(\Paymorrow\endsWith("A text", "text"));
    }

    /**
     * Initial request -> pm_order_transaction_id and response data are saved into session.
     */
    public function testPrepareOrder_initialRequest()
    {
        $requestData = array();
        $requestDataUpdated = array('prepareOrder_requestType' => 'INITIAL');
        $id = 123456;
        $pmTxnId = 'pm_order_transaction_id';
        $response = array($pmTxnId => $id, 'order_id' => '111abc');

        $this->pmClientMock->expects($this->once())
            ->method('sendRequest')
            ->with($this->equalTo($requestDataUpdated))
            ->will($this->returnValue($response));

        $this->gateway->setResponseHandler(null); // no need to handle response in this test
        $this->gateway->prepareOrder($requestData);

        $this->assertEquals($_SESSION[$pmTxnId], $id, "Correct pm_order_transaction_id should be stored in session.");
        $this->assertEquals($_SESSION['pm_response'], $response, "Response data should be stored in session.");
    }

    /**
     * Initial request -> pm_order_transaction_id and response data are saved into session.
     */
    public function testPrepareOrder_updatedRequest()
    {
        $id = 123456;
        $pmTxnId = 'pm_order_transaction_id';
        $_SESSION[$pmTxnId] = $id;

        $requestData = array();
        $requestDataUpdated = array($pmTxnId => $id, 'prepareOrder_requestType' => 'UPDATED');

        $this->pmClientMock->expects($this->once())
            ->method('sendRequest')
            ->with($this->equalTo($requestDataUpdated))
            ->will($this->returnValue(null));

        $this->gateway->setResponseHandler(null); // no need to handle response in this test
        $this->gateway->prepareOrder($requestData);
    }

    public function handlersDataProvider_prepareOrder()
    {
        return array(
            // prepared order Not valid request:  error "100" - missing required field
            array('PrepareOrder1.txt', 'handlePrepareOrderResponseError'),
            // Not valid request:  error "401"
            array('PrepareOrder2.txt', 'handlePrepareOrderResponseError'),
            // Valid request: error simulate
            array('PrepareOrder3.txt', 'handlePrepareOrderResponseError'),
            // Valid request: operationMode=VALIDATE
            array('PrepareOrder4.txt', 'handlePrepareOrderResponseOK'),
            // Valid request: requestType=UPDATED
            array('PrepareOrder5.txt', 'handlePrepareOrderResponseOK'),
            // Valid request: operationMode=VALIDATE
            array('PrepareOrder6.txt', 'handlePrepareOrderResponseOK'),
            // Valid request: operationMode=RISK_CHECK
            array('PrepareOrder7.txt', 'handlePrepareOrderResponseOK'),
            // Valid request: operationMode=RISK_PRECHECK
            array('PrepareOrder8.txt', 'handlePrepareOrderResponseOK'),
            // Valid request: order declined
            array('PrepareOrder9.txt', 'handlePrepareOrderResponseError'),
            // Not valid request direct debit
            array('PrepareOrder10.txt', 'handlePrepareOrderResponseError'),
            // Valid request direct debit: operationMode=RISK_CHECK
            array('PrepareOrder11.txt', 'handlePrepareOrderResponseOK'),
            // Valid request direct debit: operationMode=RISK_CHECK-IBAN
            array('PrepareOrder12.txt', 'handlePrepareOrderResponseOK'),
            // Valid request direct debit: operationMode=VALIDATE
            array('PrepareOrder13.txt', 'handlePrepareOrderResponseOK')
        );
    }

    /**
     * Receiving a response is mocked by loading response from file. Then it is checked whether expected response
     * handler was called.
     *
     * @dataProvider handlersDataProvider_prepareOrder
     */
    public function testHandlerCalled_prepareOrder($fileName, $expectedMethodCalled)
    {
        $responseData = $this->readData('response/' . $fileName);

        $this->pmClientMock->expects($this->once())
            ->method('sendRequest')
            ->will($this->returnValue($responseData));

        $this->responseHandlerMock->expects($this->once())
            ->method($expectedMethodCalled)
            ->with($this->equalTo($responseData));

        $this->gateway->prepareOrder(null);
    }

    public function handlersDataProvider_confirmOrder()
    {
        return array(
            // confirm order accepted
            array('ConfirmOrderAccepted.txt', 'handleConfirmOrderResponseOK'),
            // confirm order declined
            array('ConfirmOrderDeclined.txt', 'handleConfirmOrderResponseError'),
            // confirm order error
            array('ConfirmOrderError.txt', 'handleConfirmOrderResponseError'),
        );
    }

    /**
     * Receiving a response is mocked by loading response from file. Then it is checked whether expected response
     * handler was called.
     *
     * @dataProvider handlersDataProvider_confirmOrder
     */
    public function testHandlerCalled_confirmOrder($fileName, $expectedMethodCalled)
    {
        $responseData = $this->readData('response/' . $fileName);

        $this->pmClientMock->expects($this->once())
            ->method('sendRequest')
            ->will($this->returnValue($responseData));

        $this->responseHandlerMock->expects($this->once())
            ->method($expectedMethodCalled)
            ->with($this->equalTo($responseData));

        $this->gateway->confirmOrder(null);
    }

    public function testPrepareOrder_mergeEshopData()
    {
        $this->gateway->setEshopDataProvider($this->eshopDataProviderMock);

        $eshopData = $this->readData('request/PrepareOrder6.txt');
        $this->assertEquals($eshopData['customer_billingAddress_postalCode'], 73492);

        $jsData = array('pm_customer_billingAddress_postalCode' => 12345);
        $jsData['another_field'] = 'foo'; // field without 'pm_' prefix is thrown away

        $mergedData = $this->readData('request/PrepareOrder6.txt');
        $mergedData['pm_customer_billingAddress_postalCode'] = 12345;

        $this->eshopDataProviderMock->expects($this->once())
            ->method('collectEshopData')
            ->will($this->returnValue($eshopData));

        $this->pmClientMock->expects($this->once())
            ->method('sendRequest')
            ->will($this->returnValue($mergedData)); // checks data was merged

        $this->gateway->prepareOrder($jsData);
    }

    public function testFilterClientData()
    {
        // response contains line:
        // clientData=customer_shippingAddress_city,customer_shippingAddress_country
        $responseData = $this->readData('response/ConfirmOrderAccepted.txt');

        $this->pmClientMock->expects($this->once())
            ->method('sendRequest')
            ->will($this->returnValue($responseData));

        $filteredData = $this->gateway->confirmOrder(null);

        $this->assertEquals(2, count($filteredData), "Unexpected number of fields in response after filtering.");
        $this->assertEquals('Rainau', $filteredData['pm_customer_shippingAddress_city'], "Unexpected field value.");
        $this->assertEquals('DE', $filteredData['pm_customer_shippingAddress_country'], "Unexpected field value.");
    }

    public function filterigDataProvider()
    {
        return array(
            array('PrepareOrder12.txt'), // response contains no 'clientData' field
            array('PrepareOrder13.txt') // response contains empty 'clientData' field
        );
    }

    /**
     * @dataProvider filterigDataProvider
     */
    public function testFilterClientData_empty($fileName)
    {
        $responseData = $this->readData('response/' . $fileName);

        $this->pmClientMock->expects($this->once())
            ->method('sendRequest')
            ->will($this->returnValue($responseData));

        $filteredData = $this->gateway->prepareOrder(null);

        $this->assertEquals(0, count($filteredData), "No data should remain in result after filtering.");
    }

    public function testSubmitCertificate()
    {
        // collect data for certificate generation
        $certData = array(
            "countryName" => 'CZ',
            "stateOrProvinceName" => 'CZ',
            "localityName" => 'Prague',
            "organizationName" => 'My Org.',
            "organizationalUnitName" => 'My Org. Unit.',
            "commonName" => 'Frantisek Dobrota',
            "emailAddress" => 'test@example.com'
        );
        // initialization code from Paymorrow
        $requestData["initializationCode"] = 'abc';

        $responseData = array(
            'keyId' => '1234356',
            'timestamp' => ''
        );

        $this->pmClientMock->expects($this->once())
            ->method('sendRequest')
            ->will($this->returnValue($responseData));

        $result = $this->gateway->submitCertificate($certData, $requestData);

        $this->assertArrayHasKey('privateKey', $result);
        $this->assertArrayHasKey('merchantCertificate', $result);
        $this->assertArrayHasKey('keyId', $result);
        $this->assertArrayHasKey('timestamp', $result);
        $this->assertArrayHasKey('merchantCertificateConfirmationPdf', $result);
        $this->assertArrayHasKey('paymorrowCertificate', $result);

    }
    public function addressHashDataProvider()
    {
        return array(
            array($this->createAddress('Müller', 'Bahnhofstraße', '123', '55996'), '8f4bef04c7f11cd556723d707f6d43e3')
        );
    }

    /**
     * @dataProvider addressHashDataProvider
     */
    public function testAddressHash($addr, $hash)
    {
        $this->assertEquals($hash, PaymorrowGateway::addressHash($addr));
    }

    private function readData($file)
    {
        $fileContent = file("./data/" . $file);
        foreach ($fileContent as $line) {
            $keyvalue = explode("=", $line, 2);
            if (count($keyvalue) == 2) {
                $data[$keyvalue[0]] = trim($keyvalue[1]);
            }
        }
        return $data;
    }

    private function createAddress($ln, $street, $house, $zip)
    {
        return array(
            'lastName' => $ln,
            'street' => $street,
            'houseNo' => $house,
            'zip' => $zip
        );
    }
}