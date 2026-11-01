<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Tests;

use Eamirgh\Basalam\BasalamServiceProvider;
use Eamirgh\Basalam\Facades\Basalam;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            BasalamServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array<string, class-string>
     */
    protected function getPackageAliases($app): array
    {
        return [
            'Basalam' => Basalam::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('basalam.token', 'test-bearer-token');
        $app['config']->set('basalam.vendor_id', 78910);
        $app['config']->set('basalam.client_id', 'test-client-id');
        $app['config']->set('basalam.client_secret', 'test-client-secret');
        $app['config']->set('basalam.redirect_uri', 'https://example.com/oauth/callback');
        $app['config']->set('basalam.webhook_secret', 'test-webhook-secret');
        $app['config']->set('basalam.gateway_url', 'https://openapi.basalam.com');
        $app['config']->set('basalam.core_url', 'https://core.basalam.com');
        $app['config']->set('basalam.auth_url', 'https://auth.basalam.com');
    }
}
