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

    public function __construct(string $anonymousToken)
    {
        $this->anonymousToken = $anonymousToken;
    }

    public function isAnonymous(Tokenable $token = null): bool
    {
        if (!$token) {
            return false;
        }

        return $token instanceof $this->anonymousToken;
    }

    public function isFullyAuthenticated(Tokenable $token = null): bool
    {
        if (!$token) {
            return false;
        }

        return !$this->isAnonymous($token);
    }
}