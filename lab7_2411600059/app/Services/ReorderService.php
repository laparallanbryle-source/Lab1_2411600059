<?php

namespace App\Services;

use App\Models\Product;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReorderService
{
    public function calculateReorderPoint(Product $product): int
    {
        $averageDailyUsage = $this->calculateAverageDailyUsage($product);
        $leadTime = $product->supplier->lead_time_days ?? 7;
        $safetyStock = $product->reorder_level ?? 10;
        return ceil(($averageDailyUsage * $leadTime) + $safetyStock);
    }

    private function calculateAverageDailyUsage(Product $product): float
    {
        $thirtyDaysAgo = now()->subDays(30);
        $totalUsage = $product->transactions()
            ->where('type', 'stock_out')
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->sum('quantity');
        return $totalUsage > 0 ? ($totalUsage / 30) : 1;
    }

    public function getProductsNeedingReorder()
    {
        return Product::with('supplier')
            ->whereNotNull('supplier_id')
            ->get()
            ->filter(function ($product) {
                $calculatedReorderPoint = $this->calculateReorderPoint($product);
                return $product->quantity <= $calculatedReorderPoint;
            });
    }

    public function generatePurchaseOrders(): array
    {
        $products = $this->getProductsNeedingReorder();
        $generatedPOs = [];
        $groupedBySupplier = $products->groupBy('supplier_id');

        foreach ($groupedBySupplier as $supplierId => $items) {
            DB::transaction(function () use ($supplierId, $items, &$generatedPOs) {
                $po = PurchaseOrder::create([
                    'reference_number' => 'AUTO-PO-' . strtoupper(Str::random(6)),
                    'supplier_id' => $supplierId,
                    'status' => 'pending',
                    'order_date' => now(),
                    'notes' => 'Automated Reorder System',
                    'created_by' => Auth::id() ?? 1, 
                ]);

                foreach ($items as $product) {
                    $orderQuantity = ($product->reorder_level * 2) - $product->quantity;
                    if ($orderQuantity <= 0) $orderQuantity = 10;

                    $po->items()->create([
                        'product_id' => $product->id,
                        'quantity_ordered' => $orderQuantity,
                        'unit_price' => $product->unit_price,
                    ]);
                }
                $po->calculateTotal();
                $generatedPOs[] = $po->reference_number;
            });
        }
        return $generatedPOs;
    }
}