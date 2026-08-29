<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $requests = $request->user()->clientRequests()->latest()->get();

        return response()->json([
            'user' => $request->user(),
            'requests' => $requests,
            'stats' => [
                'total' => $requests->count(),
                'completed' => $requests->where('status', 'Completed')->count(),
                'pending' => $requests->whereIn('status', ['Pending', 'Processing'])->count(),
            ],
        ]);
    }
}
