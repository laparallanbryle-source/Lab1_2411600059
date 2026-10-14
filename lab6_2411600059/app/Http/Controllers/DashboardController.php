<?php

namespace App\Http\Controllers;

use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $lowStock = Product::whereColumn('quantity', '<=', 'reorder_level')
                            ->where('quantity', '>', 0)
                            ->count();
        $outOfStock = Product::where('quantity', 0)->count();
        $inventoryValue = Product::selectRaw('SUM(quantity * unit_price) as total')
                            ->value('total') ?? 0;
        $recentProducts = Product::latest()->take(5)->get();

        return view('dashboard', compact(
            'totalProducts', 'lowStock', 'outOfStock',
            'inventoryValue', 'recentProducts'
        ));
    }
}