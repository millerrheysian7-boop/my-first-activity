@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h2 class="mb-4">Dashboard</h2>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-header">Total Products</div>
                <div class="card-body">
                    <h3>{{ $totalProducts }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-header">Low Stock</div>
                <div class="card-body">
                    <h3>{{ $lowStockCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-header">Out of Stock</div>
                <div class="card-body">
                    <h3>{{ $outOfStockCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-header">Inventory Value</div>
                <div class="card-body">
                    <h3>${{ number_format($totalValue, 2) }}</h3>
                </div>
            </div>
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
