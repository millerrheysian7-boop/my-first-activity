@extends('layouts.app')

@section('title', $supplier->company_name)

@section('content')
    <h2 class="mb-4">{{ $supplier->company_name }}</h2>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">Supplier Information</div>
                <div class="card-body">
                    <p><strong>Contact:</strong> {{ $supplier->full_contact ?: '—' }}</p>
                    <p><strong>Address:</strong> {{ $supplier->address ?: '—' }}</p>
                    <p><strong>Payment Terms:</strong> {{ $supplier->payment_terms }}</p>
                    <p><strong>Lead Time:</strong> {{ $supplier->lead_time_days }} days</p>
                    <p><strong>Minimum Order:</strong> ${{ number_format($supplier->minimum_order_amount, 2) }}</p>
                    <p class="mb-0"><strong>Status:</strong>
                        <span class="badge {{ $supplier->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $supplier->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">Performance Metrics</div>
                <div class="card-body">
                    @if ($supplier->performance_rating === null)
                        <p class="text-muted mb-0">No completed orders yet to calculate performance.</p>
                    @else
                        <h3>{{ $supplier->performance_rating }}%</h3>
                        <p class="text-muted mb-0">On-time delivery rate</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Recent Purchase Orders</div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>Reference #</th><th>Status</th><th>Order Date</th><th>Total</th></tr></thead>
                <tbody>
                    @forelse ($supplier->purchaseOrders as $po)
                        <tr>
                            <td><a href="{{ route('purchase-orders.show', $po) }}">{{ $po->reference_number }}</a></td>
                            <td>{{ ucfirst($po->status) }}</td>
                            <td>{{ $po->order_date?->format('M d, Y') }}</td>
                            <td>${{ number_format($po->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted">No purchase orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Products Supplied</div>
        <ul class="list-group list-group-flush">
            @forelse ($supplier->products as $p)
                <li class="list-group-item">{{ $p->name }} ({{ $p->sku }})</li>
            @empty
                <li class="list-group-item text-muted">No products linked to this supplier yet.</li>
            @endforelse
        </ul>
    </div>

    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-primary">Edit</a>
    <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">Back to List</a>
@endsection
