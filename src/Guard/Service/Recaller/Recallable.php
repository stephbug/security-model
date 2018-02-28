<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Service\Recaller;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use Symfony\Component\HttpFoundation\Response;

interface Recallable
{
    public function autoLogin(Request $request): ?Tokenable;

    public function loginFail(Request $request): void;

    public function loginSuccess(Request $request, Response $response, Tokenable $token): void;
}