@extends('layouts.dashboard')

@section('title', 'Moneris Secure Checkout — ' . ($settings->platform_name ?? 'DOOTOR ENTERPRISES'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                    <div>
                        <h4 class="fw-bold text-dark mb-1"><i class="bi bi-shield-lock-fill text-success me-2"></i>Moneris Secure Checkout</h4>
                        <p class="text-secondary small mb-0">Application Ref: <span class="fw-bold font-monospace">{{ $serviceRequest->reference_number }}</span></p>
                    </div>
                    <div class="text-end">
                        <div class="small text-muted">Amount Due</div>
                        <div class="h5 fw-bold text-success mb-0">{{ $currency }} {{ number_format($amount, 2) }}</div>
                    </div>
                </div>

                <div id="monerisCheckout" style="min-height: 460px;"></div>

                <div id="monerisLoading" class="text-center py-4 text-secondary small">
                    <div class="spinner-border spinner-border-sm text-success me-2" role="status"></div>
                    Loading encrypted Moneris payment form...
                </div>

                <div class="mt-3 text-center">
                    <a href="{{ route('client.request.details', $serviceRequest->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-4">
                        <i class="bi bi-arrow-left me-1"></i> Cancel &amp; Return to Application
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="monerisCallbackForm" action="{{ route('payment.moneris.callback') }}" method="POST" class="d-none">
    @csrf
    <input type="hidden" name="ticket" value="{{ $ticket }}">
    <input type="hidden" name="service_request_id" value="{{ $serviceRequest->id }}">
</form>
@endsection

@section('scripts')
<script src="{{ $scriptUrl }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const loader = document.getElementById('monerisLoading');
    if (typeof monerisCheckout === 'undefined') {
        loader.innerHTML = '<div class="alert alert-danger small">Unable to load Moneris Checkout SDK. Please refresh or check your network.</div>';
        return;
    }

    const myCheckout = new monerisCheckout();
    myCheckout.setMode("{{ $environment }}");
    myCheckout.setCheckoutDiv("monerisCheckout");

    myCheckout.setCallback("page_loaded", function () {
        if (loader) loader.style.display = 'none';
    });

    myCheckout.setCallback("cancel_transaction", function () {
        window.location.href = "{{ route('client.request.details', $serviceRequest->id) }}";
    });

    myCheckout.setCallback("error_event", function (data) {
        if (loader) {
            loader.style.display = 'block';
            loader.innerHTML = '<div class="alert alert-warning small">Payment error encountered. Please try again.</div>';
        }
    });

    myCheckout.setCallback("payment_receipt", function () {
        document.getElementById('monerisCallbackForm').submit();
    });

    myCheckout.setCallback("payment_complete", function () {
        document.getElementById('monerisCallbackForm').submit();
    });

    myCheckout.startCheckout("{{ $ticket }}");
});
</script>
@endsection
