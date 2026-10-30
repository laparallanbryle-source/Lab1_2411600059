<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'quantity_ordered',
        'unit_price',
        'quantity_received',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
    ];

    // ===== Relationships =====
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // ===== Helper Methods (Accessors) =====
    public function getLineTotalAttribute(): float
    {
        return $this->quantity_ordered * $this->unit_price;
    }

    public function getOutstandingQuantityAttribute(): int
    {
        return $this->quantity_ordered - $this->quantity_received;
    }
}