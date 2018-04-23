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
     * HTTP port
     *
     * @var int
     */
    protected $port;

    /**
     * Api Response
     *
     * @var stdClass
     */
    protected $response;

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
        // set the class params
        $this->login = (string) $login;
        $this->apiKey = (string) $key;
        $this->port = (int) $port;
        // set the scheme url
        $scheme = $this->port === 443 ? 'https' : 'http';
        $this->url = sprintf('%s:%s', $scheme, $this->url);
    }

    /**
     * Send a curl request
     *
     * @param  string $url   The url API
     * @param  array $params The query params
     * @return \Octopush\Client
     *
     * @throws \Octopush\Exceptions\CurlRequiredException
     * @throws \Octopush\Exceptions\CurlResponseException
     * @throws \Octopush\Exceptions\CurlResponseCodeException
     */
    public function request($url, array $params = [])
    {
        // Verify if curl is activate
        if (!$this->hasCurl()) {
            throw new CurlRequiredException(
              'Curl extension is required to use Octopush-sdk',
              500
          );
        }
        $params = array_merge($params, [
          'user_login' => $this->login,
          'api_key' => $this->apiKey
        ]);
        $query = http_build_query($params);
        // $query = $this->buidQuery($params);

        $ch = curl_init();
        curl_setopt_array($ch, $this->setCurlOptions($this->url . $url, $query));
        $response = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($responseCode !== 200) {
            curl_close($ch);
            throw new CurlResponseCodeException(
                sprintf('Octopush API Server returns error code %d', $responseCode),
                500
            );
        }

        if (false === $response) {
            $erroMessage = curl_error($ch) ?? 'no curl error specify';
            $errno = curl_errno($ch);
            curl_close($ch);
            throw new CurlResponseException(
                sprintf('Could not get response from %s: %s', $url, $errorMessage),
                $errno
            );
        }

        $this->setErrors($response);
        $this->response = $this->decode($response);

        curl_close($ch);

        return $this;
    }

    /**
     * Returns the formatted url with http scheme for port 80
     * or https for port 443
     *
     * @return string The formatted url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Returns the response
     *
     * @see decode()
     * @return stdClass the curl response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Returns the array errors
     *
     * @see setErrors()
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Decode Curl Response xml to php stdClass
     *
     * @TODO maybe move in Trait
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
     */
    protected function hasCurl()
    {
        return extension_loaded('curl');
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
        if (!empty($errors)) {
            $this->errors[] = 'Xml response is invalid';
        }

        return $this;
    }
}
