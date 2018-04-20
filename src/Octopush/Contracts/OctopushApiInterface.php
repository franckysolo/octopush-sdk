<?php

namespace Octopush\Contracts;

interface OctopushApiInterface
{
    /**
     * [SMS_LOW_COST description]
     *
     * @var string
     */
    const SMS_LOW_COST = 'XXX';

    /**
     * [SMS_PREMIUM description]
     *
     * @var string
     */
    const SMS_PREMIUM = 'FR';

    /**
     * [SMS_WORLD description]
     *
     * @var string
     */
    const SMS_WORLD = 'WWW';

    /**
     * [REAL_MODE description]
     * @var string
     */
    const REAL_MODE = 'real';

    /**
     * [SIMULATION_MODE description]
     * @var string
     */
    const SIMULATION_MODE = 'simu';

    /**
     * [NO_DELAY description]
     * @var integer
     */
    const NO_DELAY = 1;

    /**
     * [WITH_DELAY description]
     * @var integer
     */
    const WITH_DELAY = 2;

    /**
     * [getCredit description]
     * @return [type] [description]
     */
    public function getCredit();

    /**
     * [getBalance description]
     * @return [type] [description]
     */
    public function getBalance($standard);

    /**
     * [sendMessage description]
     * @return [type] [description]
     */
    public function sendMessage($message, $options);
}
