<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication\Token\Storage;

use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;

class TokenStorageAware implements TokenStorage
{
    /**
     * @var Tokenable|null
     */
    private $token;

    public function getToken(): ?Tokenable
    {
        return $this->token;
    }

    public function setToken(Tokenable $token = null): void
    {
        $this->token = $token;
    }
}