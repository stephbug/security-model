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

    public function vote(Tokenable $token, array $attributes, $subject = null): int
    {
        $vote = $this->abstain();

        foreach ($attributes as $attribute) {
            if ($this->noMatch($attribute)) {
                continue;
            }

            $vote = $this->deny();

            if ($this->fully() === $attribute && $this->trustResolver->isFullyAuthenticated($token)) {
                return $this->grant();
            }

            if ($this->remembered() === $attribute
                && ($this->trustResolver->isRememberMe($token)
                    || $this->trustResolver->isFullyAuthenticated($token))) {
                return $this->grant();
            }

            if ($this->anonymously() === $attribute
                && ($this->trustResolver->isAnonymous($token)
                    || $this->trustResolver->isRememberMe($token))
                || $this->trustResolver->isFullyAuthenticated($token)) {
                return $this->grant();
            }

            return $vote;
        }

        return $vote;
    }

    private function noMatch(string $attribute = null): bool
    {
        return null === $attribute ||
            (
                $this->fully() !== $attribute
                && $this->remembered() !== $attribute
                && $this->anonymously() !== $attribute
            );
    }

    public function fully(): string
    {
        return static::FULLY;
    }

    public function remembered(): string
    {
        return static::REMEMBERED;
    }

    public function anonymously(): string
    {
        return static::ANONYMOUSLY;
    }

}