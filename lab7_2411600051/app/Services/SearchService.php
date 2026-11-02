<?php

namespace App\Services;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;

class SearchService
{
    public function searchProducts(array $filters)
    {
        $query = Product::query()->with('supplier');

        if (! empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('sku', 'like', "%{$keyword}%");
            });
        }

        if (! empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (! empty($filters['supplier_id'])) {
            $query->where('supplier_id', $filters['supplier_id']);
        }

        if (! empty($filters['min_price'])) {
            $query->where('unit_price', '>=', $filters['min_price']);
        }

        if (! empty($filters['max_price'])) {
            $query->where('unit_price', '<=', $filters['max_price']);
        }

        if (! empty($filters['stock_status'])) {
            match ($filters['stock_status']) {
                'low' => $query->whereColumn('quantity', '<=', 'reorder_level')->where('quantity', '>', 0),
                'out' => $query->where('quantity', '<=', 0),
                'in' => $query->whereColumn('quantity', '>', 'reorder_level'),
                default => null,
            };
        }

        return $query->paginate(15)->withQueryString();
    }

    public function searchSuppliers(array $filters)
    {
        $query = Supplier::query();

        if (! empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('company_name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        return $query->paginate(15)->withQueryString();
    }

    public function searchPurchaseOrders(array $filters)
    {
        $query = PurchaseOrder::query()->with(['supplier', 'createdBy']);

        if (! empty($filters['keyword'])) {
            $query->where('reference_number', 'like', '%' . $filters['keyword'] . '%');
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['supplier_id'])) {
            $query->where('supplier_id', $filters['supplier_id']);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('order_date', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('order_date', '<=', $filters['date_to']);
        }

        return $query->latest()->paginate(15)->withQueryString();
    }
}
