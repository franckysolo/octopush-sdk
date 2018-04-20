<?php

namespace Octopush;

use Octopush\Exceptions\ResponseCurlException;

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
     * Port number 80 for http | 443 for https
     *
     * @var int
     */
    protected $port = 443;

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
     * @throws CurlResponseCodeException
     * @throws CurlResponseException
     * @return [type]         [description]
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

        // @TODO set the errors table datas
        // $this->setErrors();
        //
        // set the response @var or @class
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

    protected function initCurl()
    {
        // @TODO...
    }

    protected function setErrors()
    {
        // @TODO...
    }
}
