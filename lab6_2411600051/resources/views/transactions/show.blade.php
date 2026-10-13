@extends('layouts.app')

@section('title', 'Transaction Details')

@section('content')
    <h2 class="mb-4">Transaction #{{ $transaction->id }}</h2>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header">Transaction</div>
                <div class="card-body">
                    <p><strong>Type:</strong> {{ str_replace('_', ' ', $transaction->type) }}</p>
                    <p><strong>Quantity:</strong> {{ $transaction->quantity > 0 ? '+' : '' }}{{ $transaction->quantity }}</p>
                    <p><strong>Reference #:</strong> {{ $transaction->reference_number ?: '—' }}</p>
                    <p><strong>Notes:</strong> {{ $transaction->notes ?: '—' }}</p>
                    <p class="mb-0"><strong>Date:</strong> {{ $transaction->created_at->format('M d, Y h:i A') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header">Product</div>
                <div class="card-body">
                    @if ($transaction->product)
                        <p><strong>Name:</strong> {{ $transaction->product->name }}</p>
                        <p><strong>SKU:</strong> {{ $transaction->product->sku }}</p>
                        <p><strong>Current Quantity:</strong> {{ $transaction->product->quantity }}</p>
                        <a href="{{ route('products.show', $transaction->product) }}" class="btn btn-sm btn-outline-primary">View Product</a>
                    @else
                        <p class="text-muted mb-0">This product has been deleted.</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header">Recorded By</div>
                <div class="card-body">
                    <p class="mb-0">{{ $transaction->user->name ?? 'Unknown user' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">Back to List</a>
        <form method="POST" action="{{ route('transactions.destroy', $transaction) }}" class="d-inline"
              onsubmit="return confirm('Delete this transaction? This will reverse the stock change.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
    </div>
@endsection
