<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Request;

use Illuminate\Http\Request as IlluminateRequest;

interface SwitchUserMatcher extends AuthenticationRequest
{
    public function isExitUserRequest(IlluminateRequest $request): bool;

    public function isImpersonateUserRequest(IlluminateRequest $request): bool;

    public function getIdentifierParameter(): string;
}