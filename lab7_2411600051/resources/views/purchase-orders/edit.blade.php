@extends('layouts.app')

@section('title', 'Edit Purchase Order')

@section('content')
    <h2 class="mb-4">Edit Purchase Order {{ $purchaseOrder->reference_number }}</h2>

    <form method="POST" action="{{ route('purchase-orders.update', $purchaseOrder) }}">
        @method('PUT')
        @csrf

        <div class="mb-3">
            <label class="form-label">Expected Delivery Date</label>
            <input type="date" name="expected_delivery_date" class="form-control" value="{{ old('expected_delivery_date', $purchaseOrder->expected_delivery_date?->format('Y-m-d')) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $purchaseOrder->notes) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="btn btn-outline-secondary">Cancel</a>
    </form>
@endsection
