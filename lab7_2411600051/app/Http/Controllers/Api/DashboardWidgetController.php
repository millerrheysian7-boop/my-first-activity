<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\ReorderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class DashboardWidgetController extends Controller
{
    public function pendingOrders(): JsonResponse
    {
        return response()->json(['count' => PurchaseOrder::pending()->count()]);
    }

    public function recentOrders(): JsonResponse
    {
        $orders = PurchaseOrder::with('supplier')->latest()->limit(5)->get()
            ->map(fn ($po) => [
                'reference_number' => $po->reference_number,
                'supplier' => $po->supplier->company_name ?? '—',
                'status' => $po->status,
            ]);

        return response()->json(['data' => $orders]);
    }

    public function supplierPerformance(): JsonResponse
    {
        $top = Cache::remember('widget.supplier-performance', 300, function () {
            return Supplier::active()->get()
                ->map(fn ($s) => ['name' => $s->company_name, 'rating' => $s->performance_rating])
                ->filter(fn ($row) => $row['rating'] !== null)
                ->sortByDesc('rating')
                ->take(5)
                ->values();
        });

        return response()->json(['data' => $top]);
    }

    public function reorderAlerts(ReorderService $reorderService): JsonResponse
    {
        $products = Cache::remember('widget.reorder-alerts', 120, function () use ($reorderService) {
            return $reorderService->getProductsNeedingReorder()->take(10)
                ->map(fn ($p) => ['name' => $p->name, 'quantity' => $p->quantity])
                ->values();
        });

        return response()->json(['data' => $products]);
    }
}
