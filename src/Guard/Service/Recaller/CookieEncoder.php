<?php

namespace StephBug\SecurityModel\Guard\Service\Recaller;

interface CookieEncoder
{
    public function encodeCookie(string $rawCookie): string;

    public function decodeCookie(string $cookie): string;

    public function compareCookieHash(array $values, string $hash): bool;

    public function generateCookieHash(array $values): string;
}