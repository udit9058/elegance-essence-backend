<!DOCTYPE html>
<html>
<head>
    <title>Payment Success</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white">
    <div class="container mx-auto p-4 text-center">
        <h1 class="text-2xl font-bold mb-4">Payment Successful</h1>
        <p class="text-lg">Your payment with ID {{ $paymentId }} was successful!</p>
        @if (isset($pdfUrl))
            <a href="{{ $pdfUrl }}" download="order-slip.pdf" class="bg-green-600 px-4 py-2 rounded-md mt-4 inline-block">Download Order Slip</a>
        @else
            <p class="text-red-500 mt-4">Order slip generation failed. Please contact support.</p>
        @endif
        <a href="http://localhost:5174/" class="bg-blue-600 px-4 py-2 rounded-md mt-4 inline-block ml-2">Back to Home</a>
    </div>
</body>
</html>