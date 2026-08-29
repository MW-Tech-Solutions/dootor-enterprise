<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function loginWeb(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->has('remember');

        if (!Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $request->session()->regenerate();
        $user = Auth::user();

        if ($user->status === 'Disabled') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Your account has been disabled.');
        }

        return $this->redirectUser($user);
    }

    public function showRegisterVendor()
    {
        return view('auth.register');
    }

    public function registerWeb(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'min:2', 'max:255'],
            'last_name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $data['role'] = 'vendor';
        $data['status'] = 'Pending'; // Needs admin approval/KYC submission
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('vendor.kyc')->with('success', 'Registration successful! Please complete your KYC details to be approved.');
    }

    public function showRegisterClient()
    {
        return view('auth.register-client');
    }

    public function registerClientWeb(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'min:2', 'max:255'],
            'last_name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $data['role'] = 'client';
        $data['status'] = 'Approved'; // Clients are auto-approved
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('client.dashboard')->with('success', 'Welcome to the platform!');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function forgotPasswordWeb(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        Password::sendResetLink($request->only('email'));

        return back()->with('success', 'If that email exists, a password reset link has been sent to it.');
    }

    public function logoutWeb(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logged out successfully.');
    }

    protected function redirectUser($user)
    {
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'vendor') {
            if ($user->status === 'Pending' || $user->status === 'Rejected') {
                return redirect()->route('vendor.kyc');
            }
            return redirect()->route('vendor.dashboard');
        } else {
            return redirect()->route('client.dashboard');
        }
    }
}
