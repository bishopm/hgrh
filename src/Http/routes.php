<?php

use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;

// Website routes
Route::middleware(['web'])->controller('\Bishopm\Hgrh\Http\Controllers\HomeController')->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/about', 'about')->name('about');
    Route::get('/blog/{author?}', 'blogs')->name('blogs');
    Route::get('/blog/{year}/{month}/{slug}', 'blog')->name('blog');
    Route::get('/liturgy', 'liturgies')->name('liturgies');
    Route::get('/liturgy/{year}/{month}/{slug}', 'liturgy')->name('liturgy');
    Route::get('/subjects/{tag}', 'tag')->name('tag');
    Route::get('/videos', 'videos')->name('videos');
    Route::get('/videos/{year}/{month}/{slug}', 'video')->name('video');
    if (substr(str_replace(env('APP_URL'),'',url()->current()),1)<>"admin"){
        Route::get('/{person}', 'person')->name('person');
    }
});


