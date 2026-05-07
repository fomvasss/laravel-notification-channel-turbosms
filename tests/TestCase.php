<?php

declare(strict_types=1);

namespace NotificationChannels\TurboSms\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use NotificationChannels\TurboSms\TurboSmsServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            TurboSmsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('services.turbosms', [
            'api_token' => 'test-token',
            'sender'    => 'TEST',
            'is_test'   => true,
        ]);
    }
}

