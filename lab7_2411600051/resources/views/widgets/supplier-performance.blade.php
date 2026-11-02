<div class="card h-100">
    <div class="card-header">Top Performing Suppliers</div>
    <ul class="list-group list-group-flush" id="widgetSupplierPerformance">
        @forelse ($topSuppliers as $row)
            <li class="list-group-item d-flex justify-content-between">
                <a href="{{ route('suppliers.show', $row['supplier']) }}">{{ $row['supplier']->company_name }}</a>
                <span>{{ $row['rating'] }}% on-time</span>
            </li>
        @empty
            <li class="list-group-item text-muted">No performance data yet.</li>
        @endforelse
    </ul>
</div>
