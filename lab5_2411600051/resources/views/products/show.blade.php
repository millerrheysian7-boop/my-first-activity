@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <h2 class="mb-4">{{ $product->name }}</h2>

    <div class="card">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">SKU</dt>
                <dd class="col-sm-9">{{ $product->sku }}</dd>

                <dt class="col-sm-3">Category</dt>
                <dd class="col-sm-9">{{ $product->category }}</dd>

                <dt class="col-sm-3">Description</dt>
                <dd class="col-sm-9">{{ $product->description ?: '—' }}</dd>

                <dt class="col-sm-3">Quantity</dt>
                <dd class="col-sm-9">{{ $product->quantity }}</dd>

                <dt class="col-sm-3">Reorder Level</dt>
                <dd class="col-sm-9">{{ $product->reorder_level }}</dd>

                <dt class="col-sm-3">Unit Price</dt>
                <dd class="col-sm-9">${{ number_format($product->unit_price, 2) }}</dd>

                <dt class="col-sm-3">Total Value</dt>
                <dd class="col-sm-9">${{ number_format($product->totalValue(), 2) }}</dd>

                <dt class="col-sm-3">Supplier</dt>
                <dd class="col-sm-9">{{ $product->supplier ?: '—' }}</dd>

                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">
                    @if ($product->isOutOfStock())
                        <span class="badge badge-out-of-stock">Out of Stock</span>
                    @elseif ($product->isLowStock())
                        <span class="badge badge-low-stock">Low Stock</span>
                    @else
                        <span class="badge badge-in-stock">In Stock</span>
                    @endif
                </dd>
            </dl>

            <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">Edit</a>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Back to List</a>

            <form method="POST" action="{{ route('products.destroy', $product) }}" class="d-inline"
                  onsubmit="return confirm('Delete this product?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>
@endsection
