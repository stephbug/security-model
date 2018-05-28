<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Providers;

use Illuminate\Contracts\Foundation\Application;
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
use StephBug\SecurityModel\Guard\Authorization\Expression\SecurityExpressionLanguage;
use StephBug\SecurityModel\Guard\Authorization\Expression\SecurityExpressionVoter;
use StephBug\SecurityModel\Guard\Authorization\Grantable;
use StephBug\SecurityModel\Guard\Authorization\Hierarchy\RoleHierarchy;
use StephBug\SecurityModel\Guard\Authorization\Strategy\AuthorizationStrategy;
use StephBug\SecurityModel\Guard\Contract\Guardable;
use StephBug\SecurityModel\Guard\Contract\SecurityEvents;
use StephBug\SecurityModel\Guard\Guard;
use StephBug\SecurityModel\Guard\SecurityEvent;

class SecurityServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;

    public function boot(): void
    {
        $this->publishes(
            [$this->getConfigPath() => config_path('security.php')],
            'config'
        );

        $this->registerAuthorizationServices();
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
            return new GenericTrustResolver(AnonymousToken::class, RecallerToken::class);
        });

        $this->app->singleton(AuthenticationProviders::class);

        $this->app->bind(Authenticatable::class, AuthenticationManager::class);

        $this->app->bind(SecurityEvents::class, SecurityEvent::class);

        $this->app->bind(Guardable::class, Guard::class);
    }

    protected function registerAuthorizationServices(): void
    {
        $config = $this->app->make('config')->get('security.authorizer');

        // Authorization checker
        $this->app->bind(Grantable::class, array_get($config, 'grant'));

        // Role hierarchy
        // need a flag configuration to bind it
        $this->app->bind(RoleHierarchy::class, function () use ($config) {
            $class = array_get($config, 'role_hierarchy.service');
            $roles = array_get($config, 'role_hierarchy.roles');

            return new $class($roles, $config['role_prefix']);
        });

        // Authorization strategy / Voters
        $this->registerExpressionVoter(); // todo config

        $this->app->bind(AuthorizationStrategy::class, function (Application $app) use ($config) {
            $class = array_get($config, 'strategy');

            $voters = array_get($config, 'voters', []);

            foreach ($voters as &$voter) {
                $voter = $app->make($voter);
            }

            return new $class($voters);
        });
    }

    protected function registerExpressionVoter(): void
    {
        $this->app->bind(SecurityExpressionLanguage::class);

        $this->app->singleton('security_expression_voter', SecurityExpressionVoter::class);
    }

    protected function mergeConfig(): void
    {
        $this->mergeConfigFrom($this->getConfigPath(), 'security');
    }

    protected function getConfigPath(): string
    {
        return __DIR__ . '/../../../config/security.php';
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