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
        // Vendor registration disabled for the mean time - redirecting to client registration
        return redirect()->route('register.client');
    }

    public function registerWeb(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'min:2', 'max:255'],
            'last_name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'country' => ['required', 'string', Rule::in(\App\Services\AfricanLocationService::countryNames())],
            'state' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!empty($data['state']) && !\App\Services\AfricanLocationService::isValidPair($data['country'], $data['state'])) {
            throw ValidationException::withMessages([
                'state' => ["The selected state/region does not belong to {$data['country']}."],
            ]);
        }

        $data['role'] = 'client';
        $data['status'] = 'Approved'; // Clients are auto-approved
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('client.dashboard')->with('success', 'Welcome to the platform!');
    }

    public function showRegisterClient()
    {
        return view('auth.register-client', [
            'africanCountries' => \App\Services\AfricanLocationService::allCountries(),
            'defaultCountry' => 'Nigeria',
        ]);
    }

    public function registerClientWeb(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'min:2', 'max:255'],
            'last_name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'country' => ['required', 'string', Rule::in(\App\Services\AfricanLocationService::countryNames())],
            'state' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!empty($data['state']) && !\App\Services\AfricanLocationService::isValidPair($data['country'], $data['state'])) {
            throw ValidationException::withMessages([
                'state' => ["The selected state/region does not belong to {$data['country']}."],
            ]);
        }

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
        $email = strtolower(trim($request->email));

        $user = \App\Models\User::where('email', $email)->first();
        if (!$user) {
            return back()->withInput()->with('error', 'No registered account found matching that email address.');
        }

        // Generate 6-character uppercase alphanumeric code
        $code = strtoupper(substr(str_shuffle('23456789ABCDEFGHJKLMNPQRSTUVWXYZ'), 0, 6));

        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $code,
                'created_at' => now(),
            ]
        );

        session(['reset_password_email' => $user->email]);

        // Send Email via EmailNotificationService
        $settings = \App\Models\SystemSetting::first();
        $companyName = $settings->platform_name ?? config('app.name', 'DOOTOR ENTERPRISES');

        $htmlBody = "
        <div style='font-family: Arial, sans-serif; max-width: 550px; margin: 0 auto; padding: 25px; border: 1px solid #e2e8f0; border-radius: 12px; background: #ffffff;'>
            <h2 style='color: #004225; margin-bottom: 10px;'>Password Reset Request</h2>
            <p style='color: #475569; font-size: 14px;'>Hello <strong>" . e($user->name) . "</strong>,</p>
            <p style='color: #475569; font-size: 14px;'>You requested to reset your password on <strong>" . e($companyName) . "</strong>. Use the 6-character alphanumeric verification code below to verify your identity and set a new password:</p>
            <div style='text-align: center; margin: 25px 0;'>
                <span style='font-size: 32px; font-weight: 800; letter-spacing: 8px; color: #004225; background: #f1f5f9; padding: 14px 28px; border-radius: 8px; border: 2px dashed #004225; display: inline-block; font-family: monospace;'>" . e($code) . "</span>
            </div>
            <p style='color: #64748b; font-size: 12px; text-align: center;'>This verification code is valid for 30 minutes. If you did not request this reset, please secure your account.</p>
        </div>
        ";

        \App\Services\EmailNotificationService::send('password_reset_code', $user->email, [
            'code' => $code,
            'subject' => "[{$companyName}] Your Password Reset Code: {$code}",
            'message' => $htmlBody,
        ], null, $user);

        return redirect()->route('password.code')->with('success', 'A 6-character verification code has been sent to your email. Please check your inbox and enter the code below.');
    }

    public function showVerifyCode(Request $request)
    {
        $email = session('reset_password_email') ?? $request->query('email');
        if (empty($email)) {
            return redirect()->route('password.request')->with('error', 'Please enter your email address to request a reset code.');
        }

        return view('auth.verify-code', compact('email'));
    }

    public function verifyResetCode(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'string', 'size:6'],
        ]);

        $email = strtolower(trim($data['email']));
        $code = strtoupper(trim($data['code']));

        $tokenRecord = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$tokenRecord || strtoupper(trim($tokenRecord->token)) !== $code) {
            return back()->withInput()->with('error', 'Invalid verification code. Please check your email and try again.');
        }

        if (\Carbon\Carbon::parse($tokenRecord->created_at)->addMinutes(30)->isPast()) {
            return back()->withInput()->with('error', 'The verification code has expired (30 minute limit). Please request a new code.');
        }

        session(['reset_password_verified_email' => $email]);

        return redirect()->route('password.new')->with('success', 'Code verified successfully! Please enter your new password below.');
    }

    public function showNewPassword()
    {
        $email = session('reset_password_verified_email');
        if (empty($email)) {
            return redirect()->route('password.request')->with('error', 'Session expired. Please request a new password reset code.');
        }

        return view('auth.new-password', compact('email'));
    }

    public function updatePasswordWeb(Request $request)
    {
        $email = session('reset_password_verified_email');
        if (empty($email)) {
            return redirect()->route('password.request')->with('error', 'Session expired. Please request a new password reset code.');
        }

        $data = $request->validate([
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = \App\Models\User::where('email', $email)->first();
        if (!$user) {
            return redirect()->route('password.request')->with('error', 'User account not found.');
        }

        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($data['new_password']),
        ]);

        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $email)->delete();
        session()->forget(['reset_password_email', 'reset_password_verified_email']);

        \App\Services\AuditLogger::log('password_reset_completed', 'User', (string) $user->id, $user->name, null, null);

        return redirect()->route('login')->with('success', 'Password reset successful! You can now log in with your new password.');
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
