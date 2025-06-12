<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChartTimeFormatIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_chart_receives_user_time_format_preference()
    {
        // Create a user with 12-hour time format preference
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'settings' => ['time_format' => '12']
        ]);

        // Login as the user
        $this->actingAs($user);

        // Mock plant data for the view
        $mockPlant = (object) [
            'uid' => 'test-plant-123',
            'name' => 'Test Plant',
            'metadata_flat' => []
        ];

        // Test that the chart partial includes the user's time format
        $view = $this->view('plants.partials.plant-chart', [
            'plant' => $mockPlant,
            'user' => $user
        ]);

        $rendered = (string) $view;

        // Check that the user's time format preference is passed to JavaScript
        $this->assertStringContainsString('window.userTimeFormat = "12"', $rendered);
        $this->assertStringContainsString('window.userTimeOffset', $rendered);
    }

    public function test_chart_defaults_to_24_hour_format_for_guest()
    {
        // Mock plant data for the view
        $mockPlant = (object) [
            'uid' => 'test-plant-123', 
            'name' => 'Test Plant',
            'metadata_flat' => []
        ];

        // Test chart partial without authenticated user
        $view = $this->view('plants.partials.plant-chart', [
            'plant' => $mockPlant,
            'user' => null
        ]);

        $rendered = (string) $view;

        // Check that it defaults to 24-hour format
        $this->assertStringContainsString('window.userTimeFormat = "24"', $rendered);
    }

    public function test_chart_uses_24_hour_format_for_new_user()
    {
        // Create a user without time format preference (should default to 24h)
        $user = User::factory()->create([
            'email' => 'new@example.com'
            // No settings specified, should default to 24h
        ]);

        $this->actingAs($user);

        $mockPlant = (object) [
            'uid' => 'test-plant-123',
            'name' => 'Test Plant', 
            'metadata_flat' => []
        ];

        $view = $this->view('plants.partials.plant-chart', [
            'plant' => $mockPlant,
            'user' => $user
        ]);

        $rendered = (string) $view;

        // Check that new user defaults to 24-hour format
        $this->assertStringContainsString('window.userTimeFormat = "24"', $rendered);
    }
}
