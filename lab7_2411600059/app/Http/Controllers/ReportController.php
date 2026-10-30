<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // 1. Stock Movement Report
        $startDate = $request->filled('start_date') ? $request->start_date : now()->subDays(30)->format('Y-m-d');
        $endDate = $request->filled('end_date') ? $request->end_date : now()->format('Y-m-d');

        $movements = InventoryTransaction::with(['product', 'user'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->recent()
            ->get();

        // 2. Low Stock Report
        $lowStockProducts = Product::lowStock()->orderBy('quantity', 'asc')->get();

        // 3. Inventory Valuation Report
        $valuationByCategory = Product::selectRaw('category, SUM(quantity * unit_price) as total_value, COUNT(*) as product_count, SUM(quantity) as total_units')
            ->groupBy('category')
            ->orderBy('total_value', 'desc')
            ->get();

        $totalInventoryValue = Product::selectRaw('SUM(quantity * unit_price) as total')->value('total') ?? 0;

        return view('reports.index', compact(
            'movements', 'lowStockProducts', 'valuationByCategory',
            'totalInventoryValue', 'startDate', 'endDate'
        ));
    }

    // BAGONG METHOD PARA SA LAB 7
    public function supplierPerformance()
    {
        $suppliers = Supplier::with(['purchaseOrders'])->get()->map(function($supplier) {
            $totalOrders = $supplier->purchaseOrders->count();
            $receivedOrders = $supplier->purchaseOrders->where('status', 'received');
            
            // Calculate on-time delivery
            $onTimeOrders = $receivedOrders->filter(function($order) {
                return $order->expected_delivery_date && $order->updated_at->lte($order->expected_delivery_date);
            })->count();

            $onTimeRate = $receivedOrders->count() > 0 ? ($onTimeOrders / $receivedOrders->count()) * 100 : 0;
            $totalValue = $supplier->purchaseOrders->sum('total');

            $supplier->total_orders = $totalOrders;
            $supplier->on_time_rate = round($onTimeRate, 2);
            $supplier->total_value = $totalValue;

            return $supplier;
        })->sortByDesc('on_time_rate');

        return view('reports.supplier-performance', compact('suppliers'));
    }
}