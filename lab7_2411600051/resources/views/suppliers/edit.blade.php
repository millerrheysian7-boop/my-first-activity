@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('content')
    <h2 class="mb-4">Edit Supplier</h2>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
                @method('PUT')
                @include('suppliers._form')
            </form>
        </div>
    </div>
@endsection
