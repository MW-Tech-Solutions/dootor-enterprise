<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ApprovalController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'vendors' => User::with('kycProfile')
                ->where('role', 'vendor')
                ->where('status', 'Pending')
                ->latest()
                ->get(),
        ]);
    }

    public function show(User $vendor): JsonResponse
    {
        abort_unless($vendor->role === 'vendor', 404);

        return response()->json(['vendor' => $vendor->load(['kycProfile', 'storefrontSetting'])]);
    }

    public function approve(User $vendor): JsonResponse
    {
        abort_unless($vendor->role === 'vendor', 404);

        $vendor->update(['status' => 'Approved']);
        $vendor->kycProfile()->updateOrCreate([], ['status' => 'Approved', 'rejection_reason' => null]);
        $this->sendKycDecisionMail($vendor->fresh(), 'Approved');

        return response()->json(['vendor' => $vendor->fresh()->load('kycProfile')]);
    }

    public function reject(Request $request, User $vendor): JsonResponse
    {
        abort_unless($vendor->role === 'vendor', 404);

        $data = $request->validate(['reason' => ['required', 'string', 'min:5']]);
        $vendor->update(['status' => 'Rejected']);
        $vendor->kycProfile()->updateOrCreate([], [
            'status' => 'Rejected',
            'rejection_reason' => $data['reason'],
        ]);
        $this->sendKycDecisionMail($vendor->fresh(), 'Rejected', $data['reason']);

        return response()->json(['vendor' => $vendor->fresh()->load('kycProfile')]);
    }

    private function sendKycDecisionMail(User $vendor, string $status, ?string $reason = null): void
    {
        $settings = SystemSetting::first();
        $companyName = $settings?->company_name ?: $settings?->platform_name ?: config('app.name');
        $logoUrl = $settings?->logo_url;
        $vendorName = trim($vendor->first_name.' '.$vendor->last_name) ?: 'Vendor';
        $isApproved = $status === 'Approved';
        $subject = $isApproved
            ? "Your {$companyName} KYC has been approved"
            : "Your {$companyName} KYC needs correction";

        $accent = $isApproved ? '#16a34a' : '#dc2626';
        $statusLabel = $isApproved ? 'KYC Approved' : 'KYC Rejected';
        $message = $isApproved
            ? 'Your vendor verification has been completed successfully. You can now access your vendor dashboard and continue setting up your services.'
            : 'Your KYC submission could not be approved yet. Please review the reason below, update your information, and submit the KYC form again.';

        $html = view('emails.kyc-decision', [
            'accent' => $accent,
            'companyName' => $companyName,
            'logoUrl' => $logoUrl,
            'message' => $message,
            'reason' => $reason,
            'statusLabel' => $statusLabel,
            'vendorName' => $vendorName,
            'isApproved' => $isApproved,
        ])->render();

        try {
            Mail::html($html, function ($mail) use ($vendor, $subject, $companyName) {
                $mail->to($vendor->email)
                    ->subject($subject)
                    ->from(config('mail.from.address'), $companyName);
            });
        } catch (Throwable $error) {
            Log::error('Unable to send KYC decision email.', [
                'vendor_id' => $vendor->id,
                'status' => $status,
                'error' => $error->getMessage(),
            ]);
        }
    }
}
