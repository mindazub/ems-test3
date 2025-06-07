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
use App\Http\Controllers\PlantBDController;
use App\Http\Controllers\PlantDBController;
use App\Http\Controllers\PlantJSONController;
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

    Route::resource('plants', PlantDBController::class);
    Route::get('/plants/{uid}', [PlantDBController::class, 'showByUid'])->name('plant.show.uid');
    // Route::resource('plants', PlantController::class);
    // Route::resource('plants', PlantJSONController::class);
    Route::resource('devices', DeviceController::class);

    // Chart Download Routes
    Route::prefix('plants/{plant}')->group(function () {
        // Download route for chart files (PNG, CSV, PDF)
        Route::get('/download/{chart}/{type}', [DownloadController::class, 'download'])
            ->name('plants.download')
            ->where('chart', 'energy|battery|savings')
            ->where('type', 'png|csv|pdf')
            ->middleware(\App\Http\Middleware\EnsureChartsDirectoryExists::class);

        // Save chart image route
        Route::post('/save-chart-image', [DownloadController::class, 'saveChartImage'])
            ->name('plants.save_chart_image')
            ->middleware(\App\Http\Middleware\EnsureChartsDirectoryExists::class);
    });

    // Other chart related routes
    Route::post('/charts/upload', [ChartUploadController::class, 'store'])
        ->name('charts.upload');

    Route::put('/profile/settings', function (Request $request) {
        $user = $request->user();
        $settings = $user->settings;
        if (is_string($settings)) {
            $settings = json_decode($settings, true) ?: [];
        } elseif (!is_array($settings)) {
            $settings = [];
        }
        $settings['time_format'] = $request->input('time_format', '24');
        $user->settings = $settings;
        $user->save();
        return redirect()->route('profile.edit')->with('status', 'settings-updated');
    })->name('profile.update.settings');
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

// Route::get('/plants', [\App\Http\Controllers\PlantJSONController::class, 'indexJson'])->name('plants.index');
Route::get('/plants-json', [\App\Http\Controllers\PlantJSONController::class, 'index'])->name('plants.json.index');



require __DIR__.'/auth.php';
