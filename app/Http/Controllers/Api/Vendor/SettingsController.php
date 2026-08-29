<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user()->load('storefrontSetting'),
        ]);
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

        return response()->json(['user' => $user->fresh()->load('storefrontSetting')]);
    }

    public function updateStorefront(Request $request): JsonResponse
    {
        $data = $request->validate([
            'storefront_name' => ['nullable', 'string', 'max:255'],
            'storefront_about' => ['nullable', 'string'],
            'storefront_template' => ['nullable', Rule::in(['classic', 'editorial', 'compact', 'showcase'])],
            'theme' => ['nullable', Rule::in(['light', 'dark'])],
            'color_palette' => ['nullable', Rule::in(['default', 'blue', 'green', 'purple', 'red', 'orange', 'yellow', 'teal'])],
            'background_image_url' => ['nullable', 'string', 'max:2048'],
            'gradient' => ['nullable', 'string', 'max:255'],
        ]);

        $storefront = $request->user()->storefrontSetting()->updateOrCreate([], $data);

        return response()->json(['storefront' => $storefront]);
    }
}
