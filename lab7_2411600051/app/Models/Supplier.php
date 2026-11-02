<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_name',
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
        'lead_time_days' => 'integer',
        'minimum_order_amount' => 'decimal:2',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithLeadTime($query, $days)
    {
        return $query->where('lead_time_days', '<=', $days);
    }

    public function getFullContactAttribute(): string
    {
        $parts = array_filter([$this->contact_person, $this->email, $this->phone]);

        return implode(' | ', $parts);
    }

    public function getPerformanceRatingAttribute(): ?float
    {
        $received = $this->purchaseOrders()->where('status', 'received')->get();

        if ($received->isEmpty()) {
            return null;
        }

        $onTime = $received->filter(function ($order) {
            return $order->received_at && $order->expected_delivery_date
                && $order->received_at->lte($order->expected_delivery_date);
        })->count();

        return round(($onTime / $received->count()) * 100, 1);
    }
}
