@extends('layouts.app')

@section('title', 'Transactions')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Inventory Transactions</h2>
        <a href="{{ route('transactions.create') }}" class="btn btn-primary">+ Record Transaction</a>
    </div>

    <form method="GET" action="{{ route('transactions.index') }}" class="row g-2 mb-3">
        <div class="col-md-3">
            <select name="product_id" class="form-select">
                <option value="">All Products</option>
                @foreach ($products as $p)
                    <option value="{{ $p->id }}" @selected(request('product_id') == $p->id)>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="type" class="form-select">
                <option value="">All Types</option>
                <option value="stock_in" @selected(request('type') == 'stock_in')>Stock In</option>
                <option value="stock_out" @selected(request('type') == 'stock_out')>Stock Out</option>
                <option value="adjustment" @selected(request('type') == 'adjustment')>Adjustment</option>
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" name="from" class="form-control" value="{{ request('from') }}" placeholder="From">
        </div>
        <div class="col-md-2">
            <input type="date" name="to" class="form-control" value="{{ request('to') }}" placeholder="To">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-outline-secondary w-100">Filter</button>
        </div>
    </form>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Date/Time</th>
                    <th>Product</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Reference #</th>
                    <th>User</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $t)
                    <tr>
                        <td>{{ $t->created_at->format('M d, Y h:i A') }}</td>
                        <td><a href="{{ route('products.show', $t->product_id) }}">{{ $t->product->name ?? 'Deleted' }}</a></td>
                        <td>
                            @php
                                $badge = match($t->type) {
                                    'stock_in' => 'bg-success',
                                    'stock_out' => 'bg-danger',
                                    default => 'bg-warning',
                                };
                            @endphp
                            <span class="badge {{ $badge }}">{{ str_replace('_', ' ', $t->type) }}</span>
                        </td>
                        <td>{{ $t->quantity > 0 ? '+' : '' }}{{ $t->quantity }}</td>
                        <td>{{ $t->reference_number ?: '—' }}</td>
                        <td>{{ $t->user->name ?? 'Unknown' }}</td>
                        <td>
                            <a href="{{ route('transactions.show', $t) }}" class="btn btn-sm btn-outline-secondary">View</a>
                            @if (auth()->check())
                                <form method="POST" action="{{ route('transactions.destroy', $t) }}" class="d-inline"
                                      onsubmit="return confirm('Delete this transaction? This will reverse the stock change.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">No transactions recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $transactions->links() }}
@endsection
