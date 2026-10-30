<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes; // Required for Lab 7

    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'payment_terms',
        'lead_time_days',
        'minimum_order_amount',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function products(): HasMany
    {
        // Assuming you will add supplier_id to products later, for now, let's point to a generic relationship
        return $this->hasMany(Product::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    // Query Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithLeadTime($query, $days)
    {
        return $query->where('lead_time_days', '<=', $days);
    }

    // Helper Methods
    public function getFullContactAttribute(): string
    {
        return "{$this->contact_person} ({$this->phone}) - {$this->email}";
    }

    public function getPerformanceRatingAttribute(): string
    {
        // Placeholder logic for now, will calculate based on PO history later
        return 'N/A';
    }
}