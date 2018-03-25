<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Firewall;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Exception\Assert\SecurityValueFailed;
use StephBug\SecurityModel\Application\Exception\AuthenticationException;
use StephBug\SecurityModel\Application\Http\Entrypoint\Entrypoint;
use StephBug\SecurityModel\Application\Http\Request\AuthenticationRequest;
use StephBug\SecurityModel\Application\Values\Contract\EmailAddress;
use StephBug\SecurityModel\Application\Values\Contract\SecurityIdentifier;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;
use StephBug\SecurityModel\Guard\Authentication\Token\IdentifierPasswordToken;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Guard;
use StephBug\SecurityModel\User\Exception\BadCredentials;
use StephBug\SecurityModel\User\UserSecurity;
use Symfony\Component\HttpFoundation\Response;

class HttpBasicAuthenticationFirewall extends AuthenticationFirewall
{
    /**
     * @var Guard
     */
    private $guard;

    /**
     * @var AuthenticationRequest
     */
    private $authenticationRequest;

    /**
     * @var Entrypoint
     */
    private $entrypoint;

    /**
     * @var SecurityKey
     */
    private $securityKey;

    public function __construct(Guard $guard,
                                AuthenticationRequest $authenticationRequest,
                                Entrypoint $entrypoint,
                                SecurityKey $securityKey)
    {
        $this->guard = $guard;
        $this->authenticationRequest = $authenticationRequest;
        $this->entrypoint = $entrypoint;
        $this->securityKey = $securityKey;
    }

    protected function processAuthentication(Request $request): ?Response
    {
        try{
         $token = $this->guard->authenticate($this->createToken($request));

         $this->guard->put($token);

         $this->guard->event()->dispatchLoginEvent($request, $token);

         return null;
        }catch (AuthenticationException $exception){
            $this->guard->forget();

            return $this->entrypoint->startAuthentication($request, $exception);
        }
    }

    protected function createToken(Request $request): IdentifierPasswordToken
    {
        [$identifier, $password] = $this->authenticationRequest->extract($request);

        if (!$identifier || !$password) {
            throw BadCredentials::invalid();
        }

        return new IdentifierPasswordToken($identifier, $password, $this->securityKey);
    }

    protected function requireAuthentication(Request $request): bool
    {
        try {
            [$identifier] = $this->authenticationRequest->extract($request);

            if (!$identifier) {
                return false;
            }

            return !$this->isAlreadyAuthenticated($identifier, $this->guard->storage()->getToken());
        } catch (SecurityValueFailed $exception) {
            return false;
        }
    }

    protected function isAlreadyAuthenticated(SecurityIdentifier $identifier, Tokenable $token = null): bool
    {
        if (!$identifier instanceof EmailAddress) {
            return false;
        }

        return $token instanceof IdentifierPasswordToken
            && $token->isAuthenticated()
            && $token->getUser() instanceof UserSecurity
            && $token->getUser()->getEmail()->sameValueAs($identifier);
    }
}