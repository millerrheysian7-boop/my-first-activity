@extends('layouts.app')

@section('title', 'Stock Adjustment')

@section('content')
    <h2 class="mb-4">Stock Adjustment</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('adjustments.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Product</label>
                    <select name="product_id" id="productSelect" class="form-select @error('product_id') is-invalid @enderror">
                        <option value="">-- Select Product --</option>
                        @foreach ($products as $p)
                            <option value="{{ $p->id }}" @selected(old('product_id') == $p->id)>{{ $p->name }} ({{ $p->sku }})</option>
                        @endforeach
                    </select>
                    @error('product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Current System Quantity</label>
                    <input type="text" id="systemQuantity" class="form-control" value="—" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Physical Count</label>
                    <input type="number" name="physical_count" id="physicalCount" min="0"
                           class="form-control @error('physical_count') is-invalid @enderror" value="{{ old('physical_count') }}">
                    @error('physical_count') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <small class="text-muted">Difference: <span id="differenceDisplay">—</span></small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Reason</label>
                    <select name="reason" class="form-select @error('reason') is-invalid @enderror">
                        @foreach ($reasons as $value => $label)
                            <option value="{{ $value }}" @selected(old('reason') == $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">Save Adjustment</button>
                <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    const productSelect = document.getElementById('productSelect');
    const systemQuantity = document.getElementById('systemQuantity');
    const physicalCount = document.getElementById('physicalCount');
    const differenceDisplay = document.getElementById('differenceDisplay');
    let currentQty = 0;

    async function loadQuantity() {
        if (!productSelect.value) {
            systemQuantity.value = '—';
            currentQty = 0;
            updateDifference();
            return;
        }
        try {
            const res = await fetch(`/adjustments/${productSelect.value}/quantity`);
            const data = await res.json();
            currentQty = data.quantity;
            systemQuantity.value = currentQty;
        } catch (err) {
            systemQuantity.value = 'Error loading';
        }
        updateDifference();
    }

    function updateDifference() {
        const physical = parseInt(physicalCount.value, 10);
        if (isNaN(physical)) {
            differenceDisplay.textContent = '—';
            return;
        }
        const diff = physical - currentQty;
        differenceDisplay.textContent = (diff > 0 ? '+' : '') + diff;
    }

    productSelect.addEventListener('change', loadQuantity);
    physicalCount.addEventListener('input', updateDifference);
</script>
@endsection
