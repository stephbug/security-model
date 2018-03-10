<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Firewall;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Http\Event\ContextEvent;
use StephBug\SecurityModel\Application\Values\UserProviders;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Guard;

class ContextFirewall
{
    /**
     * @var Guard
     */
    private $guard;

    /**
     * @var UserProviders
     */
    private $userProviders;

    /**
     * @var ContextEvent
     */
    private $event;

    public function __construct(Guard $guard, UserProviders $userProviders, ContextEvent $contextEvent)
    {
        $this->guard = $guard;
        $this->userProviders = $userProviders;
        $this->event = $contextEvent;
    }

    public function handle(Request $request, \Closure $next)
    {
        $this->guard->event()->dispatchEvent($this->event);

        $tokenString = $request->session()->get($this->event->sessionKey());

        if ($tokenString) {
            $this->handleToken($tokenString);
        }

        return $next($request);
    }

    private function handleToken(string $tokenString): void
    {
        $token = unserialize($tokenString, [Tokenable::class]);

        $this->guard->forget();

        if ($token instanceof Tokenable) {
            if ($token = $this->userProviders->refreshUser($token)) {
                $this->guard->put($token);
            }
        }
    }
}