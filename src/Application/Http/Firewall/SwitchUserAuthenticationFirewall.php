<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Firewall;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Exception\CredentialsNotFound;
use StephBug\SecurityModel\Application\Exception\InvalidArgument;
use StephBug\SecurityModel\Application\Http\Event\UserImpersonated;
use StephBug\SecurityModel\Application\Http\Request\SwitchUserMatcher;
use StephBug\SecurityModel\Application\Values\Contract\SecurityIdentifier;
use StephBug\SecurityModel\Application\Values\SecurityKey;
use StephBug\SecurityModel\Application\Values\SwitchUserRole;
use StephBug\SecurityModel\Guard\Authentication\Token\IdentifierPasswordToken;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Authorization\Grantable;
use StephBug\SecurityModel\Guard\Guard;
use StephBug\SecurityModel\Role\Exception\AuthorizationDenied;
use StephBug\SecurityModel\User\LocalUser;
use StephBug\SecurityModel\User\UserProvider;
use Symfony\Component\HttpFoundation\Response;

class SwitchUserAuthenticationFirewall extends AuthenticationFirewall
{
    /**
     * @var Guard
     */
    private $guard;

    /**
     * @var Grantable
     */
    private $authorizer;

    /**
     * @var SwitchUserMatcher
     */
    private $switchUserMatcher;

    /**
     * @var UserProvider
     */
    private $userProvider;

    /**
     * @var SecurityKey
     */
    private $securityKey;

    /**
     * @var bool
     */
    private $stateless;

    public function __construct(Guard $guard,
                                Grantable $authorizer,
                                SwitchUserMatcher $switchUserMatcher,
                                UserProvider $userProvider,
                                SecurityKey $securityKey,
                                bool $stateless)
    {
        $this->guard = $guard;
        $this->authorizer = $authorizer;
        $this->switchUserMatcher = $switchUserMatcher;
        $this->userProvider = $userProvider;
        $this->securityKey = $securityKey;
        $this->stateless = $stateless;
    }

    protected function processAuthentication(Request $request): ?Response
    {
        $identifier = $this->switchUserMatcher->extract($request);

        $token = null;

        if ($this->switchUserMatcher->isExitUserRequest($request)) {
            $token = $this->attemptExitUser($request);
        } elseif ($this->switchUserMatcher->isImpersonateUserRequest($request)) {
            $token = $this->attemptImpersonateUser($identifier, $request);
        }

        if ($token) {
            $this->guard->put($token);
        }

        return $this->createRedirectResponse($request);
    }

    protected function attemptImpersonateUser(SecurityIdentifier $identifier, Request $request): ?Tokenable
    {
        $token = $this->guard->requireToken();

        if ($current = $this->hasAlreadySwitched($identifier, $token)) {
            return $current;
        }

        $this->requireGrantedAllowedToSwitch($token);

        return $this->createImpersonatedToken($identifier, $request);
    }

    protected function attemptExitUser(Request $request): Tokenable
    {
        if (!$tokenSourced = $this->getOriginalToken($this->guard->requireToken())) {
            throw CredentialsNotFound::reason('Original token from impersonated user not found');
        }

        $this->requireGrantedAllowedToSwitch($tokenSourced);

        $user = $this->userProvider->refreshUser($tokenSourced->getUser());
        $tokenSourced->setUser($user);

        $this->guard->event()->dispatchEvent(
            new UserImpersonated($user, $request)
        );

        return $tokenSourced;
    }

    protected function hasAlreadySwitched(SecurityIdentifier $identifier, Tokenable $current): ?Tokenable
    {
        if ($this->getOriginalToken($current)) {
            if ($current->getIdentifier()->sameValueAs($identifier)) {
                return $current;
            }

            throw InvalidArgument::reason('Already impersonate user');
        }

        return null;
    }

    protected function createImpersonatedToken(SecurityIdentifier $identifier, Request $request): Tokenable
    {
        $user = $this->userProvider->requireByIdentifier($identifier);

        if (!$user instanceof LocalUser) {
            throw InvalidArgument::reason(
                sprintf('Impersonate user only allowed for user whom implement %', LocalUser::class)
            );
        }

        $roles = $user->getRoles();
        $roles->push(new SwitchUserRole('ROLE_PREVIOUS_ADMIN', $this->guard->requireToken()));

        $token = new IdentifierPasswordToken($user, $user->getPassword(), $this->securityKey, $roles->toArray());

        $this->guard->event()->dispatchEvent(
            new UserImpersonated($user, $request)
        );

        return $token;
    }

    protected function getOriginalToken(Tokenable $token): ?Tokenable
    {
        foreach ($token->getRoles() as $role) {
            if ($role instanceof SwitchUserRole) {
                return $role->source();
            }
        }

        return null;
    }

    protected function createRedirectResponse(Request $request): ?Response
    {
        if ($this->stateless) {
            return null;
        }

        $switchParameter = $this->switchUserMatcher->getIdentifierParameter();
        $exitParameter = $this->switchUserMatcher->getExitParameter();

        if ($request->query->has($switchParameter)) {
            $request->query->remove($switchParameter);
        }

        if ($request->query->has($exitParameter)) {
            $request->query->remove($exitParameter);
        }

        $request->server->set('QUERY_STRING', http_build_query($request->query->all()));

        return new RedirectResponse($request->getUri(), 302);
    }

    protected function requireGrantedAllowedToSwitch(Tokenable $token): void
    {
        if (!$this->authorizer->isGranted($token, ['ROLE_ALLOWED_TO_SWITCH'])) {
            throw AuthorizationDenied::reason('Insufficient authorization to impersonate user');
        }
    }

    protected function requireAuthentication(Request $request): bool
    {
        return $this->switchUserMatcher->matches($request)
            && $this->authorizer->isGranted($this->guard->requireToken(), ['ROLE_USER']);
    }
}