@extends('layouts.app')

@section('title', 'Inventory Valuation Report')

@section('content')
    <h2 class="mb-4">Inventory Valuation Report</h2>

    <div class="card mb-3">
        <div class="card-body">
            <h4>Total Inventory Value: ${{ number_format($totalValue, 2) }}</h4>
        </div>
    </div>

    <h5>By Category</h5>
    <table class="table table-hover mb-4">
        <thead><tr><th>Category</th><th>Product Count</th><th>Value</th></tr></thead>
        <tbody>
            @foreach ($byCategory as $category => $info)
                <tr>
                    <td>{{ $category }}</td>
                    <td>{{ $info['count'] }}</td>
                    <td>${{ number_format($info['value'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h5>All Products</h5>
    <table class="table table-hover">
        <thead><tr><th>Product</th><th>Category</th><th>Quantity</th><th>Unit Price</th><th>Total Value</th></tr></thead>
        <tbody>
            @foreach ($products as $p)
                <tr>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->category }}</td>
                    <td>{{ $p->quantity }}</td>
                    <td>${{ number_format($p->unit_price, 2) }}</td>
                    <td>${{ number_format($p->quantity * $p->unit_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">Back to Reports</a>
@endsection
