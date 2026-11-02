<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $sort = $request->get('sort', 'company_name');
        $direction = $request->get('direction', 'asc');
        $query->orderBy($sort, $direction);

        $suppliers = $query->paginate(15)->withQueryString();

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateSupplier($request);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')->with('success', 'Supplier added successfully.');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load(['purchaseOrders' => function ($q) {
            $q->latest()->limit(10);
        }, 'products']);

        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $this->validateSupplier($request, $supplier->id);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $pendingOrders = $supplier->purchaseOrders()
            ->whereIn('status', ['draft', 'pending', 'approved', 'sent'])
            ->count();

        if ($pendingOrders > 0) {
            return back()->with('error', "Cannot delete: this supplier has {$pendingOrders} active purchase order(s).");
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted (soft delete). Historical orders are preserved.');
    }

    private function validateSupplier(Request $request, $ignoreId = null): array
    {
        return $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:suppliers,email' . ($ignoreId ? ",{$ignoreId}" : '')],
            'phone' => ['nullable', 'regex:/^[0-9+\-\s()]{7,20}$/'],
            'address' => ['nullable', 'string'],
            'payment_terms' => ['required', 'in:Net 15,Net 30,Net 60,COD'],
            'lead_time_days' => ['required', 'integer', 'min:1'],
            'minimum_order_amount' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}
