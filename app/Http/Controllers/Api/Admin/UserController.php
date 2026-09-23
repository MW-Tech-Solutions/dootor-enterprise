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
        $query = User::query()->with(['kycProfile', 'storefrontSetting', 'roles'])->latest();

        if ($role = $request->query('role', $request->route('role'))) {
            $query->where(function ($q) use ($role) {
                $q->where('role', $role)
                  ->orWhereHas('roles', function ($rQ) use ($role) {
                      $rQ->where('slug', $role)->orWhere('name', $role)->orWhere('roles.id', $role);
                  });
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        return response()->json(['users' => $query->paginate(50)]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $allRoleSlugs = \App\Models\Role::pluck('slug')->toArray();
        $allRoleIds = \App\Models\Role::pluck('id')->toArray();
        $allowedRoles = array_unique(array_merge(['admin', 'vendor', 'client'], $allRoleSlugs, array_map('strval', $allRoleIds)));

        $data = $request->validate([
            'status' => ['sometimes', Rule::in(['Approved', 'Pending', 'Rejected', 'Disabled'])],
            'role' => ['sometimes', Rule::in($allowedRoles)],
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'avatar_url' => ['nullable', 'string', 'max:2048'],
        ]);

        if (isset($data['role'])) {
            $selectedVal = $data['role'];
            $roleModel = is_numeric($selectedVal) 
                ? \App\Models\Role::find((int) $selectedVal) 
                : \App\Models\Role::where('slug', $selectedVal)->orWhere('name', $selectedVal)->first();

            if ($roleModel) {
                $user->roles()->sync([$roleModel->id]);
                $data['role'] = in_array($roleModel->slug, ['client', 'vendor']) ? $roleModel->slug : 'admin';
            } else {
                if (in_array($selectedVal, ['client', 'vendor'])) {
                    $user->roles()->detach();
                }
            }
        }

        $user->update($data);

        return response()->json(['user' => $user->fresh()->load(['kycProfile', 'storefrontSetting', 'roles'])]);
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json(['message' => 'User deleted.']);
    }
}
