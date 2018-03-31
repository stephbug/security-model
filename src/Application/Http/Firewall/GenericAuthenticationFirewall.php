<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Firewall;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Exception\AuthenticationException;
use StephBug\SecurityModel\Application\Http\Entrypoint\Entrypoint;
use StephBug\SecurityModel\Application\Http\Request\AuthenticationRequest;
use StephBug\SecurityModel\Application\Http\Response\AuthenticationSuccess;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Contract\Guardable;
use StephBug\SecurityModel\Guard\Contract\SecurityEvents;
use StephBug\SecurityModel\Guard\Service\Recaller\Recallable;
use Symfony\Component\HttpFoundation\Response;

abstract class GenericAuthenticationFirewall extends AuthenticationFirewall
{
    /**
     * @var Guardable
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

    /**
     * @var null|Recallable
     */
    protected $recallerService;

    public function __construct(Guardable $guard,
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
            $token = $this->createToken($request);

            if (!$this->stateless) {
                $this->guard->dispatch(SecurityEvents::ATTEMPT_LOGIN_EVENT, [$token, $request]);
            }

            return $this->onSuccess($request, $this->guard->putAuthenticatedToken($token));

        } catch (AuthenticationException $exception) {
            if (!$this->stateless) {
                $this->guard->dispatch(SecurityEvents::FAILURE_LOGIN_EVENT, [$this->securityKey, $request]);
            }

            return $this->entrypoint->startAuthentication($request, $exception);
        }
    }

    abstract protected function createToken(Request $request): Tokenable;

    protected function onSuccess(Request $request, Tokenable $token): Response
    {
        if (!$this->stateless) {
            $this->guard->dispatch(SecurityEvents::LOGIN_EVENT, [$request, $token]);
        }

        $response = $this->authenticationSuccess->onAuthenticationSuccess($request, $token);

        if ($this->recallerService) {
            $this->recallerService->loginSuccess($request, $response, $token);
        }

        return $response;
    }

    protected function requireAuthentication(Request $request): bool
    {
        return $this->authenticationRequest->matches($request);
    }

    public function setRecaller(Recallable $recallerService): void
    {
        $this->recallerService = $recallerService;
    }
}