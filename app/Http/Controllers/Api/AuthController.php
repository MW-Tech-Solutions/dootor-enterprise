<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'min:2', 'max:255'],
            'last_name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['nullable', Rule::in(['vendor', 'client'])],
            'registered_by_vendor_id' => ['nullable', 'exists:users,id'],
        ]);

        $role = $data['role'] ?? 'vendor';
        $data['role'] = $role;
        $data['status'] = $role === 'vendor' ? 'Pending' : 'Approved';
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        $token = $user->createToken('web')->plainTextToken;

        return response()->json([
            'user' => $user->load(['kycProfile', 'storefrontSetting']),
            'token' => $token,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! $user->password || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if ($user->status === 'Disabled') {
            abort(403, 'Your account is disabled.');
        }

        $token = $user->createToken('web')->plainTextToken;

        return response()->json([
            'user' => $user->load(['kycProfile', 'storefrontSetting']),
            'token' => $token,
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user()->load(['kycProfile', 'storefrontSetting']),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logged out.']);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        Password::sendResetLink($request->only('email'));

        return response()->json(['message' => 'If that email exists, a reset link has been sent.']);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'avatar_url' => ['nullable', 'string', 'max:2048'],
        ]);

        $user->update($data);

        return response()->json(['user' => $user->fresh()->load(['kycProfile', 'storefrontSetting'])]);
    }
}
