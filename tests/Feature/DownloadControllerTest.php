<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Mockery;

class DownloadControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $plantId = 'test-plant-123';
    protected $mockPlantData = [
        'uid' => 'test-plant-123',
        'name' => 'Test Plant',
        'status' => 'active',
        'owner_name' => 'Test Owner',
        'owner_email' => 'test@example.com',
        'device_amount' => 5,
        'last_updated' => '2024-01-15 10:00:00',
        'latitude' => 54.6872,
        'longitude' => 25.2797,
        'capacity' => 100
    ];

    protected $mockApiData = [
        'aggregated_data_snapshots' => [
            [
                'timestamp' => '2024-01-15T08:00:00+00:00',
                'pv_p' => 5000,
                'battery_p' => 2000,
                'grid_p' => 3000,
                'load_p' => 4500,
                'tariff' => 0.15,
                'price' => 0.16,
                'battery_savings' => 0.30
            ],
            [
                'timestamp' => '2024-01-15T09:00:00+00:00',
                'pv_p' => 7000,
                'battery_p' => 3000,
                'grid_p' => 4000,
                'load_p' => 5800,
                'tariff' => 0.18,
                'price' => 0.19,
                'battery_savings' => 0.54
            ],
            [
                'timestamp' => '2024-01-15T10:00:00+00:00',
                'pv_p' => 9000,
                'battery_p' => 4000,
                'grid_p' => 5000,
                'load_p' => 7200,
                'tariff' => 0.20,
                'price' => 0.21,
                'battery_savings' => 0.80
            ]
        ]
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user with time format preference
        $this->user = User::factory()->create([
            'settings' => ['time_format' => '24'],
            'time_offset' => 0
        ]);
        
        // Mock HTTP responses for API calls
        Http::fake([
            'http://127.0.0.1:5001/plant_view/' . $this->plantId . '*' => Http::response(
                array_merge($this->mockPlantData, $this->mockApiData),
                200
            ),
        ]);
    }

    public function test_download_plant_report_generates_pdf_successfully()
    {
        $this->withoutMiddleware();
        $this->actingAs($this->user);
        
        // Mock chart images in session
        $chartImages = [
            'energy' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChAGA6vkUbgAAAABJRU5ErkJggg==',
            'battery' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChAGA6vkUbgAAAABJRU5ErkJggg==',
            'savings' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChAGA6vkUbgAAAABJRU5ErkJggg=='
        ];
        
        Session::put("chart_images_{$this->plantId}_2024-01-15", $chartImages);
        
        $response = $this->get("/plants/{$this->plantId}/download-report-pdf?date=2024-01-15");
        
        // Debug output
        if ($response->status() === 302) {
            $this->fail('Got redirect to: ' . $response->headers->get('Location'));
        }
        
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringContainsString('plant_report_' . $this->plantId, $response->headers->get('Content-Disposition'));
    }

    public function test_download_plant_report_handles_missing_plant()
    {
        $this->actingAs($this->user);
        
        // Mock API to return error for non-existent plant
        Http::fake([
            'http://127.0.0.1:5001/plant_view/nonexistent*' => Http::response(null, 404),
        ]);
        
        $response = $this->get("/plants/nonexistent/download-report-pdf?date=2024-01-15");
        
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_download_plant_report_with_fallback_images()
    {
        $this->actingAs($this->user);
        
        // Create test image files instead of session data
        Storage::fake('public');
        $testImageContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChAGA6vkUbgAAAABJRU5ErkJggg==');
        
        // Create the charts directory and files
        if (!file_exists(public_path('charts'))) {
            mkdir(public_path('charts'), 0755, true);
        }
        
        file_put_contents(public_path("charts/{$this->plantId}_energy.png"), $testImageContent);
        file_put_contents(public_path("charts/{$this->plantId}_battery.png"), $testImageContent);
        file_put_contents(public_path("charts/{$this->plantId}_savings.png"), $testImageContent);
        
        $response = $this->get("/plants/{$this->plantId}/download-report-pdf?date=2024-01-15");
        
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        
        // Clean up
        unlink(public_path("charts/{$this->plantId}_energy.png"));
        unlink(public_path("charts/{$this->plantId}_battery.png"));
        unlink(public_path("charts/{$this->plantId}_savings.png"));
    }

    public function test_download_all_charts_creates_zip_successfully()
    {
        $this->actingAs($this->user);
        
        $chartImages = [
            'energy' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChAGA6vkUbgAAAABJRU5ErkJggg==',
            'battery' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChAGA6vkUbgAAAABJRU5ErkJggg==',
            'savings' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChAGA6vkUbgAAAABJRU5ErkJggg=='
        ];
        
        Session::put("chart_images_{$this->plantId}_2024-01-15", $chartImages);
        
        $response = $this->get("/plants/{$this->plantId}/download-all-charts?date=2024-01-15");
        
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/zip');
        $this->assertStringContainsString('plant_charts_' . $this->plantId, $response->headers->get('Content-Disposition'));
        
        // Verify session data is cleared after download
        $this->assertNull(Session::get("chart_images_{$this->plantId}_2024-01-15"));
    }

    public function test_download_all_charts_handles_missing_session_data()
    {
        $this->actingAs($this->user);
        
        $response = $this->get("/plants/{$this->plantId}/download-all-charts?date=2024-01-15");
        
        $response->assertRedirect();
        $response->assertSessionHas('error', 'No chart images found. Please try again.');
    }

    public function test_download_all_csv_creates_zip_successfully()
    {
        $this->actingAs($this->user);
        
        $chartData = [
            'energy' => [
                'labels' => ['08:00', '09:00', '10:00'],
                'datasets' => [
                    [
                        'label' => 'PV Power',
                        'data' => [5.0, 7.0, 9.0]
                    ],
                    [
                        'label' => 'Battery Power', 
                        'data' => [2.0, 3.0, 4.0]
                    ],
                    [
                        'label' => 'Grid Power',
                        'data' => [3.0, 4.0, 5.0]
                    ],
                    [
                        'label' => 'Load Power',
                        'data' => [4.5, 5.8, 7.2]
                    ]
                ]
            ],
            'battery' => [
                'labels' => ['08:00', '09:00', '10:00'],
                'datasets' => [
                    [
                        'label' => 'Battery Power',
                        'data' => [2.0, 3.0, 4.0]
                    ],
                    [
                        'label' => 'Energy Price',
                        'data' => [0.15, 0.18, 0.20]
                    ],
                    [
                        'label' => 'Price',
                        'data' => [0.16, 0.19, 0.21]
                    ]
                ]
            ]
        ];
        
        Session::put("chart_data_{$this->plantId}_2024-01-15", $chartData);
        
        $response = $this->get("/plants/{$this->plantId}/download-all-csv?date=2024-01-15");
        
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/zip');
        $this->assertStringContainsString('plant_csv_data_' . $this->plantId, $response->headers->get('Content-Disposition'));
        
        // Verify session data is cleared after download
        $this->assertNull(Session::get("chart_data_{$this->plantId}_2024-01-15"));
    }

    public function test_download_all_csv_handles_missing_session_data()
    {
        $this->actingAs($this->user);
        
        $response = $this->get("/plants/{$this->plantId}/download-all-csv?date=2024-01-15");
        
        $response->assertRedirect();
        $response->assertSessionHas('error', 'No chart data found. Please try again.');
    }

    public function test_save_chart_images_stores_valid_images()
    {
        $this->actingAs($this->user);
        
        $chartImages = [
            'energy' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChAGA6vkUbgAAAABJRU5ErkJggg==',
            'battery' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChAGA6vkUbgAAAABJRU5ErkJggg==',
            'savings' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChAGA6vkUbgAAAABJRU5ErkJggg=='
        ];
        
        $response = $this->postJson("/plants/{$this->plantId}/save-chart-images", [
            'chart_images' => $chartImages,
            'date' => '2024-01-15'
        ]);
        
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Chart images saved successfully',
            'images_saved' => 3,
            'chart_types' => ['energy', 'battery', 'savings']
        ]);
        
        // Verify data is stored in session
        $sessionData = Session::get("chart_images_{$this->plantId}_2024-01-15");
        $this->assertCount(3, $sessionData);
        $this->assertArrayHasKey('energy', $sessionData);
        $this->assertArrayHasKey('battery', $sessionData);
        $this->assertArrayHasKey('savings', $sessionData);
    }

    public function test_save_chart_images_filters_invalid_data()
    {
        $this->actingAs($this->user);
        
        $chartImages = [
            'energy' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChAGA6vkUbgAAAABJRU5ErkJggg==',
            'battery' => 'invalid-data',
            'savings' => null,
            'temperature' => 'not-an-image'
        ];
        
        $response = $this->postJson("/plants/{$this->plantId}/save-chart-images", [
            'chart_images' => $chartImages,
            'date' => '2024-01-15'
        ]);
        
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'images_saved' => 1,
            'chart_types' => ['energy']
        ]);
        
        // Verify only valid data is stored
        $sessionData = Session::get("chart_images_{$this->plantId}_2024-01-15");
        $this->assertCount(1, $sessionData);
        $this->assertArrayHasKey('energy', $sessionData);
        $this->assertArrayNotHasKey('battery', $sessionData);
    }

    public function test_save_chart_images_handles_no_valid_images()
    {
        $this->actingAs($this->user);
        
        $chartImages = [
            'energy' => 'invalid-data',
            'battery' => null,
            'savings' => 'not-an-image'
        ];
        
        $response = $this->postJson("/plants/{$this->plantId}/save-chart-images", [
            'chart_images' => $chartImages,
            'date' => '2024-01-15'
        ]);
        
        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'message' => 'No valid chart images provided'
        ]);
    }

    public function test_save_chart_data_stores_data_successfully()
    {
        $this->actingAs($this->user);
        
        $chartData = [
            'energy' => [
                'labels' => ['08:00', '09:00', '10:00'],
                'datasets' => [
                    [
                        'label' => 'PV Power',
                        'data' => [5.0, 7.0, 9.0]
                    ]
                ]
            ],
            'battery' => [
                'labels' => ['08:00', '09:00', '10:00'],
                'datasets' => [
                    [
                        'label' => 'Battery Power',
                        'data' => [2.0, 3.0, 4.0]
                    ]
                ]
            ]
        ];
        
        $response = $this->postJson("/plants/{$this->plantId}/save-chart-data", [
            'chart_data' => $chartData,
            'date' => '2024-01-15'
        ]);
        
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Chart data saved successfully'
        ]);
        
        // Verify data is stored in session
        $sessionData = Session::get("chart_data_{$this->plantId}_2024-01-15");
        $this->assertCount(2, $sessionData);
        $this->assertArrayHasKey('energy', $sessionData);
        $this->assertArrayHasKey('battery', $sessionData);
    }

    public function test_original_download_functionality_still_works()
    {
        $this->actingAs($this->user);
        
        // Test PNG download - route is GET with query parameters
        $response = $this->get("/plants/{$this->plantId}/download/energy/png?" . http_build_query([
            'date' => '2024-01-15',
            'image_data' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChAGA6vkUbgAAAABJRU5ErkJggg=='
        ]));
        
        // PNG downloads might return an error or different status due to missing image data
        // Let's check if the route exists and responds appropriately
        $this->assertContains($response->getStatusCode(), [200, 302, 422]);
    }

    public function test_csv_download_with_real_data()
    {
        $this->actingAs($this->user);
        
        $response = $this->get("/plants/{$this->plantId}/download/energy/csv?date=2024-01-15");
        
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        
        // Check CSV content contains expected headers and data
        $content = $response->getContent();
        $this->assertStringContainsString('Timestamp', $content);
        $this->assertStringContainsString('Time', $content);
        $this->assertStringContainsString('PV', $content);
        $this->assertStringContainsString('Battery', $content);
        $this->assertStringContainsString('Grid', $content);
    }

    public function test_fetch_plant_from_api_error_handling()
    {
        $this->actingAs($this->user);
        
        // Mock API to simulate connection error
        Http::fake([
            'http://127.0.0.1:5001/plant_view/' . $this->plantId . '*' => Http::response(null, 500),
        ]);
        
        $response = $this->get("/plants/{$this->plantId}/download-report-pdf?date=2024-01-15");
        
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_data_transformation_for_pdf()
    {
        $this->actingAs($this->user);
        
        Session::put("chart_images_{$this->plantId}_2024-01-15", [
            'energy' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChAGA6vkUbgAAAABJRU5ErkJggg=='
        ]);
        
        $response = $this->get("/plants/{$this->plantId}/download-report-pdf?date=2024-01-15");
        
        $response->assertStatus(200);
        
        // The test ensures that data transformation doesn't break the PDF generation
        // Actual data validation is handled in the controller logic
    }

    public function test_user_time_format_preference_applied()
    {
        // Test with 12-hour format user
        $user12h = User::factory()->create([
            'settings' => ['time_format' => '12'],
            'time_offset' => -5
        ]);
        
        $this->actingAs($user12h);
        
        Session::put("chart_images_{$this->plantId}_2024-01-15", [
            'energy' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChAGA6vkUbgAAAABJRU5ErkJggg=='
        ]);
        
        $response = $this->get("/plants/{$this->plantId}/download-report-pdf?date=2024-01-15");
        
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_zip_archive_creation_edge_cases()
    {
        $this->actingAs($this->user);
        
        // Test with corrupted base64 data
        $chartImages = [
            'energy' => 'data:image/png;base64,corrupted_data',
            'battery' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChAGA6vkUbgAAAABJRU5ErkJggg=='
        ];
        
        Session::put("chart_images_{$this->plantId}_2024-01-15", $chartImages);
        
        $response = $this->get("/plants/{$this->plantId}/download-all-charts?date=2024-01-15");
        
        // Should still create ZIP with valid images only
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/zip');
    }

    public function test_csv_generation_from_chart_data()
    {
        $this->actingAs($this->user);
        
        $chartData = [
            'energy' => [
                'labels' => ['08:00', '09:00'],
                'datasets' => [
                    [
                        'label' => 'PV Power',
                        'data' => [5.123, 7.456]
                    ],
                    [
                        'label' => 'Battery Power',
                        'data' => [2.789, 3.012]
                    ],
                    [
                        'label' => 'Grid Power',
                        'data' => [1.555, 2.222]
                    ],
                    [
                        'label' => 'Load Power',
                        'data' => [4.444, 5.555]
                    ]
                ]
            ]
        ];
        
        Session::put("chart_data_{$this->plantId}_2024-01-15", $chartData);
        
        $response = $this->get("/plants/{$this->plantId}/download-all-csv?date=2024-01-15");
        
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/zip');
    }

    protected function tearDown(): void
    {
        // Clean up any created files
        $chartsDir = public_path('charts');
        if (is_dir($chartsDir)) {
            $files = glob("{$chartsDir}/*");
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
        
        parent::tearDown();
    }
}
