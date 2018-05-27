<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Service\Recaller\Encoder;

class FreeCookieSecurity implements CookieEncoder
{
    public function encode(array $values): string
    {
        return implode('', $values);
    }

    public function decode(string $cookie): string
    {
        return $cookie;
    }

    public function compare(array $values, string $hash): bool
    {
        return true;
    }
}