<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockAdjustmentController extends Controller
{
    public function create()
    {
        $products = Product::orderBy('name')->get();
        $reasons = [
            'physical_count' => 'Physical Count Discrepancy',
            'damaged' => 'Damaged Goods',
            'theft_loss' => 'Theft / Loss',
            'system_error' => 'System Error Correction',
            'other' => 'Other',
        ];

        return view('transactions.adjustment', compact('products', 'reasons'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'physical_count' => ['required', 'integer', 'min:0'],
            'reason' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $difference = $validated['physical_count'] - $product->quantity;

        if ($difference === 0) {
            return back()->withInput()->with('success', 'No adjustment needed — physical count matches system count.');
        }

        InventoryTransaction::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'type' => 'adjustment',
            'quantity' => $difference,
            'reference_number' => null,
            'notes' => 'Reason: ' . $validated['reason'] . '. ' . ($validated['notes'] ?? ''),
        ]);
        // Observer updates product quantity automatically.

        return redirect()->route('transactions.index')
            ->with('success', "Stock adjusted by {$difference} units for {$product->name}.");
    }

    /**
     * AJAX endpoint used by the adjustment form to load the current
     * system quantity for a selected product without a full page reload.
     */
    public function currentQuantity(Product $product)
    {
        return response()->json(['quantity' => $product->quantity]);
    }
}
