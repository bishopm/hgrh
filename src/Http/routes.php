<?php

use Illuminate\Support\Facades\Route;

// App routes
Route::middleware(['web'])->controller('\Bishopm\Hgrh\Http\Controllers\HomeController')->group(function () {
    Route::get('/', 'home')->name('home');
});


