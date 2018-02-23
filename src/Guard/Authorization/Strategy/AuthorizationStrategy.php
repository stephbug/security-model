<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authorization\Strategy;

use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;

interface AuthorizationStrategy
{
    public function decide(Tokenable $token, array $attributes, object $object): bool;
}