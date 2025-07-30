<!DOCTYPE html>
<html>
<head>
    <title>Enter Shipping Address</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Enter Shipping Address</h1>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('error'))
            <div class="text-red-500 mb-4">{{ session('error') }}</div>
        @endif
        <!-- Debug output for cart data -->
        {{-- <pre class="text-gray-400 mb-4">{{ print_r($cart, true) }}</pre> --}}
        <form action="{{ route('payment.save') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-300">Address</label>
                <input type="text" name="address[address]" class="mt-1 p-2 border border-gray-600 rounded-md bg-gray-800 w-full" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-300">City</label>
                <input type="text" name="address[city]" class="mt-1 p-2 border border-gray-600 rounded-md bg-gray-800 w-full" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-300">State</label>
                <input type="text" name="address[state]" class="mt-1 p-2 border border-gray-600 rounded-md bg-gray-800 w-full" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-300">Pincode</label>
                <input type="text" name="address[pincode]" class="mt-1 p-2 border border-gray-600 rounded-md bg-gray-800 w-full" required>
            </div>
            <input type="hidden" name="cart" value="{{ $cart ? json_encode($cart) : old('cart', '[]') }}">
            <input type="hidden" name="name" value="{{ auth()->user()->name ?? 'User' }}">
            <input type="hidden" name="email" value="{{ auth()->user()->email ?? 'user@example.com' }}">
            <input type="hidden" name="contact" value="{{ auth()->user()->contact ?? '9999999999' }}">
            <button type="submit" class="bg-blue-600 px-4 py-2 rounded-md">Continue to Payment</button>
        </form>
    </div>
</body>
</html>