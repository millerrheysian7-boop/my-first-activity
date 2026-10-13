@extends('layouts.app')

@section('title', 'Add Product')

@section('content')
    <h2 class="mb-4">Add New Product</h2>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('products.store') }}">
                @include('products._form')
            </form>
        </div>
    </div>
@endsection
