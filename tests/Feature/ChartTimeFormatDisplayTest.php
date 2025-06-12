<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChartTimeFormatDisplayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_chart_displays_12_hour_format_for_user_preference()
    {
        // Create a user with 12-hour time format preference
        $user = User::factory()->create([
            'settings' => ['time_format' => '12'],
            'time_offset' => 0
        ]);

        $this->actingAs($user);

        // Test the chart partial view directly
        $view = $this->view('plants.partials.plant-chart', [
            'plant' => (object) ['uid' => 'test-plant-123', 'name' => 'Test Plant'],
            'user' => $user
        ]);

        $rendered = (string) $view;
        
        $this->assertStringContainsString('window.userTimeFormat = "12"', $rendered);
        $this->assertStringContainsString('formatChartTimeLabel', $rendered);
    }

    public function test_chart_displays_24_hour_format_for_user_preference()
    {
        // Create a user with 24-hour time format preference
        $user = User::factory()->create([
            'settings' => ['time_format' => '24'],
            'time_offset' => 6
        ]);

        $this->actingAs($user);

        // Test the chart partial view directly
        $view = $this->view('plants.partials.plant-chart', [
            'plant' => (object) ['uid' => 'test-plant-123', 'name' => 'Test Plant'],
            'user' => $user
        ]);

        $rendered = (string) $view;
        
        $this->assertStringContainsString('window.userTimeFormat = "24"', $rendered);
        $this->assertStringContainsString('formatChartTimeLabel', $rendered);
    }

    public function test_chart_defaults_to_24_hour_format_for_guest()
    {
        // Test chart without authenticated user
        $view = $this->view('plants.partials.plant-chart', [
            'plant' => (object) ['uid' => 'test-plant-123', 'name' => 'Test Plant'],
            'user' => null
        ]);

        $rendered = (string) $view;
        
        $this->assertStringContainsString('window.userTimeFormat = "24"', $rendered);
    }

    public function test_chart_defaults_to_24_hour_format_for_new_user()
    {
        // Create a user without time format preference
        $user = User::factory()->create([
            // No settings specified, should default to 24h
        ]);

        $this->actingAs($user);

        $view = $this->view('plants.partials.plant-chart', [
            'plant' => (object) ['uid' => 'test-plant-123', 'name' => 'Test Plant'],
            'user' => $user
        ]);

        $rendered = (string) $view;
        
        $this->assertStringContainsString('window.userTimeFormat = "24"', $rendered);
    }

    public function test_chart_axis_callback_uses_format_function()
    {
        $user = User::factory()->create([
            'settings' => ['time_format' => '12']
        ]);

        $this->actingAs($user);

        $view = $this->view('plants.partials.plant-chart', [
            'plant' => (object) ['uid' => 'test-plant-123', 'name' => 'Test Plant'],
            'user' => $user
        ]);

        $rendered = (string) $view;
        
        // Check that all three charts use the formatChartTimeLabel function
        $this->assertStringContainsString('return formatChartTimeLabel(time);', $rendered);
        
        // Count occurrences - should be 3 (Energy, Battery, Savings charts)
        $count = substr_count($rendered, 'return formatChartTimeLabel(time);');
        $this->assertEquals(3, $count, 'All three charts should use formatChartTimeLabel function');
    }

    public function test_pdf_export_includes_user_data()
    {
        $user = User::factory()->create([
            'settings' => ['time_format' => '12']
        ]);

        $this->actingAs($user);

        // Test the PDF template directly with proper chart data
        $chartData = [
            '2024-06-12 14:30:00' => ['pv_p' => 5000, 'battery_p' => 2000, 'grid_p' => 1000],
            '2024-06-12 15:00:00' => ['pv_p' => 6000, 'battery_p' => 2500, 'grid_p' => 1200],
        ];

        $view = $this->view('plants.exports.pdf', [
            'plant' => (object) ['uid' => 'test-plant-123', 'name' => 'Test Plant'],
            'chart' => 'energy',
            'chartImage' => 'test-image-data',
            'chartData' => $chartData,
            'selectedDate' => '2024-06-12',
            'summary' => [],
            'generatedAt' => '2024-06-12 14:30:00',
            'user' => $user
        ]);

        $rendered = (string) $view;

        // Check that PDF template correctly uses 12-hour format for chart data
        $this->assertStringContainsString('2:30 PM', $rendered); // 14:30 should appear as 2:30 PM
        $this->assertStringContainsString('3:00 PM', $rendered); // 15:00 should appear as 3:00 PM
        
        // Check that generatedAt timestamp also uses 12-hour format
        $this->assertStringContainsString('2024-06-12 2:30:00 PM', $rendered); // generatedAt should be formatted
        
        // Ensure 24-hour format is NOT present in time columns (only in date part)
        $this->assertStringNotContainsString('14:30', $rendered);
        $this->assertStringNotContainsString('15:00', $rendered);
    }
}
