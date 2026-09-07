<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Services\CredoService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected CredoService $credoService;

    public function __construct(CredoService $credoService)
    {
        $this->credoService = $credoService;
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

        $callbackUrl = route('payment.credo.callback');

        $result = $this->credoService->initializePayment([
            'amount' => $paymentAmount,
            'email' => $user->email,
            'callback_url' => $callbackUrl,
            'reference' => 'DE-' . $serviceRequest->id . '-' . time(),
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
                'payment_reference' => $result['data']['reference'] ?? $result['data']['data']['reference'] ?? ('DE-' . $serviceRequest->id . '-' . time()),
            ]);

            return redirect()->away($checkoutUrl);
        }

        $errorMsg = $result['message'] ?? 'Unable to initialize transaction with Credo Gateway. Please verify API keys and network connection.';
        return back()->with('error', 'Credo Gateway Initialization Failed: ' . $errorMsg);
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

        // Locate ServiceRequest record by reference, transRef, or reference ID pattern (e.g. DE-2-1788790463)
        $serviceRequest = null;
        if ($reference) {
            $serviceRequest = ServiceRequest::where('payment_reference', $reference)->first();
        }
        if (!$serviceRequest && $transRef) {
            $serviceRequest = ServiceRequest::where('payment_reference', $transRef)->first();
        }
        if (!$serviceRequest && $reference && preg_match('/^DE-(\d+)-/', $reference, $matches)) {
            $serviceRequest = ServiceRequest::find($matches[1]);
        }

        // Check if payment was cancelled or failed on Credo gateway
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

        // Handle Successful Payment
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
