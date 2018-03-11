<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Mock;

use Ramsey\Uuid\UuidInterface;
use StephBug\SecurityModel\Application\Values\Contract\SecurityValue;
use StephBug\SecurityModel\Application\Values\Contract\UniqueIdentifier;

class UserSecurityId implements UniqueIdentifier
{
    /**
     * @var UuidInterface
     */
    private $uid;

    /**
     * @var bool
     */
    private $sameValueAs;

    public function __construct(UuidInterface $uid, bool $sameValueAs)
    {
        $this->uid = $uid;
        $this->sameValueAs = $sameValueAs;
    }

    public function identify(): string
    {
        return $this->uid->toString();
    }

    public function sameValueAs(SecurityValue $aValue): bool
    {
        return $this->sameValueAs;
    }

    public function getUniqueId(): UuidInterface
    {
        return $this->uid;
    }
}