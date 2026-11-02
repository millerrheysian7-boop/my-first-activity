<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #F3E4C9; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 10px; overflow: hidden; }
        .header { background: #7A8F4A; color: #fff; padding: 20px; text-align: center; }
        .body { padding: 20px; color: #333; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header"><h2>Purchase Order Approved</h2></div>
        <div class="body">
            <p>Your purchase order has been approved and is ready to be sent to the supplier.</p>
            <p><strong>Reference:</strong> {{ $purchaseOrder->reference_number }}</p>
            <p><strong>Approved By:</strong> {{ $purchaseOrder->approvedBy->name ?? '—' }}</p>
            <p><strong>Approved At:</strong> {{ $purchaseOrder->approved_at?->format('M d, Y h:i A') }}</p>
            <p><strong>Total:</strong> ${{ number_format($purchaseOrder->total, 2) }}</p>
        </div>
    </div>
</body>
</html>
