<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Contract;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Http\Event\UserAttemptLogin;
use StephBug\SecurityModel\Application\Http\Event\UserFailureLogin;
use StephBug\SecurityModel\Application\Http\Event\UserLogin;
use StephBug\SecurityModel\Application\Http\Event\UserLogout;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;

interface SecurityEvents
{
    const LOGIN_EVENT = UserLogin::class;
    const FAILURE_LOGIN_EVENT = UserFailureLogin::class;
    const ATTEMPT_LOGIN_EVENT = UserAttemptLogin::class;
    const LOGOUT_EVENT = UserLogout::class;

    /**
     * @param string|object $event
     * @param array $payload
     * @param bool $stateless
     * @return mixed
     */
    public function dispatch($event, array $payload = [], bool $stateless = false);

    public function loginEvent(Request $request, Tokenable $token, bool $stateless = false): void;

    public function failureLoginEvent(SecurityKey $securityKey, Request $request, bool $stateless = false): void;

    public function attemptLoginEvent(Tokenable $token, Request $request, bool $stateless = false): void;

    public function logoutEvent(Tokenable $token): void;
}