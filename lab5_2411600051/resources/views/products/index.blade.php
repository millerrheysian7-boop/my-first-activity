@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Products</h2>
        <a href="{{ route('products.create') }}" class="btn btn-primary">+ Add Product</a>
    </div>

    <form method="GET" action="{{ route('products.index') }}" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search by name or SKU"
                   value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="">All Categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}" @selected(request('category') == $category)>{{ $category }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-outline-secondary w-100">Filter</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr class="transition-row">
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->sku }}</td>
                        <td>{{ $product->category }}</td>
                        <td>{{ $product->quantity }}</td>
                        <td>${{ number_format($product->unit_price, 2) }}</td>
                        <td>
                            @if ($product->isOutOfStock())
                                <span class="badge badge-out-of-stock">Out of Stock</span>
                            @elseif ($product->isLowStock())
                                <span class="badge badge-low-stock">Low Stock</span>
                            @else
                                <span class="badge badge-in-stock">In Stock</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-secondary">View</a>
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form method="POST" action="{{ route('products.destroy', $product) }}" class="d-inline"
                                  onsubmit="return confirm('Delete this product?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">No products found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $products->links() }}
@endsection
