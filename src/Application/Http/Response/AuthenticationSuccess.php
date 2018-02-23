<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Response;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use Symfony\Component\HttpFoundation\Response;

interface AuthenticationSuccess
{
    public function onAuthenticationSuccess(Request $request, Tokenable $token): Response;
}