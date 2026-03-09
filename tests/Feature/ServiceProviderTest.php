<?php

namespace Hassan\Termii\Tests\Feature;

use Hassan\Termii\Services\InsightService;
use Hassan\Termii\Services\SwitchService;
use Hassan\Termii\Services\TokenService;
use Hassan\Termii\Termii;
use Hassan\Termii\Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    public function test_termii_is_bound_in_container(): void
    {
        $this->assertInstanceOf(Termii::class, $this->app->make(Termii::class));
        $this->assertInstanceOf(Termii::class, $this->app->make('termii'));
    }

    public function test_services_are_bound_in_container(): void
    {
        $this->assertInstanceOf(SwitchService::class, $this->app->make(SwitchService::class));
        $this->assertInstanceOf(TokenService::class, $this->app->make(TokenService::class));
        $this->assertInstanceOf(InsightService::class, $this->app->make(InsightService::class));
    }

    public function test_facade_resolves_correctly(): void
    {
        $termii = \Hassan\Termii\Facades\Termii::getFacadeRoot();
        $this->assertInstanceOf(Termii::class, $termii);
    }

    public function test_config_is_published(): void
    {
        $this->assertEquals('test-api-key-123', config('termii.api_key'));
        $this->assertEquals('TestApp', config('termii.sender_id'));
        $this->assertEquals('generic', config('termii.channel'));
    }

    public function test_termii_exposes_service_accessors(): void
    {
        $termii = $this->app->make(Termii::class);
        $this->assertInstanceOf(SwitchService::class, $termii->switch());
        $this->assertInstanceOf(TokenService::class, $termii->token());
        $this->assertInstanceOf(InsightService::class, $termii->insight());
    }

    public function test_bad_method_call_throws_exception(): void
    {
        $termii = $this->app->make(Termii::class);
        $this->expectException(\BadMethodCallException::class);
        $termii->nonExistentMethod();
    }
}
