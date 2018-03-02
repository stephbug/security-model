<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Response;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Exception\AuthorizationException;
use Symfony\Component\HttpFoundation\Response;

interface Unauthorized
{
    public function handle(Request $request, AuthorizationException $exception): Response;
}