<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Providers;

use Illuminate\Support\ServiceProvider;
use StephBug\SecurityModel\Guard\Authentication\Authenticatable;
use StephBug\SecurityModel\Guard\Authentication\AuthenticationManager;
use StephBug\SecurityModel\Guard\Authentication\AuthenticationProviders;
use StephBug\SecurityModel\Guard\Authentication\GenericTrustResolver;
use StephBug\SecurityModel\Guard\Authentication\Token\AnonymousToken;
use StephBug\SecurityModel\Guard\Authentication\Token\RecallerToken;
use StephBug\SecurityModel\Guard\Authentication\Token\Storage\TokenStorage;
use StephBug\SecurityModel\Guard\Authentication\Token\Storage\TokenStorageAware;
use StephBug\SecurityModel\Guard\Authentication\TrustResolver;
use StephBug\SecurityModel\Guard\Contract\Guardable;
use StephBug\SecurityModel\Guard\Contract\SecurityEvents;
use StephBug\SecurityModel\Guard\Guard;
use StephBug\SecurityModel\Guard\SecurityEvent;

class AuthenticationServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;

    /**
     * @var array
     */
    public $bindings = [
        Authenticatable::class => AuthenticationManager::class,
        SecurityEvents::class => SecurityEvent::class,
        Guardable::class => Guard::class
    ];

    /**
     * @var array
     */
    public $singletons = [
        TokenStorage::class => TokenStorageAware::class,
        AuthenticationProviders::class
    ];

    public function register(): void
    {
        $this->mergeConfig();

        $this->registerTrustResolver();
    }

    protected function registerTrustResolver(): void
    {
        $this->app->bind(TrustResolver::class, function () {
            return new GenericTrustResolver(AnonymousToken::class, RecallerToken::class);
        });
    }

    public function provides(): array
    {
        return [
            TokenStorage::class,
            TrustResolver::class,
            AuthenticationProviders::class,
            Authenticatable::class,
            SecurityEvents::class,
            Guardable::class
        ];
    }
}