@extends('layouts.app')

@section('title', 'Purchase Orders')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Purchase Orders</h2>
        <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary">+ New Purchase Order</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                @foreach (['draft', 'pending', 'approved', 'rejected', 'sent', 'received', 'cancelled'] as $status)
                    <option value="{{ $status }}" @selected(request('status') == $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="supplier_id" class="form-select">
                <option value="">All Suppliers</option>
                @foreach ($suppliers as $s)
                    <option value="{{ $s->id }}" @selected(request('supplier_id') == $s->id)>{{ $s->company_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2"><input type="date" name="from" class="form-control" value="{{ request('from') }}"></div>
        <div class="col-md-2"><input type="date" name="to" class="form-control" value="{{ request('to') }}"></div>
        <div class="col-md-2"><button class="btn btn-outline-secondary w-100">Filter</button></div>
    </form>

    <table class="table table-hover">
        <thead>
            <tr><th>Reference #</th><th>Supplier</th><th>Status</th><th>Order Date</th><th>Total</th><th>Created By</th><th>Actions</th></tr>
        </thead>
        <tbody>
            @forelse ($orders as $po)
                <tr>
                    <td>{{ $po->reference_number }}</td>
                    <td>{{ $po->supplier->company_name ?? '—' }}</td>
                    <td>
                        @php
                            $badge = match($po->status) {
                                'draft' => 'bg-secondary',
                                'pending' => 'bg-warning',
                                'approved' => 'bg-info',
                                'rejected', 'cancelled' => 'bg-danger',
                                'sent' => 'bg-primary',
                                'received' => 'bg-success',
                                default => 'bg-secondary',
                            };
                        @endphp
                        <span class="badge {{ $badge }}">{{ ucfirst($po->status) }}</span>
                    </td>
                    <td>{{ $po->order_date?->format('M d, Y') }}</td>
                    <td>${{ number_format($po->total, 2) }}</td>
                    <td>{{ $po->createdBy->name ?? '—' }}</td>
                    <td><a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-sm btn-outline-secondary">View</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">No purchase orders found.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $orders->links() }}
@endsection
