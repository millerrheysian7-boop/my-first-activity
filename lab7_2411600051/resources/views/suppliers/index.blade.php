@extends('layouts.app')

@section('title', 'Suppliers')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Suppliers</h2>
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">+ Add Supplier</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control" placeholder="Search by name, contact, or email" value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="active" @selected(request('status') == 'active')>Active</option>
                <option value="inactive" @selected(request('status') == 'inactive')>Inactive</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-outline-secondary w-100">Filter</button>
        </div>
    </form>

    <table class="table table-hover">
        <thead>
            <tr><th>Company</th><th>Contact</th><th>Email</th><th>Lead Time</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
            @forelse ($suppliers as $s)
                <tr>
                    <td>{{ $s->company_name }}</td>
                    <td>{{ $s->contact_person ?: '—' }}</td>
                    <td>{{ $s->email }}</td>
                    <td>{{ $s->lead_time_days }} days</td>
                    <td>
                        <span class="badge {{ $s->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $s->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('suppliers.show', $s) }}" class="btn btn-sm btn-outline-secondary">View</a>
                        <a href="{{ route('suppliers.edit', $s) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('suppliers.destroy', $s) }}" class="d-inline" onsubmit="return confirm('Delete this supplier?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">No suppliers found.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $suppliers->links() }}
@endsection
