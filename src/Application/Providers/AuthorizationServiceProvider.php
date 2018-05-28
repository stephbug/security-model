<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use StephBug\SecurityModel\Application\Exception\InvalidArgument;
use StephBug\SecurityModel\Guard\Authorization\Expression\SecurityExpressionLanguage;
use StephBug\SecurityModel\Guard\Authorization\Expression\SecurityExpressionVoter;
use StephBug\SecurityModel\Guard\Authorization\Grantable;
use StephBug\SecurityModel\Guard\Authorization\Hierarchy\RoleHierarchy;
use StephBug\SecurityModel\Guard\Authorization\Strategy\AuthorizationStrategy;

class AuthorizationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerAuthorizationChecker();

        $this->registerRoleHierarchy();

        $this->registerExpressionVoter();

        $this->registerAuthorizationStrategy();
    }

    protected function registerRoleHierarchy(): void
    {
        $this->app->bind(RoleHierarchy::class, function () {
            $config = $this->getSecurityConfig();

            $class = array_get($config, 'role_hierarchy.service');
            $roles = array_get($config, 'role_hierarchy.roles');

            return new $class($roles, $config['role_prefix']);
        });
    }

    protected function registerAuthorizationStrategy(): void
    {
        $this->app->bind(AuthorizationStrategy::class, function (Application $app) {
            $config = $this->getSecurityConfig();

            $class = array_get($config, 'strategy');

            if (!class_exists($class)) {
                throw InvalidArgument::reason(
                    sprintf('Class %s does not exists for strategy key in security configuration')
                );
            }

            $voters = array_get($config, 'voters', []);

            if (!$voters) {
                throw InvalidArgument::reason(
                    'You must add at least one voter in the security configuration'
                );
            }

            foreach ($voters as &$voter) {
                $voter = $app->make($voter);
            }

            return new $class($voters);
        });
    }

    protected function registerExpressionVoter(): void
    {
        $alias = 'security_expression_voter';

        if (in_array($alias, $this->getSecurityConfig()['voters'])) {
            $this->app->bind(SecurityExpressionLanguage::class);

            $this->app->singleton($alias, SecurityExpressionVoter::class);
        }
    }

    protected function registerAuthorizationChecker(): void
    {
        $this->app->bind(Grantable::class, array_get($this->getSecurityConfig(), 'grant'));
    }

    protected function getSecurityConfig(): array
    {
        return $this->app->make('config')->get('security.authorizer');
    }
}