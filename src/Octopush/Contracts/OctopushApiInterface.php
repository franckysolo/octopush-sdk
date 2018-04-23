<?php

namespace Octopush\Contracts;

interface OctopushApiInterface
{
    /**
     * Returns the remaining credit in euro
     *
     * @return float the value in sms
     */
    public function getCredit();

    /**
     * Returns the remaining balance in premium or standard
     *
     * @return float the value in sms
     */
    public function getBalance($standard);

    /**
     * Sending SMS Octopush Message
     *
     * @return [type] [description]
     */
    public function sendMessage($message, array $options);
}
