<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Response;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

interface AuthenticationFailure
{
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response;
}