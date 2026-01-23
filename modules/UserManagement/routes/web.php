<?php

use Illuminate\Support\Facades\Route;
use Modules\UserManagement\Http\Controllers\UserController;

Route::middleware(['auth', 'module.access:1'])->prefix('users')->group(function () {
    // Read operations
    Route::get('/', [UserController::class, 'index'])->name('users.index');
    Route::get('/{id}', [UserController::class, 'show'])->name('users.show');

    // Create operations
    Route::middleware('module.permission:1,create')->group(function () {
        Route::get('/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
    });

    // Update operations
    Route::middleware('module.permission:1,update')->group(function () {
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('users.update');
    });

    // Delete operations
    Route::delete('/{id}', [UserController::class, 'destroy'])
        ->middleware('module.permission:1,delete')
        ->name('users.destroy');
});
