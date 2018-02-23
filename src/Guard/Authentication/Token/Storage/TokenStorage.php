<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication\Token\Storage;

use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;

interface TokenStorage
{
    public function getToken(): ?Tokenable;

    public function setToken(Tokenable $token = null): void;
}