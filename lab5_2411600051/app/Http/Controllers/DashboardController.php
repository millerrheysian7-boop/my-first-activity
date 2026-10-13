<?php

namespace App\Http\Controllers;

use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $lowStockCount = Product::whereColumn('quantity', '<=', 'reorder_level')
            ->where('quantity', '>', 0)
            ->count();
        $outOfStockCount = Product::where('quantity', '<=', 0)->count();
        $totalValue = Product::selectRaw('SUM(quantity * unit_price) as total')->value('total') ?? 0;

        $recentProducts = Product::latest()->take(5)->get();

        $categorySummary = Product::selectRaw('category, COUNT(*) as count, SUM(quantity * unit_price) as value')
            ->groupBy('category')
            ->get();

        return view('dashboard', compact(
            'totalProducts',
            'lowStockCount',
            'outOfStockCount',
            'totalValue',
            'recentProducts',
            'categorySummary'
        ));
    }
}
