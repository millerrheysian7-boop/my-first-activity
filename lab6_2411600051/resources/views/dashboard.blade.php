@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h2 class="mb-4">Dashboard</h2>
    <p class="text-muted small" id="lastUpdated">Last updated: just now</p>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-header">Total Products</div>
                <div class="card-body">
                    <h3 id="statTotalProducts">{{ $totalProducts }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-header">Low Stock</div>
                <div class="card-body">
                    <h3 id="statLowStock">{{ $lowStockCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-header">Out of Stock</div>
                <div class="card-body">
                    <h3 id="statOutOfStock">{{ $outOfStockCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-header">Inventory Value</div>
                <div class="card-body">
                    <h3 id="statTotalValue" data-format="currency">${{ number_format($totalValue, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

        <div class="card mb-4">
            <div class="card-header">Low Stock Alerts</div>
                <div class="card-body" id="lowStockAlerts">
                <p class="text-muted mb-0">Loading...</p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Recent Transactions</div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr><th>Product</th><th>Type</th><th>Qty</th><th>By</th></tr>
                        </thead>
                        <tbody id="recentTransactionsBody">
                            <tr><td colspan="4" class="text-center text-muted">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Recently Added Products</div>
                <ul class="list-group list-group-flush">
                    @forelse ($recentProducts as $product)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $product->name }}</span>
                            <span class="text-muted">{{ $product->created_at->diffForHumans() }}</span>
                        </li>
                    @empty
                        <li class="list-group-item">No products yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Inventory Value by Category</div>
                <ul class="list-group list-group-flush">
                    @foreach ($categorySummary as $row)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $row->category }} ({{ $row->count }})</span>
                            <span>${{ number_format($row->value, 2) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="{{ asset('js/dashboard-polling.js') }}"></script>
@endsection
