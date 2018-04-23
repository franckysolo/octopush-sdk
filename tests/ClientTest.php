<?php

namespace Test;

use PHPUnit\Framework\TestCase;
use Octopush\Client;
use Octopush\Exceptions\CurlRequiredException;
use Octopush\Exceptions\CurlResponseException;
use Octopush\Exceptions\CurlResponseCodeException;

// defines api key & login octopush in config/env.php
require_once 'config/env.php';

class ClientTest extends TestCase
{
    /**
     * The login identifier
     *
     * @var string
     */
    protected $login;

    /**
     * The api key identifier
     *
     * @var string
     */
    protected $apiKey;


    public function setUp()
    {
        parent::setUp();
        $this->login = OCTOPUSH_LOGIN;
        $this->apiKey = OCTOPUSH_API_KEY;
    }

    /** @test */
    public function testCreateInstanceOfClient()
    {
        $client = new Client($this->login, $this->apiKey);
        $this->assertInstanceOf(Client::class, $client);
        return $client;
    }

    /**
     * @test
     * @depends testCreateInstanceOfClient
     *
     * @param \Octopush\Client $client
     * @return void
     */
    public function testGetDefaultFormattedUrl($client)
    {
        $this->assertStringStartsWith('https', $client->getUrl());
    }

    /**
     * @test
     * @depends testCreateInstanceOfClient
     *
     * @param \Octopush\Client $client
     * @return void
     */
    public function testClientRequestGetCredit($client)
    {
        $url = 'credit';
        $client->request($url);
        $this->assertCount(0, $client->getErrors());
        $this->assertNotNull($client->getResponse());
    }

    /**
     * @test
     * @depends testCreateInstanceOfClient
     * @expectedException Octopush\Exceptions\CurlResponseCodeException
     *
     * @param \Octopush\Client $client
     * @return void
     */
    public function testClientBadRequest($client)
    {
        $url = 'test-credit';
        $client->request($url);
    }
    
    /**
    * @test
    * @return void
     */
    public function testHttpConnexion()
    {
        $client = new Client($this->login, $this->apiKey, 80);
        $this->assertStringStartsWith('http://', $client->getUrl());
    }
}
