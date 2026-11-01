<?php

declare(strict_types=1);

namespace Eamirgh\Basalam;

use Illuminate\Support\ServiceProvider;

class BasalamServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/basalam.php',
            'basalam'
        );

        $this->app->singleton('basalam', function ($app) {
            $config = (array) $app['config']->get('basalam', []);

            return new BasalamManager($config);
        });

        $this->app->alias('basalam', BasalamManager::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/basalam.php' => $this->app->configPath('basalam.php'),
            ], 'basalam-config');
        }
    }

    /**
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [
            'basalam',
            BasalamManager::class,
        ];
    }
}
