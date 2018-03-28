<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Event;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;

class UserFailureLogin
{
    /**
     * @var SecurityKey
     */
    private $securityKey;

    /**
     * @var Request
     */
    private $request;

    public function __construct(SecurityKey $securityKey, Request $request)
    {
        $this->securityKey = $securityKey;
        $this->request = $request;
    }

    public function securityKey(): SecurityKey
    {
        return $this->securityKey;
    }

    public function request(): Request
    {
        return $this->request;
    }
}