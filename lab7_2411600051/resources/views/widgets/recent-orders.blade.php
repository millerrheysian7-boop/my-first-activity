<div class="card h-100">
    <div class="card-header">Recent Purchase Orders</div>
    <ul class="list-group list-group-flush" id="widgetRecentOrders">
        @forelse ($recentOrders as $po)
            <li class="list-group-item d-flex justify-content-between">
                <a href="{{ route('purchase-orders.show', $po) }}">{{ $po->reference_number }}</a>
                <span>{{ $po->supplier->company_name ?? '—' }} · {{ ucfirst($po->status) }}</span>
            </li>
        @empty
            <li class="list-group-item text-muted">No purchase orders yet.</li>
        @endforelse
    </ul>
</div>
