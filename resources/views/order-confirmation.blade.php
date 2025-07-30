<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation</title>
</head>
<body>
    <h1>Order Confirmation</h1>
    <p>Dear {{ $name }},</p>
    <p>Your order has been successfully placed. Below are the details:</p>
    <ul>
        <li><strong>Order ID:</strong> {{ $order_id }}</li>
        <li><strong>Payment ID:</strong> {{ $payment_id }}</li>
        <li><strong>Total Amount:</strong> ₹{{ $amount }}</li>
        <li><strong>Date:</strong> {{ $date }}</li>
        <li><strong>Shipping Address:</strong> {{ $address }}</li>
    </ul>
    <p>Please use the attached PDF for your order slip. You can track your order using the Order ID. For any queries, contact support.</p>
    <p>Thank you for shopping with us!</p>
</body>
</html>