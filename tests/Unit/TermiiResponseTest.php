<?php

namespace Hassan\Termii\Tests\Unit;

use Hassan\Termii\Http\TermiiResponse;
use Hassan\Termii\Tests\TestCase;

class TermiiResponseTest extends TestCase
{
    public function test_successful_response(): void
    {
        $response = new TermiiResponse(
            data: ['message_id' => 'abc123', 'message' => 'Successfully Sent'],
            statusCode: 200,
            successful: true,
        );

        $this->assertTrue($response->successful());
        $this->assertFalse($response->failed());
        $this->assertEquals(200, $response->status());
        $this->assertEquals('abc123', $response->messageId());
    }

    public function test_failed_response(): void
    {
        $response = new TermiiResponse(
            data: ['message' => 'Unauthorized'],
            statusCode: 401,
            successful: false,
        );

        $this->assertFalse($response->successful());
        $this->assertTrue($response->failed());
        $this->assertEquals(401, $response->status());
    }

    public function test_to_array(): void
    {
        $data = ['key' => 'value', 'nested' => ['a' => 1]];
        $response = new TermiiResponse($data, 200, true);

        $this->assertEquals($data, $response->toArray());
    }

    public function test_collect(): void
    {
        $response = new TermiiResponse(['items' => [1, 2, 3]], 200, true);
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $response->collect());
    }

    public function test_get_with_dot_notation(): void
    {
        $response = new TermiiResponse(['data' => ['balance' => '100.00']], 200, true);
        $this->assertEquals('100.00', $response->get('data.balance'));
        $this->assertNull($response->get('data.missing'));
        $this->assertEquals('default', $response->get('data.missing', 'default'));
    }

    public function test_verified_returns_true(): void
    {
        $response = new TermiiResponse(['verified' => 'True', 'msisdn' => '2348109077743'], 200, true);
        $this->assertTrue($response->verified());
    }

    public function test_verified_returns_false(): void
    {
        $response = new TermiiResponse(['verified' => 'False'], 200, true);
        $this->assertFalse($response->verified());
    }

    public function test_magic_property_access(): void
    {
        $response = new TermiiResponse(['balance' => '500', 'currency' => 'NGN'], 200, true);
        $this->assertEquals('500', $response->balance);
        $this->assertEquals('NGN', $response->currency);
        $this->assertNull($response->nonexistent);
    }

    public function test_to_string(): void
    {
        $data = ['message' => 'ok'];
        $response = new TermiiResponse($data, 200, true);
        $this->assertEquals(json_encode($data), (string) $response);
    }
}
