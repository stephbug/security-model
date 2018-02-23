<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authorization\Voter;

abstract class AccessVoter implements Votable
{
    protected function abstain(): int
    {
        return Votable::ACCESS_ABSTAIN;
    }

    protected function grant(): int
    {
        return Votable::ACCESS_GRANTED;
    }

    protected function deny(): int
    {
        return Votable::ACCESS_DENIED;
    }
}