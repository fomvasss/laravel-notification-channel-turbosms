<?php

declare(strict_types=1);

namespace NotificationChannels\TurboSms;

use Illuminate\Support\ServiceProvider;

class TurboSmsServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->app->singleton(TurboSmsApi::class, function ($app) {
            $config = $app['config']['services.turbosms'] ?? [];

            return new TurboSmsApi(
                apiToken: $config['api_token'] ?? '',
                smsSender: $config['sender'] ?? '',
                configs: $config,
            );
        });
    }

    public function provides(): array
    {
        return [
            TurboSmsApi::class,
        ];
    }
}
