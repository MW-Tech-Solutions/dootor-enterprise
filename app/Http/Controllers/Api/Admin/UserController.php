<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::query()->with(['kycProfile', 'storefrontSetting'])->latest();

        if ($role = $request->query('role', $request->route('role'))) {
            $query->where('role', $role);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        return response()->json(['users' => $query->paginate(50)]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'status' => ['sometimes', Rule::in(['Approved', 'Pending', 'Rejected', 'Disabled'])],
            'role' => ['sometimes', Rule::in(['admin', 'vendor', 'client'])],
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

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json(['message' => 'User deleted.']);
    }
}
