<!DOCTYPE html>
<html>
<head>
    <title>Complete Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body class="bg-gray-900 text-white flex items-center justify-center min-h-screen">
    <div class="container mx-auto p-4 text-center">
        <h1 class="text-2xl font-bold mb-4">Complete Payment</h1>
        @if (session('error'))
            <div class="text-red-500 mb-4">{{ session('error') }}</div>
        @endif
        <p class="text-lg mb-4">Total Amount: ₹{{ $total / 100 }}</p>
        <form action="{{ route('payment.success') }}" method="POST" id="razorpay-form">
            @csrf
            <input type="hidden" name="razorpay_order_id" value="{{ $order->id }}">
            <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
            <input type="hidden" name="razorpay_signature" id="razorpay_signature">
            <button
                type="button"
                id="rzp-button"
                class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
            >
                Pay with Razorpay
            </button>
        </form>
        <div id="payment-status" class="mt-4 text-sm"></div>
    </div>

    <script>
        document.getElementById('rzp-button').onclick = function(e) {
            var options = {
                "key": "{{ env('RAZORPAY_KEY_ID') }}",
                "amount": "{{ $total }}",
                "currency": "INR",
                "name": "{{ $name }}",
                "description": "Cart Purchase",
                "order_id": "{{ $order->id }}",
                "handler": function(response) {
                    document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                    document.getElementById('razorpay_signature').value = response.razorpay_signature;
                    document.getElementById('razorpay-form').submit();
                },
                "prefill": {
                    "name": "{{ $name }}",
                    "email": "{{ $email }}",
                    "contact": "{{ auth()->check() ? auth()->user()->contact ?? $contact : $contact }}" // Use registered contact if available
                },
                "notes": {
                    "purpose": "Cart Purchase"
                },
                "theme": {
                    "color": "#10b981"
                },
                "modal": {
                    "ondismiss": function() {
                        document.getElementById('payment-status').innerText = 'Payment cancelled by user.';
                    }
                }
            };
            var rzp = new Razorpay(options);

            rzp.on('payment.success', function(response) {
                console.log('Payment Success: ', response);
                document.getElementById('payment-status').innerText = 'Payment processing...';
            });

            rzp.on('payment.failed', function(response) {
                console.log('Payment Failed: ', response.error);
                document.getElementById('payment-status').innerText = 'Payment failed: ' + response.error.description;
            });

            rzp.open();
            e.preventDefault();
        };
    </script>
</body>
</html>