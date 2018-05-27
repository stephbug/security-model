<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Service\Recaller\Encoder;

use StephBug\SecurityModel\Application\Values\Security\RecallerKey;
use StephBug\SecurityModel\Guard\Service\Recaller\Value\Recaller;

class CookieSecurity implements CookieEncoder
{
    /**
     * @var string
     */
    private $hash;

    public function __construct(RecallerKey $recallerKey)
    {
        $this->hash = $recallerKey->value();
    }

    public function encode(array $values): string
    {
        $values = array_merge($values, [$this->hash]);

        $hashed = hash_hmac('sha256', implode(Recaller::DELIMITER, $values), $this->hash);

        return base64_encode($hashed);
    }

    public function decode(string $cookie): string
    {
        return base64_decode($cookie);
    }

    public function compare(array $values, string $hash): bool
    {
        return hash_equals($hash, $this->encode($values));
    }
}