<?php
/**
 * The API sdk
 *
 * @version 1.0.2
 * @package Octopush
 * @author franckysolo <franckysolo@gmail.com>
 */
namespace Octopush;

use Octopush\Contracts\OctopushApiInterface;

/**
 * The API to push sms message, get response, credit and balance
 *
 * @see http://www.octopush.com/api-sms-documentation
 */
class Api implements OctopushApiInterface
{
    /**
     * Octopush client
     *
     * @var \Octopush\Client
     */
    protected $client;

    /**
     * Create a new API service
     *
     * @param string $login The Octopush login key
     * @param string $key   The Octopush API key available on your Octopush manager.
     * @return void
     */
    public function __construct($login, $key)
    {
        $this->client = new Client($login, $key);
    }

    /**
     * Returns the client response
     *
     * @return array The client response php array
     */
    public function getResponse()
    {
        return $this->client->getResponse();
    }

    /**
     * Returns the api client
     *
     * @return \Octopush\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Retourne le solde sms en Euros.
     *
     * @return float
     */
    public function getCredit()
    {
        $this->client->request('credit');
        $response = $this->getResponse();

        if ($response['error_code'] !== '000') {
            return 0.00;
        }

        return (float) $response['credit'];
    }

    /**
     * Returns the array remaining balance with premium and low cost value
     *
     * @return array The balance array
     */
    public function getBalance()
    {
        $this->client->request('balance');
        $response = $this->getResponse();

        if ($response['error_code'] !== '000') {
            return 0;
        }

        return $response['balance'];
    }

    /**
     * Returns the remaining balance in premium
     *
     * @return int the value in sms
     */
    public function getPremiumBalance()
    {
        list($premium) = $this->getBalance();
        return floor($premium);
    }

    /**
     * Returns the remaining balance in low cost
     *
     * @return int the value in sms
     */
    public function getLowCostBalance()
    {
        list(, $lowcost) = $this->getBalance();
        return floor($lowcost);
    }

    /**
     * Send a sms message
     *
     * @param  string $message The string message
     * @param  array  $options The array options
     * @return bool   true if message is send otherwise false
     */
    public function sendMessage($message, array $options = [])
    {
        return $this->client->send($message, $options);
    }
}
