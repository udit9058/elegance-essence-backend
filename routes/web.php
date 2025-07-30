<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;


Route::get('/address', [PaymentController::class, 'showAddressForm'])->name('payment.address');
Route::post('/save-address', [PaymentController::class, 'saveAddressAndCreateOrder'])->name('payment.save');
Route::post('/webhook/razorpay', [PaymentController::class, 'handleWebhook']);


Route::post('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');

