<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AggregatorAuthController extends Controller
{
    // Register Aggregator
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'phone' => 'required|unique:users',
            'gender' => 'required',
            'dob' => 'required|date',
            'address' => 'required|string',
            'referral_code' => 'nullable|string',
            'transaction_pin' => 'required|string|max:6',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'dob' => $request->dob,
            'address' => $request->address,
            'referral_code' => $request->referral_code,
            'transaction_pin' => bcrypt($request->transaction_pin),
            'password' => bcrypt($request->password),
            'role' => 'Aggregator',
        ]);

        $token = $user->createToken('aggregator_token')->plainTextToken;

        return response()->json([
            'message' => 'Aggregator registered successfully.',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    // Login Aggregator
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->where('role', 'Aggregator')->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid login credentials.'], 401);
        }

        $token = $user->createToken('aggregator_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'user' => $user,
            'token' => $token,
        ]);
    }

    // Logout Aggregator
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out.']);
    }
}
