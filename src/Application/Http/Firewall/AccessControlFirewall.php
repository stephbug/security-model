<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Firewall;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Guard\Authorization\Grantable;
use StephBug\SecurityModel\Guard\Guard;
use StephBug\SecurityModel\Role\Exception\AuthorizationDenied;

class AccessControlFirewall
{
    /**
     * @var Guard
     */
    private $guard;

    /**
     * @var Grantable
     */
    private $decisionManager;

    /**
     * @var array
     */
    private $attributes;

    public function __construct(Guard $guard, Grantable $decisionManager, array $attributes = [])
    {
        $this->guard = $guard;
        $this->decisionManager = $decisionManager;
        $this->attributes = $attributes;
    }

    public function handle(Request $request, \Closure $next, ...$attributes)
    {
        $token = $this->guard->requireToken();

        $attributes = array_merge($this->attributes, $attributes);

        if (!empty($attributes)) {
            if (!$this->decisionManager->isGranted($token, $attributes, $request)) {
                throw AuthorizationDenied::reason('Authorization denied');
            }
        }

        return $next($request);
    }

    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }
}