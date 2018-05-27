<?php

namespace StephBug\SecurityModel\Guard\Service\Recaller\Encoder;

interface CookieEncoder
{
    public function encode(array $values): string;

    public function decode(string $cookie): string;

    public function compare(array $values, string $hash): bool;
}