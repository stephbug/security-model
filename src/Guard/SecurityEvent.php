<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Http\Event\UserAttemptLogin;
use StephBug\SecurityModel\Application\Http\Event\UserFailureLogin;
use StephBug\SecurityModel\Application\Http\Event\UserLogin;
use StephBug\SecurityModel\Application\Http\Event\UserLogout;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;

class SecurityEvent
{
    /**
     * @var Dispatcher
     */
    private $eventDispatcher;

    public function __construct(Dispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function dispatchLoginEvent(Request $request, Tokenable $token): void
    {
        $this->eventDispatcher->dispatch(new UserLogin($request, $token));
    }

    public function dispatchFailureLoginEvent(SecurityKey $securityKey, Request $request): void
    {
        $this->eventDispatcher->dispatch(new UserFailureLogin($securityKey, $request));
    }

    public function dispatchAttemptLoginEvent(Tokenable $token, Request $request): void
    {
        $this->eventDispatcher->dispatch(new UserAttemptLogin($token, $request));
    }

    public function dispatchLogoutEvent(Tokenable $token): void
    {
        $this->eventDispatcher->dispatch(new UserLogout($token));
    }

    /**
     * @param object $event
     *
     * @return array|null
     */
    public function dispatchEvent($event)
    {
        return $this->eventDispatcher->dispatch($event);
    }
}