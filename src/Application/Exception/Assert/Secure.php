<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Exception\Assert;

use Assert\Assertion;

class Secure extends Assertion
{
    protected static $exceptionClass = SecurityValueFailed::class;
}