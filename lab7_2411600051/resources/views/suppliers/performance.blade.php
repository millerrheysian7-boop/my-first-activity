@extends('layouts.app')

@section('title', 'Supplier Performance')

@section('content')
    <h2 class="mb-4">Supplier Performance Report</h2>

    <table class="table table-hover">
        <thead>
            <tr>
                <th>Supplier</th>
                <th>Total Orders</th>
                <th>Delivered On Time</th>
                <th>Avg Delivery Days</th>
                <th>Total Value</th>
                <th>On-Time Rating</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($suppliers as $row)
                <tr>
                    <td><a href="{{ route('suppliers.show', $row['supplier']) }}">{{ $row['supplier']->company_name }}</a></td>
                    <td>{{ $row['total_orders'] }}</td>
                    <td>{{ $row['delivered_on_time'] }}</td>
                    <td>{{ $row['avg_delivery_days'] ?? '—' }}</td>
                    <td>${{ number_format($row['total_value'], 2) }}</td>
                    <td>
                        @if ($row['performance_rating'] === null)
                            <span class="text-muted">No data</span>
                        @else
                            <span class="badge {{ $row['performance_rating'] >= 80 ? 'bg-success' : ($row['performance_rating'] >= 50 ? 'bg-warning' : 'bg-danger') }}">
                                {{ $row['performance_rating'] }}%
                            </span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">No active suppliers.</td></tr>
            @endforelse
        </tbody>
    </table>

    <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">Back to Suppliers</a>
@endsection
