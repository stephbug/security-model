<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Firewall;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AuthenticationFirewall
{
    public function handle(Request $request, \Closure $next)
    {
        $response = null;

        if ($this->requireAuthentication($request)) {
            $response = $this->processAuthentication($request);
        }

        return $response ?? $next($request);
    }

    abstract protected function processAuthentication(Request $request): ?Response;

    abstract protected function requireAuthentication(Request $request): bool;
}