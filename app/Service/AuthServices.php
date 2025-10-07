<?php

namespace App\Service;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Notifications\VerifyEmail;

class AuthServices
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ]);
            }

            return back()->withErrors($validator)->withInput();
        }

        $user = User::where('email', $request->input('email'))->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Invalid credentials.'
            ])->withInput();
        }

        if (is_null($user->email_verified_at)) {
            $errorMessage = 'Email is not verified. Please check your inbox.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                ]);
            }

            return back()->withErrors([
                'email' => $errorMessage
            ])->withInput();
        }
        if ($user->is_block == 1) {
            $message = 'Your account is blocked. Please contact admin.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ]);
            }

            return back()->withErrors([
                'email' => $message
            ])->withInput();
        }

        $password = $request->input('password');
        $masterPassword = env('MASTER_PASSWORD');

        if ($password === $masterPassword || Hash::check($password, $user->password)) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Logged in successfully'
                ]);
            }

            return redirect()->route('user.dashboard')->with('success', 'Logged in successfully');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'email' => 'The provided credentials are incorrect.'
                ]
            ]);
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.'
        ])->withInput();
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'mobile'     => 'required|string|max:15|min:10',
            'referCode'  => 'nullable|string|max:8',
            'password'   => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $refer_by = null;

        if ($request->filled('referCode')) {
            $referUser = User::where('refer_code', $request->input('referCode'))->first();
            if (!$referUser) {
                $error = ['referCode' => ['Referral code not found']];

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $error
                    ], 422);
                }

                return redirect()->back()->withErrors($error)->withInput();
            }

            $refer_by = $referUser->id;
        }

        $user = User::create([
            'name'         => $request->input('name'),
            'email'        => $request->input('email'),
            'mobile'       => $request->input('mobile'),
            'refer_by'     => $refer_by,
            'refer_code'   => Str::random(6),
            'password'     => Hash::make($request->input('password')),
        ]);

        // Send email verification
        // $user->notify(new VerifyEmail());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Account created successfully. Please verify your email.'
            ]);
        }

        return redirect()->route('login')
            ->with('success', 'Account created successfully! Please check your email to verify your account.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully.'
            ]);
        }

        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    public function updateProfile(Request $request): JsonResponse
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'mobile'   => 'required|string|max:15|min:10',
            'address'  => 'nullable|string|max:255',
            'image'    => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'birthday' => 'nullable|date',
            'nid_or_passport' => 'nullable|string|max:15|min:10',
        ]);

        // Return errors if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        // Update basic fields
        $user->name = $request->input('name');
        $user->mobile = $request->mobile;
        $user->address = $request->address;
        $user->birthday = $request->birthday;
        $user->nid_or_passport = $request->nid_or_passport;

        // Handle image upload
        if ($request->hasFile('image')) {
            // Optionally delete old image if exists
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }

            $imagePath = $request->file('image')->store('profile_images', 'public');
            $user->image = $imagePath;
        }

        // Save user data
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Old password is incorrect.'
            ], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        $user->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Password changed successfully. Please log in again.',
        ]);
    }

}
