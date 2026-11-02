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
    </style>
</head>
<body>
    <div class="container">
        <div class="header"><h2>Purchase Order {{ $purchaseOrder->reference_number }}</h2></div>
        <div class="body">
            <p>Dear {{ $purchaseOrder->supplier->contact_person ?? 'Supplier' }},</p>
            <p>Please find our purchase order details below.</p>

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

            <p><strong>Total: ${{ number_format($purchaseOrder->total, 2) }}</strong></p>
            <p>Please confirm receipt and expected delivery date.</p>
            <p>Thank you,<br>BuildRight Hardware</p>
        </div>
    </div>
</body>
</html>
