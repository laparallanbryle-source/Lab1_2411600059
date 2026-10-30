<!DOCTYPE html>
<html>
<head>
    <title>Inventory Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Inventory Report</h1>
    <p>Generated on: {{ now()->format('M d, Y h:i A') }}</p>
    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Name</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $p)
            <tr>
                <td>{{ $p->sku }}</td>
                <td>{{ $p->name }}</td>
                <td>{{ $p->category }}</td>
                <td>{{ $p->quantity }}</td>
                <td>₱{{ number_format($p->unit_price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>