<?php

use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

Route::middleware('auth:sanctum')
    ->controller(ContactController::class)
    ->prefix('/contacts')
    ->group(function () {
        Route::post('/add/{contactUser}', 'addContact')->name('contacts.add');
        Route::delete('/remove/{contactUser}', 'removeContact');
    });
Route::middleware('auth:sanctum')
    ->controller(TeamController::class)
    ->prefix('/team')
    ->group(function () {
        Route::post('/create', 'storeTeam')->name('team.create');
    });
