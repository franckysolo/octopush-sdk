<?php

namespace Test;

use Test\AbstractTest;
use Octopush\Api;
use Octopush\Client;
use Octopush\Message;

class ApiTest extends AbstractTest
{
    /** @test */
    public function testCreateInstanceOfApi()
    {
        $api = new Api($this->login, $this->apiKey);
        $this->assertInstanceOf(Api::class, $api);
        return $api;
    }

    /**
     * @test
     * @depends testCreateInstanceOfApi
     * @return void
     */
    public function testApiGetClientReturnClientClass($api)
    {
        $this->assertInstanceOf(Client::class, $api->getClient());
    }

    /**
     * @test
     * @depends testCreateInstanceOfApi
     * @return void
     */
    public function testApiGetCredit($api)
    {
        $this->assertGreaterThan(0, $api->getCredit());
    }

    /**
     * @test
     * @depends testCreateInstanceOfApi
     * @return void
     */
    public function testApiGetBalance($api)
    {
        $this->assertCount(2, $api->getBalance());
    }


    /**
     * @test
     * @depends testCreateInstanceOfApi
     * @return void
     */
    public function testApiGetLowCostBalance($api)
    {
        $this->assertGreaterThan(0, $api->getLowCostBalance());
    }

    /**
     * @test
     * @depends testCreateInstanceOfApi
     * @return void
     */
    public function testApiGetPremiumBalance($api)
    {
        $this->assertGreaterThan(0, $api->getPremiumBalance());
    }

    /**
     * @test
     * @depends testCreateInstanceOfApi
     * @return void
     */
    public function testApiSimulateErrorCode($api)
    {
        $api = new Api($this->login, 'wrong_api_key');
        $balance = $api->getPremiumBalance();
        $this->assertEquals(0, $balance);
        $credit = $api->getCredit();
        $this->assertEquals(0, $credit);
    }

    /**
     * @test
     * @depends testCreateInstanceOfApi
     * @return void
     */
    public function testApiGetCreditClientResponseReturnsArray($api)
    {
        $credit = $api->getCredit();
        $response = $api->getResponse();
        $this->assertArrayHasKey('credit', $response);
        $this->assertArrayHasKey('error_code', $response);
        $this->assertEquals('000', $response['error_code']);
    }

    /**
     * @test
     * @depends testCreateInstanceOfApi
     * @return void
     */
    public function testSendSimpleMessage($api)
    {
        $message = 'This a test from Octopush SDK';
        $isSend = $api->sendMessage($message, [
          'sms_recipients' => TEST_PHONE_NUMBER,
          'sms_text' => $message,
          'sms_type' => Message::SMS_LOW_COST,
          'sms_sender' => 'Octopush sdk',
          'request_mode' => Message::SIMULATION_MODE
        ]);

        $this->assertTrue($isSend);

        $response = $api->getClient()->getResponse();
        $this->assertArrayHasKey('error_code', $response);
        $this->assertArrayHasKey('cost', $response);
        $this->assertArrayHasKey('ticket', $response);
        $this->assertArrayHasKey('balance', $response);
        $this->assertArrayHasKey('sending_date', $response);
        $this->assertArrayHasKey('number_of_sendings', $response);
        $this->assertArrayHasKey('currency_code', $response);
        $this->assertArrayHasKey('successs', $response);
        $this->assertArrayHasKey('success', $response['successs']);
        $this->assertArrayHasKey('recipient', $response['successs']['success']);
        $this->assertArrayHasKey('country_code', $response['successs']['success']);
        $this->assertArrayHasKey('cost', $response['successs']['success']);
        $this->assertArrayHasKey('sms_needed', $response['successs']['success']);
    }
}
