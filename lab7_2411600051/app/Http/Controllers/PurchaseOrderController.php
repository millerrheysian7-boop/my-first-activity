<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Mail\PurchaseOrderApproved;
use App\Mail\PurchaseOrderCreated;
use App\Mail\PurchaseOrderSent;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', PurchaseOrder::class);

        $query = PurchaseOrder::with(['supplier', 'createdBy']);

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        if ($request->filled('from')) {
            $query->whereDate('order_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('order_date', '<=', $request->to);
        }

        $orders = $query->latest()->paginate(15)->withQueryString();
        $suppliers = Supplier::active()->orderBy('company_name')->get();

        return view('purchase-orders.index', compact('orders', 'suppliers'));
    }

    public function create()
    {
        $this->authorize('create', PurchaseOrder::class);

        $suppliers = Supplier::active()->orderBy('company_name')->get();
        $products = Product::orderBy('name')->get();

        return view('purchase-orders.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', PurchaseOrder::class);

        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'expected_delivery_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $supplier = Supplier::findOrFail($validated['supplier_id']);
        if (! $supplier->is_active) {
            return back()->withInput()->withErrors(['supplier_id' => 'This supplier is inactive.']);
        }

        $order = DB::transaction(function () use ($validated) {
            $order = PurchaseOrder::create([
                'reference_number' => PurchaseOrder::generateReferenceNumber(),
                'supplier_id' => $validated['supplier_id'],
                'status' => 'pending',
                'order_date' => now(),
                'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            foreach ($validated['items'] as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity_ordered' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }

            $order->calculateTotal();

            return $order;
        });

        foreach (\App\Models\User::where('role', 'admin')->get() as $admin) {
            Mail::to($admin->email)->send(new PurchaseOrderCreated($order));
        }

        return redirect()->route('purchase-orders.show', $order)->with('success', 'Purchase order created.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $this->authorize('view', $purchaseOrder);

        $purchaseOrder->load(['supplier', 'items.product', 'createdBy', 'approvedBy']);

        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        $this->authorize('update', $purchaseOrder);

        $suppliers = Supplier::active()->orderBy('company_name')->get();
        $products = Product::orderBy('name')->get();
        $purchaseOrder->load('items');

        return view('purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'products'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->authorize('update', $purchaseOrder);

        $validated = $request->validate([
            'expected_delivery_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $purchaseOrder->update($validated);

        return redirect()->route('purchase-orders.show', $purchaseOrder)->with('success', 'Purchase order updated.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $this->authorize('delete', $purchaseOrder);

        $purchaseOrder->update(['status' => 'cancelled']);
        $purchaseOrder->delete();

        return redirect()->route('purchase-orders.index')->with('success', 'Purchase order cancelled and deleted.');
    }

    public function approve(PurchaseOrder $purchaseOrder)
    {
        $this->authorize('approve', $purchaseOrder);

        $purchaseOrder->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        if ($purchaseOrder->createdBy) {
            Mail::to($purchaseOrder->createdBy->email)->send(new PurchaseOrderApproved($purchaseOrder));
        }

        return back()->with('success', 'Purchase order approved.');
    }

    public function reject(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->authorize('approve', $purchaseOrder);

        $validated = $request->validate(['rejection_reason' => ['required', 'string', 'max:500']]);

        $purchaseOrder->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return back()->with('success', 'Purchase order rejected.');
    }

    public function send(PurchaseOrder $purchaseOrder)
    {
        $this->authorize('update', $purchaseOrder);

        $purchaseOrder->update(['status' => 'sent']);

        if ($purchaseOrder->supplier && $purchaseOrder->supplier->email) {
            Mail::to($purchaseOrder->supplier->email)->send(new PurchaseOrderSent($purchaseOrder));
        }

        return back()->with('success', 'Purchase order marked as sent to supplier.');
    }

    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->authorize('receive', $purchaseOrder);

        $validated = $request->validate([
            'received_quantities' => ['required', 'array'],
            'received_quantities.*' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($validated, $purchaseOrder) {
            foreach ($purchaseOrder->items as $item) {
                $receivedQty = $validated['received_quantities'][$item->id] ?? 0;
                if ($receivedQty <= 0) {
                    continue;
                }

                $item->update(['quantity_received' => $item->quantity_received + $receivedQty]);

                InventoryTransaction::create([
                    'product_id' => $item->product_id,
                    'user_id' => Auth::id(),
                    'type' => 'stock_in',
                    'quantity' => $receivedQty,
                    'reference_number' => $purchaseOrder->reference_number,
                    'notes' => 'Received from purchase order ' . $purchaseOrder->reference_number,
                ]);
            }

            $allReceived = $purchaseOrder->items()->get()->every(function ($item) {
                return $item->quantity_received >= $item->quantity_ordered;
            });

            $purchaseOrder->update([
                'status' => $allReceived ? 'received' : $purchaseOrder->status,
                'received_at' => $allReceived ? now() : $purchaseOrder->received_at,
            ]);
        });

        return back()->with('success', 'Receipt recorded and inventory updated.');
    }
}
