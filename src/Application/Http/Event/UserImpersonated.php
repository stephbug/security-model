<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Event;

use Illuminate\Http\Request;
use StephBug\SecurityModel\User\UserSecurity;

class UserImpersonated
{
    /**
     * @var UserSecurity
     */
    private $user;

    /**
     * @var Request
     */
    private $request;

    public function __construct(UserSecurity $user, Request $request)
    {
        $this->user = $user;
        $this->request = $request;
    }

    public function target(): UserSecurity
    {
        return $this->user;
    }

    public function request(): Request
    {
        return $this->request;
    }

}