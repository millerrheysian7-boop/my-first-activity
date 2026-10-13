<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class InventoryTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryTransaction::with(['product', 'user'])->recent();

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $transactions = $query->paginate(20)->withQueryString();
        $products = Product::orderBy('name')->get();

        return view('transactions.index', compact('transactions', 'products'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();
        $types = ['stock_in' => 'Stock In', 'stock_out' => 'Stock Out', 'adjustment' => 'Adjustment'];

        return view('transactions.create', compact('products', 'types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'type' => ['required', Rule::in(['stock_in', 'stock_out', 'adjustment'])],
            'quantity' => ['required', 'integer', 'not_in:0'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Normalize the sign: stock_out is always a decrease, stock_in is
        // always an increase. The user enters a positive number either way;
        // we flip the sign here so it's stored consistently.
        $signedQuantity = match ($validated['type']) {
            'stock_in' => abs($validated['quantity']),
            'stock_out' => -abs($validated['quantity']),
            'adjustment' => $validated['quantity'], // can be positive or negative
            default => $validated['quantity'],
        };

        if ($validated['type'] === 'stock_out' && $product->quantity < abs($signedQuantity)) {
            return back()
                ->withInput()
                ->withErrors(['quantity' => 'Insufficient stock. Only ' . $product->quantity . ' units available.']);
        }

        InventoryTransaction::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'type' => $validated['type'],
            'quantity' => $signedQuantity,
            'reference_number' => $validated['reference_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);
        // The observer (InventoryTransactionObserver) automatically updates
        // the product's quantity and fires a low-stock notification if needed.

        return redirect()->route('transactions.index')->with('success', 'Transaction recorded successfully.');
    }

    public function show(InventoryTransaction $transaction)
    {
        $transaction->load(['product', 'user']);

        return view('transactions.show', compact('transaction'));
    }

    public function destroy(InventoryTransaction $transaction)
    {
        // Deleting a transaction does NOT automatically reverse the stock
        // change (no deleting/deleted observer hook is defined), so we
        // reverse it explicitly here before removing the record. This keeps
        // the decision visible and intentional rather than implicit magic.
        $product = $transaction->product;
        if ($product) {
            $product->quantity -= $transaction->quantity;
            $product->quantity = max(0, $product->quantity);
            $product->save();
        }

        $transaction->delete();

        return redirect()->route('transactions.index')->with('success', 'Transaction deleted and stock reversed.');
    }
}
