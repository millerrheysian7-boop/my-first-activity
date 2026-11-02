<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        h1 { color: #8A5F41; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background: #A77F60; color: #fff; }
    </style>
</head>
<body>
    <h1>Purchase Order {{ $purchaseOrder->reference_number }}</h1>
    <p><strong>Supplier:</strong> {{ $purchaseOrder->supplier->company_name ?? '—' }}</p>
    <p><strong>Order Date:</strong> {{ $purchaseOrder->order_date?->format('M d, Y') }}</p>
    <p><strong>Status:</strong> {{ ucfirst($purchaseOrder->status) }}</p>

    <table>
        <thead><tr><th>Product</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr></thead>
        <tbody>
            @foreach ($purchaseOrder->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? 'Deleted' }}</td>
                    <td>{{ $item->quantity_ordered }}</td>
                    <td>${{ number_format($item->unit_price, 2) }}</td>
                    <td>${{ number_format($item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top: 15px;"><strong>Total: ${{ number_format($purchaseOrder->total, 2) }}</strong></p>
</body>
</html>
