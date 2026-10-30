<!DOCTYPE html>
<html>
<head>
    <title>Purchase Order Approved</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h2 style="color: #6D57D6;">Purchase Order Approved</h2>
        <p>Hello,</p>
        <p>We are pleased to inform you that your purchase order has been approved.</p>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Reference No:</td>
                <td style="padding: 8px; border: 1px solid #ddd;">{{ $purchaseOrder->reference_number }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Supplier:</td>
                <td style="padding: 8px; border: 1px solid #ddd;">{{ $purchaseOrder->supplier->name }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Total Amount:</td>
                <td style="padding: 8px; border: 1px solid #ddd;">₱{{ number_format($purchaseOrder->total, 2) }}</td>
            </tr>
        </table>

        <p style="margin-top: 20px;">You can now send this order to the supplier.</p>
        <p>Thank you,<br>Hardware Inventory System</p>
    </div>
</body>
</html>