<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Service\Recaller;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Exception\AuthenticationException;
use StephBug\SecurityModel\Application\Exception\CookieTheft;
use StephBug\SecurityModel\Application\Values\FirewallKey;
use StephBug\SecurityModel\Application\Values\RecallerKey;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Service\Logout\Logout;
use StephBug\SecurityModel\User\UserSecurity;
use Symfony\Component\HttpFoundation\Response;

abstract class RecallerService implements Recallable, Logout
{
    /**
     * @var CookieHandler
     */
    protected $handler;

    /**
     * @var UserRecallerProvider
     */
    protected $provider;

    /**
     * @var RecallerKey
     */
    protected $recallerKey;

    /**
     * @var FirewallKey
     */
    protected $firewallKey;

    public function __construct(CookieHandler $handler,
                                UserRecallerProvider $provider,
                                RecallerKey $recallerKey,
                                FirewallKey $firewallKey)
    {
        $this->handler = $handler;
        $this->provider = $provider;
        $this->recallerKey = $recallerKey;
        $this->firewallKey = $firewallKey;
    }

    public function autoLogin(Request $request): ?Tokenable
    {
        if ($recaller = $this->handler->getRecaller($request)) {
            try {
                return $this->processAutoLogin($recaller, $request);
            } catch (CookieTheft $cookieTheft) {
                $this->handler->cancel($request);

                throw new CookieTheft('Wrong cookie');
            } catch (AuthenticationException $exception) {
                $this->handler->cancel($request);

                return null;
            }
        }

        return null;
    }

    public function loginFail(Request $request): void
    {
        $this->handler->cancel($request);
    }

    public function loginSuccess(Request $request, Response $response, Tokenable $token): void
    {
        $this->handler->cancel($request);

        if (!$token->getUser() instanceof UserSecurity) {
            return;
        }

        if (!$this->isRecallerRequested($request)) {
            return;
        }

        $this->onLoginSuccess($request, $response, $token);
    }

    public function logout(Request $request, Response $response, Tokenable $token): void
    {
        $this->handler->cancel($request);
    }

    protected function isRecallerRequested(Request $request): bool
    {
        if (!$request->isMethod('post')) {
            return false;
        }

        return in_array(
            $request->input('remember-me'),
            ['true', 'yes', '1', 'on', 'remember-me'],
            true
        );
    }

    protected function hashValues(array $values): string
    {
        return $this->handler->getCookieSecurity()->generateCookieHash($values);
    }

    protected function checkHash(array $values, string $hash): bool
    {
        if ($this->handler->getCookieSecurity()->compareCookieHash($values, $hash)) {
            return true;
        }

        throw new AuthenticationException('Invalid cookie hash');
    }

    abstract public function processAutoLogin(Recaller $recaller, Request $request): Tokenable;

    abstract public function onLoginSuccess(Request $request, Response $response, Tokenable $token): void;
}