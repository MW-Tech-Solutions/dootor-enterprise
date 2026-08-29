<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApprovedVendor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === 'vendor' && $user->status !== 'Approved') {
            return redirect()->route('vendor.kyc')->with('error', 'Your KYC profile must be approved to access this page.');
        }

        return $next($request);
    }
}
