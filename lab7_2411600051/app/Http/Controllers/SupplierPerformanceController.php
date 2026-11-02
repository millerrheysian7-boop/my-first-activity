<?php

namespace App\Http\Controllers;

use App\Models\Supplier;

class SupplierPerformanceController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::active()->get()->map(function ($supplier) {
            $orders = $supplier->purchaseOrders;
            $received = $orders->where('status', 'received');

            $onTime = $received->filter(function ($order) {
                return $order->received_at && $order->expected_delivery_date
                    && $order->received_at->lte($order->expected_delivery_date);
            })->count();

            $avgDeliveryDays = $received->filter(fn ($o) => $o->received_at && $o->order_date)
                ->avg(fn ($o) => $o->order_date->diffInDays($o->received_at));

            return [
                'supplier' => $supplier,
                'total_orders' => $orders->count(),
                'delivered_on_time' => $onTime,
                'avg_delivery_days' => $avgDeliveryDays ? round($avgDeliveryDays, 1) : null,
                'total_value' => $orders->sum('total'),
                'performance_rating' => $supplier->performance_rating,
            ];
        })->sortByDesc('performance_rating')->values();

        return view('suppliers.performance', compact('suppliers'));
    }
}
