<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Event;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;

class UserAttemptLogin
{
    /**
     * @var Tokenable
     */
    private $token;

    /**
     * @var Request
     */
    private $request;

    public function __construct(Tokenable $token, Request $request)
    {
        $this->token = $token;
        $this->request = $request;
    }

    public function token(): Tokenable
    {
        return $this->token;
    }

    public function request(): Request
    {
        return $this->request;
    }
}