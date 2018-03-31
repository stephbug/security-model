<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Contract;

use StephBug\SecurityModel\Application\Http\Event\UserAttemptLogin;
use StephBug\SecurityModel\Application\Http\Event\UserFailureLogin;
use StephBug\SecurityModel\Application\Http\Event\UserLogin;
use StephBug\SecurityModel\Application\Http\Event\UserLogout;

interface SecurityEvents
{
    const LOGIN_EVENT = UserLogin::class;
    const FAILURE_LOGIN_EVENT = UserFailureLogin::class;
    const ATTEMPT_LOGIN_EVENT = UserAttemptLogin::class;
    const LOGOUT_EVENT = UserLogout::class;

    /**
     * @param string|object $event
     * @param array $payload
     * @return mixed
     */
    public function dispatch($event, array $payload = []);
}