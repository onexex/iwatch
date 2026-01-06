<?php

use Inertia\Inertia;
use App\Models\SmsMessage;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SMS\SmsController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\ClassificationController;
use App\Http\Controllers\IncidentWatermarkController;
use App\Http\Controllers\UserManagementController;

Route::get('/', function () {
    return Inertia::render('auth/Login', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('mapping', function () {
    return Inertia::render('IncidentMap');
})->middleware(['auth', 'verified'])->name('mapping');


Route::get('/mapping_incidents', [IncidentController::class, 'index']);
 
Route::middleware('auth', 'verified')->group(function () {
    Route::get('/sms', [SmsController::class, 'index'])->name('sms');
    Route::post('/sms/fetch-message', [SmsController::class, 'store'])->name('sms.fetchMessages');

    Route::get('/processed-messages', [IncidentController::class, 'processedMessages'])->name('processed-messages.index');

    Route::resource('classifications', ClassificationController::class);
    Route::get('/processed-sms-get-reference', [SmsController::class, 'getReference'])->name('sms.reference');
    Route::get('/download-incident/{incident}', [IncidentController::class, 'download'])->name('incident.download');

    Route::resource('incidentwatermarks', IncidentWatermarkController::class);
});

Route::middleware('auth', 'verified')->group(function () {
    
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::post('/users/store', [UserManagementController::class, 'store'])->name('users.store');
    Route::put('/users/update/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/delete/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
});

require __DIR__.'/settings.php';
