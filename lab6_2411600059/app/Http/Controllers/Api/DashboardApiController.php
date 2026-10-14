<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryTransaction;
use App\Models\Product;

class DashboardApiController extends Controller
{
    public function stats()
    {
        return response()->json([
            'total_products' => Product::count(),
            'low_stock' => Product::lowStock()->count(),
            'out_of_stock' => Product::outOfStock()->count(),
            'inventory_value' => (float) Product::selectRaw('SUM(quantity * unit_price) as total')->value('total') ?? 0,
            'last_updated' => now()->format('M d, Y h:i:s A'),
        ]);
    }

    public function lowStockProducts()
    {
        return response()->json(
            Product::lowStock()->select('id', 'name', 'sku', 'quantity', 'reorder_level')->get()
        );
    }

    public function recentTransactions()
    {
        return response()->json(
            InventoryTransaction::with(['product:id,name', 'user:id,name'])
                ->recent()
                ->take(5)
                ->get()
                ->map(function ($t) {
                    return [
                        'id' => $t->id,
                        'product' => $t->product->name ?? 'N/A',
                        'type' => $t->type,
                        'quantity' => $t->quantity,
                        'user' => $t->user->name ?? 'N/A',
                        'date' => $t->created_at->format('M d, H:i'),
                    ];
                })
        );
    }
}