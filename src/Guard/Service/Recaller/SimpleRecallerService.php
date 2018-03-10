<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Service\Recaller;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Exception\CookieTheft;
use StephBug\SecurityModel\Guard\Authentication\Token\RecallerToken;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\User\UserSecurity;
use Symfony\Component\HttpFoundation\Response;

class SimpleRecallerService extends RecallerService
{
    public function processAutoLogin(Recaller $recaller, Request $request): Tokenable
    {
        $this->checkHash([$recaller->id(), $recaller->token()], $recaller->hash());

        $user = $this->requireUserFromRecaller($recaller->id(), $recaller->token());

        $this->handler->cancel($request);

        $user = $this->refreshRecallerToken($user, $tokenString = $this->createRecallerTokenString());

        $this->handler->queue([$user->getId()->identify(), $tokenString]);

        return new RecallerToken($user, $this->firewallKey, $this->recallerKey);
    }

    public function onLoginSuccess(Request $request, Response $response, Tokenable $token): void
    {
        $recallerTokenString = $this->createRecallerTokenString();

        $user = $this->refreshRecallerToken($token->getUser(), $recallerTokenString);

        $this->handler->queue([$user->getId()->identify(), $recallerTokenString]);
    }

    private function requireUserFromRecaller(string $id, string $token): UserSecurity
    {
        $user = $this->provider->requireByRecallerToken($token);

        if (!$user->getId()->identify() !== $id) {
            throw new CookieTheft('Wrong user identifier for recaller token');
        }

        return $user;
    }

    private function refreshRecallerToken(UserSecurity $user, string $newRecallerToken): UserSecurity
    {
        return $this->provider->refreshRecaller($user, $newRecallerToken);
    }

    private function createRecallerTokenString(): string
    {
        return base64_encode(random_bytes(64));
    }
}