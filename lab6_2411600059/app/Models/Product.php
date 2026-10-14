<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'description',
        'category',
        'quantity',
        'reorder_level',
        'unit_price',
        'supplier',
    ];

    // ===== Relationships =====
    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function latestTransaction(): HasOne
    {
        return $this->hasOne(InventoryTransaction::class)->latestOfMany();
    }

    // ===== Helper Methods =====
    public function isLowStock(): bool
    {
        return $this->quantity <= $this->reorder_level && $this->quantity > 0;
    }

    public function isOutOfStock(): bool
    {
        return $this->quantity <= 0;
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->isOutOfStock()) {
            return 'Out of Stock';
        }
        if ($this->isLowStock()) {
            return 'Low Stock';
        }
        return 'In Stock';
    }

    public function getStockStatusColorAttribute(): string
    {
        if ($this->isOutOfStock()) {
            return 'red';
        }
        if ($this->isLowStock()) {
            return 'yellow';
        }
        return 'green';
    }

    // ===== Query Scopes =====
    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity', '<=', 'reorder_level')
                     ->where('quantity', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('quantity', '<=', 0);
    }

    public function scopeInCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}