<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #F3E4C9; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 10px; overflow: hidden; }
        .header { background: #B45B4A; color: #fff; padding: 20px; text-align: center; }
        .body { padding: 20px; color: #333; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header"><h2>Low Stock Alert</h2></div>
        <div class="body">
            <p><strong>{{ $product->name }}</strong> ({{ $product->sku }}) is running low on stock.</p>
            <p><strong>Current Quantity:</strong> {{ $product->quantity }}</p>
            <p><strong>Reorder Level:</strong> {{ $product->reorder_level }}</p>
            <p>Please review and reorder if needed.</p>
        </div>
    </div>
</body>
</html>
