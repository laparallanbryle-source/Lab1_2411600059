<?php

use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\InventoryTransactionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () { return redirect()->route('dashboard'); });

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('products', ProductController::class);
    Route::resource('suppliers', SupplierController::class);

    // Purchase Orders
    Route::post('purchase-orders/{purchase_order}/approve', [PurchaseOrderController::class, 'approve'])->name('purchase-orders.approve');
    Route::post('purchase-orders/{purchase_order}/reject', [PurchaseOrderController::class, 'reject'])->name('purchase-orders.reject');
    Route::post('purchase-orders/{purchase_order}/send', [PurchaseOrderController::class, 'send'])->name('purchase-orders.send');
    Route::post('purchase-orders/{purchase_order}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');
    Route::resource('purchase-orders', PurchaseOrderController::class);

    // Transactions
    Route::get('transactions/adjustment', [InventoryTransactionController::class, 'adjustmentForm'])->name('transactions.adjustment');
    Route::post('transactions/adjustment', [InventoryTransactionController::class, 'processAdjustment'])->name('transactions.adjustment.store');
    Route::resource('transactions', InventoryTransactionController::class)->except(['edit', 'update']);

    // Reports & Exports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/supplier-performance', [ReportController::class, 'supplierPerformance'])->name('reports.supplier-performance');
    Route::get('export/products', [ExportController::class, 'exportProducts'])->name('export.products');
    Route::get('export/purchase-orders', [ExportController::class, 'exportPurchaseOrders'])->name('export.purchase-orders');
    Route::get('export/pdf', [ExportController::class, 'exportPdf'])->name('export.pdf');

    // API
    Route::prefix('api')->group(function () {
        Route::get('/dashboard/stats', [DashboardApiController::class, 'stats']);
        Route::get('/products/low-stock', [DashboardApiController::class, 'lowStockProducts']);
        Route::get('/transactions/recent', [DashboardApiController::class, 'recentTransactions']);
        Route::get('/dashboard/pending-orders', [DashboardApiController::class, 'pendingOrders']);
        Route::get('/dashboard/recent-orders', [DashboardApiController::class, 'recentOrders']);
        Route::get('/dashboard/reorder-alerts', [DashboardApiController::class, 'reorderAlerts']);
    });
});

require __DIR__.'/auth.php';