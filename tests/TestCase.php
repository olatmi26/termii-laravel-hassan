<?php

namespace Hassan\Termii\Tests;

use Hassan\Termii\TermiiServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [TermiiServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('termii.api_key', 'test-api-key-123');
        $app['config']->set('termii.base_url', 'https://api.ng.termii.com');
        $app['config']->set('termii.sender_id', 'TestApp');
        $app['config']->set('termii.channel', 'generic');
    }
}
