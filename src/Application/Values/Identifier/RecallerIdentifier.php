<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Values\Identifier;

use StephBug\SecurityModel\Application\Exception\Assert\Secure;
use StephBug\SecurityModel\Application\Values\Contract\RecallerIdentifier as BaseIdentifier;
use StephBug\SecurityModel\Application\Values\Contract\SecurityValue;

class RecallerIdentifier implements BaseIdentifier
{
    /**
     * @var string
     */
    private $recallerToken;

    private function __construct(string $recallerToken)
    {
        $this->recallerToken = $recallerToken;
    }

    public static function nextIdentity(): self
    {
        return new self(base64_encode(random_bytes(64)));
    }

    public static function fromString($recallerToken): self
    {
        Secure::string($recallerToken);
        Secure::notEmpty($recallerToken);

        return new self($recallerToken);
    }

    public function identify(): string
    {
        return $this->recallerToken;
    }

    public function sameValueAs(SecurityValue $aValue): bool
    {
        return $aValue instanceof $this && $this->recallerToken === $aValue->identify();
    }
}