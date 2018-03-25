<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Values\Identifier;

use Ramsey\Uuid\UuidInterface;
use StephBug\SecurityModel\Application\Exception\Assert\Secure;
use StephBug\SecurityModel\Application\Values\Contract\SecurityValue;
use StephBug\SecurityModel\Application\Values\Contract\UniqueIdentifier;

abstract class UniqueId implements UniqueIdentifier
{
    /**
     * @var UuidInterface
     */
    protected $uniqueId;

    /**
     * @var string
     */
    protected static $message = 'Unique identifier is not valid';

    protected static function validate($uniqueId): bool
    {
        Secure::string($uniqueId, self::$message);
        Secure::notEmpty($uniqueId, self::$message);
        Secure::uuid($uniqueId, self::$message);

        return true;
    }

    public function identify(): string
    {
        return $this->uniqueId->toString();
    }

    public function sameValueAs(SecurityValue $aValue): bool
    {
        return $aValue instanceof $this && $this->getUniqueId()->equals($aValue->getUniqueId());
    }

    public function getUniqueId(): UuidInterface
    {
        return $this->uniqueId;
    }
}