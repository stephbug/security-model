<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Firewall;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Exception\AuthenticationException;
use StephBug\SecurityModel\Application\Http\Request\AuthenticationRequest;
use StephBug\SecurityModel\Application\Http\Response\AuthenticationFailure;
use StephBug\SecurityModel\Application\Http\Response\AuthenticationSuccess;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;
use StephBug\SecurityModel\Guard\Authentication\SimplePreAuthenticator;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Guard;
use Symfony\Component\HttpFoundation\Response;

class SimplePreAuthenticationFirewall extends AuthenticationFirewall
{
    /**
     * @var Guard
     */
    private $guard;

    /**
     * @var SimplePreAuthenticator
     */
    private $authenticator;

    /**
     * @var AuthenticationRequest
     */
    private $authenticationRequest;

    /**
     * @var string
     */
    private $securityKey;

    /**
     * @var bool
     */
    private $stateless;

    public function __construct(Guard $guard,
                                SimplePreAuthenticator $authenticator,
                                AuthenticationRequest $authenticationRequest,
                                SecurityKey $securityKey,
                                bool $stateless)
    {
        $this->guard = $guard;
        $this->authenticator = $authenticator;
        $this->authenticationRequest = $authenticationRequest;
        $this->securityKey = $securityKey;
        $this->stateless = $stateless;
    }

    protected function processAuthentication(Request $request): ?Response
    {
        try {
            $token = $this->authenticator->createToken($request, $this->securityKey);

            if (!$this->stateless) {
                $this->guard->event()->dispatchAttemptLoginEvent($token, $request);
            }

            return $this->onSuccess($request, $this->guard->authenticate($token));
        } catch (AuthenticationException $exception) {
            return $this->onFailure($request, $exception);
        }
    }

    protected function requireAuthentication(Request $request): bool
    {
        return $this->guard->isStorageEmpty() && $this->authenticationRequest->matches($request);
    }

    private function onSuccess(Request $request, Tokenable $token): ?Response
    {
        $this->guard->put($token);

        if (!$this->stateless) {
            $this->guard->event()->dispatchLoginEvent($request, $token);
        }

        $response = null;

        if ($this->authenticator instanceof AuthenticationSuccess) {
            $response = $this->authenticator->onAuthenticationSuccess($request, $token);
        }

        return $response;
    }

    private function onFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if (!$this->stateless) {
            $this->guard->event()->dispatchFailureLoginEvent($this->securityKey, $request);
        }

        if ($this->authenticator instanceof AuthenticationFailure) {
            return $this->authenticator->onAuthenticationFailure($request, $exception);
        }

        return null;
    }
}