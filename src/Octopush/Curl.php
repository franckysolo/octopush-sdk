<?php
/**
 * The curl object
 *
 * @version 1.0.1
 * @package Octopush
 * @author franckysolo <franckysolo@gmail.com>
 *
 */
namespace Octopush;

use Octopush\Exceptions\CurlResponseException;
use Octopush\Exceptions\CurlResponseCodeException;
use Octopush\Exceptions\CurlRequiredException;

/**
 * The curl object for Octopush request API
 */
class Curl
{
    /**
     * The handle resource
     *
     * @var resource curl
     */
    protected $handle = null;

    /**
     * Init a new curl object
     */
    public function __construct()
    {
        $this->initCurl();
        $this->handle = curl_init();
    }

    /**
     * Check if curl is activate
     *
     * @return void
     *
     * @throws \Octopush\Exceptions\CurlRequiredException
     */
    public function initCurl()
    {
        if (!extension_loaded('curl')) {
            throw new CurlRequiredException(
              'Curl is required to use Octopush-sdk',
              500
            );
        }
    }

    /**
     * Execute the curl request
     *
     * @return string xml response
     *
     * @throws \Octopush\Exceptions\CurlResponseException
     * @throws \Octopush\Exceptions\CurlResponseCodeException
     */
    public function exec()
    {
        $response = curl_exec($this->handle);
        $responseCode = $this->infos();

        if ($responseCode !== 200) {
            throw new CurlResponseCodeException(
                sprintf('Octopush API Server returns error code %d', $responseCode),
                500
            );
        }

        if (false === $response) {
            $erroMessage = curl_error($ch) ?? 'no curl error specify';
            $errno = curl_errno($ch);
            throw new CurlResponseException(
                sprintf('Could not get response from Octopush service: %s', $errorMessage),
                $errno
            );
        }

        return $response;
    }

    /**
     * Get curl infos
     *
     * @return int
     */
    public function infos()
    {
        return curl_getinfo($this->handle, CURLINFO_HTTP_CODE);
    }

    /**
     * Sets the Curl options
     *
     * @param  string $url  The request url
     * @param string $query The query string builded
     * @param int $port The port must be 80 or 443
     * @return void
     */
    public function setOptions($url, $query, $port)
    {
        curl_setopt_array($this->handle, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_PORT => $port,
            CURLOPT_POSTFIELDS => $query,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FRESH_CONNECT => true
        ]);
    }

    /**
     * Close curl connexion
     *
     * @return void
     */
    public function __destruct()
    {
        if ($this->handle) {
            curl_close($this->handle);
        }
    }
}
