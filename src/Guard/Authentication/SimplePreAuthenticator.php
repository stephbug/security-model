<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;

interface SimplePreAuthenticator extends SimpleAuthenticator
{
    public function createToken(Request $request, SecurityKey $securityKey): Tokenable;
}