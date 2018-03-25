<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication\Providers;

use StephBug\SecurityModel\Application\Exception\UnsupportedProvider;
use StephBug\SecurityModel\Application\Values\Identifier\AnonymousIdentifier;
use StephBug\SecurityModel\Application\Values\Security\AnonymousKey;
use StephBug\SecurityModel\Guard\Authentication\Token\AnonymousToken;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;

class AnonymousAuthenticationProvider implements AuthenticationProvider
{
    /**
     * @var AnonymousKey
     */
    private $anonymousKey;

    public function __construct(AnonymousKey $anonymousKey)
    {
        $this->anonymousKey = $anonymousKey;
    }

    public function authenticate(Tokenable $token): Tokenable
    {
        if (!$this->supports($token)) {
            throw UnsupportedProvider::withSupport($token, $this);
        }

        return new AnonymousToken(new AnonymousIdentifier(), $this->anonymousKey);
    }

    public function supports(Tokenable $token): bool
    {
        return $token instanceof AnonymousToken && $token->getSecurityKey()->sameValueAs($this->anonymousKey);
    }
}