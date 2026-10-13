@extends('layouts.app')

@section('title', 'Stock Movement Report')

@section('content')
    <h2 class="mb-4">Stock Movement Report</h2>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-3">
            <input type="date" name="from" class="form-control" value="{{ request('from') }}">
        </div>
        <div class="col-md-3">
            <input type="date" name="to" class="form-control" value="{{ request('to') }}">
        </div>
        <div class="col-md-2">
            <button class="btn btn-outline-secondary w-100">Filter</button>
        </div>
    </form>

    <table class="table table-hover">
        <thead>
            <tr><th>Date</th><th>Product</th><th>Type</th><th>Quantity</th><th>User</th></tr>
        </thead>
        <tbody>
            @forelse ($transactions as $t)
                <tr>
                    <td>{{ $t->created_at->format('M d, Y h:i A') }}</td>
                    <td>{{ $t->product->name ?? 'Deleted' }}</td>
                    <td>{{ str_replace('_', ' ', $t->type) }}</td>
                    <td>{{ $t->quantity > 0 ? '+' : '' }}{{ $t->quantity }}</td>
                    <td>{{ $t->user->name ?? 'Unknown' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">No transactions in this range.</td></tr>
            @endforelse
        </tbody>
    </table>

    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">Back to Reports</a>
@endsection
