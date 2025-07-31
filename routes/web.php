<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RoughController;


Route::get('/address', [PaymentController::class, 'showAddressForm'])->name('payment.address');
Route::post('/save-address', [PaymentController::class, 'saveAddressAndCreateOrder'])->name('payment.save');
Route::post('/webhook/razorpay', [PaymentController::class, 'handleWebhook']);
Route::get('/rough', [RoughController::class, 'index'])->name('rough.index');

Route::post('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');

Route::get('/ping', function () {
    return 'Backend is working!';
});

Route::get('/rough', [RoughController::class, 'index'])->name('rough.index');