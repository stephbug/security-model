<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Response;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

interface LogoutSuccess
{
    public function onLogoutSuccess(Request $request): Response;
}