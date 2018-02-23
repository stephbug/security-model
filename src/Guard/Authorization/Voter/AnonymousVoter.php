<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authorization\Voter;

use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Authentication\TrustResolver;

class AnonymousVoter extends Voter
{
    /**
     * @var TrustResolver
     */
    private $trustResolver;

    public function __construct(TrustResolver $trustResolver)
    {
        $this->trustResolver = $trustResolver;
    }

    protected function supports(string $attribute, object $subject): bool
    {
        return 'anonymous' === $attribute;
    }

    protected function voteOn(string $attribute, object $subject, Tokenable $token): bool
    {
        return $this->trustResolver->isAnonymous($token);
    }
}