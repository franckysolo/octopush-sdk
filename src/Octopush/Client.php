<?php

namespace Octopush;

use Octopush\Exceptions\CurlResponseException;
use Octopush\Exceptions\CurlResponseCodeException;
use Octopush\Exceptions\CurlRequiredException;

/**
 * @author franckysolo <franckysolo@gmail.com>
 * @version 1.0
 * @package Octopush
 *
 *  The curl client for Octopush request API
 */
class Client
{
    /**
     * Url API SMS HTTP(S)
     *
     * @var string
     */
    protected $url = '//www.octopush-dm.com/api/';

    /**
     * Login identifier
     *
     * @var string
     */
    protected $login;

    /**
     * Api key
     *
     * @var string
     */
    protected $apiKey;

    /**
     * Api Response
     *
     * @var stdClass
     */
    protected $response;

    /**
     * Port number 80 for http | 443 for https
     *
     * @var int
     */
    protected $port = 443;

    /**
     * The API errors
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Create a new sms client
     *
     * @param string  $login The Octopush login email
     * @param string  $key   The Octopush api key
     * @param int     $port  The HTTP Port to determine scheme url
     * @return void
     */
    public function __construct($login, $key, $port = 443)
    {
        // Verify if curl is activate
        $this->initCurl();
        // set the class params
        $this->login = (string) $login;
        $this->apiKey = (string) $key;
        $this->port = (int) $port;
    }

    /**
     * Send a curl request
     *
     * @param  string $url   The url API
     * @param  array $params The query params
     * @return \Octopush\Client
     *
     * @throws \Octopush\Exceptions\CurlResponseException
     * @throws \Octopush\Exceptions\CurlResponseCodeException
     */
    public function request($url, $params)
    {
        $query = http_build_query($params);

        $ch = curl_init();
        curl_setopt_array($ch, $this->serCurlOptions($url, $query));
        $response = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($responseCode !== 200) {
            throw new CurlResponseCodeException(
                sprintf('Octopush API Server returns error code %d', $responseCode),
                500
            );
        }

        if (false === $response) {
            $erroMessage = curl_error($ch) ?? 'no curl error specify';
            curl_close($ch);
            throw new CurlResponseException(
                sprintf('Could not get response from %s: %s', $url, $errorMessage),
                curl_errno($ch)
            );
        }

        $this->setErrors($response);
        $this->response = $this->decode($response);

        return $this;
    }

    /**
     * Decode Curl Response xml to php stdClass
     *
     * @param string $response The xml response returns by curl request
     * @return mixed stdClass
     */
    public function decode($response)
    {
        return json_decode(json_encode(simplexml_load_string($response)), true);
    }

    /**
     * Sets the Curl options
     *
     * @param  string $url   The url API
     * @param string $query The query string builded
     * @return array
     */
    protected function setCurlOptions($url, $query)
    {
        return [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_PORT => $this->port,
            CURLOPT_POSTFIELDS => $query,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FRESH_CONNECT => true
        ];
    }

    /**
     * Check if curl is activate
     *
     * @return void
     *
     * @throws \Octopush\Exceptions\CurlRequiredException
     */
    protected function initCurl()
    {
        if (!extension_loaded('curl')) {
            throw new CurlRequiredException(
                'Curl extension is required to use Octopush-sdk',
                500
            );
        }
    }

    /**
     * Set the xml errors
     *
     * @param string $response
     * @return \Octopush\Client
     */
    protected function setErrors($response)
    {
        libxml_use_internal_errors(true);

        $doc = new \DOMDocument('1.0', 'utf-8');
        $doc->loadXML($response);

        $errors = libxml_get_errors();
        if (! empty($errors)) {
            $this->errors[] = 'Xml response is invalid';
        }

        return $this;
    }
}
