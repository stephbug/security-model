<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication;

use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;

interface Authenticatable
{
    public function authenticate(Tokenable $token): Tokenable;
}