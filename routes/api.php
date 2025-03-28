<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

Route::middleware('auth:sanctum')
    ->controller(ContactController::class)
    ->prefix('/contacts')
    ->group(function () {
        Route::post('/add/{user}', 'addContact')->name('contacts.add');
        Route::post('/accept', 'acceptContact');
        Route::delete('/remove', 'removeContact');
        Route::get('', 'getContacts');
        Route::get('/pending', 'getPendingContacts');
    });
