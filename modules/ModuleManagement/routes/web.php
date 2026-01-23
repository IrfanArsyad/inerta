<?php

use Illuminate\Support\Facades\Route;
use Modules\ModuleManagement\Http\Controllers\ModuleController;

Route::middleware(['auth', 'module.access:3'])->prefix('modules')->group(function () {
    // Read operations
    Route::get('/', [ModuleController::class, 'index'])->name('modules.index');
    Route::get('/{id}', [ModuleController::class, 'show'])->name('modules.show');

    // Create operations
    Route::middleware('module.permission:3,create')->group(function () {
        Route::get('/create', [ModuleController::class, 'create'])->name('modules.create');
        Route::post('/', [ModuleController::class, 'store'])->name('modules.store');
    });

    // Update operations
    Route::middleware('module.permission:3,update')->group(function () {
        Route::get('/{id}/edit', [ModuleController::class, 'edit'])->name('modules.edit');
        Route::put('/{id}', [ModuleController::class, 'update'])->name('modules.update');
    });

    // Delete operations
    Route::delete('/{id}', [ModuleController::class, 'destroy'])
        ->middleware('module.permission:3,delete')
        ->name('modules.destroy');
});
