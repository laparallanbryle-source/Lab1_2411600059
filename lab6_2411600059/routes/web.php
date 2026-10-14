<?php

use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryTransactionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('products', ProductController::class);

    // Stock Adjustment routes — DAPAT nasa TAAS ng resource
    Route::get('transactions/adjustment', [InventoryTransactionController::class, 'adjustmentForm'])->name('transactions.adjustment');
    Route::post('transactions/adjustment', [InventoryTransactionController::class, 'processAdjustment'])->name('transactions.adjustment.store');

    Route::resource('transactions', InventoryTransactionController::class)
        ->except(['edit', 'update']);

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

    // API routes
    Route::prefix('api')->group(function () {
        Route::get('/dashboard/stats', [DashboardApiController::class, 'stats']);
        Route::get('/products/low-stock', [DashboardApiController::class, 'lowStockProducts']);
        Route::get('/transactions/recent', [DashboardApiController::class, 'recentTransactions']);
    });
});

require __DIR__.'/auth.php';