<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Values;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class InMemoryUserId extends UniqueId
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