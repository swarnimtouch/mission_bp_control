<?php

use Illuminate\Support\Facades\Route;
use  \App\Http\Controllers\BannerController;

//Route::get('/', function () {
//    return view('welcome');
//});

Route::get('/', [BannerController::class, 'index']);
Route::post('/generate-banner', [BannerController::class, 'generate'])->name('generate.banner');
