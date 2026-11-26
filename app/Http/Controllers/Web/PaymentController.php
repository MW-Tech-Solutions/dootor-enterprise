<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\SystemSetting;
use App\Services\CredoService;
use App\Services\MonerisService;
use App\Services\PayPalService;
use App\Services\PaystackService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected CredoService $credoService;
    protected PaystackService $paystackService;
    protected PayPalService $paypalService;
    protected MonerisService $monerisService;

    public function __construct(
        CredoService $credoService,
        PaystackService $paystackService,
        PayPalService $paypalService,
        MonerisService $monerisService
    ) {
        $this->credoService = $credoService;
        $this->paystackService = $paystackService;
        $this->paypalService = $paypalService;
        $this->monerisService = $monerisService;
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

        // 1. PayPal Payment Branch (REST API v2 Orders)
        if ($activeGateway === 'paypal') {
            $result = $this->paypalService->createOrder([
                'amount' => $paymentAmount,
                'currency' => $currency,
                'reference' => $txRef,
                'description' => ($serviceRequest->service_name ?? 'Consular Service') . ' — Ref ' . $serviceRequest->reference_number,
                'return_url' => route('payment.paypal.callback'),
                'cancel_url' => route('payment.paypal.cancel', ['ref' => $txRef]),
            ]);

            if ($result['status'] && !empty($result['data']['authorizationUrl'])) {
                $serviceRequest->update([
                    'payment_gateway' => 'PayPal',
                    'payment_reference' => $result['data']['order_id'] ?? $txRef,
                ]);

                return redirect()->away($result['data']['authorizationUrl']);
            }

            $errorMsg = $result['message'] ?? 'Unable to initialize PayPal order.';
            return back()->with('error', 'PayPal Initialization Failed: ' . $errorMsg);
        }

        // 2. Moneris Checkout v2 Branch
        if ($activeGateway === 'moneris') {
            $result = $this->monerisService->preload([
                'amount' => $paymentAmount,
                'order_no' => $txRef,
                'email' => $user->email,
                'first_name' => $user->first_name ?? 'Valued',
                'last_name' => $user->last_name ?? 'Client',
            ]);

            if ($result['status'] && !empty($result['data']['ticket'])) {
                $ticket = $result['data']['ticket'];
                $serviceRequest->update([
                    'payment_gateway' => 'Moneris',
                    'payment_reference' => $ticket,
                ]);

                return view('client.moneris-checkout', [
                    'serviceRequest' => $serviceRequest,
                    'ticket' => $ticket,
                    'amount' => $paymentAmount,
                    'currency' => $currency,
                    'environment' => $result['data']['environment'],
                    'scriptUrl' => $result['data']['script_url'],
                ]);
            }

            $errorMsg = $result['message'] ?? 'Unable to initialize Moneris checkout.';
            return back()->with('error', 'Moneris Initialization Failed: ' . $errorMsg);
        }

        // 3. Paystack Payment Branch
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

        if ($serviceRequest) {
            if (auth()->check()) {
                return redirect()->route('client.request.details', $serviceRequest)->with('error', 'Paystack payment was not confirmed or was cancelled.');
            }
            return redirect()->route('login')->with('error', 'Paystack payment was not confirmed or was cancelled.');
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
            
            $verifyResult = $this->credoService->verifyTransaction($refToVerify);
            if ($verifyResult['status'] && !empty($verifyResult['is_successful'])) {
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
                    return redirect()->route('client.request.details', $serviceRequest)->with('success', 'Credo payment confirmed successfully!');
                }
                return redirect()->route('login')->with('success', 'Credo payment confirmed successfully! Please log in to view details.');
            }

            if (auth()->check()) {
                return redirect()->route('client.request.details', $serviceRequest)->with('error', 'Credo Payment Verification Failed: ' . ($verifyResult['message'] ?? 'Payment not confirmed.'));
            }
            return redirect()->route('login')->with('error', 'Credo Payment Verification Failed: ' . ($verifyResult['message'] ?? 'Payment not confirmed.'));
        }

        if (auth()->check()) {
            return redirect()->route('client.requests')->with('info', 'Payment callback processed.');
        }
        return redirect()->route('login')->with('info', 'Payment callback processed.');
    }

    /**
     * Handle PayPal Return Callback (Captures Order via REST API v2).
     */
    public function paypalCallback(Request $request)
    {
        $orderId = $request->query('token');
        $serviceRequest = $orderId ? ServiceRequest::where('payment_reference', $orderId)->first() : null;

        if ($orderId) {
            $capture = $this->paypalService->captureOrder($orderId);
            if ($capture['status'] && !empty($capture['is_successful'])) {
                if ($serviceRequest) {
                    $serviceRequest->update([
                        'payment_status' => 'Paid',
                        'amount_paid' => $serviceRequest->price,
                        'outstanding_balance' => 0,
                        'status' => 'Payment Confirmed',
                        'payment_gateway' => 'PayPal',
                        'payment_reference' => $orderId,
                    ]);
                    $serviceRequest->syncStatusToWorkflowStage('Payment Confirmed', 'Payment captured and confirmed via PayPal.');

                    return auth()->check()
                        ? redirect()->route('client.request.details', $serviceRequest)->with('success', 'PayPal payment confirmed successfully!')
                        : redirect()->route('login')->with('success', 'PayPal payment confirmed! Please log in to view details.');
                }
            }

            $errMsg = $capture['message'] ?? 'PayPal payment could not be captured.';
            if ($serviceRequest) {
                return auth()->check()
                    ? redirect()->route('client.request.details', $serviceRequest)->with('error', $errMsg)
                    : redirect()->route('login')->with('error', $errMsg);
            }
        }

        return auth()->check()
            ? redirect()->route('client.requests')->with('error', 'PayPal payment was not completed.')
            : redirect()->route('login')->with('error', 'PayPal payment was not completed.');
    }

    /**
     * Handle PayPal Cancellation.
     */
    public function paypalCancel(Request $request)
    {
        $orderId = $request->query('token');
        $serviceRequest = $orderId ? ServiceRequest::where('payment_reference', $orderId)->first() : null;

        if ($serviceRequest && auth()->check()) {
            return redirect()->route('client.request.details', $serviceRequest)->with('error', 'PayPal payment was cancelled.');
        }

        return auth()->check()
            ? redirect()->route('client.requests')->with('error', 'PayPal payment was cancelled.')
            : redirect()->route('login')->with('error', 'PayPal payment was cancelled.');
    }

    /**
     * Handle Moneris Checkout v2 Callback (Server-to-Server Receipt Verification).
     */
    public function monerisCallback(Request $request)
    {
        $ticket = $request->input('ticket');
        $srId = $request->input('service_request_id');
        $serviceRequest = $srId ? ServiceRequest::find($srId) : ($ticket ? ServiceRequest::where('payment_reference', $ticket)->first() : null);

        if ($ticket) {
            $receipt = $this->monerisService->receipt($ticket);
            if ($receipt['status'] && !empty($receipt['is_successful']) && $serviceRequest) {
                $serviceRequest->update([
                    'payment_status' => 'Paid',
                    'amount_paid' => $serviceRequest->price,
                    'outstanding_balance' => 0,
                    'status' => 'Payment Confirmed',
                    'payment_gateway' => 'Moneris',
                    'payment_reference' => $ticket,
                ]);
                $serviceRequest->syncStatusToWorkflowStage('Payment Confirmed', 'Payment approved and verified via Moneris Checkout.');

                return auth()->check()
                    ? redirect()->route('client.request.details', $serviceRequest)->with('success', 'Moneris payment approved and confirmed successfully!')
                    : redirect()->route('login')->with('success', 'Moneris payment confirmed! Please log in to view details.');
            }

            $errMsg = $receipt['message'] ?? 'Moneris transaction was not approved.';
            if ($serviceRequest && auth()->check()) {
                return redirect()->route('client.request.details', $serviceRequest)->with('error', $errMsg);
            }
        }

        return auth()->check()
            ? redirect()->route('client.requests')->with('error', 'Moneris payment could not be verified.')
            : redirect()->route('login')->with('error', 'Moneris payment could not be verified.');
    }
}
