<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Event;

use Illuminate\Http\Request;
use StephBug\SecurityModel\User\LocalUser;

class UserImpersonated
{
    /**
     * @var LocalUser
     */
    private $user;

    /**
     * @var Request
     */
    private $request;

    public function __construct(LocalUser $user, Request $request)
    {
        $this->user = $user;
        $this->request = $request;
    }

    public function target(): LocalUser
    {
        return $this->user;
    }

    public function request(): Request
    {
        return $this->request;
    }

}