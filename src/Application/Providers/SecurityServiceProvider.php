<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Providers;

use Illuminate\Support\AggregateServiceProvider;

class SecurityServiceProvider extends AggregateServiceProvider
{
    /**
     * @var array
     */
    protected $providers = [
        AuthenticationServiceProvider::class,
        AuthorizationServiceProvider::class
    ];

    public function boot(): void
    {
        $this->publishes(
            [$this->getConfigPath() => config_path('security.php')],
            'config'
        );
    }

    public function register(): void
    {
        $this->mergeConfigFrom($this->getConfigPath(), 'security');

        parent::register();
    }

    protected function getConfigPath(): string
    {
        return __DIR__ . '/../../../config/security.php';
    }
}