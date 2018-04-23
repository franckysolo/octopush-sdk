<?php

namespace Test;

use PHPUnit\Framework\TestCase;

// defines api key & login octopush in config/env.php
require_once 'config/env.php';

abstract class AbstractTest extends TestCase
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
}
