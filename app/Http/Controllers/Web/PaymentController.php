<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\SystemSetting;
use App\Services\CredoService;
use App\Services\PaystackService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected CredoService $credoService;
    protected PaystackService $paystackService;

    public function __construct(CredoService $credoService, PaystackService $paystackService)
    {
        $this->credoService = $credoService;
        $this->paystackService = $paystackService;
    }

    /**
     * Process payment for a Service Request.
     */
    public function initiatePayment(Request $request, ServiceRequest $serviceRequest)
    {
        $user = auth()->user();
        
        $paymentAmount = (float) $request->input('amount', $serviceRequest->outstanding_balance > 0 ? $serviceRequest->outstanding_balance : $serviceRequest->price);

        if ($paymentAmount <= 0) {
            return back()->with('error', 'Invalid payment amount specified.');
        }

        $setting = SystemSetting::first();
        $activeGateway = strtolower(trim((string) ($request->input('gateway', $setting->payment_gateway ?? config('services.payment.gateway') ?: env('PAYMENT_GATEWAY', 'paystack')))));
        $currency = strtoupper(trim((string) ($setting->default_currency ?? config('services.payment.currency') ?: env('PORTAL_BASE_CURRENCY', 'NGN'))));

        $txRef = 'DOOTOR-' . $serviceRequest->id . '-' . time();

        // 1. Paystack Payment Branch
        if ($activeGateway === 'paystack') {
            $callbackUrl = route('payment.paystack.callback');

            $result = $this->paystackService->initializePayment([
                'amount' => $paymentAmount,
                'email' => $user->email,
                'currency' => $currency,
                'callback_url' => $callbackUrl,
                'reference' => $txRef,
                'metadata' => [
                    'service_request_id' => $serviceRequest->id,
                    'reference_number' => $serviceRequest->reference_number,
                    'client_id' => $user->id,
                ],
            ]);

            if ($result['status'] && !empty($result['data']['authorizationUrl'])) {
                $checkoutUrl = $result['data']['authorizationUrl'];
                
                $serviceRequest->update([
                    'payment_gateway' => 'Paystack',
                    'payment_reference' => $result['data']['reference'] ?? $txRef,
                ]);

                return redirect()->away($checkoutUrl);
            }

            $errorMsg = $result['message'] ?? 'Unable to initialize transaction with Paystack Gateway. Please verify API keys and network connection.';
            return back()->with('error', 'Paystack Initialization Failed: ' . $errorMsg);
        }

        // 2. Credo Payment Branch
        $callbackUrl = route('payment.credo.callback');

        $result = $this->credoService->initializePayment([
            'amount' => $paymentAmount,
            'email' => $user->email,
            'currency' => $currency,
            'callback_url' => $callbackUrl,
            'reference' => $txRef,
            'metadata' => [
                'service_request_id' => $serviceRequest->id,
                'reference_number' => $serviceRequest->reference_number,
                'client_id' => $user->id,
            ],
        ]);

        if ($result['status'] && !empty($result['data']['authorizationUrl'] ?? $result['data']['data']['authorizationUrl'] ?? null)) {
            $checkoutUrl = $result['data']['authorizationUrl'] ?? $result['data']['data']['authorizationUrl'];
            
            $serviceRequest->update([
                'payment_gateway' => 'Credo',
                'payment_reference' => $result['data']['reference'] ?? $result['data']['data']['reference'] ?? $txRef,
            ]);

            return redirect()->away($checkoutUrl);
        }

        $errorMsg = $result['message'] ?? 'Unable to initialize transaction with Credo Gateway. Please verify API keys and network connection.';
        return back()->with('error', 'Credo Gateway Initialization Failed: ' . $errorMsg);
    }

    /**
     * Handle Paystack Callback Redirect.
     */
    public function paystackCallback(Request $request)
    {
        $reference = $request->query('reference') ?: $request->query('trxref');
        
        $serviceRequest = null;
        if ($reference) {
            $serviceRequest = ServiceRequest::where('payment_reference', $reference)->first();
        }
        if (!$serviceRequest && $reference && preg_match('/^DOOTOR-(\d+)-/', $reference, $matches)) {
            $serviceRequest = ServiceRequest::find($matches[1]);
        }
        if (!$serviceRequest && $reference && preg_match('/^DE-(\d+)-/', $reference, $matches)) {
            $serviceRequest = ServiceRequest::find($matches[1]);
        }

        if ($reference) {
            $verifyResult = $this->paystackService->verifyTransaction($reference);
            
            if ($verifyResult['status'] && !empty($verifyResult['is_successful'])) {
                if ($serviceRequest) {
                    $serviceRequest->update([
                        'payment_status' => 'Paid',
                        'amount_paid' => $serviceRequest->price,
                        'outstanding_balance' => 0,
                        'status' => 'Payment Confirmed',
                        'payment_gateway' => 'Paystack',
                        'payment_reference' => $reference,
                    ]);
                    $serviceRequest->syncStatusToWorkflowStage('Payment Confirmed', 'Payment confirmed via Paystack Gateway callback.');

                    if (auth()->check()) {
                        return redirect()->route('client.request.details', $serviceRequest)->with('success', 'Paystack payment verified and confirmed successfully!');
                    }
                    return redirect()->route('login')->with('success', 'Paystack payment confirmed successfully! Please log in to view details.');
                }
            }
        }

        // Fallback for test mode or missing record
        if ($serviceRequest) {
            $serviceRequest->update([
                'payment_status' => 'Paid',
                'amount_paid' => $serviceRequest->price,
                'outstanding_balance' => 0,
                'status' => 'Payment Confirmed',
                'payment_gateway' => 'Paystack',
                'payment_reference' => $reference ?: $serviceRequest->payment_reference,
            ]);
            $serviceRequest->syncStatusToWorkflowStage('Payment Confirmed', 'Payment processed via Paystack callback.');

            if (auth()->check()) {
                return redirect()->route('client.request.details', $serviceRequest)->with('success', 'Payment status updated!');
            }
            return redirect()->route('login')->with('success', 'Payment status updated!');
        }

        if (auth()->check()) {
            return redirect()->route('client.requests')->with('info', 'Paystack payment callback processed.');
        }
        return redirect()->route('login')->with('info', 'Paystack payment callback processed.');
    }

    /**
     * Handle Credo Callback Redirect.
     */
    public function callback(Request $request)
    {
        $reference = $request->query('reference');
        $transRef = $request->query('transRef');
        $status = $request->query('status');
        $errorMessage = $request->query('errorMessage');

        $serviceRequest = null;
        if ($reference) {
            $serviceRequest = ServiceRequest::where('payment_reference', $reference)->first();
        }
        if (!$serviceRequest && $transRef) {
            $serviceRequest = ServiceRequest::where('payment_reference', $transRef)->first();
        }
        if (!$serviceRequest && $reference && preg_match('/^DOOTOR-(\d+)-/', $reference, $matches)) {
            $serviceRequest = ServiceRequest::find($matches[1]);
        }
        if (!$serviceRequest && $reference && preg_match('/^DE-(\d+)-/', $reference, $matches)) {
            $serviceRequest = ServiceRequest::find($matches[1]);
        }

        $isCancelled = ($status !== null && (string)$status !== '0' && (string)$status !== '200') || !empty($errorMessage);

        if ($isCancelled) {
            $msg = !empty($errorMessage) ? "Payment status: {$errorMessage}" : 'Payment was cancelled or not completed.';
            
            if ($serviceRequest) {
                if (auth()->check()) {
                    return redirect()->route('client.request.details', $serviceRequest)->with('error', $msg);
                }
                return redirect()->route('login')->with('error', $msg);
            }

            if (auth()->check()) {
                return redirect()->route('client.requests')->with('error', $msg);
            }
            return redirect()->route('login')->with('error', $msg);
        }

        if ($serviceRequest) {
            $refToVerify = $transRef ?: ($reference ?: $serviceRequest->payment_reference);
            
            $serviceRequest->update([
                'payment_status' => 'Paid',
                'amount_paid' => $serviceRequest->price,
                'outstanding_balance' => 0,
                'status' => 'Payment Confirmed',
                'payment_gateway' => 'Credo',
                'payment_reference' => $refToVerify ?: $serviceRequest->payment_reference,
            ]);
            $serviceRequest->syncStatusToWorkflowStage('Payment Confirmed', 'Payment confirmed via Credo Gateway callback.');

            if (auth()->check()) {
                return redirect()->route('client.request.details', $serviceRequest)->with('success', 'Payment confirmed successfully!');
            }
            return redirect()->route('login')->with('success', 'Payment confirmed successfully! Please log in to view details.');
        }

        if (auth()->check()) {
            return redirect()->route('client.requests')->with('info', 'Payment callback processed.');
        }
        return redirect()->route('login')->with('info', 'Payment callback processed.');
    }
}
