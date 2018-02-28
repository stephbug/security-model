<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication;

use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;

class GenericTrustResolver implements TrustResolver
{
    /**
     * @var string
     */
    private $anonymousToken;

    /**
     * @var string
     */
    private $rememberMe;

    public function __construct(string $anonymousToken, string $rememberMe)
    {
        $this->anonymousToken = $anonymousToken;
        $this->rememberMe = $rememberMe;
    }

    public function isAnonymous(Tokenable $token = null): bool
    {
        if (!$token) {
            return false;
        }

        return $token instanceof $this->anonymousToken;
    }

    public function isRememberMe(Tokenable $token = null): bool
    {
        if (!$token) {
            return false;
        }

        return $token instanceof $this->rememberMe;
    }

    public function isFullyAuthenticated(Tokenable $token = null): bool
    {
        if (!$token) {
            return false;
        }

        return !$this->isAnonymous($token) && !$this->isRememberMe($token);
    }
}