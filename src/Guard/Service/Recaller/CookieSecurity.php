<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Service\Recaller;

use StephBug\SecurityModel\Application\Values\RecallerKey;

class CookieSecurity
{
    /**
     * @var string
     */
    private $hash;

    public function __construct(RecallerKey $recallerKey)
    {
        $this->hash = $recallerKey->value();
    }

    public function encodeCookie(string $rawCookie): string
    {
        return base64_encode($rawCookie);
    }

    public function decodeCookie(string $cookie): string
    {
        return base64_decode($cookie);
    }

    public function compareCookieHash(array $values, string $hash): bool
    {
        return hash_equals($this->generateCookieHash($values), $hash);
    }

    public function generateCookieHash(array $values): string
    {
        return hash_hmac('sha256', implode('', $values), $this->hash);
    }
}