<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CsrfController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\SellerAuthController;
use App\Http\Controllers\SellerProductController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ImageTransformController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'loginApi']);
Route::post('/register', [AuthController::class, 'registerApi']);
Route::get('/check-auth', [AuthController::class, 'checkAuthApi'])->middleware('auth:sanctum');
Route::post('/logout', [AuthController::class, 'logoutApi'])->middleware('auth:sanctum');
Route::post('/update-profile', [AuthController::class, 'updateProfileApi'])->middleware('auth:sanctum');

Route::post('/send-otp', [AuthController::class, 'sendOtpApi']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtpApi']);
Route::post('/create-order', [PaymentController::class, 'createOrder']);
Route::post('/webhook', [PaymentController::class, 'handleWebhook']);
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
Route::post('/webhook/razorpay', [PaymentController::class, 'handleWebhook']);
Route::get('/csrf-token', [CsrfController::class, 'getCsrfToken']);

Route::post('/customer/register', [CustomerAuthController::class, 'register']);
Route::post('/customer/login', [CustomerAuthController::class, 'login']);
Route::get('/customer/check-auth', [CustomerAuthController::class, 'checkAuth']);

Route::prefix('seller')->group(function () {
    Route::post('/register', [SellerAuthController::class, 'registerApi']);
    Route::post('/login', [SellerAuthController::class, 'loginApi']);
    Route::post('/request-otp', [SellerAuthController::class, 'requestOtp']);
    Route::post('/login-otp', [SellerAuthController::class, 'loginOtp']);
    Route::get('/check-auth', [SellerAuthController::class, 'checkAuthApi'])->middleware('auth:seller');
    Route::post('/logout', [SellerAuthController::class, 'logoutApi'])->middleware('auth:seller');
    Route::get('/profile', [SellerAuthController::class, 'profile'])->middleware('auth:seller');

    Route::middleware('auth:seller')->group(function () {
        Route::get('/products', [SellerProductController::class, 'index']);
        Route::post('/products', [SellerProductController::class, 'store']);
        Route::put('/products/{id}', [SellerProductController::class, 'update']);
        Route::delete('/products/{id}', [SellerProductController::class, 'delete']);
    });
});

Route::get('/product', [ProductController::class, 'index']);
Route::post('/product', [ProductController::class, 'store']);
// routes/api.php
Route::post('/upload-temp-image', [ImageController::class, 'uploadTempImage']);

