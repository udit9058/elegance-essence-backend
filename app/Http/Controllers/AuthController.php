<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class AuthController extends Controller
{
    // Registration
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'contact_number' => 'required|string|max:15',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'pincode' => 'required|string|max:10',
        ]);

        DB::insert(
            'INSERT INTO users (name, email, password, contact_number, address, city, state, pincode, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())',
            [
                $request->name,
                $request->email,
                Hash::make($request->password),
                $request->contact_number,
                $request->address,
                $request->city,
                $request->state,
                $request->pincode,
            ]
        );

        return redirect()->route('login')->with('success', 'Registration successful! Please log in.');
    }

    // Login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    // API Login
    public function loginApi(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = DB::selectOne('SELECT * FROM users WHERE email = ?', [$request->email]);

        if (!$user) {
            return response()->json([
                'accountExists' => false,
                'isMatch' => false,
                'message' => 'There is no account, please register first.',
                'token' => null,
            ], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'accountExists' => true,
                'isMatch' => false,
                'message' => 'Please enter correct credentials.',
                'token' => null,
            ], 401);
        }

        $userModel = \App\Models\User::where('email', $user->email)->first();
        Auth::login($userModel);
        $token = $userModel->createToken('auth_token')->plainTextToken;

        return response()->json([
            'accountExists' => true,
            'isMatch' => true,
            'message' => 'Login successful!',
            'token' => $token,
        ], 200);
    }

    // API Registration
    public function registerApi(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'contact_number' => 'required|string|max:15',
            'profile_image' => 'nullable|image|max:2048',
            'age' => 'required|integer|min:1',
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'pincode' => 'required|string|max:10',
        ]);

        $profileImagePath = null;
        if ($request->hasFile('profile_image')) {
            $profileImagePath = $request->file('profile_image')->store('profile_images', 'public');
        }

        DB::insert(
            'INSERT INTO users (name, email, password, contact_number, profile_image, age, gender, address, city, state, pincode, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())',
            [
                $request->name,
                $request->email,
                Hash::make($request->password),
                $request->contact_number,
                $profileImagePath,
                $request->age,
                $request->gender,
                $request->address,
                $request->city,
                $request->state,
                $request->pincode,
            ]
        );

        return response()->json([
            'message' => 'Registration successful! Please log in.',
        ], 201);
    }

    // Check Authentication
    public function checkAuthApi(Request $request)
    {
        if (Auth::check()) {
            return response()->json(['isLoggedIn' => true, 'user' => Auth::user()], 200);
        }

        return response()->json(['isLoggedIn' => false], 401);
    }

    // API Logout
    public function logoutApi(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Successfully logged out'], 200);
    }

    // OTP Login
    public function showOtpLoginForm()
    {
        return view('auth.otp-login');
    }

    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = DB::selectOne('SELECT * FROM users WHERE email = ?', [$request->email]);
        if (!$user) {
            return back()->withErrors(['email' => 'Email not found.']);
        }

        $otp = Str::random(6);
        $expiresAt = now()->addSeconds(60);

        DB::update(
            'UPDATE users SET otp = ?, otp_expires_at = ? WHERE email = ?',
            [$otp, $expiresAt, $request->email]
        );

        Mail::raw("Your OTP is: $otp (Valid for 60 seconds)", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Your OTP for Login');
        });

        Session::put('otp_email', $user->email);

        return redirect()->route('otp.verify')->with('success', 'OTP sent to your email.');
    }

    public function showOtpVerifyForm()
    {
        return view('auth.otp-verify');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|string']);

        $email = Session::get('otp_email');
        $user = DB::selectOne('SELECT * FROM users WHERE email = ?', [$email]);

        if ($user && $user->otp === $request->otp && now()->lessThan($user->otp_expires_at)) {
            $userModel = new \App\Models\User();
            $userModel->setAttribute('email', $user->email);
            Auth::login($userModel);
            DB::update('UPDATE users SET otp = NULL, otp_expires_at = NULL WHERE email = ?', [$email]);
            Session::forget('otp_email');
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['otp' => 'Invalid or expired OTP. Please try again.']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    // Update Profile
    public function updateProfileApi(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'contact_number' => 'required|string|max:15',
            'profile_image' => 'nullable|image|max:2048',
            'age' => 'required|integer|min:1',
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'pincode' => 'required|string|max:10',
        ]);

        $user = Auth::user();
        $profileImagePath = $user->profile_image; // Default to existing image

        if ($request->hasFile('profile_image')) {
            if ($profileImagePath && Storage::exists('public/' . $profileImagePath)) {
                Storage::delete('public/' . $profileImagePath);
            }
            $profileImagePath = $request->file('profile_image')->store('profile_images', 'public');
        } // If no new file, keep the existing path

        DB::update(
            'UPDATE users SET name = ?, email = ?, contact_number = ?, profile_image = ?, age = ?, gender = ?, address = ?, city = ?, state = ?, pincode = ?, updated_at = NOW() WHERE id = ?',
            [
                $request->name,
                $request->email,
                $request->contact_number,
                $profileImagePath, // Will be the new path or existing path
                $request->age,
                $request->gender,
                $request->address,
                $request->city,
                $request->state,
                $request->pincode,
                $user->id,
            ]
        );

        return response()->json(['message' => 'Profile updated successfully', 'user' => Auth::user()], 200);
    }


    // API: Send OTP
    public function sendOtpApi(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = DB::selectOne('SELECT * FROM users WHERE email = ?', [$request->email]);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Email not found.'], 404);
        }

        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addSeconds(60);

        DB::update(
            'UPDATE users SET otp = ?, otp_expires_at = ? WHERE email = ?',
            [$otp, $expiresAt, $request->email]
        );

        Mail::raw("Your OTP is: $otp (Valid for 60 seconds)", function ($message) use ($request) {
            $message->to($request->email)->subject('Your OTP for Login');
        });

        return response()->json(['success' => true, 'message' => 'OTP sent to your email.'], 200);
    }

    // API: Verify OTP
    public function verifyOtpApi(Request $request)
    {
        $request->validate(['email' => 'required|email', 'otp' => 'required|string|size:6']);

        $user = DB::selectOne('SELECT * FROM users WHERE email = ? AND otp = ? AND otp_expires_at > ?', [
            $request->email,
            $request->otp,
            now(),
        ]);

        if ($user) {
            $userModel = User::where('email', $request->email)->first();
            Auth::login($userModel);
            $token = $userModel->createToken('auth_token')->plainTextToken;

            DB::update('UPDATE users SET otp = NULL, otp_expires_at = NULL WHERE email = ?', [$request->email]);

            return response()->json([
                'success' => true,
                'message' => 'Login successful!',
                'token' => $token,
            ], 200);
        }

        return response()->json(['success' => false, 'message' => 'Invalid or expired OTP.'], 400);
    }
}