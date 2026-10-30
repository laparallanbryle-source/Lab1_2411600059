<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PurchaseOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference_number',
        'supplier_id',
        'status',
        'order_date',
        'expected_delivery_date',
        'subtotal',
        'tax',
        'total',
        'notes',
        'created_by',
        'approved_by',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // ===== Relationships =====
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ===== Query Scopes =====
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['received', 'cancelled']);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // ===== Helper Methods =====
    public function calculateTotal(): float
    {
        $this->subtotal = $this->items->sum(fn($item) => $item->quantity_ordered * $item->unit_price);
        $this->tax = $this->subtotal * 0.12; // 12% VAT
        $this->total = $this->subtotal + $this->tax;
        $this->save();

        return $this->total;
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeReceived(): bool
    {
        return in_array($this->status, ['approved', 'sent']);
    }

    public function generateReferenceNumber(): string
    {
        return 'PO-' . strtoupper(Str::random(8));
    }
}