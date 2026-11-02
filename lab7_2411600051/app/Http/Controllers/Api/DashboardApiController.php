<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class DashboardApiController extends Controller
{
    public function stats(): JsonResponse
    {
        return response()->json([
            'total_products' => Product::count(),
            'low_stock_count' => Product::lowStock()->count(),
            'out_of_stock_count' => Product::outOfStock()->count(),
            'total_value' => round(Product::selectRaw('SUM(quantity * unit_price) as total')->value('total') ?? 0, 2),
            'generated_at' => now()->toIso8601String(),
        ]);
    }

    public function lowStockProducts(): JsonResponse
    {
        $products = Product::lowStock()
            ->orderBy('quantity')
            ->get(['id', 'name', 'sku', 'quantity', 'reorder_level', 'category']);

        return response()->json(['data' => $products]);
    }

    public function recentTransactions(): JsonResponse
    {
        $transactions = InventoryTransaction::with(['product:id,name,sku', 'user:id,name'])
            ->recent()
            ->limit(10)
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'product_name' => $t->product->name ?? 'Deleted product',
                    'type' => $t->type,
                    'quantity' => $t->quantity,
                    'user_name' => $t->user->name ?? 'Unknown',
                    'created_at' => $t->created_at->diffForHumans(),
                ];
            });

        return response()->json(['data' => $transactions]);
    }
}
