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

    protected function supports(string $attribute, $subject = null): bool
    {
        return 'anonymous' === $attribute;
    }

    protected function voteOn(string $attribute, Tokenable $token, $subject = null): bool
    {
        return $this->trustResolver->isAnonymous($token);
    }
}