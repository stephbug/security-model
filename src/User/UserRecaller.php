<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\User;

use StephBug\SecurityModel\Application\Values\Contract\SecurityIdentifier;

interface UserRecaller
{
    public function getRecallerToken(): ?SecurityIdentifier;
}