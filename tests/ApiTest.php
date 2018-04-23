<?php

namespace Test;

use Test\AbstractTest;
use Octopush\Api;
use Octopush\Client;

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
        $this->assertGreaterThan(0, $api->getBalance());
    }

    /**
     * @test
     * @depends testCreateInstanceOfApi
     * @return void
     */
    public function testApiGetBalancePremium($api)
    {
        $this->assertGreaterThan(0, $api->getBalance(false));
    }
}
