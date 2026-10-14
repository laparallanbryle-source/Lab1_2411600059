<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // 1. Stock Movement Report (filterable by date)
        $startDate = $request->filled('start_date') ? $request->start_date : now()->subDays(30)->format('Y-m-d');
        $endDate = $request->filled('end_date') ? $request->end_date : now()->format('Y-m-d');

        $movements = InventoryTransaction::with(['product', 'user'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->recent()
            ->get();

        // 2. Low Stock Report
        $lowStockProducts = Product::lowStock()->orderBy('quantity', 'asc')->get();

        // 3. Inventory Valuation Report (by category)
        $valuationByCategory = Product::selectRaw('category, SUM(quantity * unit_price) as total_value, COUNT(*) as product_count, SUM(quantity) as total_units')
            ->groupBy('category')
            ->orderBy('total_value', 'desc')
            ->get();

        $totalInventoryValue = Product::selectRaw('SUM(quantity * unit_price) as total')->value('total') ?? 0;

        return view('reports.index', compact(
            'movements',
            'lowStockProducts',
            'valuationByCategory',
            'totalInventoryValue',
            'startDate',
            'endDate'
        ));
    }
}