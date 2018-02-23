<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Entrypoint;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

interface Entrypoint
{
    public function startAuthentication(Request $request, AuthenticationException $exception = null): Response;
}