<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\ReorderService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        View::composer('widgets.pending-orders', function ($view) {
            $view->with('pendingCount', PurchaseOrder::pending()->count());
        });

        View::composer('widgets.recent-orders', function ($view) {
            $view->with('recentOrders', PurchaseOrder::with('supplier')->latest()->limit(5)->get());
        });

        View::composer('widgets.supplier-performance', function ($view) {
            $topSuppliers = Cache::remember('widget.supplier-performance', 300, function () {
                return Supplier::active()->get()
                    ->map(function ($s) {
                        return ['supplier' => $s, 'rating' => $s->performance_rating];
                    })
                    ->filter(fn ($row) => $row['rating'] !== null)
                    ->sortByDesc('rating')
                    ->take(5);
            });

            $view->with('topSuppliers', $topSuppliers);
        });

        View::composer('widgets.reorder-alerts', function ($view) {
            $products = Cache::remember('widget.reorder-alerts', 120, function () {
                return app(ReorderService::class)->getProductsNeedingReorder()->take(10);
            });

            $view->with('reorderProducts', $products);
        });
    }
}
