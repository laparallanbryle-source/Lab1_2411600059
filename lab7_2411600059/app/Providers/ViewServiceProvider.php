<?php

namespace App\Providers;

use App\Models\PurchaseOrder;
use App\Models\Product;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Magpapasa tayo ng data sa 'dashboard' view
        View::composer('dashboard', function ($view) {
            $pendingOrdersCount = PurchaseOrder::pending()->count();
            $recentOrders = PurchaseOrder::with('supplier')->latest()->take(5)->get();
            $lowStockProducts = Product::lowStock()->take(5)->get();

            $view->with([
                'widgetPendingOrders' => $pendingOrdersCount,
                'widgetRecentOrders' => $recentOrders,
                'widgetLowStock' => $lowStockProducts,
            ]);
        });
    }
}