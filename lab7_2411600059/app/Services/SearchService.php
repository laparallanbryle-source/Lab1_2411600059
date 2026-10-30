<?php

namespace App\Services;

use App\Models\Product;
use App\Models\PurchaseOrder;

class SearchService
{
    public function searchProducts($query)
    {
        return Product::where('name', 'like', "%{$query}%")
            ->orWhere('sku', 'like', "%{$query}%")
            ->orWhere('category', 'like', "%{$query}%")
            ->get();
    }

    public function searchPurchaseOrders($query)
    {
        return PurchaseOrder::where('reference_number', 'like', "%{$query}%")
            ->with('supplier')
            ->get();
    }
}