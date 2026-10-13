@extends('layouts.app')

@section('title', 'Reports')

@section('content')
    <h2 class="mb-4">Inventory Reports</h2>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header">Stock Movement Report</div>
                <div class="card-body d-flex flex-column">
                    <p>All inventory transactions within a date range.</p>
                    <a href="{{ route('reports.stock-movement') }}" class="btn btn-primary mt-auto">View Report</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header">Low Stock Report</div>
                <div class="card-body d-flex flex-column">
                    <p>Products at or below their reorder level.</p>
                    <a href="{{ route('reports.low-stock') }}" class="btn btn-primary mt-auto">View Report</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header">Inventory Valuation Report</div>
                <div class="card-body d-flex flex-column">
                    <p>Total value of current inventory, by category.</p>
                    <a href="{{ route('reports.valuation') }}" class="btn btn-primary mt-auto">View Report</a>
                </div>
            </div>
        </div>
    </div>
@endsection
