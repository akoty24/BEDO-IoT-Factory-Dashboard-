<?php

use App\Http\Controllers\FactoryController;
use Illuminate\Support\Facades\Route;

Route::controller(FactoryController::class)->name('factory.')->group(function () {
    Route::get('/', 'dashboard')->name('dashboard');
    Route::get('/manual', 'manual')->name('manual');
    Route::get('/automatic', 'automatic')->name('automatic');
    // Route::get('/remote', 'remote')->name('remote');
});
