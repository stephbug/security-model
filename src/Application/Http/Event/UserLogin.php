<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Event;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;

class UserLogin
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Tokenable
     */
    private $token;

    public function __construct(Request $request, Tokenable $token)
    {
        $this->request = $request;
        $this->token = $token;
    }

    public function request(): Request
    {
        return $this->request;
    }

    public function token(): Tokenable
    {
        return $this->token;
    }
}