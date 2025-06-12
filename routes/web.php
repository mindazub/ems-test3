<?php

use App\Http\Controllers\ChartUploadController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\PlantController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DownloadController;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});



Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/profile/settings', [ProfileController::class, 'updateSettings'])->name('profile.update.settings');

    Route::resource('plants', PlantController::class);
    Route::get('/plants/{plant}', [PlantController::class, 'showRemote'])->name('plants.show');
    
    // Device routes - exclude 'show' from resource and define it manually
    Route::resource('devices', DeviceController::class)->except(['show']);
    Route::get('/devices/{id}', [DeviceController::class, 'show'])->name('devices.show');
    
    // Add new route for devices by feed view
    Route::get('/devices-by-feed', [DeviceController::class, 'devicesByFeed'])->name('devices.by-feed');

    // Add new route for plant data with date parameters
    Route::get('/plants/{plant}/data', [PlantController::class, 'getData'])->name('plants.data');
    
    // Add route for available dates
    Route::get('/plants/{plant}/available-dates', [PlantController::class, 'getAvailableDates'])->name('plants.available-dates');
    
    // Download PNG, CSV, PDF
    Route::get('/plants/{plant}/download/{chart}/{type}', [DownloadController::class, 'download'])
    ->name('plants.download');
    Route::post('/charts/upload', [ChartUploadController::class, 'store'])
    ->name('charts.upload');

    Route::post('/plants/{plant}/save-chart-image', [DownloadController::class, 'saveChartImage'])
    ->name('plants.save_chart_image');
    
    // New routes for saving bulk data
    Route::post('/plants/{plant}/save-chart-images', [DownloadController::class, 'saveChartImages'])
    ->name('plants.save_chart_images');
    Route::post('/plants/{plant}/save-chart-data', [DownloadController::class, 'saveChartData'])
    ->name('plants.save_chart_data');
    
    // Comprehensive plant downloads from show page
    Route::get('/plants/{plant}/download-report-pdf', [DownloadController::class, 'downloadPlantReport'])
    ->name('plants.download.report');
    Route::get('/plants/{plant}/download-all-charts', [DownloadController::class, 'downloadAllCharts']) 
    ->name('plants.download.charts');
    Route::get('/plants/{plant}/download-all-csv', [DownloadController::class, 'downloadAllCSV'])
    ->name('plants.download.csv');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('roles', RoleController::class)
        ->only(['index', 'edit', 'update'])
        ->parameter('roles', 'user');

    Route::resource('customers', CustomerController::class)
        ->only(['index', 'show', 'edit', 'update'])
        ->parameter('customers', 'user')
        ->name('customers.index', 'customers');

    // Admin dashboard route
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});

// Route::get('/plants/remote/{id}', [App\Http\Controllers\PlantController::class, 'showRemote'])->name('plants.show.remote');


require __DIR__.'/auth.php';
