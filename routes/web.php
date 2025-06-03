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

    Route::resource('plants', PlantController::class);
    Route::resource('devices', DeviceController::class);

    // Download PNG, CSV, PDF

    //
    Route::get('/plants/{plant}/download/{chart}/{type}', [DownloadController::class, 'download'])
    ->name('plants.download');
    Route::post('/charts/upload', [ChartUploadController::class, 'store'])
    ->name('charts.upload');

    Route::post('/plants/{plant}/save-chart-image', [DownloadController::class, 'saveChartImage'])
    ->name('plants.save_chart_image');

    Route::put('/profile/settings', function (Request $request) {
        $user = $request->user();
        $settings = $user->settings ?? [];
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


require __DIR__.'/auth.php';
