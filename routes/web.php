<?php

use Inertia\Inertia;
use App\Models\SmsMessage;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IncidentController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('mapping', function () {
    return Inertia::render('IncidentMap');
})->middleware(['auth', 'verified'])->name('mapping');

Route::get('/sms', function () {
    // Fetch messages from the database
    $messages = SmsMessage::orderBy('created_at', 'desc')->get(); // adjust query as needed

    // Pass messages to Inertia
    return Inertia::render('Sms', [
        'messages' => $messages
    ]);
})->middleware(['auth', 'verified'])->name('sms');

Route::get('/mapping_incidents', [IncidentController::class, 'index']);

 

require __DIR__.'/settings.php';
