<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Service\Recaller;

use Illuminate\Contracts\Cookie\QueueingFactory;
use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Cookie;

class CookieHandler
{
    /**
     * @var QueueingFactory
     */
    private $cookie;

    /**
     * @var CookieSecurity
     */
    private $cookieSecurity;

    /**
     * @var string
     */
    private $cookieName = '_security_remember-me';

    public function __construct(QueueingFactory $cookie, CookieSecurity $cookieSecurity)
    {
        $this->cookie = $cookie;
        $this->cookieSecurity = $cookieSecurity;
    }

    public function cancel(Request $request): void
    {
        if ($this->getRecaller($request)) {
            $this->cookie->queue(
                $this->cookie->forget($this->getCookieName())
            );
        };
    }

    public function queue(array $values): void
    {
        $hash = $this->cookieSecurity->generateCookieHash($values);

        $value = $this->cookieSecurity->encodeCookie(
            implode(Recaller::DELIMITER, $values + [$hash])
        );

        $this->cookie->queue($this->createForeverCookie($value));
    }

    public function getRecaller(Request $request): ?Recaller
    {
        if (!$recaller = $request->cookie($this->getCookieName())) {
            return null;
        }

        $recaller = new Recaller($this->cookieSecurity->decodeCookie($recaller));
        if ($recaller->valid()) {
            return $recaller;
        }

        throw new AuthenticationException('Invalid recaller');
    }

    private function createForeverCookie(string $value): Cookie
    {
        return $this->cookie->forever($this->getCookieName(), $value);
    }

    public function getCookieName(): string
    {
        return $this->cookieName;
    }

    public function setCookieName(string $cookieName): void
    {
        $this->cookieName = $cookieName;
    }

    public function getCookieSecurity(): CookieSecurity
    {
        return $this->cookieSecurity;
    }
}