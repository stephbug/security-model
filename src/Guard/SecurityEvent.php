<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Contract\SecurityEvents;

class SecurityEvent implements SecurityEvents
{
    /**
     * @var Dispatcher
     */
    private $eventDispatcher;

    public function __construct(Dispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function loginEvent(Request $request, Tokenable $token): void
    {
        $this->dispatch(static::LOGIN_EVENT, [$request, $token]);
    }

    public function failureLoginEvent(SecurityKey $securityKey, Request $request): void
    {
        $this->dispatch(static::FAILURE_LOGIN_EVENT, [$securityKey, $request]);
    }

    public function attemptLoginEvent(Tokenable $token, Request $request): void
    {
        $this->dispatch(static::ATTEMPT_LOGIN_EVENT, [$token, $request]);
    }

    public function logoutEvent(Tokenable $token): void
    {
        $this->dispatch(static::LOGOUT_EVENT, [$token]);
    }

    public function dispatch($event, array $payload = [])
    {
        if (is_object($event)) {
            return $this->eventDispatcher->dispatch($event);
        }

        return $this->eventDispatcher->dispatch($event, $payload);
    }
}