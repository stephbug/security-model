<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Exception;

class AuthorizationException extends \RuntimeException implements SecurityException
{
}