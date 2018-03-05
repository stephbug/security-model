<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authorization;

use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;

interface Grantable
{
    public function isGranted(Tokenable $token, array $attributes, $object = null): bool;

    public function forceAuthentication(bool $force): Grantable;
}