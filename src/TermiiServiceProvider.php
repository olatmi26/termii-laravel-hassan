<?php

namespace Hassan\Termii;

use Illuminate\Support\ServiceProvider;
use Hassan\Termii\Http\TermiiClient;
use Hassan\Termii\Services\InsightService;
use Hassan\Termii\Services\SwitchService;
use Hassan\Termii\Services\TokenService;

class TermiiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/termii.php',
            'termii'
        );

        $this->app->singleton(TermiiClient::class, function ($app) {
            $config = $app['config']['termii'];

            return new TermiiClient(
                baseUrl: $config['base_url'],
                apiKey: $config['api_key'],
                timeout: $config['timeout'] ?? 30,
            );
        });

        $this->app->singleton(SwitchService::class, function ($app) {
            $config = $app['config']['termii'];

            return new SwitchService(
                client: $app->make(TermiiClient::class),
                defaultSenderId: $config['sender_id'],
                defaultChannel: $config['channel'],
            );
        });

        $this->app->singleton(TokenService::class, function ($app) {
            $config = $app['config']['termii'];

            return new TokenService(
                client: $app->make(TermiiClient::class),
                defaultSenderId: $config['sender_id'],
                defaultChannel: $config['channel'],
            );
        });

        $this->app->singleton(InsightService::class, function ($app) {
            return new InsightService(
                client: $app->make(TermiiClient::class),
            );
        });

        $this->app->singleton(Termii::class, function ($app) {
            return new Termii(
                switch: $app->make(SwitchService::class),
                token: $app->make(TokenService::class),
                insight: $app->make(InsightService::class),
            );
        });

        $this->app->alias(Termii::class, 'termii');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/termii.php' => config_path('termii.php'),
            ], 'termii-config');
        }
    }
}
