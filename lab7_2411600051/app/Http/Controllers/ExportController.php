<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    public function productsCsv()
    {
        $products = Product::with('supplier')->orderBy('name')->get();

        $callback = function () use ($products) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'SKU', 'Category', 'Quantity', 'Unit Price', 'Supplier']);

            foreach ($products as $p) {
                fputcsv($handle, [
                    $p->name, $p->sku, $p->category, $p->quantity,
                    $p->unit_price, $p->supplier->company_name ?? '—',
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="products_' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    public function suppliersCsv()
    {
        $suppliers = Supplier::orderBy('company_name')->get();

        $callback = function () use ($suppliers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Company', 'Contact', 'Email', 'Phone', 'Lead Time (days)', 'Status']);

            foreach ($suppliers as $s) {
                fputcsv($handle, [
                    $s->company_name, $s->contact_person, $s->email,
                    $s->phone, $s->lead_time_days, $s->is_active ? 'Active' : 'Inactive',
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="suppliers_' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    public function purchaseOrderPdf(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items.product']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.purchase-order-pdf', compact('purchaseOrder'));

        return $pdf->download($purchaseOrder->reference_number . '.pdf');
    }
}
