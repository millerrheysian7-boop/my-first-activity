@csrf

<div class="mb-3">
    <label class="form-label">Product Name</label>
    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $product->name ?? '') }}">
    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">SKU</label>
    <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror"
           value="{{ old('sku', $product->sku ?? '') }}">
    @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Description</label>
    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $product->description ?? '') }}</textarea>
    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Category</label>
    <select name="category" class="form-select @error('category') is-invalid @enderror">
        <option value="">-- Select Category --</option>
        @foreach ($categories as $category)
            <option value="{{ $category }}" @selected(old('category', $product->category ?? '') == $category)>
                {{ $category }}
            </option>
        @endforeach
    </select>
    @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label">Quantity</label>
        <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
               value="{{ old('quantity', $product->quantity ?? 0) }}">
        @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Reorder Level</label>
        <input type="number" name="reorder_level" class="form-control @error('reorder_level') is-invalid @enderror"
               value="{{ old('reorder_level', $product->reorder_level ?? 5) }}">
        @error('reorder_level') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Unit Price</label>
        <input type="number" step="0.01" name="unit_price" class="form-control @error('unit_price') is-invalid @enderror"
               value="{{ old('unit_price', $product->unit_price ?? 0) }}">
        @error('unit_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Supplier</label>
    <input type="text" name="supplier" class="form-control @error('supplier') is-invalid @enderror"
           value="{{ old('supplier', $product->supplier ?? '') }}">
    @error('supplier') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<button type="submit" class="btn btn-primary">Save Product</button>
<a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Cancel</a>
