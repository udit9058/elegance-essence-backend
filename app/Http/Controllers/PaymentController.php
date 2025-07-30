<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function showAddressForm(Request $request)
    {
        $cart = json_decode(urldecode($request->query('cart', '[]')), true) ?: [];
        Log::info('Raw cart data from query: ', ['cart' => $request->query('cart')]);
        Log::info('Decoded cart data in showAddressForm: ', $cart);
        if (empty($cart) || !is_array($cart)) {
            return redirect()->back()->with('error', 'Invalid or empty cart data. Please try again.');
        }
        return view('address-form', compact('cart'));
    }

    public function saveAddressAndCreateOrder(Request $request)
    {
        Log::info('Received request data: ', $request->all());
        try {
            $cart = $request->input('cart');
            if (!$cart) {
                Log::warning('Invalid or empty cart data received: ', ['cart' => $cart]);
                throw new \Exception('Invalid or empty cart data');
            }
            // Decode cart if it’s a JSON string
            if (is_string($cart)) {
                $cart = json_decode($cart, true);
                if (json_last_error() !== JSON_ERROR_NONE || !$cart || !is_array($cart)) {
                    Log::warning('Failed to decode cart JSON: ', ['cart' => $cart, 'error' => json_last_error_msg()]);
                    throw new \Exception('Invalid cart data format');
                }
            } elseif (!is_array($cart)) {
                Log::warning('Cart is not an array: ', ['cart' => $cart]);
                throw new \Exception('Invalid cart data format');
            }

            $address = $request->input('address', []);
            $name = $request->input('name', 'User');
            $email = $request->input('email', 'user@example.com');
            $contact = $request->input('contact', '9999999999');

            $total = array_sum(array_map(function ($item) {
                return isset($item['price']) && isset($item['quantity']) ? $item['price'] * $item['quantity'] : 0;
            }, $cart)) * 100; // Amount in paise
            if ($total <= 0) {
                throw new \Exception('Total amount must be greater than zero');
            }

            $keyId = env('RAZORPAY_KEY_ID', 'rzp_test_FyydhXjhrirOxe');
            $keySecret = env('RAZORPAY_KEY_SECRET', 'I4DYz5cOSwEM27uyyeVCXazS');
            Log::info('Using Razorpay credentials - Key ID: ' . $keyId . ', Key Secret: ' . $keySecret);

            $api = new Api($keyId, $keySecret);
            $orderData = [
                'receipt' => 'order_' . time(),
                'amount' => $total,
                'currency' => 'INR',
                'notes' => ['purpose' => 'Cart Purchase'],
            ];

            $order = $api->order->create($orderData);

            // Store order details in session
            Session::put('order_details', [
                'order_id' => $order->id,
                'amount' => $total,
                'cart' => $cart,
                'address' => $address,
                'name' => $name,
                'email' => $email,
                'contact' => $contact,
            ]);

            // Return payment view
            return view('payment', compact('order', 'total', 'name', 'email', 'contact'));
        } catch (\Exception $e) {
            Log::error('Order creation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create order: ' . $e->getMessage())->withInput();
        }
    }

    public function success(Request $request)
    {
        Log::info('Entered success method with request data', $request->all());

        $razorpayPaymentId = $request->input('razorpay_payment_id');
        $razorpayOrderId = $request->input('razorpay_order_id');
        $razorpaySignature = $request->input('razorpay_signature');

        Log::info('Razorpay data', [
            'payment_id' => $razorpayPaymentId,
            'order_id' => $razorpayOrderId,
            'signature' => $razorpaySignature
        ]);

        if (!$razorpayPaymentId || !$razorpayOrderId || !$razorpaySignature) {
            Log::warning('Missing Razorpay parameters');
            return redirect()->route('payment.cancel')->with('error', 'Missing payment details');
        }

        try {
            $api = new Api(env('RAZORPAY_KEY_ID', 'rzp_test_FyydhXjhrirOxe'), env('RAZORPAY_KEY_SECRET', 'I4DYz5cOSwEM27uyyeVCXazS'));
            $generatedSignature = hash_hmac('sha256', $razorpayOrderId . '|' . $razorpayPaymentId, env('RAZORPAY_KEY_SECRET', 'I4DYz5cOSwEM27uyyeVCXazS'));
            Log::info('Signature comparison', [
                'generated' => $generatedSignature,
                'received' => $razorpaySignature
            ]);

            if ($generatedSignature === $razorpaySignature) {
                $orderDetails = Session::get('order_details');
                if ($orderDetails && $orderDetails['order_id'] === $razorpayOrderId) {
                    // Generate order slip as PDF
                    $data = [
                        'payment_id' => $razorpayPaymentId,
                        'order_id' => $razorpayOrderId,
                        'amount' => $orderDetails['amount'] / 100,
                        'name' => $orderDetails['name'],
                        'email' => $orderDetails['email'],
                        'contact' => $orderDetails['contact'],
                        'address' => implode(', ', $orderDetails['address']),
                        'cart' => $orderDetails['cart'],
                        'date' => now()->format('Y-m-d H:i:s'),
                    ];
                    $pdf = Pdf::loadView('order-slip', $data);
                    $pdfPath = 'orders/order_' . time() . '.pdf';
                    Storage::put($pdfPath, $pdf->output());
                    $pdfUrl = Storage::url($pdfPath);

                    // Attempt to send email, catch any mail-specific errors
                    try {
                        Mail::send('emails.order-confirmation', $data, function ($message) use ($orderDetails, $pdfPath) {
                            $message->to($orderDetails['email'], $orderDetails['name'])
                                    ->subject('Order Confirmation - Tracking Details')
                                    ->attach(storage_path('app/' . $pdfPath));
                        });
                        Log::info('Email sent successfully');
                    } catch (\Exception $e) {
                        Log::error('Failed to send email: ' . $e->getMessage());
                    }

                    Session::forget('order_details');
                    Log::info('Payment successful, redirecting to React success page');
                    // Redirect to React success page with payment ID and PDF URL
                    return redirect()->to('http://localhost:5173/payment-success?paymentId=' . urlencode($razorpayPaymentId) . '&pdfUrl=' . urlencode($pdfUrl));
                } else {
                    Log::warning('Order details mismatch', ['session_order_id' => $orderDetails['order_id'] ?? 'null', 'received_order_id' => $razorpayOrderId]);
                    return redirect()->route('payment.cancel')->with('error', 'Order details mismatch');
                }
            } else {
                Log::error('Signature verification failed');
                throw new \Exception('Payment signature verification failed');
            }
        } catch (\Exception $e) {
            Log::error('Payment success verification failed: ' . $e->getMessage());
            Session::forget('order_details');
            return redirect()->route('payment.cancel')->with('error', 'Payment verification failed: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        Session::forget('order_details');
        return view('payment-cancel');
    }

    public function handleWebhook(Request $request)
    {
        try {
            $api = new Api(env('RAZORPAY_KEY_ID', 'rzp_test_FyydhXjhrirOxe'), env('RAZORPAY_KEY_SECRET', 'I4DYz5cOSwEM27uyyeVCXazS'));
            $payload = $request->getContent();
            $sigHeader = $request->header('X-Razorpay-Signature');
            $secret = env('RAZORPAY_KEY_SECRET', 'I4DYz5cOSwEM27uyyeVCXazS');

            $api->utility->verifyWebhookSignature($payload, $sigHeader, $secret);
            $event = json_decode($payload, true);
            if ($event['event'] === 'payment.captured') {
                $payment = $api->payment->fetch($event['payload']['payment']['entity']['id']);
                $orderId = $payment->order_id;
                $orderDetails = Session::get('order_details');
                if ($orderDetails && $orderDetails['order_id'] === $orderId) {
                    // Update order status in database (implement your logic here if needed)
                    Session::forget('order_details');
                }
            }
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Webhook verification failed: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}