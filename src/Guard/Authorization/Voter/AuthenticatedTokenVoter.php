<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authorization\Voter;

use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Authentication\TrustResolver;

class AuthenticatedTokenVoter extends AccessVoter
{
    const FULLY = 'authenticated_fully';
    const REMEMBERED = 'authenticated_remembered';
    const ANONYMOUSLY = 'authenticated_anonymously';

    /**
     * @var TrustResolver
     */
    private $trustResolver;

    public function __construct(TrustResolver $trustResolver)
    {
        $this->trustResolver = $trustResolver;
    }

    public function vote(Tokenable $token, $subject = null, array $attributes): int
    {
        $vote = $this->abstain();

        foreach ($attributes as $attribute) {
            if ($this->noMatch($attribute)) {
                continue;
            }

            return $this->grant();
        }

        return $vote;
    }

    private function noMatch(string $attribute): bool
    {
        return null === $attribute ||
            (
                $this->isAuthenticatedFully() !== $attribute
                && $this->isAuthenticatedRemembered() !== $attribute
                && $this->isAuthenticatedAnonymously() !== $attribute
            );
    }

    public function isAuthenticatedFully(): string
    {
        return static::FULLY;
    }

    public function isAuthenticatedRemembered(): string
    {
        return static::REMEMBERED;
    }

    public function isAuthenticatedAnonymously(): string
    {
        return static::ANONYMOUSLY;
    }

}