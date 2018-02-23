<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Http\Event\UserLogin;
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

    /**
     * @param object $event
     *
     * @return array|null
     */
    public function dispatchEvent(object $event)
    {
        return $this->eventDispatcher->dispatch($event);
    }
}