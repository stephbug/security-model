<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Event;

use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;

class UserLogout
{
    /**
     * @var Tokenable
     */
    private $token;

    public function __construct(Tokenable $token)
    {
        $this->token = $token;
    }

    public function token(): Tokenable
    {
        return $this->token;
    }
}