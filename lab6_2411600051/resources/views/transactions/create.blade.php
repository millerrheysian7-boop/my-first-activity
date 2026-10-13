@extends('layouts.app')

@section('title', 'Record Transaction')

@section('content')
    <h2 class="mb-4">Record Inventory Transaction</h2>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('transactions.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Product</label>
                    <select name="product_id" id="productSelect" class="form-select @error('product_id') is-invalid @enderror">
                        <option value="">-- Select Product --</option>
                        @foreach ($products as $p)
                            <option value="{{ $p->id }}" data-quantity="{{ $p->quantity }}" @selected(old('product_id') == $p->id)>
                                {{ $p->name }} ({{ $p->sku }})
                            </option>
                        @endforeach
                    </select>
                    @error('product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <small class="text-muted">Current stock: <span id="currentStockDisplay">—</span></small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Transaction Type</label>
                    <select name="type" id="typeSelect" class="form-select @error('type') is-invalid @enderror">
                        @foreach ($types as $value => $label)
                            <option value="{{ $value }}" @selected(old('type') == $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity" id="quantityInput" min="1"
                           class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}">
                    <small class="text-muted" id="maxQtyHint"></small>
                    @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Reference Number</label>
                    <input type="text" name="reference_number" class="form-control" value="{{ old('reference_number') }}" placeholder="PO-1001, SO-2045, etc.">
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">Save Transaction</button>
                <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    const productSelect = document.getElementById('productSelect');
    const typeSelect = document.getElementById('typeSelect');
    const quantityInput = document.getElementById('quantityInput');
    const currentStockDisplay = document.getElementById('currentStockDisplay');
    const maxQtyHint = document.getElementById('maxQtyHint');

    function updateStockDisplay() {
        const opt = productSelect.options[productSelect.selectedIndex];
        const qty = opt ? opt.dataset.quantity : null;
        currentStockDisplay.textContent = qty !== undefined && qty !== null && qty !== '' ? qty : '—';
        updateMaxQuantity();
    }

    function updateMaxQuantity() {
        const opt = productSelect.options[productSelect.selectedIndex];
        const qty = opt ? parseInt(opt.dataset.quantity || '0', 10) : 0;

        if (typeSelect.value === 'stock_out') {
            quantityInput.max = qty;
            maxQtyHint.textContent = qty > 0 ? `Max available: ${qty}` : 'No stock available to remove.';
        } else {
            quantityInput.removeAttribute('max');
            maxQtyHint.textContent = '';
        }
    }

    productSelect.addEventListener('change', updateStockDisplay);
    typeSelect.addEventListener('change', updateMaxQuantity);
    document.addEventListener('DOMContentLoaded', updateStockDisplay);
</script>
@endsection
