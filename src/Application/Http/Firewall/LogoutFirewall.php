<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Firewall;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Http\Request\AuthenticationRequest;
use StephBug\SecurityModel\Application\Http\Response\LogoutSuccess;
use StephBug\SecurityModel\Guard\Authentication\TrustResolver;
use StephBug\SecurityModel\Guard\Contract\Guardable;
use StephBug\SecurityModel\Guard\Service\Logout\Logout;
use Symfony\Component\HttpFoundation\Response;

class LogoutFirewall extends AuthenticationFirewall
{
    /**
     * @var Guardable
     */
    private $guard;

    /**
     * @var array
     */
    private $logoutHandlers = [];

    /**
     * @var AuthenticationRequest
     */
    private $authenticationRequest;

    /**
     * @var TrustResolver
     */
    private $trustResolver;

    /**
     * @var LogoutSuccess
     */
    private $response;

    public function __construct(Guardable $guard,
                                AuthenticationRequest $authenticationRequest,
                                TrustResolver $trustResolver,
                                LogoutSuccess $response)
    {
        $this->guard = $guard;
        $this->authenticationRequest = $authenticationRequest;
        $this->trustResolver = $trustResolver;
        $this->response = $response;
    }

    protected function processAuthentication(Request $request): ?Response
    {
        $response = $this->response->onLogoutSuccess($request);

        $token = $this->guard->requireToken();

        foreach ($this->logoutHandlers as $handler) {
            $handler->logout($request, $response, $token);
        }

        $this->guard->clearStorage();

        $this->guard->events()->logoutEvent($token);

        return $response;
    }

    public function addHandler(Logout $logoutHandler): LogoutFirewall
    {
        $this->logoutHandlers[] = $logoutHandler;

        return $this;
    }

    protected function requireAuthentication(Request $request): bool
    {
        if ($this->guard->isStorageEmpty()) {
            return false;
        }

        if ($this->trustResolver->isAnonymous($this->guard->getToken())) {
            return false;
        }

        return $this->authenticationRequest->matches($request);
    }
}
