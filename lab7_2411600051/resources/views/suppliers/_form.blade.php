@csrf

<div class="mb-3">
    <label class="form-label">Company Name</label>
    <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name', $supplier->company_name ?? '') }}">
    @error('company_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Contact Person</label>
    <input type="text" name="contact_person" class="form-control @error('contact_person') is-invalid @enderror" value="{{ old('contact_person', $supplier->contact_person ?? '') }}">
    @error('contact_person') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $supplier->email ?? '') }}">
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $supplier->phone ?? '') }}" placeholder="+1 555-123-4567">
        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Address</label>
    <textarea name="address" class="form-control" rows="2">{{ old('address', $supplier->address ?? '') }}</textarea>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label">Payment Terms</label>
        <select name="payment_terms" class="form-select">
            @foreach (['Net 15', 'Net 30', 'Net 60', 'COD'] as $term)
                <option value="{{ $term }}" @selected(old('payment_terms', $supplier->payment_terms ?? 'Net 30') == $term)>{{ $term }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Lead Time (days)</label>
        <input type="number" name="lead_time_days" min="1" class="form-control @error('lead_time_days') is-invalid @enderror" value="{{ old('lead_time_days', $supplier->lead_time_days ?? 7) }}">
        @error('lead_time_days') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Minimum Order Amount</label>
        <input type="number" step="0.01" name="minimum_order_amount" class="form-control @error('minimum_order_amount') is-invalid @enderror" value="{{ old('minimum_order_amount', $supplier->minimum_order_amount ?? 0) }}">
        @error('minimum_order_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="form-check mb-3">
    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="isActive" @checked(old('is_active', $supplier->is_active ?? true))>
    <label class="form-check-label" for="isActive">Active</label>
</div>

<button type="submit" class="btn btn-primary">Save Supplier</button>
<a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">Cancel</a>
