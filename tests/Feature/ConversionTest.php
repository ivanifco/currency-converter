<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ConversionTest extends TestCase
{
    use RefreshDatabase;

    /** @test 
     * 
     * Convert EUR to MKD and store in DB.
     */
    public function it_converts_currency_and_stores_in_db()
    {
        Http::fake([
            'data.fixer.io/*' => Http::response([
                'success' => true,
                'rates' => [
                    'EUR' => 1.00,
                    'MKD' => 61.0,
                ]
            ], 200),
        ]);

        $payload = [
            'source_currency' => 'EUR',
            'target_currency' => 'MKD',
            'value' => 100,
        ];

        $response = $this->postJson('/api/convert', $payload);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'source_currency',
                    'target_currency',
                    'value',
                    'converted_value',
                    'rate',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $this->assertDatabaseHas('conversions', [
            'source_currency' => 'EUR',
            'target_currency' => 'MKD',
            'value' => 100,
        ]);
    }

    public function test_api_failure_returns_error()
    {
        // Fake the HTTP request to simulate API failure
        Http::fake([
            'data.fixer.io/*' => Http::response([
                'success' => false,
                'error' => ['code' => 101, 'type' => 'invalid_access_key']
            ], 200),
        ]);

        $payload = [
            'source_currency' => 'EUR',
            'target_currency' => 'USD',
            'value' => 100,
        ];

        $response = $this->postJson('/api/convert', $payload);

        $response->assertStatus(500)
            ->assertJson([
                'error' => 'Failed to fetch exchange rates',
            ]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->postJson('/api/convert', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['source_currency', 'target_currency', 'value']);
    }
}