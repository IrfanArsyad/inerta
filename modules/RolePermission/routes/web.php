<?php

use Illuminate\Support\Facades\Route;
use Modules\RolePermission\Http\Controllers\RoleController;

Route::middleware(['auth', 'module.access:2'])->prefix('roles')->group(function () {
    // Read operations
    Route::get('/', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/{id}', [RoleController::class, 'show'])->name('roles.show');

    // Create operations
    Route::middleware('module.permission:2,create')->group(function () {
        Route::get('/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/', [RoleController::class, 'store'])->name('roles.store');
    });

    // Update operations
    Route::middleware('module.permission:2,update')->group(function () {
        Route::get('/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/{id}', [RoleController::class, 'update'])->name('roles.update');
    });

    // Delete operations
    Route::delete('/{id}', [RoleController::class, 'destroy'])
        ->middleware('module.permission:2,delete')
        ->name('roles.destroy');
});
