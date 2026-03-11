<?php

use Uspdev\SenhaunicaSocialite\Http\Controllers\SenhaunicaController;
use Uspdev\SenhaunicaSocialite\Http\Controllers\UserController;
use Uspdev\SenhaunicaSocialite\Http\Controllers\LocalUserController;
use Uspdev\SenhaunicaSocialite\Http\Controllers\LoginLocalController;

Route::get('login', [SenhaunicaController::class, 'redirectToProvider'])->name('login');
Route::get('callback', [SenhaunicaController::class, 'handleProviderCallback']);
Route::post('logout', [SenhaunicaController::class, 'logout'])->name('logout');

if (!config('senhaunica.disableLoginas')) {
    Route::get('loginas', [UserController::class, 'loginAsForm'])->name('SenhaunicaLoginAsForm');
    Route::post('loginas', [UserController::class, 'loginAs'])->name('SenhaunicaLoginAs');
    Route::get('undologinas', [UserController::class, 'undoLoginAs'])->name('SenhaunicaUndoLoginAs');
}

if (config('senhaunica.userRoutes')) {
    Route::get(config('senhaunica.userRoutes') . '/find', [UserController::class, 'find'])->name('SenhaunicaFindUsers');
    Route::get(config('senhaunica.userRoutes') . '/{id}/jsonModalContent', [UserController::class, 'getJsonModalContent'])->name('SenhaunicaGetJsonModalContent');
    Route::post(config('senhaunica.userRoutes') . '/{id}/updatePermission', [UserController::class, 'updatePermission'])->name('SenhaunicaUpdatePermission');
    Route::resource(config('senhaunica.userRoutes'), UserController::class);
}

Route::get('loginlocal', [LoginLocalController::class, 'create'])->name('loginlocal');
if (config('senhaunica.localUserRoutes')) {
    Route::post('loginlocal', [LoginLocalController::class, 'store'])->name('SenhaunicaLocalLoginAs');
    Route::resource(config('senhaunica.localUserRoutes'), LocalUserController::class)->only(['store', 'edit', 'update']);
}
