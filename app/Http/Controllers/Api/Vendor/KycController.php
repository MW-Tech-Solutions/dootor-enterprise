<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KycController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'kyc' => $request->user()->kycProfile()->firstOrCreate([], ['status' => 'NotStarted']),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nin' => ['required', 'string', 'size:11'],
            'id_card_url' => ['required', 'string', 'max:2048'],
            'proof_of_address_url' => ['required', 'string', 'max:2048'],
            'personal_image_url' => ['required', 'string', 'max:2048'],
            'bank_name' => ['required', 'string', 'max:255'],
            'account_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:50'],
            'terms_agreed' => ['accepted'],
            'privacy_agreed' => ['accepted'],
            'data_consent_agreed' => ['accepted'],
            'digital_signature' => ['required', 'string', 'max:255'],
        ]);

        $kyc = $request->user()->kycProfile()->updateOrCreate([], [
            ...$data,
            'status' => 'Submitted',
            'rejection_reason' => null,
        ]);

        $request->user()->update(['status' => 'Pending']);

        return response()->json(['kyc' => $kyc]);
    }
}
