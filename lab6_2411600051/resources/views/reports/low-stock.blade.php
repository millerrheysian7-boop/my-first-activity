@extends('layouts.app')

@section('title', 'Low Stock Report')

@section('content')
    <h2 class="mb-4">Low Stock Report</h2>

    <table class="table table-hover">
        <thead>
            <tr><th>Product</th><th>SKU</th><th>Category</th><th>Quantity</th><th>Reorder Level</th><th>Status</th></tr>
        </thead>
        <tbody>
            @forelse ($products as $p)
                <tr>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->sku }}</td>
                    <td>{{ $p->category }}</td>
                    <td>{{ $p->quantity }}</td>
                    <td>{{ $p->reorder_level }}</td>
                    <td><span class="badge bg-{{ $p->stock_status_color }}">{{ $p->stock_status }}</span></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">No low stock items.</td></tr>
            @endforelse
        </tbody>
    </table>

    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">Back to Reports</a>
@endsection
