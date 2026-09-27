<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        // 1. Check legacy user role attribute
        if (in_array($user->role, $roles, true)) {
            return $next($request);
        }

        // 2. Check assigned RBAC roles
        if ($user->roles()->whereIn('slug', $roles)->exists()) {
            return $next($request);
        }

        // 3. Allow staff/admin RBAC accounts to enter administrative route group when 'admin' is checked
        if (in_array('admin', $roles, true) && ($user->isAdmin() || $user->roles()->exists())) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        abort(403, 'You are not authorized to access this resource.');
    }
}
