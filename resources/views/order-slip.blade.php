<!DOCTYPE html>
<html>
<head>
    <title>Order Slip</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 80%; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .details { margin-bottom: 10px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Order Slip</h1>
            <p>Payment ID: {{ $payment_id }}</p>
            <p>Order ID: {{ $order_id }}</p>
            <p>Date: {{ $date }}</p>
        </div>
        <div class="details">
            <p><strong>Customer Name:</strong> {{ $name }}</p>
            <p><strong>Email:</strong> {{ $email }}</p>
            <p><strong>Contact:</strong> {{ $contact }}</p>
            <p><strong>Shipping Address:</strong> {{ $address }}</p>
            <p><strong>Total Amount:</strong> ₹{{ $amount }}</p>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cart as $item)
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>₹{{ $item['price'] }}</td>
                        <td>₹{{ $item['price'] * $item['quantity'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>