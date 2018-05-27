<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Service\Recaller;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Exception\AuthenticationException;
use StephBug\SecurityModel\Application\Values\Contract\RecallerIdentifier as BaseIdentifier;
use StephBug\SecurityModel\Application\Values\Identifier\RecallerIdentifier;
use StephBug\SecurityModel\Guard\Authentication\Token\RecallerToken;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Service\Recaller\Value\RecallerValue;
use StephBug\SecurityModel\User\UserSecurity;
use Symfony\Component\HttpFoundation\Response;

class SimpleRecallerService extends RecallerService
{
    public function processAutoLogin(RecallerValue $recaller, Request $request): Tokenable
    {
        if (!$this->encoder->compare($recaller->toArray(), $recaller->hash())) {
            throw new AuthenticationException('Invalid cookie hash');
        }

        $recallerIdentifier = RecallerIdentifier::fromString($recaller->token());

        $refreshedUser = $this->refreshUser(
            $this->recallerProvider->requireUserFromRecaller($recallerIdentifier),
            $request
        );

        return new RecallerToken($refreshedUser, $this->securityKey, $this->recallerKey);
    }

    public function onLoginSuccess(Request $request, Response $response, Tokenable $token): void
    {
        $this->refreshUser($token->getUser(), $request);
    }

    protected function refreshUser(UserSecurity $user, Request $request): UserSecurity
    {
        $this->cancelCookie($request);

        $token = $this->newRecallerIdentifier();

        $refreshedUser = $this->recallerProvider->refreshUserRecaller($user, $token);

        $this->queueCookie([$refreshedUser->getId()->identify(), $refreshedUser->getRecallerToken()]);

        return $refreshedUser;
    }

    protected function newRecallerIdentifier(): BaseIdentifier
    {
        return RecallerIdentifier::nextIdentity();
    }
}