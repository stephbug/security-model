<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Firewall;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Exception\AuthenticationException;
use StephBug\SecurityModel\Application\Values\Identifier\AnonymousIdentifier;
use StephBug\SecurityModel\Application\Values\Security\AnonymousKey;
use StephBug\SecurityModel\Guard\Authentication\Token\AnonymousToken;
use StephBug\SecurityModel\Guard\Guard;
use Symfony\Component\HttpFoundation\Response;

class AnonymousAuthenticationFirewall extends AuthenticationFirewall
{
    /**
     * @var Guard
     */
    private $guard;

    /**
     * @var AnonymousKey
     */
    private $anonymousKey;

    public function __construct(Guard $guard, AnonymousKey $anonymousKey)
    {
        $this->guard = $guard;
        $this->anonymousKey = $anonymousKey;
    }

    protected function processAuthentication(Request $request): ?Response
    {
        $token = new AnonymousToken(new AnonymousIdentifier(), $this->anonymousKey);

        try{
            $this->guard->put(
                $this->guard->authenticate($token)
            );
        }catch (AuthenticationException $exception){
        }

        return null;
    }

    protected function requireAuthentication(Request $request): bool
    {
        return $this->guard->isStorageEmpty();
    }
}