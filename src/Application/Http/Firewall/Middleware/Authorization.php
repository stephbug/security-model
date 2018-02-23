<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Firewall\Middleware;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Guard\Authorizer;

class Authorization
{
    /**
     * @var Authorizer
     */
    private $authorizer;

    /**
     * @var array
     */
    private $attributes;

    public function __construct(Authorizer $authorizer, ...$attributes)
    {
        $this->authorizer = $authorizer;
        $this->attributes = $attributes;
    }

    public function handle(Request $request, \Closure $next)
    {
        $this->authorizer->requireGranted($this->attributes);

        return $next($request);
    }
}