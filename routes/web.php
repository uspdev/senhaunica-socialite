<?php

use Illuminate\Support\Facades\Route;
use Uspdev\SenhaunicaSocialite\Http\Controllers\SenhaunicaController;

Route::get('login', [SenhaunicaController::class, 'redirectToProvider']);
Route::get('callback', [SenhaunicaController::class, 'handleProviderCallback']);
Route::post('logout', [SenhaunicaController::class, 'logout']);