<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #F3E4C9; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 10px; overflow: hidden; }
        .header { background: #8A5F41; color: #fff; padding: 20px; text-align: center; }
        .body { padding: 20px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 8px; border-bottom: 1px solid #EDE0C8; text-align: left; }
        th { background: #A77F60; color: #fff; }
        .total { font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header"><h2>New Purchase Order Created</h2></div>
        <div class="body">
            <p>A new purchase order has been created and requires approval.</p>
            <p><strong>Reference:</strong> {{ $purchaseOrder->reference_number }}</p>
            <p><strong>Supplier:</strong> {{ $purchaseOrder->supplier->company_name ?? '—' }}</p>
            <p><strong>Order Date:</strong> {{ $purchaseOrder->order_date?->format('M d, Y') }}</p>

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

            <p class="total">Total: ${{ number_format($purchaseOrder->total, 2) }}</p>
        </div>
    </div>
</body>
</html>
