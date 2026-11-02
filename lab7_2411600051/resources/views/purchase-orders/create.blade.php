@extends('layouts.app')

@section('title', 'New Purchase Order')

@section('content')
    <h2 class="mb-4">New Purchase Order</h2>

    <form method="POST" action="{{ route('purchase-orders.store') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Supplier</label>
            <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror">
                <option value="">-- Select Supplier --</option>
                @foreach ($suppliers as $s)
                    <option value="{{ $s->id }}" @selected(old('supplier_id') == $s->id)>{{ $s->company_name }}</option>
                @endforeach
            </select>
            @error('supplier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Expected Delivery Date</label>
            <input type="date" name="expected_delivery_date" class="form-control" value="{{ old('expected_delivery_date') }}">
        </div>

        <table class="table" id="itemsTable">
            <thead>
                <tr><th>Product</th><th>Quantity</th><th>Unit Price</th><th>Line Total</th><th></th></tr>
            </thead>
            <tbody id="itemsBody"></tbody>
        </table>
        <button type="button" id="addItemBtn" class="btn btn-outline-secondary btn-sm mb-3">+ Add Line Item</button>

        <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Create Purchase Order</button>
        <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </form>

    <template id="rowTemplate">
        <tr>
            <td>
                <select name="items[__i__][product_id]" class="form-select product-select">
                    <option value="">-- Product --</option>
                    @foreach ($products as $p)
                        <option value="{{ $p->id }}" data-price="{{ $p->unit_price }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" name="items[__i__][quantity]" min="1" class="form-control qty-input" value="1"></td>
            <td><input type="number" step="0.01" name="items[__i__][unit_price]" class="form-control price-input"></td>
            <td class="line-total">0.00</td>
            <td><button type="button" class="btn btn-sm btn-outline-danger remove-row">Remove</button></td>
        </tr>
    </template>
@endsection

@section('scripts')
<script>
    let rowIndex = 0;
    const itemsBody = document.getElementById('itemsBody');
    const template = document.getElementById('rowTemplate').innerHTML;

    function addRow() {
        const html = template.replaceAll('__i__', rowIndex);
        const tr = document.createElement('tbody');
        tr.innerHTML = html;
        itemsBody.appendChild(tr.firstElementChild);
        rowIndex++;
        bindRowEvents();
    }

    function bindRowEvents() {
        document.querySelectorAll('.product-select').forEach(function (select) {
            select.onchange = function () {
                const opt = select.options[select.selectedIndex];
                const row = select.closest('tr');
                row.querySelector('.price-input').value = opt.dataset.price || 0;
                updateLineTotal(row);
            };
        });
        document.querySelectorAll('.qty-input, .price-input').forEach(function (input) {
            input.oninput = function () {
                updateLineTotal(input.closest('tr'));
            };
        });
        document.querySelectorAll('.remove-row').forEach(function (btn) {
            btn.onclick = function () {
                btn.closest('tr').remove();
            };
        });
    }

    function updateLineTotal(row) {
        const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        row.querySelector('.line-total').textContent = (qty * price).toFixed(2);
    }

    document.getElementById('addItemBtn').addEventListener('click', addRow);
    addRow();
</script>
@endsection
