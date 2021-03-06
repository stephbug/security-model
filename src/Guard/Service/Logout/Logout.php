<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Service\Logout;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use Symfony\Component\HttpFoundation\Response;

interface Logout
{
    public function logout(Request $request, Response $response, Tokenable $token): void;
}