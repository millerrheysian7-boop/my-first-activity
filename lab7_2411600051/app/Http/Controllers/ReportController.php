<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function stockMovement(Request $request)
    {
        $query = InventoryTransaction::with(['product', 'user'])->recent();

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $transactions = $query->get();

        return view('reports.stock-movement', compact('transactions'));
    }

    public function lowStock()
    {
        $products = Product::lowStock()->orWhere(function ($q) {
            $q->outOfStock();
        })->orderBy('quantity')->get();

        return view('reports.low-stock', compact('products'));
    }

    public function valuation()
    {
        $products = Product::orderBy('category')->get();
        $totalValue = $products->sum(function ($p) {
            return $p->quantity * $p->unit_price;
        });

        $byCategory = $products->groupBy('category')->map(function ($group) {
            return [
                'count' => $group->count(),
                'value' => $group->sum(function ($p) { return $p->quantity * $p->unit_price; }),
            ];
        });

        return view('reports.valuation', compact('products', 'totalValue', 'byCategory'));
    }
}
