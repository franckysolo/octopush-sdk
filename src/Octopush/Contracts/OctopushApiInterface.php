<?php
/**
 * The API Interface
 *
 * @version 1.0.1
 * @package Octopush
 * @subpackage Contracts
 * @author franckysolo <franckysolo@gmail.com>
 */
namespace Octopush\Contracts;

/**
 * The API Interface
 */
interface OctopushApiInterface
{
    /**
     * Returns the response php array
     *
     * @return array the php array response
     */
    public function getResponse();

    /**
     * Returns the remaining credit in euro
     *
     * @return float the value in sms
     */
    public function getCredit();

    /**
     * Returns the remaining balance in premium or standard
     *
     * @param  bool $standard true for standard | false for premium
     * @return float the value in sms
     */
    public function getBalance($standard);

    /**
     * Send a sms message
     *
     * @param  string $message The string message
     * @param  array  $options The array options
     * @return bool   true if message is send otherwise false
     */
    public function sendMessage($message, array $options);
}
