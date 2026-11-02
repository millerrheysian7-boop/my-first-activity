@extends('layouts.app')

@section('title', $purchaseOrder->reference_number)

@section('content')
    <h2 class="mb-4">Purchase Order {{ $purchaseOrder->reference_number }}</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">Order Information</div>
                <div class="card-body">
                    <p><strong>Supplier:</strong> {{ $purchaseOrder->supplier->company_name ?? '—' }}</p>
                    <p><strong>Status:</strong> {{ ucfirst($purchaseOrder->status) }}</p>
                    <p><strong>Order Date:</strong> {{ $purchaseOrder->order_date?->format('M d, Y') }}</p>
                    <p><strong>Expected Delivery:</strong> {{ $purchaseOrder->expected_delivery_date?->format('M d, Y') ?? '—' }}</p>
                    <p class="mb-0"><strong>Created By:</strong> {{ $purchaseOrder->createdBy->name ?? '—' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">Approval</div>
                <div class="card-body">
                    @if ($purchaseOrder->approvedBy)
                        <p><strong>Approved By:</strong> {{ $purchaseOrder->approvedBy->name }}</p>
                        <p class="mb-0"><strong>Approved At:</strong> {{ $purchaseOrder->approved_at?->format('M d, Y h:i A') }}</p>
                    @elseif ($purchaseOrder->rejection_reason)
                        <p class="text-danger mb-0"><strong>Rejected:</strong> {{ $purchaseOrder->rejection_reason }}</p>
                    @else
                        <p class="text-muted mb-0">Not yet reviewed.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Line Items</div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>Product</th><th>Qty Ordered</th><th>Unit Price</th><th>Line Total</th><th>Received</th><th>Outstanding</th></tr></thead>
                <tbody>
                    @foreach ($purchaseOrder->items as $item)
                        <tr>
                            <td>{{ $item->product->name ?? 'Deleted' }}</td>
                            <td>{{ $item->quantity_ordered }}</td>
                            <td>${{ number_format($item->unit_price, 2) }}</td>
                            <td>${{ number_format($item->line_total, 2) }}</td>
                            <td>{{ $item->quantity_received }}</td>
                            <td>{{ $item->outstanding_quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer text-end">
            <strong>Subtotal: ${{ number_format($purchaseOrder->subtotal, 2) }} |
            Tax: ${{ number_format($purchaseOrder->tax, 2) }} |
            Total: ${{ number_format($purchaseOrder->total, 2) }}</strong>
        </div>
    </div>

    <div class="d-flex gap-2 flex-wrap">
        @can('approve', $purchaseOrder)
            <form method="POST" action="{{ route('purchase-orders.approve', $purchaseOrder) }}">
                @csrf
                <button class="btn btn-success">Approve</button>
            </form>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">Reject</button>
        @endcan

        @can('update', $purchaseOrder)
            @if ($purchaseOrder->status === 'approved')
                <form method="POST" action="{{ route('purchase-orders.send', $purchaseOrder) }}">
                    @csrf
                    <button class="btn btn-primary">Mark as Sent</button>
                </form>
            @endif
        @endcan

        @can('receive', $purchaseOrder)
            <a href="#receiveSection" class="btn btn-outline-primary">Record Receipt</a>
        @endcan

        <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">Back to List</a>
    </div>

    @can('approve', $purchaseOrder)
        <div class="modal fade" id="rejectModal" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('purchase-orders.reject', $purchaseOrder) }}">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header"><h5 class="modal-title">Reject Purchase Order</h5></div>
                        <div class="modal-body">
                            <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Reason for rejection" required></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Reject</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endcan

    @can('receive', $purchaseOrder)
        <div class="card mt-4" id="receiveSection">
            <div class="card-header">Record Receipt</div>
            <div class="card-body">
                <form method="POST" action="{{ route('purchase-orders.receive', $purchaseOrder) }}">
                    @csrf
                    <table class="table">
                        <thead><tr><th>Product</th><th>Outstanding</th><th>Receiving Now</th></tr></thead>
                        <tbody>
                            @foreach ($purchaseOrder->items as $item)
                                <tr>
                                    <td>{{ $item->product->name ?? 'Deleted' }}</td>
                                    <td>{{ $item->outstanding_quantity }}</td>
                                    <td>
                                        <input type="number" name="received_quantities[{{ $item->id }}]" min="0" max="{{ $item->outstanding_quantity }}" class="form-control" value="0">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-primary">Save Receipt</button>
                </form>
            </div>
        </div>
    @endcan
@endsection
