<div class="card h-100">
    <div class="card-header">Products Needing Reorder</div>
    <ul class="list-group list-group-flush" id="widgetReorderAlerts">
        @forelse ($reorderProducts as $p)
            <li class="list-group-item d-flex justify-content-between">
                <a href="{{ route('products.show', $p) }}">{{ $p->name }}</a>
                <span>{{ $p->quantity }} in stock</span>
            </li>
        @empty
            <li class="list-group-item text-muted">No products currently need reordering.</li>
        @endforelse
    </ul>
</div>
