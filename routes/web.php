<?php

use Illuminate\Support\Facades\Route;
use Uspdev\SenhaunicaSocialite\Http\Controllers\SenhaunicaController;
use Uspdev\SenhaunicaSocialite\Http\Controllers\UserController;

Route::get('login', [SenhaunicaController::class, 'redirectToProvider']);
Route::get('callback', [SenhaunicaController::class, 'handleProviderCallback']);
Route::post('logout', [SenhaunicaController::class, 'logout']);

Route::get('loginas', [UserController::class, 'loginAsForm']);
Route::post('loginas', [UserController::class, 'loginAs'])->name('SenhaunicaLoginAs');

Route::get(config('senhaunica.userRoutes'), [UserController::class, 'users']);
Route::get(config('senhaunica.userRoutes') . '/{id}/jsonModalContent', [UserController::class, 'getJsonModalContent'])->name('getJsonModalContent');
Route::post(config('senhaunica.userRoutes') . '/{id}/updatePermission', [UserController::class, 'updatePermission'])->name('SenhaunicaUpdatePermission');
