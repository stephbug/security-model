<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Providers;

use Illuminate\Support\ServiceProvider;
use StephBug\SecurityModel\Guard\Authentication\Authenticatable;
use StephBug\SecurityModel\Guard\Authentication\AuthenticationManager;
use StephBug\SecurityModel\Guard\Authentication\AuthenticationProviderCollection;
use StephBug\SecurityModel\Guard\Authentication\GenericTrustResolver;
use StephBug\SecurityModel\Guard\Authentication\Token\AnonymousToken;
use StephBug\SecurityModel\Guard\Authentication\Token\Storage\TokenStorage;
use StephBug\SecurityModel\Guard\Authentication\Token\Storage\TokenStorageAware;
use StephBug\SecurityModel\Guard\Authentication\TrustResolver;

class SecurityServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes(
            [$this->getConfigPath() => config_path('security.php')],
            'config'
        );
    }

    public function register(): void
    {
        $this->mergeConfig();

        $this->registerAuthenticationServices();
    }

    protected function registerAuthenticationServices(): void
    {
        $this->app->singleton(TokenStorage::class, TokenStorageAware::class);

        $this->app->bind(TrustResolver::class, function () {
            return new GenericTrustResolver(AnonymousToken::class);
        });

        $this->app->singleton(AuthenticationProviderCollection::class);

        $this->app->bindIf(Authenticatable::class, AuthenticationManager::class);
    }

    protected function mergeConfig(): void
    {
        $this->mergeConfigFrom($this->getConfigPath(), 'security');
    }

    protected function getConfigPath(): string
    {
        return __DIR__ . '/../../../../config/security.php';
    }

    public function provides(): array
    {
        return [
            TokenStorage::class, TrustResolver::class, AuthenticationProviderCollection::class, Authenticatable::class
        ];
    }
}