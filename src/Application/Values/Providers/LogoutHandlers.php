<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Values\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Service\Logout\Logout;
use Symfony\Component\HttpFoundation\Response;

class LogoutHandlers
{
    /**
     * @var Collection
     */
    private $logoutHandlers;

    public function __construct(array $logoutHandlers = null)
    {
        $this->logoutHandlers = new Collection($logoutHandlers ?? []);
    }

    public function add(Logout $handler): self
    {
        $this->logoutHandlers->push($handler);

        return $this;
    }

    public function processLogout(Request $request, Response $response, Tokenable $token): void
    {
        $this->logoutHandlers->each(function (Logout $handler) use ($request, $response, $token) {
            $handler->logout($request, $response, $token);
        });
    }
}