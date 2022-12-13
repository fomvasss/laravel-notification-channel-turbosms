<?php

namespace NotificationChannels\TurboSms;

use Illuminate\Support\ServiceProvider;

class TurboSmsServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->singleton(TurboSmsApi::class, function ($app) {
            $apiToken = $this->app['config']['services.turbosms.api_token'];
            $sender = $this->app['config']['services.turbosms.sender'];
            $isTest = $this->app['config']['services.turbosms.is_test'];
            $client = new TurboSmsApi($apiToken, $sender, $isTest);

            return $client;
        });
    }

    public function provides(): array
    {
        return [
            TurboSmsApi::class,
        ];
    }
}
