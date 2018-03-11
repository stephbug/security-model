<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Service\Recaller;

use Illuminate\Contracts\Cookie\QueueingFactory;
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
     * @var QueueingFactory
     */
    private $cookie;

    /**
     * @var CookieEncoder
     */
    protected $cookieEncoder;

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

    public function __construct(QueueingFactory $cookie,
                                CookieEncoder $cookieEncoder,
                                UserRecallerProvider $provider,
                                RecallerKey $recallerKey,
                                FirewallKey $firewallKey)
    {
        $this->cookie = $cookie;
        $this->cookieEncoder = $cookieEncoder;
        $this->provider = $provider;
        $this->recallerKey = $recallerKey;
        $this->firewallKey = $firewallKey;
    }

    public function autoLogin(Request $request): ?Tokenable
    {
        try {
            if (!$recaller = $this->getRecaller($request)) {
                return null;
            }

            return $this->processAutoLogin($recaller, $request);
        } catch (CookieTheft $cookieTheft) {
            $this->cancelCookie($request);

            throw new CookieTheft('Wrong cookie');
        } catch (AuthenticationException $exception) {
            $this->cancelCookie($request);

            return null;
        }
    }

    public function loginFail(Request $request): void
    {
        $this->cancelCookie($request);
    }

    public function loginSuccess(Request $request, Response $response, Tokenable $token): void
    {
        $this->cancelCookie($request);

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
        $this->cancelCookie($request);
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

    public function getCookieName(): string
    {
        return '_security_remember-me_' . $this->firewallKey->value();
    }

    protected function cancelCookie(Request $request): void
    {
        $this->cookie->queue(
            $this->cookie->forget($this->getCookieName())
        );
    }

    protected function queueCookie(array $values): void
    {
        array_merge($values, [$this->cookieEncoder->generateCookieHash($values)]);

        $value = $this->cookieEncoder->encodeCookie(implode(Recaller::DELIMITER, $values));

        $this->cookie->queue($this->cookie->forever($this->getCookieName(), $value));
    }

    protected function getRecaller(Request $request): ?RecallerValue
    {
        if (!$recaller = $request->cookie($this->getCookieName())) {
            return null;
        }

        $recaller = $this->cookieEncoder->decodeCookie($recaller);

        $recaller = new Recaller($recaller);
        if ($recaller->valid()) {
            return $recaller;
        }

        throw new AuthenticationException('Invalid recaller');
    }

    abstract public function processAutoLogin(RecallerValue $recaller, Request $request): Tokenable;

    abstract public function onLoginSuccess(Request $request, Response $response, Tokenable $token): void;
}