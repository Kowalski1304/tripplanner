<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

Route::middleware('auth:sanctum')
    ->controller(ContactController::class)
    ->prefix('/contacts')
    ->group(function () {
        Route::post('/add/{contactUser}', 'addContact')->name('contacts.add');
        Route::delete('/remove/{contactUser}', 'removeContact');
        Route::get('', 'getContacts');
        Route::get('/pending', 'getPendingContacts');
    });
