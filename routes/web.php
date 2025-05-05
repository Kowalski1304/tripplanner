<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebSocketController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

require __DIR__.'/auth.php';

Route::get('private-file/{path}', function ($path) {
    return Storage::disk('private')->response('team_files/' . $path);
})->middleware('auth', 'signed')->name('private.file');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('users')
    ->middleware(['auth'])
    ->controller(UserController::class)
    ->group(function () {
        Route::get('/', 'index')->name('users.index');
        Route::get('/{user}', 'show')->name('users.show');
    });

Route::prefix('team')
    ->middleware(['auth'])
    ->controller(TeamController::class)
    ->group(function () {
        Route::get('/', 'createTeamPage')->name('team.index');
    });

Route::post('/ws/message', [WebSocketController::class, 'handleMessage'])
    ->middleware(['auth'])
    ->name('ws.message');

