<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:10240'],
        ]);

        $path = \App\Helpers\FileUploadHelper::store($request->file('file'), 'uploads');

        return response()->json([
            'path' => '/storage/' . $path,
        ], 201);
    }
}
