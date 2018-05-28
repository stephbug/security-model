<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication;

use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;

class GenericTrustResolver implements TrustResolver
{
    /**
     * @var string
     */
    private $anonymousTokenClass;

    /**
     * @var string
     */
    private $rememberMeClass;

    public function __construct(string $anonymousTokenClass, string $rememberMeClass)
    {
        $this->anonymousTokenClass = $anonymousTokenClass;
        $this->rememberMeClass = $rememberMeClass;
    }

    public function isAnonymous(Tokenable $token = null): bool
    {
        return !$token
            ? false
            : $token instanceof $this->anonymousTokenClass;
    }

    public function isRememberMe(Tokenable $token = null): bool
    {
        return !$token
            ? false
            : $token instanceof $this->rememberMeClass;
    }

    public function isFullyAuthenticated(Tokenable $token = null): bool
    {
        return !$token
            ? false
            : !$this->isAnonymous($token) && !$this->isRememberMe($token);
    }
}