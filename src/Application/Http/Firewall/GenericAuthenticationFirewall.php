<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Firewall;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Exception\AuthenticationException;
use StephBug\SecurityModel\Application\Http\Entrypoint\Entrypoint;
use StephBug\SecurityModel\Application\Http\Request\AuthenticationRequest;
use StephBug\SecurityModel\Application\Http\Response\AuthenticationSuccess;
use StephBug\SecurityModel\Application\Values\SecurityKey;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Guard;
use Symfony\Component\HttpFoundation\Response;

abstract class GenericAuthenticationFirewall extends AuthenticationFirewall
{
    /**
     * @var Guard
     */
    protected $guard;

    /**
     * @var AuthenticationRequest
     */
    protected $authenticationRequest;

    /**
     * @var AuthenticationSuccess
     */
    protected $authenticationSuccess;

    /**
     * @var Entrypoint
     */
    protected $entrypoint;

    /**
     * @var SecurityKey
     */
    protected $securityKey;

    /**
     * @var bool
     */
    protected $stateless;

    public function __construct(Guard $guard,
                                AuthenticationRequest $authenticationRequest,
                                AuthenticationSuccess $authenticationSuccess,
                                Entrypoint $entrypoint,
                                SecurityKey $securityKey,
                                bool $stateless)
    {
        $this->guard = $guard;
        $this->authenticationRequest = $authenticationRequest;
        $this->authenticationSuccess = $authenticationSuccess;
        $this->entrypoint = $entrypoint;
        $this->securityKey = $securityKey;
        $this->stateless = $stateless;
    }

    protected function processAuthentication(Request $request): ?Response
    {
        try {
            $token = $this->guard->authenticate($this->createToken($request));

            return $this->onSuccess($request, $token);

        } catch (AuthenticationException $exception) {
            return $this->entrypoint->startAuthentication($request, $exception);
        }
    }

    abstract protected function createToken(Request $request): Tokenable;

    protected function onSuccess(Request $request, Tokenable $token): Response
    {
        $this->guard->put($token);

        if (!$this->stateless) {
            $this->guard->event()->dispatchLoginEvent($request, $token);
        }

        return $this->authenticationSuccess->onAuthenticationSuccess($request, $token);
    }

    protected function requireAuthentication(Request $request): bool
    {
        if ($this->guard->isStorageNotEmpty()) {
            return false;
        }

        return $this->authenticationRequest->matches($request);
    }
}