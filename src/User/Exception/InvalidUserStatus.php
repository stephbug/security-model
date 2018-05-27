<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\User\Exception;

use StephBug\SecurityModel\Application\Exception\AuthenticationException;

class InvalidUserStatus extends AuthenticationException
{
}