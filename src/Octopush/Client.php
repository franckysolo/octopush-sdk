<?php

namespace Octopush;

use Octopush\Curl;

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
     * Send a sms message
     *
     * @param  string $message The string message
     * @param  array  $options The array options
     * @return bool   true if message is send otherwise false
     */
    public function send($message, array $options = [])
    {
        $sms = new Message($options);
        $this->request('sms', $sms->getParams());
        $response = $this->getResponse();

        if ($response['error_code'] !== '000') {
            $this->errors[] = $response['error_code'];
            return false;
        }

        return true;
    }

    /**
     * Send a curl request
     *
     * @param  string $url   The url API
     * @param  array $params The query params
     * @return \Octopush\Client
     */
    public function request($url, array $params = [])
    {
        $curl = new Curl();
        $query = $this->buildQuery($params);
        $curl->setOptions($this->url . $url, $query, $this->port);
        $response = $curl->exec();
        $this->setResponse($response);
        $this->setErrors($response);
        return $this;
    }

    /**
     * Merge the credentials and params then returns the string query
     *
     * @param  array  $params The params array
     * @return string  The query string
     */
    public function buildQuery(array $params = [])
    {
        $params = array_merge($params, [
          'user_login' => $this->login,
          'api_key' => $this->apiKey
        ]);
        return http_build_query($params);
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
     * Sets and encode the respoponse xml to php array
     * @param string $response The xml response string
     */
    public function setResponse($response)
    {
        $this->response = $this->decode($response);
        return $this;
    }

    /**
     * Returns the response
     *
     * @see decode()
     * @return array the curl response
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
     * @param string $response The xml response returns by curl request
     * @return array
     */
    public function decode($response)
    {
        return json_decode(json_encode(simplexml_load_string($response)), true);
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
