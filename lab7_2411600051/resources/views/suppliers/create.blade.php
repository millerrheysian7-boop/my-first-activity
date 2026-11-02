@extends('layouts.app')

@section('title', 'Add Supplier')

@section('content')
    <h2 class="mb-4">Add Supplier</h2>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('suppliers.store') }}">
                @include('suppliers._form')
            </form>
        </div>
    </div>
@endsection
