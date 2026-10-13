@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
    <h2 class="mb-4">Edit Product</h2>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('products.update', $product) }}">
                @method('PUT')
                @include('products._form')
            </form>
        </div>
    </div>
@endsection
