<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Request;

use Illuminate\Http\Request as IlluminateRequest;
use StephBug\SecurityModel\Application\Values\Contract\SecurityIdentifier;
use StephBug\SecurityModel\Application\Values\EmailAddress;
use StephBug\SecurityModel\Application\Values\NullIdentifier;
use Symfony\Component\HttpFoundation\Request;

class SwitchUserAuthenticationRequest implements SwitchUserMatcher
{
    const IDENTIFIER_PARAMETER = '_switch_user';
    const EXIT_USER = '_exit';

    public function extract(IlluminateRequest $request): SecurityIdentifier
    {
        if ($this->isExitUserRequest($request)) {
            return new NullIdentifier();
        }

        return EmailAddress::fromString($this->getIdentifierFromRequest($request));
    }

    public function matches(Request $request): bool
    {
        if ($this->isExitUserRequest($request)) {
            return true;
        }

        return $this->isImpersonateUserRequest($request);
    }

    public function isExitUserRequest(IlluminateRequest $request): bool
    {
        return $request->query->has(static::EXIT_USER);
    }

    public function isImpersonateUserRequest(IlluminateRequest $request): bool
    {
        return null !== $this->getIdentifierFromRequest($request);
    }

    public function getIdentifierParameter(): string
    {
        return static::IDENTIFIER_PARAMETER;
    }

    public function getIdentifierFromRequest(IlluminateRequest $request): ?string
    {
        return $request->get(static::IDENTIFIER_PARAMETER);
    }
}