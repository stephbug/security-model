<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Values\User;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use StephBug\SecurityModel\Application\Values\Identifier\UniqueId;

final class InMemoryUserId extends UniqueId
{
    protected function __construct(UuidInterface $uniqueId)
    {
        $this->uniqueId = $uniqueId;
    }

    public static function nextIdentity(): UniqueId
    {
        return new self(Uuid::uuid4());
    }
}