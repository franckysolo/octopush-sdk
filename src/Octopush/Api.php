<?php

namespace Octopush;

use Octopush\Contracts\OctopushApiInterface;

/**
 * @author franckysolo <franckysolo@gmail.com>
 * @version 1.0
 * @package Octopush
 */
class Api implements OctopushApiInterface
{
    protected $client;

    public function __construct($login, $key)
    {
        $this->client = new Client($login, $key);
    }

    /**
     * Returns the api client
     *
     * @return Client
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
        $response = $this->client->getResponse();

        if ($response['error_code'] !== '000') {
            return 0.00;
        }

        // @TODO use money_format string response
        return (float) $response['credit'];
    }
    /**
     * Retourne le solde en nombre de SMS
     * Le résultat donne le solde en Premium et en Standard.
     *
     * @param  boolean $standard [description]
     * @return [type]            [description]
     */
    public function getBalance($standard = true)
    {
        $this->client->request('balance');
        $response = $this->client->getResponse();

        if ($response['error_code'] !== '000') {
            return 0;
        }

        if ($standard) {
            return floor($response['balance'][1]);
        }

        return floor($response['balance'][0]);
    }

    public function sendMessage($message, array $options = [])
    {
        // @TODO implement
    }
}
