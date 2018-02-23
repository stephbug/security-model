<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Values\Contract;

interface Credentials extends SecurityValue
{
    /**
     * @return mixed
     */
    public function credentials();
}