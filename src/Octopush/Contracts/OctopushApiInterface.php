<?php

namespace Octopush\Contracts;

interface OctopushApiInterface
{
    public function getCredit();

    public function getBalance();

    public function sendMessage();
}
