<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryTransaction extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'reference_number',
        'notes',
    ];

    // Relationship: A transaction belongs to a product
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Relationship: A transaction belongs to a user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scope: filter stock-in transactions
    public function scopeStockIn($query)
    {
        return $query->where('type', 'stock_in');
    }

    // Scope: filter stock-out transactions
    public function scopeStockOut($query)
    {
        return $query->where('type', 'stock_out');
    }

    // Scope: order by newest first
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}