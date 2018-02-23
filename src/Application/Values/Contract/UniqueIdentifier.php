<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Values\Contract;

use Ramsey\Uuid\UuidInterface;

interface UniqueIdentifier extends SecurityIdentifier
{
    public function getUniqueId(): UuidInterface;
}