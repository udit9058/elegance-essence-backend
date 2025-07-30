<?php

namespace App\Http\Controllers;

use App\Models\SellerUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SellerAuthController extends Controller
{
    public function registerApi(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:191',
                'email' => 'required|email|max:191|unique:seller_users,email',
                'password' => 'required|string|min:6',
                'contact_number' => 'required|string|max:15',
                'business_name' => 'required|string|max:191',
                'business_address' => 'required|string',
                'city' => 'required|string|max:100',
                'state' => 'required|string|max:100',
                'pincode' => 'required|string|max:10',
                'profile_image' => 'nullable|image|max:2048',
                'plan_type' => 'required|string|in:basic',
                'plan_duration' => 'required|integer|in:1,3,5',
                'plan_price' => 'required|numeric|in:500,1200,2000',
            ]);

            $imagePath = null;
            if ($request->hasFile('profile_image')) {
                $imagePath = $request->file('profile_image')->store('profile_images', 'public');
            }

            $seller = SellerUser::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'contact_number' => $request->contact_number,
                'business_name' => $request->business_name,
                'business_address' => $request->business_address,
                'city' => $request->city,
                'state' => $request->state,
                'pincode' => $request->pincode,
                'profile_image' => $imagePath,
                'plan_type' => $request->plan_type,
                'plan_duration' => (int) $request->plan_duration,
                'plan_price' => (float) $request->plan_price,
                'role' => 'seller',
                'created_at' => now(),
            ]);

            $token = $seller->createToken('seller-token', ['seller'])->plainTextToken;

            return response()->json([
                'message' => 'Seller registered successfully',
                'token' => $token,
                'seller' => $seller,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Registration failed: ' . $e->getMessage()], 500);
        }
    }

    public function loginApi(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:191',
            'password' => 'required|string',
        ]);

        $seller = DB::selectOne('SELECT * FROM seller_users WHERE email = ?', [$request->email]);

        if (!$seller) {
            return response()->json([
                'accountExists' => false,
                'isMatch' => false,
                'message' => 'There is no account, please register first.',
                'token' => null,
            ], 404);
        }

        if (!Hash::check($request->password, $seller->password)) {
            return response()->json([
                'accountExists' => true,
                'isMatch' => false,
                'message' => 'Please enter correct credentials.',
                'token' => null,
            ], 401);
        }

        $sellerModel = SellerUser::where('email', $seller->email)->first();
        $token = $sellerModel->createToken('seller_token')->plainTextToken;

        return response()->json([
            'accountExists' => true,
            'isMatch' => true,
            'message' => 'Login successful!',
            'token' => $token,
            'seller' => [
                'id' => $seller->id,
                'name' => $seller->name,
                'email' => $seller->email,
                'business_name' => $seller->business_name,
            ],
        ], 200);
    }

    public function checkAuthApi(Request $request)
    {
        if ($request->user('seller')) {
            $seller = $request->user('seller');
            return response()->json([
                'isLoggedIn' => true,
                'seller' => [
                    'id' => $seller->id,
                    'name' => $seller->name,
                    'email' => $seller->email,
                    'business_name' => $seller->business_name,
                ],
            ], 200);
        }
        return response()->json(['isLoggedIn' => false], 401);
    }

    public function logoutApi(Request $request)
    {
        $request->user('seller')->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    public function profile(Request $request)
    {
        $seller = Auth::guard('seller')->user();
        if (!$seller) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        return response()->json($seller, 200);
    }

    public function requestOtp(Request $request)
    {
        return response()->json(['message' => 'OTP request not implemented'], 501);
    }

    public function loginOtp(Request $request)
    {
        return response()->json(['message' => 'OTP login not implemented'], 501);
    }
}