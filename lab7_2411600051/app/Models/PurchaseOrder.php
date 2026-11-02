<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference_number',
        'supplier_id',
        'status',
        'order_date',
        'expected_delivery_date',
        'received_at',
        'subtotal',
        'tax',
        'total',
        'notes',
        'terms',
        'rejection_reason',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'received_at' => 'datetime',
        'approved_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['received', 'cancelled', 'rejected']);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function calculateTotal(): float
    {
        $subtotal = $this->items()->get()->sum(function ($item) {
            return $item->quantity_ordered * $item->unit_price;
        });
        $tax = round($subtotal * 0.12, 2);
        $total = $subtotal + $tax;

        $this->update(['subtotal' => $subtotal, 'tax' => $tax, 'total' => $total]);

        return $total;
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeReceived(): bool
    {
        return in_array($this->status, ['sent', 'approved']);
    }

    public static function generateReferenceNumber(): string
    {
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(4));

        return "PO-{$date}-{$random}";
    }
}
