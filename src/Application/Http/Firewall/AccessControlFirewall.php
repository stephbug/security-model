<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Firewall;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Guard\Authorizer;
use StephBug\SecurityModel\Guard\Guard;

class AccessControlFirewall
{
    /**
     * @var Guard
     */
    private $guard;

    /**
     * @var Authorizer
     */
    private $authorizer;

    /**
     * @var array
     */
    private $attributes;

    public function __construct(Guard $guard, Authorizer $authorizer, array $attributes = [])
    {
        $this->guard = $guard;
        $this->authorizer = $authorizer;
        $this->attributes = $attributes;
    }

    public function handle(Request $request, \Closure $next, ...$attributes)
    {
        $token = $this->guard->requireToken();

        $attributes = array_merge($this->attributes, $attributes);

        if ($attributes) {
            if (!$token->isAuthenticated()) {
                $this->guard->put($token = $this->guard->authenticate($token));
            }

            $this->authorizer->requireGranted($attributes, $request);
        }

        return $next($request);
    }

    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }
}