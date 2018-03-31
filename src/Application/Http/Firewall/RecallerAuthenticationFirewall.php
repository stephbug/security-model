<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Firewall;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Exception\AuthenticationException;
use StephBug\SecurityModel\Guard\Contract\Guardable;
use StephBug\SecurityModel\Guard\Service\Recaller\Recallable;
use Symfony\Component\HttpFoundation\Response;

class RecallerAuthenticationFirewall extends AuthenticationFirewall
{
    /**
     * @var Guardable
     */
    private $guard;

    /**
     * @var Recallable
     */
    private $recallerService;

    public function __construct(Guardable $guard, Recallable $recallerService)
    {
        $this->guard = $guard;
        $this->recallerService = $recallerService;
    }

    protected function processAuthentication(Request $request): ?Response
    {
        if (!$token = $this->recallerService->autoLogin($request)) {
            return null;
        }

        try {
            $token = $this->guard->putAuthenticatedToken($token);

            $this->guard->events()->loginEvent($request, $token);

            return null;
        } catch (AuthenticationException $exception) {
            $this->recallerService->loginFail($request);

            throw $exception;
        }
    }

    protected function requireAuthentication(Request $request): bool
    {
        return $this->guard->isStorageEmpty();
    }
}