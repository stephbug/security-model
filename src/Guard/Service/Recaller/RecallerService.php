<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Service\Recaller;

use Illuminate\Contracts\Cookie\QueueingFactory;
use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Exception\AuthenticationException;
use StephBug\SecurityModel\Application\Exception\CookieTheft;
use StephBug\SecurityModel\Application\Values\Security\RecallerKey;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Service\Logout\Logout;
use StephBug\SecurityModel\Guard\Service\Recaller\Encoder\CookieEncoder;
use StephBug\SecurityModel\Guard\Service\Recaller\Providers\RecallerProvider;
use StephBug\SecurityModel\Guard\Service\Recaller\Value\Recaller;
use StephBug\SecurityModel\Guard\Service\Recaller\Value\RecallerValue;
use StephBug\SecurityModel\User\UserSecurity;
use Symfony\Component\HttpFoundation\Response;

abstract class RecallerService implements Recallable, Logout
{
    /**
     * @var QueueingFactory
     */
    protected $cookie;

    /**
     * @var CookieEncoder
     */
    protected $encoder;

    /**
     * @var RecallerProvider
     */
    protected $recallerProvider;

    /**
     * @var RecallerKey
     */
    protected $recallerKey;

    /**
     * @var SecurityKey
     */
    protected $securityKey;

    public function __construct(QueueingFactory $cookie,
                                CookieEncoder $encoder,
                                RecallerProvider $recallerProvider,
                                RecallerKey $recallerKey,
                                SecurityKey $securityKey)
    {
        $this->cookie = $cookie;
        $this->encoder = $encoder;
        $this->recallerProvider = $recallerProvider;
        $this->recallerKey = $recallerKey;
        $this->securityKey = $securityKey;
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

    protected function cancelCookie(Request $request): void
    {
        $this->cookie->queue(
            $this->cookie->forget($this->getCookieName())
        );
    }

    protected function queueCookie(array $values): void
    {
        $value = $this->encoder->encode($values);

        $this->cookie->queue(
            $this->cookie->forever($this->getCookieName(), $value)
        );
    }

    protected function getRecaller(Request $request): ?RecallerValue
    {
        if (!$recaller = $request->cookie($this->getCookieName())) {
            return null;
        }

        $recaller = new Recaller($this->encoder->decode($recaller));

        if ($recaller->valid()) {
            return $recaller;
        }

        throw new AuthenticationException('Invalid recaller');
    }

    protected function getCookieName(): string
    {
        // should be overridden in implementation when multiples cookies
        // are generated for the same firewall
        return '_security_remember-me_' . $this->securityKey->value();
    }

    abstract protected function processAutoLogin(RecallerValue $recaller, Request $request): Tokenable;

    abstract protected function onLoginSuccess(Request $request, Response $response, Tokenable $token): void;
}