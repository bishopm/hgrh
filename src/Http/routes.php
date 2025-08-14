<?php

use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;

// Website routes
Route::middleware(['web'])->controller('\Bishopm\Hgrh\Http\Controllers\HomeController')->group(function () {
    Route::get('/', 'home')->name('home');
});


