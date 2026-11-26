@extends('layouts.app')

@section('title', ($settings->platform_name ?? 'DOOTOR ENTERPRISES') . ' | Fast Business Registration, Passport & Document Services')
@section('meta_description', 'Premier international administrative document & consular consultancy. Fast-track global passport approvals, visas, court affidavits, corporate registrations, and verified identity records worldwide.')
@section('canonical_url', route('home'))

@section('seo_schema')
@if(isset($faqs) && $faqs->count() > 0)
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        @foreach($faqs as $f)
        {
            "@type": "Question",
            "name": {!! json_encode($f->question) !!},
            "acceptedAnswer": {
                "@type": "Answer",
                "text": {!! json_encode(strip_tags($f->answer)) !!}
            }
        }{{ !$loop->last ? ',' : '' }}
        @endforeach
    ]
}
</script>
@endif

@if(isset($primaryServices) && $primaryServices->count() > 0)
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@type": "ItemList",
    "name": "Administrative & Corporate Services",
    "itemListElement": [
        @foreach($primaryServices as $idx => $s)
        {
            "@type": "ListItem",
            "position": {{ $idx + 1 }},
            "item": {
                "@type": "Service",
                "name": {!! json_encode($s->name) !!},
                "description": {!! json_encode(strip_tags($s->description ?? $s->short_description ?? '')) !!},
                "provider": {
                    "@id": "{{ url('/#organization') }}"
                }
            }
        }{{ !$loop->last ? ',' : '' }}
        @endforeach
    ]
}
</script>
@endif
@endsection

@section('styles')
<style>
    /* Custom mobile nav - uses inline styles via JS so Tailwind cannot interfere */
    #mainNavbar {
        display: none;
    }
    @media (min-width: 992px) {
        #mainNavbar {
            display: flex !important;
            flex-direction: row;
            align-items: center;
            flex: 1;
        }
    }

    /* Social Media Handles Hover Styles */
    .footer-social-btn {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #ffffff !important;
        text-decoration: none;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        font-size: 16px;
    }
    .footer-social-btn i {
        color: #ffffff !important;
        transition: all 0.25s ease;
    }
    .footer-social-btn:hover {
        transform: translateY(-3px) scale(1.08);
        color: #ffffff !important;
    }
    .footer-social-btn:hover i {
        color: #ffffff !important;
    }
    .footer-social-btn.social-fb:hover {
        background-color: #1877F2 !important;
        border-color: #1877F2 !important;
        box-shadow: 0 6px 16px rgba(24, 119, 242, 0.45);
    }
    .footer-social-btn.social-x:hover {
        background-color: #000000 !important;
        border-color: #ffffff !important;
        box-shadow: 0 6px 16px rgba(255, 255, 255, 0.25);
    }
    .footer-social-btn.social-ig:hover {
        background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285AEB 90%) !important;
        border-color: #d6249f !important;
        box-shadow: 0 6px 16px rgba(214, 36, 159, 0.45);
    }
    .footer-social-btn.social-in:hover {
        background-color: #0A66C2 !important;
        border-color: #0A66C2 !important;
        box-shadow: 0 6px 16px rgba(10, 102, 194, 0.45);
    }
    .footer-social-btn.social-wa:hover {
        background-color: #25D366 !important;
        border-color: #25D366 !important;
        box-shadow: 0 6px 16px rgba(37, 211, 102, 0.45);
    }
    .footer-social-btn.social-yt:hover {
        background-color: #FF0000 !important;
        border-color: #FF0000 !important;
        box-shadow: 0 6px 16px rgba(255, 0, 0, 0.45);
    }
    .footer-social-btn.social-tt:hover {
        background-color: #000000 !important;
        border-color: #00f2fe !important;
        box-shadow: 0 6px 16px rgba(0, 242, 254, 0.45);
    }

    /* ========================================================
       PREMIUM HERO SECTION — Canvas Particles + 3D Depth
       ======================================================== */
    .hero-section {
        background: linear-gradient(135deg, #030a06 0%, #040d08 40%, #060f09 70%, #030a06 100%);
        position: relative;
        overflow: hidden;
        min-height: 88vh;
        display: flex;
        align-items: center;
        color: #ffffff;
    }

    /* Particle canvas fills hero */
    #heroParticleCanvas {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
        pointer-events: none;
    }

    /* Radial depth glow — gold top-right, subtle green bottom-left */
    .hero-section::before {
        content: '';
        position: absolute;
        top: -10%;
        right: -5%;
        width: 55vw;
        height: 55vw;
        background: radial-gradient(circle, rgba(212,175,55,0.07) 0%, rgba(212,175,55,0.02) 50%, transparent 70%);
        pointer-events: none;
        z-index: 1;
    }
    .hero-section::after {
        content: '';
        position: absolute;
        bottom: -10%;
        left: -5%;
        width: 40vw;
        height: 40vw;
        background: radial-gradient(circle, rgba(0,66,37,0.18) 0%, transparent 65%);
        pointer-events: none;
        z-index: 1;
    }

    /* ---- Text stagger-in animations ---- */
    .hero-stagger-child {
        opacity: 0;
        transform: translateY(28px);
        animation: heroStaggerIn 0.75s cubic-bezier(0.34,1.56,0.64,1) forwards;
    }
    .hero-stagger-child:nth-child(1) { animation-delay: 0.1s; }
    .hero-stagger-child:nth-child(2) { animation-delay: 0.22s; }
    .hero-stagger-child:nth-child(3) { animation-delay: 0.34s; }
    .hero-stagger-child:nth-child(4) { animation-delay: 0.46s; }
    .hero-stagger-child:nth-child(5) { animation-delay: 0.56s; }
    .hero-stagger-child:nth-child(6) { animation-delay: 0.66s; }
    @keyframes heroStaggerIn {
        to { opacity: 1; transform: translateY(0); }
    }

    /* ---- Live ping badge ---- */
    .hero-live-badge {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        background: rgba(212,175,55,0.08);
        border: 1px solid rgba(212,175,55,0.28);
        padding: 6px 18px;
        border-radius: 9999px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.8px;
        color: #D4AF37;
        backdrop-filter: blur(10px);
        text-transform: uppercase;
    }
    .hero-ping-wrap {
        position: relative;
        display: inline-flex;
        width: 9px;
        height: 9px;
    }
    .hero-ping-ring {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: #D4AF37;
        opacity: 0.7;
        animation: heroPingRing 2s cubic-bezier(0,0,0.2,1) infinite;
    }
    .hero-ping-core {
        position: relative;
        width: 9px;
        height: 9px;
        border-radius: 50%;
        background: #D4AF37;
    }
    @keyframes heroPingRing {
        75%, 100% { transform: scale(2.6); opacity: 0; }
    }

    /* ---- Split headline words (each word is a span) ---- */
    .hero-headline {
        font-size: clamp(2rem, 4.5vw, 3.4rem);
        font-weight: 800;
        line-height: 1.1;
        letter-spacing: -1px;
        color: #ffffff;
        margin-bottom: 0;
    }
    .hero-headline .word-gold {
        background: linear-gradient(135deg, #FFE57F 0%, #D4AF37 50%, #AA820A 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .hero-headline .word-rotate-wrap {
        display: inline-block;
        overflow: hidden;
        vertical-align: bottom;
        min-width: 260px;
    }
    .hero-headline .word-rotate-inner {
        display: inline-block;
        background: linear-gradient(135deg, #D4AF37 0%, #FFE57F 60%, #D4AF37 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        transition: transform 0.4s cubic-bezier(0.34,1.56,0.64,1), opacity 0.35s ease;
    }

    /* ---- CTA Buttons ---- */
    .hero-btn-primary {
        background: linear-gradient(135deg, #004225 0%, #006435 60%, #004225 100%);
        color: #fff !important;
        border: 1px solid rgba(212,175,55,0.35);
        box-shadow: 0 10px 28px -4px rgba(0,66,37,0.5), inset 0 1px 0 rgba(255,255,255,0.12);
        font-weight: 700;
        transition: all 0.28s cubic-bezier(0.34,1.56,0.64,1);
        position: relative;
        overflow: hidden;
    }
    .hero-btn-primary::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, transparent 0%, rgba(212,175,55,0.15) 50%, transparent 100%);
        transform: translateX(-100%);
        transition: transform 0.5s ease;
    }
    .hero-btn-primary:hover { transform: translateY(-3px) scale(1.02); box-shadow: 0 18px 36px -4px rgba(0,66,37,0.6), 0 0 0 1px rgba(212,175,55,0.3); }
    .hero-btn-primary:hover::after { transform: translateX(100%); }

    .hero-btn-ghost {
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.18);
        color: #fff !important;
        font-weight: 600;
        backdrop-filter: blur(10px);
        transition: all 0.28s cubic-bezier(0.34,1.56,0.64,1);
    }
    .hero-btn-ghost:hover { background: rgba(255,255,255,0.1); border-color: rgba(212,175,55,0.4); transform: translateY(-3px); }

    /* ---- Hero search bar ---- */
    .hero-search-bar {
        background: rgba(255,255,255,0.09);
        border: 1px solid rgba(255,255,255,0.22);
        border-radius: 50px;
        backdrop-filter: blur(12px);
        overflow: hidden;
        max-width: 500px;
        transition: border-color 0.25s ease, background 0.25s ease;
    }
    .hero-search-bar:focus-within {
        background: rgba(255,255,255,0.13);
        border-color: rgba(212,175,55,0.55);
    }
    .hero-search-bar input {
        color: #ffffff !important;
        caret-color: #FFE57F;
    }
    .hero-search-bar input::placeholder {
        color: rgba(255,255,255,0.72) !important;
        opacity: 1 !important;
    }

    /* ---- Stat counters ---- */
    .hero-stat {
        text-align: center;
        padding: 10px 18px;
        border-radius: 14px;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.08);
        backdrop-filter: blur(8px);
        transition: border-color 0.25s ease;
    }
    .hero-stat:hover { border-color: rgba(212,175,55,0.3); }
    .hero-stat-number {
        font-size: 1.55rem;
        font-weight: 800;
        background: linear-gradient(135deg, #FFE57F, #D4AF37);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1;
    }
    .hero-stat-label {
        font-size: 10.5px;
        font-weight: 600;
        color: rgba(255,255,255,0.5);
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin-top: 3px;
    }

    /* ---- Right: Document Pipeline SVG diagram area ---- */
    .hero-pipeline-wrap {
        position: relative;
        z-index: 3;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: heroFadeSlideIn 0.9s cubic-bezier(0.34,1.4,0.64,1) 0.3s both;
    }
    @keyframes heroFadeSlideIn {
        from { opacity: 0; transform: translateX(40px) scale(0.96); }
        to   { opacity: 1; transform: translateX(0) scale(1); }
    }

    /* Glass panel containing the pipeline */
    .hero-panel {
        background: rgba(10,20,14,0.72);
        backdrop-filter: blur(24px) saturate(160%);
        -webkit-backdrop-filter: blur(24px) saturate(160%);
        border: 1px solid rgba(212,175,55,0.14);
        border-radius: 28px;
        box-shadow: 0 32px 64px -16px rgba(0,0,0,0.75), 0 0 0 1px rgba(255,255,255,0.04), inset 0 1px 0 rgba(255,255,255,0.06);
        padding: 28px 24px;
        max-width: 480px;
        width: 100%;
        transform-style: preserve-3d;
        transition: transform 0.3s cubic-bezier(0.16,1,0.3,1), box-shadow 0.3s ease;
    }

    /* Floating micro-badges around the right panel */
    .hero-micro-badge {
        position: absolute;
        background: rgba(10,22,14,0.92);
        backdrop-filter: blur(14px);
        border: 1px solid rgba(255,255,255,0.14);
        border-radius: 16px;
        padding: 10px 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 12px 30px rgba(0,0,0,0.5);
        z-index: 10;
        font-size: 12px;
    }
    .hero-micro-badge:hover { border-color: rgba(212,175,55,0.5); }
    .hero-mb-1 { top: -18px; left: -24px; animation: mbFloat1 6s ease-in-out infinite; }
    .hero-mb-2 { top: -14px; right: -20px; animation: mbFloat2 7s ease-in-out infinite 0.4s; }
    .hero-mb-3 { bottom: -18px; left: 10px; animation: mbFloat1 5.5s ease-in-out infinite 0.9s; }
    .hero-mb-4 { bottom: 20px; right: -24px; animation: mbFloat2 7.5s ease-in-out infinite 1.3s; }
    @keyframes mbFloat1 {
        0%,100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-10px) rotate(1.2deg); }
    }
    @keyframes mbFloat2 {
        0%,100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(10px) rotate(-1.5deg); }
    }

    /* ---- Pipeline steps ---- */
    .pipe-step {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 10px 12px;
        border-radius: 12px;
        transition: background 0.2s ease;
        position: relative;
        z-index: 2;
    }
    .pipe-step:hover { background: rgba(255,255,255,0.03); }
    .pipe-step-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 15px;
    }
    .pipe-connector {
        width: 2px;
        height: 22px;
        margin-left: 29px;
        border-left: 2px dashed rgba(255,255,255,0.1);
        animation: connectorPulse 2s ease-in-out infinite;
    }
    .pipe-connector.active-line {
        border-left: 2px dashed rgba(212,175,55,0.4);
        animation: connectorPulse 1.6s ease-in-out infinite;
    }
    @keyframes connectorPulse {
        0%,100% { opacity: 0.4; }
        50% { opacity: 1; }
    }

    /* ---- Progress bar (pipeline) ---- */
    .pipe-progress-track {
        height: 5px;
        background: rgba(255,255,255,0.08);
        border-radius: 999px;
        overflow: hidden;
    }
    .pipe-progress-fill {
        height: 100%;
        width: 72%;
        background: linear-gradient(90deg, #004225, #D4AF37, #FFE57F);
        border-radius: 999px;
        animation: pipeFillGlow 2.5s ease-in-out infinite alternate;
    }
    @keyframes pipeFillGlow {
        0% { box-shadow: 0 0 8px rgba(212,175,55,0.3); }
        100% { box-shadow: 0 0 18px rgba(212,175,55,0.75); }
    }

    /* ========================================================
       HOMEPAGE SERVICES — Clean Icon Card Design
       ======================================================== */
    .service-catalog-card {
        border: 1px solid #e8edf3 !important;
        border-radius: 22px !important;
        transition: all 0.38s cubic-bezier(0.34,1.56,0.64,1) !important;
        background: #ffffff !important;
        box-shadow: 0 2px 16px -2px rgba(0,0,0,0.05) !important;
        overflow: hidden;
    }
    .service-catalog-card:hover {
        transform: translateY(-8px) scale(1.01);
        box-shadow: 0 24px 48px -8px rgba(0,66,37,0.15), 0 0 0 1.5px rgba(0,66,37,0.12) !important;
        border-color: rgba(0,66,37,0.2) !important;
    }

    /* ---- Service Icon Header (replaces image banner) ---- */
    .svc-icon-header {
        height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    .svc-icon-bg {
        position: absolute;
        inset: 0;
        opacity: 0.92;
    }
    .svc-icon-circle {
        width: 76px;
        height: 76px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 2;
        box-shadow: 0 8px 24px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.2);
        transition: transform 0.4s cubic-bezier(0.34,1.56,0.64,1);
    }
    .service-catalog-card:hover .svc-icon-circle {
        transform: scale(1.12) rotate(5deg);
    }
    .svc-icon-circle i {
        font-size: 2rem;
        color: #fff;
    }
    .svc-corner-seal {
        position: absolute;
        top: 12px;
        right: 12px;
        z-index: 3;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: rgba(10,20,14,0.75);
        backdrop-filter: blur(6px);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(212,175,55,0.5);
    }
    .svc-category-pill {
        position: absolute;
        top: 12px;
        left: 12px;
        z-index: 3;
        background: rgba(10,20,14,0.75);
        backdrop-filter: blur(6px);
        color: rgba(255,255,255,0.9);
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.5px;
        padding: 4px 10px;
        border-radius: 999px;
        border: 1px solid rgba(255,255,255,0.15);
    }

    /* Background pattern dots on icon header */
    .svc-icon-bg-dots {
        position: absolute;
        inset: 0;
        background-image: radial-gradient(rgba(255,255,255,0.08) 1px, transparent 1px);
        background-size: 18px 18px;
        z-index: 1;
    }

    /* ---- Sub-services button on card ---- */
    .btn-sub-services {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        padding: 6px 14px;
        font-size: 12px;
        font-weight: 600;
        color: #004225;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.34,1.56,0.64,1);
        text-decoration: none;
    }
    .btn-sub-services:hover {
        background: #004225;
        color: #fff;
        border-color: #004225;
        transform: scale(1.04);
    }
    .btn-sub-services .sub-count-badge {
        background: #004225;
        color: #fff;
        border-radius: 999px;
        padding: 1px 7px;
        font-size: 10px;
        font-weight: 700;
        transition: background 0.2s ease;
    }
    .btn-sub-services:hover .sub-count-badge {
        background: rgba(255,255,255,0.25);
    }

    /* ========================================================
       iOS-STYLE SUB-SERVICES MODAL (Full Screen Blur + Sheet)
       ======================================================== */
    .ios-modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(0, 0, 0, 0.55);
        backdrop-filter: blur(18px) saturate(160%);
        -webkit-backdrop-filter: blur(18px) saturate(160%);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.32s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .ios-modal-overlay.ios-open {
        opacity: 1;
        pointer-events: all;
    }
    .ios-modal-sheet {
        background: rgba(255, 255, 255, 0.97);
        backdrop-filter: blur(28px) saturate(180%);
        -webkit-backdrop-filter: blur(28px) saturate(180%);
        border: 1px solid rgba(255, 255, 255, 0.9);
        border-radius: 28px;
        width: 100%;
        max-width: 640px;
        max-height: 86vh;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transform: scale(0.92) translateY(24px);
        transition: transform 0.38s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 32px 80px -16px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(0, 0, 0, 0.05);
    }
    .ios-modal-overlay.ios-open .ios-modal-sheet {
        transform: scale(1) translateY(0);
    }
    .ios-modal-handle-bar {
        width: 40px;
        height: 5px;
        background: #d1d5db;
        border-radius: 999px;
        margin: 14px auto 0;
        flex-shrink: 0;
    }
    .ios-modal-header {
        padding: 16px 24px 12px;
        border-bottom: 1px solid #f1f5f9;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .ios-modal-title {
        font-size: 1.1rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.3px;
    }
    .ios-modal-subtitle {
        font-size: 12px;
        color: #64748b;
        margin-top: 2px;
    }
    .ios-modal-close {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #f1f5f9;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.34,1.56,0.64,1);
        flex-shrink: 0;
    }
    .ios-modal-close:hover { background: #e2e8f0; transform: scale(1.1); }
    .ios-modal-body {
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        padding: 16px 20px 32px;
        flex: 1;
    }
    .ios-sub-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 13px 16px;
        border-radius: 14px;
        background: #f8fafc;
        border: 1px solid #e8edf3;
        margin-bottom: 10px;
        transition: all 0.25s cubic-bezier(0.34,1.56,0.64,1);
        cursor: pointer;
        text-decoration: none;
        color: inherit;
    }
    .ios-sub-item:hover {
        background: #f0fdf4;
        border-color: rgba(0,66,37,0.25);
        transform: translateX(4px);
        box-shadow: 0 4px 14px rgba(0,66,37,0.08);
        color: #004225;
    }
    .ios-sub-item-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: linear-gradient(135deg, #004225, #006435);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 15px;
        flex-shrink: 0;
    }
    .ios-sub-item-name {
        font-size: 14px;
        font-weight: 600;
        color: #1e293b;
        line-height: 1.3;
    }
    .ios-sub-item-price {
        font-size: 11px;
        color: #64748b;
        margin-top: 1px;
    }
    .ios-modal-cta {
        margin: 0 20px 24px;
        flex-shrink: 0;
    }
    .ios-modal-cta .btn {
        background: linear-gradient(135deg, #004225, #006435);
        border: none;
        border-radius: 14px;
        font-weight: 700;
        padding: 14px;
        font-size: 15px;
        letter-spacing: 0.2px;
        box-shadow: 0 8px 20px rgba(0,66,37,0.3);
        transition: all 0.3s cubic-bezier(0.34,1.56,0.64,1);
    }
    .ios-modal-cta .btn:hover { transform: scale(1.02); box-shadow: 0 12px 28px rgba(0,66,37,0.4); }

    /* Modern Features Grid (Elevated Glass Architecture) */
    .modern-feature-card {
        border: 1px solid #e8edf3 !important;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.04) !important;
        transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
        background: #ffffff !important;
    }
    .modern-feature-card:hover {
        transform: translateY(-8px) !important;
        box-shadow: 0 20px 40px -8px rgba(0, 66, 37, 0.12), 0 0 0 1.5px rgba(0, 66, 37, 0.15) !important;
        border-color: rgba(0, 66, 37, 0.25) !important;
    }
    .feature-icon-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .feature-icon-box {
        width: 58px;
        height: 58px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .modern-feature-card:hover .feature-icon-box {
        transform: scale(1.1) rotate(5deg);
    }

    /* Floating Promo Pill Animation */
    #promoVideoPill button {
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        border: 1px solid rgba(212, 175, 55, 0.5) !important;
    }
    #promoVideoPill button:hover {
        transform: scale(1.05) translateY(-3px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4) !important;
        border-color: #D4AF37 !important;
    }

    /* ========================================================
       ON-SCROLL REVEAL ANIMATIONS (iOS Spring Physics)
       ======================================================== */
    .scroll-reveal {
        opacity: 0;
        transform: translateY(34px) scale(0.97);
        transition:
            opacity 0.65s cubic-bezier(0.22, 1, 0.36, 1),
            transform 0.7s cubic-bezier(0.22, 1, 0.36, 1);
        will-change: opacity, transform;
    }
    .scroll-reveal.reveal-left {
        transform: translateX(-32px);
    }
    .scroll-reveal.reveal-right {
        transform: translateX(32px);
    }
    .scroll-reveal.reveal-scale {
        transform: scale(0.92) translateY(20px);
    }
    .scroll-reveal.is-visible {
        opacity: 1 !important;
        transform: translate3d(0, 0, 0) scale(1) !important;
    }
    @media (prefers-reduced-motion: reduce) {
        .scroll-reveal {
            opacity: 1 !important;
            transform: none !important;
            transition: none !important;
        }
    }
</style>
@endsection

@section('body')
@php
    $user = Auth::user();
    
    // Fallback static services matching Next.js if database has none or just standard ones
    $staticServices = [
        ['title' => 'NIN', 'icon' => 'bi-fingerprint', 'description' => 'Secure your National Identification Number (NIN) registration, modification, or verification.'],
        ['title' => 'BVN', 'icon' => 'bi-bank', 'description' => 'Link and update your Bank Verification Number (BVN) across your financial accounts.'],
        ['title' => 'Passport', 'icon' => 'bi-passport', 'description' => 'Fast-track processing for new international passport applications or renewals.'],
        ['title' => 'Emergency Travel Certificate (ETC)', 'icon' => 'bi-file-earmark-badge', 'description' => 'Expedite emergency travel certificate issuance for urgent international travel.'],
        ['title' => 'Driver\'s Licence Authentication', 'icon' => 'bi-card-list', 'description' => 'Verify and authenticate driver\'s license letters and certification reports.'],
        ['title' => 'Visa: SEV, MEV, TWP, STR & eVisa', 'icon' => 'bi-ticket-perforated', 'description' => 'Smooth processing support for Single Entry, Multi Entry, Temporary Work Permits, and eVisas.'],
        ['title' => 'Police Report', 'icon' => 'bi-shield-check', 'description' => 'Apply for official character clearance, loss of documents, or police reports.'],
        ['title' => 'Newspaper Publication', 'icon' => 'bi-newspaper', 'description' => 'Publish official changes of name, lost items, or announcements in national news.'],
        ['title' => 'Court Affidavit of Name and Age', 'icon' => 'bi-journal-check', 'description' => 'Draft and process legally binding sworn court affidavits for name, age, or declarations.'],
        ['title' => 'LGA Certificate of Indigenization', 'icon' => 'bi-house-check', 'description' => 'Obtain official state/local government origin and indigene certificates.'],
        ['title' => 'Birth Certificate/Attestation by the National Population Commission (NPC)', 'icon' => 'bi-file-earmark-medical', 'description' => 'Acquire official birth registration records or attestation documents from the NPC.'],
    ];

    $dbPrimaryServices = isset($primaryServices) && $primaryServices->count() > 0
        ? $primaryServices
        : \App\Models\Service::where('status', 'Active')
            ->whereNull('parent_id')
            ->with(['subServices' => function($q) { 
                $q->where('status', 'Active')
                  ->orderByRaw('CASE WHEN priority IS NULL OR priority = 0 THEN 999999 ELSE priority END ASC')
                  ->orderBy('name', 'asc'); 
            }])
            ->orderByRaw('CASE WHEN priority IS NULL OR priority = 0 THEN 999999 ELSE priority END ASC')
            ->orderBy('name', 'asc')
            ->get();

    $allActiveServices = \App\Models\Service::where('status', 'Active')->get();

    if (!function_exists('getServiceCategory')) {
        function getServiceCategory($name) {
            $name = strtolower($name);
            if (str_contains($name, 'nin') || str_contains($name, 'bvn') || str_contains($name, 'licence') || str_contains($name, 'license') || str_contains($name, 'lga') || str_contains($name, 'indigen')) {
                return 'identity';
            }
            if (str_contains($name, 'passport') || str_contains($name, 'travel') || str_contains($name, 'visa') || str_contains($name, 'etc')) {
                return 'travel';
            }
            return 'legal';
        }
    }

    if (!function_exists('getServiceImage')) {
        function getServiceImage($name, $existingUrl = null) {
            if (!empty($existingUrl) && file_exists(public_path($existingUrl))) {
                return asset($existingUrl);
            }
            $n = strtolower($name);
            if (str_contains($n, 'passport')) return asset('images/services/passport.svg');
            if (str_contains($n, 'visa')) return asset('images/services/visa.svg');
            if (str_contains($n, 'nin')) return asset('images/services/nin.svg');
            if (str_contains($n, 'bvn')) return asset('images/services/bvn.svg');
            if (str_contains($n, 'emergency') || str_contains($n, 'etc')) return asset('images/services/emergency-travel.svg');
            if (str_contains($n, 'police')) return asset('images/services/police-report.svg');
            if (str_contains($n, 'affidavit')) return asset('images/services/court-affidavit.svg');
            if (str_contains($n, 'birth') || str_contains($n, 'npc')) return asset('images/services/birth-certificate.svg');
            if (str_contains($n, 'licence') || str_contains($n, 'license')) return asset('images/services/drivers-license.svg');
            if (str_contains($n, 'indigene') || str_contains($n, 'lga')) return asset('images/services/lga-indigene.svg');
            if (str_contains($n, 'newspaper')) return asset('images/services/newspaper.svg');
            if (str_contains($n, 'attorney') || str_contains($n, 'power')) return asset('images/services/power-of-attorney.svg');
            if (str_contains($n, 'reschedule') || str_contains($n, 'waiver')) return asset('images/services/appointment-reschedule.svg');
            if (str_contains($n, 'same day')) return asset('images/services/same-day-collection.svg');
            return asset('images/services/default-service.svg');
        }
    }
@endphp

<!-- Disclaimer Top Banner Marquee (High-Visibility Warning & Danger Styling) -->
<div id="disclaimerBanner" class="position-relative overflow-hidden py-2" style="background-color: #1a0505; border-bottom: 2px solid #ef4444; z-index: 1060;">
    <div class="d-flex align-items-center w-100">
        <div class="px-3 py-1 bg-danger text-white fw-bold d-flex align-items-center gap-1.5 flex-shrink-0 rounded-end-pill shadow-sm" style="font-size: 11.5px; letter-spacing: 0.8px; z-index: 2;">
            <i class="bi bi-exclamation-octagon-fill text-warning"></i>
            <span>WARNING</span>
        </div>
        <div class="flex-grow-1 overflow-hidden mx-2">
            <marquee behavior="scroll" direction="left" scrollamount="6" onmouseover="this.stop();" onmouseout="this.start();" class="mb-0 fw-bold py-1" style="color: #ff4d4f; font-size: 13.5px; letter-spacing: 1px; text-transform: uppercase;">
                <span class="text-warning"><i class="bi bi-exclamation-triangle-fill me-1"></i> NOTICE:</span> &nbsp; WE ARE NOT GOVERNMENT OFFICIALS, AFFILIATES, OR AN ANNEX OF ANY EMBASSY HIGH COMMISSION OR CONSULATES &nbsp;&bull;&nbsp; DOOTOR ENTERPRISES IS AN INDEPENDENT ADMINISTRATIVE CONSULTANCY &nbsp;&bull;&nbsp; <span class="text-warning"><i class="bi bi-shield-exclamation me-1"></i></span> WE ARE NOT GOVERNMENT OFFICIALS, AFFILIATES, OR AN ANNEX OF ANY EMBASSY HIGH COMMISSION OR CONSULATES
            </marquee>
        </div>
        <button type="button" class="btn-close btn-close-white flex-shrink-0 me-3 ms-1" aria-label="Close" onclick="dismissDisclaimer()" style="font-size: 10px; z-index: 2;" title="Dismiss notification"></button>
    </div>
</div>

<!-- Navigation Header -->
<header class="bg-white border-bottom sticky-top py-3" style="z-index: 1055;">
    <div class="container d-flex align-items-center justify-content-between">
        <a class="d-flex align-items-center gap-1 fw-bold text-dark text-decoration-none" href="{{ route('home') }}">
            @if($settings && $settings->logo_url)
                <img src="{{ app_file_url($settings->logo_url) }}" alt="{{ $settings->platform_name ?? 'DOOTOR ENTERPRISES' }} Official Logo" class="rounded" style="height: 32px; width: 32px; object-fit: contain;">
            @else
                <svg width="34" height="34" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-1">
                    <path d="M20 20C40 20 52 32 52 50C52 68 40 80 20 80C14 80 14 74 14 74V26C14 26 14 20 20 20Z" fill="url(#goldLogo)" />
                    <path d="M28 32C38 32 44 40 44 50C44 60 38 68 28 68V32Z" fill="#FFFFFF" />
                    <path d="M50 24H80V35H64V44H76V53H64V63H80V74H50V24Z" fill="#004225" />
                    <defs>
                        <linearGradient id="goldLogo" x1="14" y1="20" x2="52" y2="80" gradientUnits="userSpaceOnUse">
                            <stop offset="0%" stop-color="#FFE57F" />
                            <stop offset="50%" stop-color="#D4AF37" />
                            <stop offset="100%" stop-color="#AA820A" />
                        </linearGradient>
                    </defs>
                </svg>
            @endif
            <span>{{ $settings->platform_name ?? 'DOOTOR ENTERPRISES' }}</span>
        </a>

        {{-- Mobile hamburger button — uses custom JS toggle, NOT Bootstrap collapse --}}
        <button id="navToggleBtn" class="border rounded p-2 bg-transparent d-lg-none" type="button" aria-label="Toggle navigation" style="line-height:1;">
            <i class="bi bi-list fs-3"></i>
        </button>

        {{-- Nav links container --}}
        <div id="mainNavbar" style="display:none;">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-3 list-unstyled d-lg-flex flex-lg-row align-items-lg-center">
                <li><a class="nav-link fw-medium text-dark text-decoration-none px-2" href="#">Home</a></li>
                <li><a class="nav-link fw-medium text-dark text-decoration-none px-2" href="#services">Services</a></li>
                <li><a class="nav-link fw-medium text-dark text-decoration-none px-2" href="#faq">FAQ</a></li>
                <li><a class="nav-link fw-medium text-dark text-decoration-none px-2" href="#contact">Contact</a></li>
            </ul>
            <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
                @if($user)
                    @php
                        $dashboardRoute = route('client.dashboard');
                        if ($user->role === 'admin') $dashboardRoute = route('admin.dashboard');
                        elseif ($user->role === 'vendor') $dashboardRoute = ($user->status === 'Pending' || $user->status === 'Rejected') ? route('vendor.kyc') : route('vendor.dashboard');
                    @endphp
                    <a href="{{ $dashboardRoute }}" class="btn btn-dark px-4 rounded-pill">
                        <i class="bi bi-grid-fill me-2"></i> Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-dark px-4 rounded-pill">Login</a>
                    <a href="{{ route('register.client') }}" class="btn btn-dark px-4 rounded-pill">Register</a>
                @endif
            </div>
        </div>
    </div>

    {{-- Mobile dropdown panel (hidden by default, shown below header on toggle) --}}
    <div id="mobileNavDrawer" style="display:none; border-top: 1px solid #dee2e6;" class="bg-white px-3 pb-3 pt-2 d-lg-none">
        <ul class="list-unstyled mb-3">
            <li class="py-2 border-bottom"><a class="text-dark text-decoration-none fw-medium" href="#">Home</a></li>
            <li class="py-2 border-bottom"><a class="text-dark text-decoration-none fw-medium" href="#services">Services</a></li>
            <li class="py-2 border-bottom"><a class="text-dark text-decoration-none fw-medium" href="#faq">FAQ</a></li>
            <li class="py-2"><a class="text-dark text-decoration-none fw-medium" href="#contact">Contact</a></li>
        </ul>
        <div class="d-flex gap-2">
            @if($user)
                <a href="{{ $dashboardRoute ?? route('client.dashboard') }}" class="btn btn-dark px-4 rounded-pill w-100">
                    <i class="bi bi-grid-fill me-2"></i> Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-dark px-4 rounded-pill flex-fill">Login</a>
                <a href="{{ route('register.client') }}" class="btn btn-dark px-4 rounded-pill flex-fill">Register</a>
            @endif
        </div>
    </div>
</header>

@php
    $heroBadgeText = $settings->hero_badge_text ?? 'Official Consular & Administrative Fast-Track';
    $heroTitle = $settings->hero_title ?? 'Streamline & Fast-Track Your Essential Services';
    $heroSubtitle = $settings->hero_subtitle ?? 'Automated fast-track passport approvals, visa processing, NIN verification, and legally sworn court affidavits. The trusted unified portal connecting citizens, diaspora applicants, and corporate organizations directly with verified consular agents.';
@endphp

<!-- ============================================================
     PREMIUM HERO SECTION — Canvas Particles + 3D Depth
     ============================================================ -->
<section class="hero-section py-5 position-relative overflow-hidden" id="heroSection">
    <!-- Particle Canvas -->
    <canvas id="heroParticleCanvas"></canvas>

    <div class="container py-4 py-lg-5 position-relative" style="z-index: 5;">
        <div class="row align-items-center g-5">

            <!-- ===== LEFT COLUMN ===== -->
            <div class="col-lg-6 text-center text-lg-start">
                <!-- Stagger-in wrapper -->
                <div class="d-flex flex-column align-items-center align-items-lg-start gap-0">

                    <!-- 1. Live badge -->
                    <div class="hero-stagger-child mb-4">
                        <div class="hero-live-badge">
                            <span class="hero-ping-wrap">
                                <span class="hero-ping-ring"></span>
                                <span class="hero-ping-core"></span>
                            </span>
                            {{ $heroBadgeText }}
                        </div>
                    </div>

                    <!-- 2. Split headline -->
                    <div class="hero-stagger-child mb-3 w-100">
                        <h1 class="hero-headline">
                            Fast-Track Your<br class="d-none d-sm-block">
                            <span class="word-gold">Global &amp; Essential</span> 
                            <span class="word-rotate-wrap"><span class="word-rotate-inner" id="heroRotatingWord">Passports</span></span><br>
                            <span style="font-size: 0.65em; opacity:0.75; font-weight:700; letter-spacing:-0.5px;">With Verified Precision &amp; Expert Guidance.</span>
                        </h1>
                    </div>

                    <!-- 3. Subtitle -->
                    <div class="hero-stagger-child mb-4" style="max-width:530px;">
                        <p class="mb-0 text-white-50" style="font-size:1.05rem; line-height:1.7;">
                            {{ $heroSubtitle }}
                        </p>
                    </div>

                    <!-- 4. CTAs -->
                    <div class="hero-stagger-child d-flex flex-column flex-sm-row justify-content-center justify-content-lg-start gap-3 mb-4 w-100">
                        <a href="#services" class="btn hero-btn-primary btn-lg px-5 py-3 rounded-pill d-inline-flex align-items-center justify-content-center gap-2 fw-bold text-decoration-none">
                            <i class="bi bi-grid-3x3-gap-fill"></i>
                            <span>Explore 15+ Services</span>
                        </a>
                        <a href="{{ route('register.client') }}" class="btn hero-btn-ghost btn-lg px-4 py-3 rounded-pill d-inline-flex align-items-center justify-content-center gap-2 fw-semibold text-decoration-none">
                            <i class="bi bi-person-plus"></i>
                            <span>Register Free</span>
                        </a>
                    </div>

                    <!-- 5. Quick Search -->
                    <div class="hero-stagger-child mb-4 w-100" style="max-width:500px;">
                        <div class="hero-search-bar d-flex align-items-center px-3 py-1">
                            <i class="bi bi-search text-white me-2 flex-shrink-0" style="opacity: 0.85;"></i>
                            <input type="text" id="heroQuickSearch" class="form-control bg-transparent border-0 text-white shadow-none py-2" placeholder="Search: Passport, Visa, Attestation, Affidavit..." style="font-size:14px; color:#ffffff !important;">
                            <a href="#services" class="btn btn-sm rounded-pill fw-bold px-3 py-1 text-white flex-shrink-0" style="background:#004225; border:1px solid rgba(212,175,55,0.3); font-size:12px; white-space:nowrap;">
                                Find <i class="bi bi-arrow-down-short"></i>
                            </a>
                        </div>
                    </div>

                    <!-- 6. Trust stats bar -->
                    <div class="hero-stagger-child">
                        <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-3">
                            <div class="hero-stat">
                                <div class="hero-stat-number" id="statDocs">0+</div>
                                <div class="hero-stat-label">Documents Expedited</div>
                            </div>
                            <div class="hero-stat">
                                <div class="hero-stat-number">99.8%</div>
                                <div class="hero-stat-label">First-Time Success</div>
                            </div>
                            <div class="hero-stat">
                                <div class="hero-stat-number">48H</div>
                                <div class="hero-stat-label">Avg. Turnaround</div>
                            </div>
                            <div class="hero-stat">
                                <div class="d-flex align-items-center gap-1 justify-content-center" style="color:#D4AF37;">
                                    <i class="bi bi-star-fill" style="font-size:0.9rem;"></i>
                                    <i class="bi bi-star-fill" style="font-size:0.9rem;"></i>
                                    <i class="bi bi-star-fill" style="font-size:0.9rem;"></i>
                                    <i class="bi bi-star-fill" style="font-size:0.9rem;"></i>
                                    <i class="bi bi-star-fill" style="font-size:0.9rem;"></i>
                                </div>
                                <div class="hero-stat-label">Client Rating 4.9/5</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ===== RIGHT COLUMN — Document Pipeline Panel ===== -->
            <div class="col-lg-6 position-relative hero-pipeline-wrap">

                <!-- Floating micro-badges -->
                <div class="hero-micro-badge hero-mb-1 d-none d-sm-flex">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:34px;height:34px;background:linear-gradient(135deg,#004225,#006435);flex-shrink:0;">
                        <i class="bi bi-passport text-white" style="font-size:14px;"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-white" style="font-size:12px;line-height:1.2;">Passport Fast-Track</div>
                        <div class="text-white-50" style="font-size:10px;">Biometrics Cleared · Book Printed</div>
                    </div>
                    <span class="badge text-white rounded-pill px-2" style="background:rgba(212,175,55,0.2);border:1px solid rgba(212,175,55,0.4);font-size:9px;">48H</span>
                </div>

                <div class="hero-micro-badge hero-mb-2 d-none d-md-flex">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:34px;height:34px;background:linear-gradient(135deg,#1d4ed8,#1e40af);flex-shrink:0;">
                        <i class="bi bi-fingerprint text-white" style="font-size:14px;"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-white" style="font-size:12px;line-height:1.2;">Registry Sync Active</div>
                        <div class="text-white-50" style="font-size:10px;">Global Identity Database Link</div>
                    </div>
                    <span class="spinner-grow spinner-grow-sm text-info" style="width:7px;height:7px;flex-shrink:0;"></span>
                </div>

                <div class="hero-micro-badge hero-mb-3 d-none d-sm-flex">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-warning" style="width:34px;height:34px;background:rgba(245,158,11,0.15);border:1px solid rgba(245,158,11,0.3);flex-shrink:0;">
                        <i class="bi bi-shield-check" style="font-size:14px;"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-white" style="font-size:12px;line-height:1.2;">99.8% Approval Rate</div>
                        <div class="text-white-50" style="font-size:10px;">Zero Rejected Consular Files</div>
                    </div>
                </div>

                <div class="hero-micro-badge hero-mb-4 d-none d-lg-flex">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width:34px;height:34px;background:linear-gradient(135deg,#b91c1c,#7f1d1d);flex-shrink:0;">
                        <i class="bi bi-award" style="font-size:14px;"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-white" style="font-size:12px;line-height:1.2;">Court Affidavit Sworn</div>
                        <div class="text-white-50" style="font-size:10px;">High Court Seal Verified</div>
                    </div>
                </div>

                <!-- Main glass panel -->
                <div class="hero-panel mx-auto" id="heroPanelCard">
                    <!-- Panel header -->
                    <div class="d-flex align-items-center justify-content-between mb-4 pb-3" style="border-bottom:1px solid rgba(255,255,255,0.08);">
                        <div>
                            <div class="text-uppercase fw-bold" style="font-size:10px;letter-spacing:1.2px;color:rgba(255,255,255,0.4);">ACTIVE CONSIGNMENT</div>
                            <div class="fw-bold text-white mt-1" style="font-size:1.1rem;letter-spacing:-0.3px;">DE-8947-INT</div>
                        </div>
                        <span class="d-flex align-items-center gap-2 rounded-pill px-3 py-1" style="background:rgba(212,175,55,0.1);border:1px solid rgba(212,175,55,0.3);font-size:11px;font-weight:700;color:#D4AF37;">
                            <span style="width:6px;height:6px;border-radius:50%;background:#D4AF37;display:inline-block;animation:heroPingRing 2s infinite;"></span>
                            EXPRESS QUEUE
                        </span>
                    </div>

                    <!-- Progress bar -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1" style="font-size:11.5px;">
                            <span class="text-white-50">Phase 3 of 4: Consular Vetting</span>
                            <span class="fw-bold" style="color:#D4AF37;">75% Complete</span>
                        </div>
                        <div class="pipe-progress-track">
                            <div class="pipe-progress-fill"></div>
                        </div>
                    </div>

                    <!-- Pipeline steps -->
                    <div class="mb-4">
                        <!-- Step 1: Done -->
                        <div class="pipe-step">
                            <div class="pipe-step-icon" style="background:rgba(0,66,37,0.9);border:2px solid #004225;">
                                <i class="bi bi-check-lg text-white"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="text-white fw-semibold" style="font-size:13px;">Order Placed &amp; Payment Cleared</div>
                                <div class="text-white-50" style="font-size:11px;">Credo Gateway · AES-256 Secured</div>
                            </div>
                            <i class="bi bi-check-circle-fill" style="color:#004225;font-size:16px;"></i>
                        </div>
                        <div class="pipe-connector"></div>

                        <!-- Step 2: Done -->
                        <div class="pipe-step">
                            <div class="pipe-step-icon" style="background:rgba(0,66,37,0.9);border:2px solid #004225;">
                                <i class="bi bi-fingerprint text-white"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="text-white fw-semibold" style="font-size:13px;">Biometric &amp; Identity Verification</div>
                                <div class="text-white-50" style="font-size:11px;">Official registry database cross-matched</div>
                            </div>
                            <i class="bi bi-check-circle-fill" style="color:#004225;font-size:16px;"></i>
                        </div>
                        <div class="pipe-connector active-line"></div>

                        <!-- Step 3: Active -->
                        <div class="pipe-step" style="background:rgba(212,175,55,0.05);border:1px solid rgba(212,175,55,0.15);border-radius:12px;">
                            <div class="pipe-step-icon" style="background:rgba(212,175,55,0.15);border:2px solid rgba(212,175,55,0.5);">
                                <span class="spinner-border spinner-border-sm text-warning" style="width:13px;height:13px;"></span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="text-white fw-bold" style="font-size:13px;">Consular Vetting &amp; Printing</div>
                                <div style="font-size:11px;color:#D4AF37;">In Progress · Embassy stamp pending</div>
                            </div>
                            <span class="badge rounded-pill px-2 py-1 fw-bold" style="background:rgba(212,175,55,0.15);color:#D4AF37;border:1px solid rgba(212,175,55,0.3);font-size:10px;">ACTIVE</span>
                        </div>
                        <div class="pipe-connector" style="opacity:0.3;"></div>

                        <!-- Step 4: Upcoming -->
                        <div class="pipe-step opacity-50">
                            <div class="pipe-step-icon" style="background:rgba(255,255,255,0.05);border:2px solid rgba(255,255,255,0.15);">
                                <i class="bi bi-box-seam text-white-50"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="text-white-50 fw-semibold" style="font-size:13px;">VIP Dispatch &amp; Handover</div>
                                <div class="text-white-50" style="font-size:11px;">Real-time SMS courier tracking</div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer grid -->
                    <div class="row g-2 pt-3" style="border-top:1px solid rgba(255,255,255,0.07);">
                        <div class="col-4 text-center">
                            <div class="text-uppercase" style="font-size:9px;letter-spacing:0.8px;color:rgba(255,255,255,0.35);">Turnaround</div>
                            <div class="fw-bold text-white" style="font-size:13px;">48 Hours</div>
                        </div>
                        <div class="col-4 text-center" style="border-left:1px solid rgba(255,255,255,0.08);border-right:1px solid rgba(255,255,255,0.08);">
                            <div class="text-uppercase" style="font-size:9px;letter-spacing:0.8px;color:rgba(255,255,255,0.35);">Success Rate</div>
                            <div class="fw-bold" style="font-size:13px;color:#D4AF37;">99.8%</div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="text-uppercase" style="font-size:9px;letter-spacing:0.8px;color:rgba(255,255,255,0.35);">Encryption</div>
                            <div class="fw-bold text-info" style="font-size:13px;">AES-256</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Modern Elevated Features Architecture Section -->
<section class="py-5 bg-white position-relative overflow-hidden" style="border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9;">
    <div class="container py-4">
        <div class="text-center max-w-lg mx-auto mb-5">
            <span class="badge rounded-pill px-3 py-1.5 extra-small fw-bold text-uppercase" style="background: rgba(0,66,37,0.08); color: #004225; letter-spacing: 1.2px;">
                Global Administrative Architecture
            </span>
            <h2 class="display-6 fw-bold text-dark mt-2 mb-2" style="letter-spacing: -0.5px;">Engineered For Uncompromising Speed &amp; Trust</h2>
            <p class="text-secondary small mb-0" style="max-width: 620px; margin: 0 auto; line-height: 1.65; font-size: 14.5px;">
                Built on bank-grade encryption protocols and direct consular liaison networks across multiple international jurisdictions.
            </p>
        </div>

        <div class="row g-4">
            <!-- Feature 1: All Services In One Place -->
            <div class="col-md-4">
                <div class="modern-feature-card h-100 p-4 p-xl-5 rounded-4 bg-white d-flex flex-column justify-content-between position-relative overflow-hidden">
                    <div>
                        <div class="feature-icon-wrapper mb-4">
                            <div class="feature-icon-box" style="background: linear-gradient(135deg, #004225, #006435);">
                                <i class="bi bi-collection-fill text-white fs-4"></i>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1 extra-small fw-bold">Unified Hub</span>
                        </div>
                        <h3 class="h5 fw-bold text-dark mb-2" style="letter-spacing: -0.3px;">All Services In One Place</h3>
                        <p class="text-secondary small mb-4" style="line-height: 1.65; font-size: 13.5px;">
                            From international passports and urgent visas to sworn court affidavits and legal attestations — initiate, manage, and track all documents inside one secure unified portal.
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-2 text-success small fw-semibold pt-3 border-top">
                        <i class="bi bi-shield-check"></i>
                        <span>Zero Bureaucratic Friction</span>
                    </div>
                </div>
            </div>

            <!-- Feature 2: Fast Processing -->
            <div class="col-md-4">
                <div class="modern-feature-card h-100 p-4 p-xl-5 rounded-4 bg-white d-flex flex-column justify-content-between position-relative overflow-hidden">
                    <div>
                        <div class="feature-icon-wrapper mb-4">
                            <div class="feature-icon-box" style="background: linear-gradient(135deg, #D4AF37, #AA820A);">
                                <i class="bi bi-lightning-charge-fill text-dark fs-4"></i>
                            </div>
                            <span class="badge rounded-pill px-2.5 py-1 extra-small fw-bold" style="background: rgba(212,175,55,0.15); color: #AA820A;">Expedited 48H</span>
                        </div>
                        <h3 class="h5 fw-bold text-dark mb-2" style="letter-spacing: -0.3px;">Rapid Priority Processing</h3>
                        <p class="text-secondary small mb-4" style="line-height: 1.65; font-size: 13.5px;">
                            Our verified liaison officers fast-track approvals with embassy counters, immigration hubs, and high court registries to deliver urgent travel orders in record time.
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-2 small fw-semibold pt-3 border-top" style="color: #AA820A;">
                        <i class="bi bi-stopwatch-fill"></i>
                        <span>Express Consular Queue</span>
                    </div>
                </div>
            </div>

            <!-- Feature 3: Secure & Trackable -->
            <div class="col-md-4">
                <div class="modern-feature-card h-100 p-4 p-xl-5 rounded-4 bg-white d-flex flex-column justify-content-between position-relative overflow-hidden">
                    <div>
                        <div class="feature-icon-wrapper mb-4">
                            <div class="feature-icon-box" style="background: linear-gradient(135deg, #1d4ed8, #1e40af);">
                                <i class="bi bi-shield-lock-fill text-white fs-4"></i>
                            </div>
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1 extra-small fw-bold">AES-256 Vault</span>
                        </div>
                        <h3 class="h5 fw-bold text-dark mb-2" style="letter-spacing: -0.3px;">Secure &amp; Live Trackable</h3>
                        <p class="text-secondary small mb-4" style="line-height: 1.65; font-size: 13.5px;">
                            Real-time status progression from biometric clearance to courier dispatch. Client documents are strictly watermarked, tamper-proofed, and protected by bank-level SSL.
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-2 text-primary small fw-semibold pt-3 border-top">
                        <i class="bi bi-lock-fill"></i>
                        <span>End-to-End Encryption</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Grid -->
<section id="services" class="py-5 bg-light">
    <div class="container-fluid px-3 px-md-4 px-xl-5 py-4" style="max-width: 1540px;">
        <div class="text-center max-w-lg mx-auto mb-4">
            <span class="text-uppercase small fw-bold tracking-wide" style="color: #004225; letter-spacing: 1px;">Catalog</span>
            <h2 class="display-5 fw-bold mt-2 text-dark">Services We Assist With</h2>
            <p style="color: #334155; font-size: 1.05rem;">Explore our catalog of official documentation and certification services.</p>
        </div>

        <!-- Search and Filter Controls -->
        <div class="row mb-5 justify-content-center">
            <div class="col-md-6 col-lg-5 mb-3 mb-md-0">
                <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                    <span class="input-group-text bg-white border-0 ps-3"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="serviceSearch" class="form-control border-0 ps-2 py-2.5 small" placeholder="Search for services (e.g. NIN, Visa, Birth)..." style="font-size: 14.5px;">
                </div>
            </div>
            <div class="col-12 mt-3 d-flex flex-wrap justify-content-center gap-2" id="serviceCategories">
                <button class="btn btn-dark rounded-pill px-4 btn-sm fw-medium active" data-category="all">All Services</button>
                <button class="btn btn-outline-dark rounded-pill px-4 btn-sm fw-medium" data-category="identity">Identity & Verifications</button>
                <button class="btn btn-outline-dark rounded-pill px-4 btn-sm fw-medium" data-category="travel">Travel & Visas</button>
                <button class="btn btn-outline-dark rounded-pill px-4 btn-sm fw-medium" data-category="legal">Legal & Publications</button>
            </div>
        </div>

        <div class="row g-4">
            @if($dbPrimaryServices->count() > 0)
                @foreach($dbPrimaryServices as $service)
                    @php
                        $subServiceNames = $service->subServices->pluck('name')->implode(' ');
                        $searchableName = strtolower($service->name . ' ' . $subServiceNames);
                        $categoryKey = getServiceCategory($service->name);
                        $categoryLabels = [
                            'identity' => 'Identity & Registry',
                            'travel'   => 'Travel & Consular',
                            'legal'    => 'Legal & Sworn',
                        ];
                        // Icon + gradient per service category
                        $sn = strtolower($service->name);
                        if (str_contains($sn,'passport')) {
                            $icon = 'bi-passport'; $grad = 'linear-gradient(135deg,#004225,#006435)'; $dotBg = 'rgba(0,66,37,0.08)';
                        } elseif (str_contains($sn,'visa')) {
                            $icon = 'bi-ticket-perforated'; $grad = 'linear-gradient(135deg,#1e40af,#1d4ed8)'; $dotBg = 'rgba(30,64,175,0.08)';
                        } elseif (str_contains($sn,'nin')) {
                            $icon = 'bi-fingerprint'; $grad = 'linear-gradient(135deg,#374151,#111827)'; $dotBg = 'rgba(55,65,81,0.08)';
                        } elseif (str_contains($sn,'bvn')) {
                            $icon = 'bi-bank'; $grad = 'linear-gradient(135deg,#b45309,#92400e)'; $dotBg = 'rgba(180,83,9,0.08)';
                        } elseif (str_contains($sn,'police')) {
                            $icon = 'bi-shield-check'; $grad = 'linear-gradient(135deg,#1e3a5f,#1e40af)'; $dotBg = 'rgba(30,58,95,0.08)';
                        } elseif (str_contains($sn,'affidavit') || str_contains($sn,'court')) {
                            $icon = 'bi-journal-check'; $grad = 'linear-gradient(135deg,#4a1942,#7c3aed)'; $dotBg = 'rgba(74,25,66,0.08)';
                        } elseif (str_contains($sn,'birth') || str_contains($sn,'npc')) {
                            $icon = 'bi-file-earmark-medical'; $grad = 'linear-gradient(135deg,#9d174d,#be185d)'; $dotBg = 'rgba(157,23,77,0.08)';
                        } elseif (str_contains($sn,'licence') || str_contains($sn,'license')) {
                            $icon = 'bi-card-list'; $grad = 'linear-gradient(135deg,#064e3b,#065f46)'; $dotBg = 'rgba(6,78,59,0.08)';
                        } elseif (str_contains($sn,'indigene') || str_contains($sn,'lga')) {
                            $icon = 'bi-house-check'; $grad = 'linear-gradient(135deg,#78350f,#92400e)'; $dotBg = 'rgba(120,53,15,0.08)';
                        } elseif (str_contains($sn,'newspaper')) {
                            $icon = 'bi-newspaper'; $grad = 'linear-gradient(135deg,#374151,#4b5563)'; $dotBg = 'rgba(55,65,81,0.08)';
                        } elseif (str_contains($sn,'emergency') || str_contains($sn,'etc')) {
                            $icon = 'bi-file-earmark-badge'; $grad = 'linear-gradient(135deg,#b91c1c,#991b1b)'; $dotBg = 'rgba(185,28,28,0.08)';
                        } elseif (str_contains($sn,'attorney') || str_contains($sn,'power')) {
                            $icon = 'bi-file-earmark-lock'; $grad = 'linear-gradient(135deg,#312e81,#4338ca)'; $dotBg = 'rgba(49,46,129,0.08)';
                        } elseif (str_contains($sn,'reschedule') || str_contains($sn,'waiver')) {
                            $icon = 'bi-calendar-check'; $grad = 'linear-gradient(135deg,#0369a1,#0284c7)'; $dotBg = 'rgba(3,105,161,0.08)';
                        } elseif (str_contains($sn,'same day')) {
                            $icon = 'bi-lightning-charge'; $grad = 'linear-gradient(135deg,#b45309,#d97706)'; $dotBg = 'rgba(180,83,9,0.08)';
                        } else {
                            $icon = $service->icon ?? 'bi-file-earmark-text'; $grad = 'linear-gradient(135deg,#004225,#065f46)'; $dotBg = 'rgba(0,66,37,0.08)';
                        }

                        // Sub-services JSON for modal (only include price if logged in, in USD)
                        $subServicesData = $service->subServices->map(fn($sub) => [
                            'name' => $sub->name,
                            'price' => Auth::check() ? ($sub->price ?? null) : null,
                            'icon' => $sub->icon ?? 'bi-check-circle',
                        ])->toJson();

                        $hasCustomImage = !empty($service->image_url) && (
                            file_exists(public_path($service->image_url)) ||
                            str_starts_with($service->image_url, 'http')
                        );
                    @endphp
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3 service-card-container" data-category="{{ $categoryKey }}" data-name="{{ $searchableName }}">
                        <div class="card service-catalog-card h-100 d-flex flex-column position-relative overflow-hidden">

                            <!-- Image Banner with Fallback to Stylized Gradient Icon -->
                            @if($hasCustomImage)
                                <div class="service-card-image-wrap position-relative" style="height: 155px; background: #0f172a; overflow: hidden;">
                                    <img src="{{ asset($service->image_url) }}" alt="{{ $service->name }}" class="service-card-img w-100 h-100" style="object-fit: cover;" loading="lazy">
                                    <div class="service-img-overlay"></div>
                                    <span class="svc-category-pill">{{ $categoryLabels[$categoryKey] ?? 'Official Service' }}</span>
                                    <div class="svc-corner-seal" title="Verified Consular Service">
                                        <i class="bi bi-patch-check-fill text-warning" style="font-size:13px;"></i>
                                    </div>
                                </div>
                            @else
                                <div class="svc-icon-header">
                                    <div class="svc-icon-bg" style="background: {{ $grad }};"></div>
                                    <div class="svc-icon-bg-dots"></div>
                                    <span class="svc-category-pill">{{ $categoryLabels[$categoryKey] ?? 'Official Service' }}</span>
                                    <div class="svc-corner-seal" title="Verified Consular Service">
                                        <i class="bi bi-patch-check-fill text-warning" style="font-size:13px;"></i>
                                    </div>
                                    <div class="svc-icon-circle" style="background: {{ $grad }};">
                                        <i class="bi {{ $icon }}"></i>
                                    </div>
                                </div>
                            @endif

                            <!-- Card Body -->
                            <div class="p-4 d-flex flex-column flex-grow-1">
                                <div class="mb-2">
                                    <span class="badge rounded-pill px-2 py-1 fw-bold" style="font-size:10px;background:rgba(0,66,37,0.08);color:#004225;border:1px solid rgba(0,66,37,0.15);">Primary Service</span>
                                    <h3 class="h5 fw-bold mb-0 text-dark mt-2" style="letter-spacing:-0.3px;line-height:1.3;">{{ $service->name }}</h3>
                                </div>

                                <p class="small text-secondary mb-3 flex-grow-1" style="line-height:1.6;font-size:13px;">{{ $service->description }}</p>

                                <!-- Sub-services trigger (iOS modal) -->
                                @if($service->subServices && $service->subServices->count() > 0)
                                    <div class="mb-3">
                                        <button class="btn-sub-services w-100 justify-content-center"
                                            onclick="openSubModal({{ json_encode($service->name) }}, {{ $subServicesData }})">
                                            <i class="bi bi-diagram-3-fill"></i>
                                            <span>View Sub-Services</span>
                                            <span class="sub-count-badge">{{ $service->subServices->count() }}</span>
                                        </button>
                                    </div>
                                @endif

                                <!-- Footer CTA (No prices shown on homepage) -->
                                <div class="pt-3 border-top d-flex align-items-center justify-content-between gap-2 mt-auto">
                                    <span class="d-inline-flex align-items-center gap-1 text-secondary" style="font-size:11px;">
                                        <i class="bi bi-shield-check text-success"></i> Direct Processing
                                    </span>
                                    <a href="{{ route('login') }}" class="btn btn-sm rounded-pill px-4 py-2 fw-bold text-white text-decoration-none d-inline-flex align-items-center gap-1" style="background:#004225;border-color:#004225;font-size:12px;">
                                        Apply Now <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                @foreach($staticServices as $service)
                    @php
                        $categoryKey = getServiceCategory($service['title']);
                        $categoryLabels = [
                            'identity' => 'Identity & Registry',
                            'travel'   => 'Travel & Consular',
                            'legal'    => 'Legal & Sworn',
                        ];
                        $sn = strtolower($service['title']);
                        if (str_contains($sn,'passport')) { $icon = 'bi-passport'; $grad = 'linear-gradient(135deg,#004225,#006435)'; }
                        elseif (str_contains($sn,'visa')) { $icon = 'bi-ticket-perforated'; $grad = 'linear-gradient(135deg,#1e40af,#1d4ed8)'; }
                        elseif (str_contains($sn,'nin')) { $icon = 'bi-fingerprint'; $grad = 'linear-gradient(135deg,#374151,#111827)'; }
                        elseif (str_contains($sn,'bvn')) { $icon = 'bi-bank'; $grad = 'linear-gradient(135deg,#b45309,#92400e)'; }
                        elseif (str_contains($sn,'police')) { $icon = 'bi-shield-check'; $grad = 'linear-gradient(135deg,#1e3a5f,#1e40af)'; }
                        elseif (str_contains($sn,'affidavit') || str_contains($sn,'court')) { $icon = 'bi-journal-check'; $grad = 'linear-gradient(135deg,#4a1942,#7c3aed)'; }
                        elseif (str_contains($sn,'birth')) { $icon = 'bi-file-earmark-medical'; $grad = 'linear-gradient(135deg,#9d174d,#be185d)'; }
                        elseif (str_contains($sn,'licence') || str_contains($sn,'license')) { $icon = 'bi-card-list'; $grad = 'linear-gradient(135deg,#064e3b,#065f46)'; }
                        elseif (str_contains($sn,'indigene') || str_contains($sn,'lga')) { $icon = 'bi-house-check'; $grad = 'linear-gradient(135deg,#78350f,#92400e)'; }
                        elseif (str_contains($sn,'newspaper')) { $icon = 'bi-newspaper'; $grad = 'linear-gradient(135deg,#374151,#4b5563)'; }
                        elseif (str_contains($sn,'emergency')) { $icon = 'bi-file-earmark-badge'; $grad = 'linear-gradient(135deg,#b91c1c,#991b1b)'; }
                        else { $icon = 'bi-file-earmark-text'; $grad = 'linear-gradient(135deg,#004225,#065f46)'; }
                    @endphp
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3 service-card-container" data-category="{{ $categoryKey }}" data-name="{{ strtolower($service['title']) }}">
                        <div class="card service-catalog-card h-100 d-flex flex-column position-relative overflow-hidden">
                            <div class="svc-icon-header">
                                <div class="svc-icon-bg" style="background: {{ $grad }};"></div>
                                <div class="svc-icon-bg-dots"></div>
                                <span class="svc-category-pill">{{ $categoryLabels[$categoryKey] ?? 'Official Service' }}</span>
                                <div class="svc-corner-seal"><i class="bi bi-patch-check-fill text-warning" style="font-size:13px;"></i></div>
                                <div class="svc-icon-circle" style="background: {{ $grad }};"><i class="bi {{ $icon }}"></i></div>
                            </div>
                            <div class="p-4 d-flex flex-column flex-grow-1">
                                <h3 class="h5 fw-bold mb-2 text-dark" style="letter-spacing:-0.3px;">{{ $service['title'] }}</h3>
                                <p class="small text-secondary flex-grow-1" style="line-height:1.6;font-size:13px;">{{ $service['description'] }}</p>
                                <div class="pt-3 border-top d-flex justify-content-end mt-auto">
                                    <a href="{{ route('login') }}" class="btn btn-sm rounded-pill px-4 py-2 fw-bold text-white w-100 text-center" style="background:#004225;border-color:#004225;font-size:12px;">
                                        Apply Now <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section id="faq" class="py-5 bg-white">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <span class="text-uppercase small fw-bold" style="color: #004225; letter-spacing: 1px;">Information</span>
                <h2 class="display-5 fw-bold mt-2 mb-4 text-dark">Frequently Asked Questions</h2>
                <p style="color: #334155; font-size: 1.05rem;">Have questions about processing times, document requirements, payments, or security? Find fast answers here.</p>
                <a href="#contact" class="btn btn-dark px-4 rounded-pill">Still have questions?</a>
            </div>
            
            <div class="col-lg-7">
                @php
                    $displayFaqs = isset($faqs) && $faqs->count() > 0 
                        ? $faqs 
                        : \App\Models\Faq::where('status', 'Active')->orderBy('sort_order', 'asc')->get();
                @endphp
                <div id="customFaqAccordion">
                    @forelse($displayFaqs as $index => $faqItem)
                        <div class="faq-item {{ !$loop->last ? 'border-bottom' : '' }} py-2">
                            <button class="faq-question w-100 text-start bg-transparent border-0 fw-semibold fs-6 d-flex justify-content-between align-items-center py-2 px-0 text-dark" type="button">
                                <span>{{ $faqItem->question }}</span>
                                <i class="bi bi-chevron-down faq-icon ms-2" style="transition: transform 0.25s ease; flex-shrink:0;"></i>
                            </button>
                            <div class="faq-answer small px-1" style="display:none; padding-top: 8px; padding-bottom: 8px; color: #334155; line-height: 1.6;">
                                {!! nl2br(e($faqItem->answer)) !!}
                            </div>
                        </div>
                    @empty
                        <div class="faq-item py-2">
                            <p class="text-secondary small mb-0">No frequently asked questions available at this time.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Customer Reviews & Testimonials Carousel/Grid Section -->
<section id="reviews" class="py-5 position-relative overflow-hidden" style="background: linear-gradient(180deg, #f8fafc 0%, #edf2f7 100%); border-top: 1px solid #e2e8f0; border-bottom: 1px solid #cbd5e1;">
    <div class="container py-4">
        <div class="row align-items-end mb-5">
            <div class="col-md-8">
                <span class="badge rounded-pill px-3 py-1.5 extra-small fw-bold text-uppercase" style="background: rgba(0,66,37,0.08); color: #004225; letter-spacing: 1.2px;">
                    Verified Client Experiences
                </span>
                <h2 class="display-6 fw-bold text-dark mt-2 mb-2" style="letter-spacing: -0.5px;">Trusted by Thousands Worldwide</h2>
                <p class="text-secondary small mb-0" style="max-width: 620px; font-size: 14.5px; line-height: 1.6;">
                    Read direct testimonials from individuals, business owners, and international travelers who fast-tracked their official documentation through DOOTOR ENTERPRISES.
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <button type="button" class="btn btn-outline-dark rounded-pill px-4 py-2 small fw-semibold d-inline-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#leaveReviewModal">
                    <i class="bi bi-star-fill text-warning"></i> Leave a Review
                </button>
            </div>
        </div>

        @if(session('review_success'))
            <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 small d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                <div>{{ session('review_success') }}</div>
            </div>
        @endif

        <style>
            .reviews-marquee-viewport {
                overflow: hidden;
                position: relative;
                width: 100%;
                mask-image: linear-gradient(to right, transparent, #000 4%, #000 96%, transparent);
                -webkit-mask-image: linear-gradient(to right, transparent, #000 4%, #000 96%, transparent);
            }
            .reviews-marquee-track {
                display: flex;
                flex-wrap: nowrap;
                gap: 20px;
                width: max-content;
                animation: reviewsMarquee 38s linear infinite;
                padding: 8px 4px 16px;
            }
            .reviews-marquee-viewport:hover .reviews-marquee-track {
                animation-play-state: paused;
            }
            .review-slide-card {
                width: 340px;
                flex: 0 0 340px;
            }
            @media (max-width: 576px) {
                .review-slide-card { width: 290px; flex: 0 0 290px; }
            }
            @keyframes reviewsMarquee {
                0%   { transform: translateX(0); }
                100% { transform: translateX(-50%); }
            }
        </style>

        @if(($reviews ?? collect())->count() > 0)
        <div class="reviews-marquee-viewport">
            <div class="reviews-marquee-track">
                @for($dup = 0; $dup < 2; $dup++)
                    @foreach($reviews as $rev)
                        <div class="review-slide-card">
                            <div class="card h-100 border-0 rounded-4 p-4 shadow-sm d-flex flex-column justify-content-between position-relative" style="background: #ffffff; border: 1px solid rgba(0,66,37,0.08) !important;">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="d-flex align-items-center gap-1 text-warning">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $rev->rating)
                                                    <i class="bi bi-star-fill" style="font-size: 13.5px;"></i>
                                                @else
                                                    <i class="bi bi-star" style="font-size: 13.5px; color: #cbd5e1;"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="badge rounded-pill extra-small fw-bold px-2.5 py-1" style="background: rgba(0,66,37,0.08); color: #004225; font-size: 10.5px;">
                                            <i class="bi bi-patch-check-fill me-1"></i> Verified
                                        </span>
                                    </div>
                                    <div class="mb-1" style="color: rgba(212,175,55,0.45); line-height: 1;">
                                        <i class="bi bi-quote fs-4"></i>
                                    </div>
                                    <p class="text-dark mb-4" style="line-height: 1.65; font-size: 13.5px;">
                                        {{ $rev->comment }}
                                    </p>
                                </div>
                                <div class="pt-3 border-top d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0 shadow-sm" style="width: 40px; height: 40px; background: linear-gradient(135deg, #004225, #D4AF37); font-size: 14px;">
                                        {{ strtoupper(substr($rev->client_name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="fw-bold text-dark text-truncate" style="font-size: 13.5px;">{{ $rev->client_name }}</div>
                                        @if(!empty($rev->client_title))
                                            <div class="text-muted text-truncate" style="font-size: 11px;">{{ $rev->client_title }}</div>
                                        @endif
                                        <div class="text-success fw-semibold text-truncate" style="font-size: 11px;">
                                            {{ $rev->service_name ?? 'Consular Service' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endfor
            </div>
        </div>
        @else
            <div class="text-center py-4 text-secondary small">
                <p class="mb-0">Be the first to share your experience with DOOTOR ENTERPRISES!</p>
            </div>
        @endif
    </div>
</section>

@php
    $rawPromoUrl = $settings->promo_video_url ?? '';
    $promoEmbedUrl = '';
    if (!empty($rawPromoUrl)) {
        if (preg_match('~(?:youtube\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{11})~', $rawPromoUrl, $ytMatch)) {
            $promoEmbedUrl = 'https://www.youtube.com/embed/' . $ytMatch[1] . '?enablejsapi=1';
        } elseif (str_starts_with($rawPromoUrl, 'http')) {
            $promoEmbedUrl = $rawPromoUrl;
        }
    }
    $promoLabel = !empty($settings->promo_video_title) ? $settings->promo_video_title : 'Featured: Official Services Video';
@endphp

@if(!empty($promoEmbedUrl))
<!-- Floating Promo Video Pill -->
<div id="promoVideoPill" class="position-fixed" style="bottom: 24px; left: 24px; z-index: 1040;">
    <button class="btn btn-dark shadow-lg rounded-pill px-3.5 py-2.5 d-flex align-items-center gap-2.5 border border-warning" style="background: rgba(15, 23, 42, 0.94); backdrop-filter: blur(12px); box-shadow: 0 10px 25px rgba(0,0,0,0.3) !important;" onclick="openPromoModal()">
        <span class="position-relative d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px; border-radius: 50%; background: #e11d48; color: #fff;">
            <i class="bi bi-play-fill fs-5" style="margin-left: 2px;"></i>
            <span class="position-absolute translate-middle p-1 bg-warning border border-light rounded-circle" style="top: 0; right: -2px;"></span>
        </span>
        <span class="text-white small fw-bold pe-1">{{ $promoLabel }}</span>
    </button>
</div>

<!-- Promo Video Modal -->
<div class="modal fade" id="promoVideoModal" tabindex="-1" aria-labelledby="promoVideoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 overflow-hidden shadow-2xl bg-dark text-white">
            <div class="modal-header border-bottom border-secondary px-4 py-3 bg-black">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-play-circle-fill text-danger fs-5"></i>
                    <h5 class="modal-title fw-bold fs-6 mb-0 text-white" id="promoVideoModalLabel">How DOOTOR ENTERPRISES Works · Official Guide</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" onclick="closePromoModal()"></button>
            </div>
            <div class="modal-body p-0 bg-black">
                <div class="ratio ratio-16x9">
                    <iframe id="promoIframe" src="{{ $promoEmbedUrl }}" title="DOOTOR Enterprises System Overview" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
            </div>
            <div class="modal-footer border-top border-secondary px-4 py-3 bg-dark d-flex justify-content-between align-items-center">
                <div class="small text-white-50">
                    <i class="bi bi-shield-check text-success me-1"></i> Official consular &amp; administrative processing channel
                </div>
                <a href="{{ auth()->check() ? route('client.services') : route('login') }}" class="btn btn-warning rounded-pill px-4 btn-sm fw-bold">
                    Start Application <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Leave Review Modal -->
<div class="modal fade" id="leaveReviewModal" tabindex="-1" aria-labelledby="leaveReviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-2xl overflow-hidden">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="leaveReviewModalLabel">Share Your Feedback</h5>
                    <p class="text-secondary small mb-0">Help others learn about our administrative and document support.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('reviews.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Your Rating *</label>
                        <div class="d-flex align-items-center gap-2" id="starRatingPicker">
                            <input type="hidden" name="rating" id="reviewRatingInput" value="5" required>
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star-fill text-warning fs-4 rating-star-picker cursor-pointer" data-rating="{{ $i }}" style="cursor: pointer;"></i>
                            @endfor
                            <span class="small fw-semibold text-muted ms-2" id="ratingScoreLabel">5.0 / 5 Stars</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Your Full Name *</label>
                        <input type="text" name="client_name" class="form-control rounded-3 small" placeholder="e.g. Samuel Adekunle" value="{{ Auth::check() ? Auth::user()->name : old('client_name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Email Address *</label>
                        <input type="email" name="client_email" class="form-control rounded-3 small" placeholder="e.g. samuel@example.com" value="{{ Auth::check() ? Auth::user()->email : old('client_email') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Service Experienced (Optional)</label>
                        <input type="text" name="service_name" class="form-control rounded-3 small" placeholder="e.g. Standard 64-Page Passport Renewal" value="{{ old('service_name') }}">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-dark mb-1">Your Review *</label>
                        <textarea name="review_text" rows="4" class="form-control rounded-3 small" placeholder="Describe your experience with our turnaround time and communication..." required minlength="10" maxlength="1000">{{ old('review_text') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3 small fw-medium" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark rounded-pill px-4 small fw-semibold" style="background:#004225;border-color:#004225;">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Contact & Footer -->
<footer id="contact" class="py-5 bg-dark text-white position-relative">
    <div class="container py-4">
        <div class="row g-5">
            <div class="col-lg-5">
                <h3 class="fw-bold mb-3 d-flex align-items-center gap-1">
                    <svg width="28" height="28" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-1">
                        <!-- Stylized D (Gold) -->
                        <path d="M20 20C40 20 52 32 52 50C52 68 40 80 20 80C14 80 14 74 14 74V26C14 26 14 20 20 20Z" fill="url(#goldLogoFooter)" />
                        <path d="M28 32C38 32 44 40 44 50C44 60 38 68 28 68V32Z" fill="#1A1A1A" />
                        <!-- Stylized E (Dark Green) -->
                        <path d="M50 24H80V35H64V44H76V53H64V63H80V74H50V24Z" fill="#2E7D32" />
                        <defs>
                            <linearGradient id="goldLogoFooter" x1="14" y1="20" x2="52" y2="80" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#FFE57F" />
                                <stop offset="50%" stop-color="#D4AF37" />
                                <stop offset="100%" stop-color="#AA820A" />
                            </linearGradient>
                        </defs>
                    </svg>
                    <span>{{ $settings->platform_name ?? 'DOOTOR ENTERPRISES' }}</span>
                </h3>
                <p class="text-white-50 small mb-4">
                    The leading portal for quick, verified, and secured document applications. Connecting business services with professional agents.
                </p>
                <div class="d-flex flex-column gap-2 text-white-50 small">
                    @php
                        $footerPhone = $settings->contact_phone ?? '+1 (416) 458-9707';
                        $footerEmail = $settings->contact_email ?? 'support@dootor-enterprises.com';
                        $footerAddress = $settings->contact_address ?? 'Toronto, Ontario, Canada';
                        $rawWa = $settings->whatsapp_number ?? '+14164589707';
                        $footerWaLink = str_starts_with($rawWa, 'http') ? $rawWa : 'https://wa.me/' . preg_replace('/[^0-9]/', '', $rawWa);
                    @endphp
                    <span class="d-flex align-items-center gap-2">
                        <i class="bi bi-whatsapp text-success"></i>
                        <a href="{{ $footerWaLink }}" class="text-white-50 text-decoration-none" style="transition: color 0.2s;" target="_blank">Direct / WhatsApp: {{ $rawWa }}</a>
                    </span>
                    <span class="d-flex align-items-center gap-2">
                        <i class="bi bi-telephone text-info"></i>
                        <a href="tel:{{ $footerPhone }}" class="text-white-50 text-decoration-none">{{ $footerPhone }}</a>
                    </span>
                    <span class="d-flex align-items-center gap-2">
                        <i class="bi bi-envelope text-white"></i>
                        <a href="mailto:{{ $footerEmail }}" class="text-white-50 text-decoration-none">{{ $footerEmail }}</a>
                    </span>
                    @if(!empty($footerAddress))
                    <span class="d-flex align-items-center gap-2">
                        <i class="bi bi-geo-alt text-danger"></i>
                        <span>{{ $footerAddress }}</span>
                    </span>
                    @endif
                    <span class="d-flex align-items-center gap-2">
                        <i class="bi bi-google text-warning"></i>
                        <span>Google Business: International Document &amp; Consular Services Consultancy</span>
                    </span>
                </div>

                <!-- Dynamic Social Media Handles (Bootstrap Icons) -->
                <div class="mt-4 pt-2">
                    <h6 class="text-white small fw-bold text-uppercase mb-2.5" style="letter-spacing: 0.8px; font-size: 11px;">Follow Us Online</h6>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        @if(!empty($settings->facebook_url))
                            <a href="{{ $settings->facebook_url }}" target="_blank" rel="noopener noreferrer" class="footer-social-btn social-fb" title="Facebook">
                                <i class="bi bi-facebook fs-6"></i>
                            </a>
                        @endif
                        @if(!empty($settings->twitter_url))
                            <a href="{{ $settings->twitter_url }}" target="_blank" rel="noopener noreferrer" class="footer-social-btn social-tw" title="Twitter / X">
                                <i class="bi bi-twitter-x fs-6"></i>
                            </a>
                        @endif
                        @if(!empty($settings->instagram_url))
                            <a href="{{ $settings->instagram_url }}" target="_blank" rel="noopener noreferrer" class="footer-social-btn social-ig" title="Instagram">
                                <i class="bi bi-instagram fs-6"></i>
                            </a>
                        @endif
                        @if(!empty($settings->linkedin_url))
                            <a href="{{ $settings->linkedin_url }}" target="_blank" rel="noopener noreferrer" class="footer-social-btn social-li" title="LinkedIn">
                                <i class="bi bi-linkedin fs-6"></i>
                            </a>
                        @endif
                        @if(!empty($settings->whatsapp_number))
                            <a href="{{ $footerWaLink }}" target="_blank" rel="noopener noreferrer" class="footer-social-btn social-wa" title="WhatsApp">
                                <i class="bi bi-whatsapp fs-6"></i>
                            </a>
                        @endif
                        @if(!empty($settings->youtube_url))
                            <a href="{{ $settings->youtube_url }}" target="_blank" rel="noopener noreferrer" class="footer-social-btn social-yt" title="YouTube">
                                <i class="bi bi-youtube fs-6"></i>
                            </a>
                        @endif
                        @if(!empty($settings->tiktok_url))
                            <a href="{{ $settings->tiktok_url }}" target="_blank" rel="noopener noreferrer" class="footer-social-btn social-tt" title="TikTok">
                                <i class="bi bi-tiktok fs-6"></i>
                            </a>
                        @endif
                        @if(empty($settings->facebook_url) && empty($settings->twitter_url) && empty($settings->instagram_url) && empty($settings->linkedin_url) && empty($settings->youtube_url) && empty($settings->tiktok_url))
                            <a href="#" class="footer-social-btn social-fb" title="Facebook"><i class="bi bi-facebook fs-6"></i></a>
                            <a href="#" class="footer-social-btn social-tw" title="Twitter / X"><i class="bi bi-twitter-x fs-6"></i></a>
                            <a href="#" class="footer-social-btn social-ig" title="Instagram"><i class="bi bi-instagram fs-6"></i></a>
                            <a href="#" class="footer-social-btn social-li" title="LinkedIn"><i class="bi bi-linkedin fs-6"></i></a>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-lg-7">
                <div class="bg-secondary bg-opacity-10 p-4 rounded-4">
                    <h4 class="h5 fw-bold mb-3">Send Us a Quick Message</h4>

                    @if(session('contact_success'))
                        <div class="alert alert-success border-0 rounded-3 mb-3 small">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('contact_success') }}
                        </div>
                    @endif
                    @if(session('contact_error'))
                        <div class="alert alert-danger border-0 rounded-3 mb-3 small">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('contact_error') }}
                        </div>
                    @endif

                    <form action="{{ route('contact.send') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <input type="text" name="first_name" class="form-control bg-dark text-white border-secondary small" placeholder="First Name *" value="{{ old('first_name') }}" required style="color:#fff; --bs-form-control-color:#fff;" autocomplete="given-name">
                                <style>#contact .form-control::placeholder { color: rgba(255,255,255,0.55) !important; }</style>
                            </div>
                            <div class="col-sm-6">
                                <input type="text" name="last_name" class="form-control bg-dark text-white border-secondary small" placeholder="Last Name *" value="{{ old('last_name') }}" required style="color:#fff;">
                            </div>
                            <div class="col-sm-6">
                                <input type="email" name="email" class="form-control bg-dark text-white border-secondary small" placeholder="Email Address *" value="{{ old('email') }}" required style="color:#fff;">
                            </div>
                            <div class="col-sm-6">
                                <input type="tel" name="phone" class="form-control bg-dark text-white border-secondary small" placeholder="Phone Number" value="{{ old('phone') }}" style="color:#fff;">
                            </div>
                            <div class="col-12">
                                <input type="text" name="subject" class="form-control bg-dark text-white border-secondary small" placeholder="Subject *" value="{{ old('subject') }}" required style="color:#fff;">
                            </div>
                            <div class="col-12">
                                <textarea name="message" class="form-control bg-dark text-white border-secondary small" rows="4" placeholder="How can we help you? *" required style="color:#fff;">{{ old('message') }}</textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-light rounded-pill px-4 small w-100 fw-semibold">
                                    <i class="bi bi-send me-2"></i>Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
        
        <hr class="my-5 border-secondary">
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 text-white-50 small">
            <div>
                <span>&copy; {{ date('Y') }} {{ $settings->platform_name ?? 'DOOTOR ENTERPRISES' }}. All rights reserved.</span>
                <span class="ms-md-2 text-white-50 opacity-75">| Developed by <a href="https://kisprojectslab.com" target="_blank" rel="noopener noreferrer" class="text-white text-decoration-none fw-medium border-bottom border-light">KendatTech</a></span>
            </div>
            <div class="d-flex flex-wrap gap-3">
                <a href="{{ route('staff.portal') }}" class="text-warning text-decoration-none fw-semibold hover-white"><i class="bi bi-shield-lock me-1"></i>Staff Portal</a>
                <a href="{{ route('terms.conditions') }}" class="text-white-50 text-decoration-none hover-white">Terms &amp; Conditions</a>
                <a href="{{ route('refund.policy') }}" class="text-white-50 text-decoration-none hover-white">Refund Policy</a>
                <a href="{{ route('privacy.policy') }}" class="text-white-50 text-decoration-none hover-white">Data &amp; Privacy Statement</a>
            </div>
        </div>
    </div>
</footer>

<!-- ============================================================
     iOS Sub-Services Modal (Full Screen Blur + Native Spring Sheet)
     ============================================================ -->
<div id="iosSubModal" class="ios-modal-overlay" onclick="closeSubModalOnBg(event)">
    <div class="ios-modal-sheet" id="iosModalSheet" onclick="event.stopPropagation()">
        <div class="ios-modal-handle-bar"></div>
        <div class="ios-modal-header">
            <div>
                <div class="ios-modal-title" id="iosModalTitle">Sub-Services</div>
                <div class="ios-modal-subtitle" id="iosModalSubtitle">Available administrative options</div>
            </div>
            <button class="ios-modal-close" onclick="closeSubModal()" aria-label="Close modal">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="ios-modal-body" id="iosModalBody">
            <!-- Populated dynamically by JS -->
        </div>
        <div class="ios-modal-cta">
            <a href="{{ route('login') }}" class="btn text-white w-100 d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-box-arrow-in-right"></i>
                <span>Log In to Proceed</span>
            </a>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // ==========================================
    // DISCLAIMER BANNER
    // ==========================================
    function dismissDisclaimer() {
        var banner = document.getElementById('disclaimerBanner');
        if (banner) banner.style.display = 'none';
        sessionStorage.setItem('disclaimerDismissed', 'true');
    }

    // ==========================================
    // iOS SUB-SERVICES MODAL SYSTEM
    // ==========================================
    function openSubModal(title, subServices) {
        var modal = document.getElementById('iosSubModal');
        var modalTitle = document.getElementById('iosModalTitle');
        var modalSubtitle = document.getElementById('iosModalSubtitle');
        var modalBody = document.getElementById('iosModalBody');

        if (!modal || !modalBody) return;

        modalTitle.textContent = title;
        var count = Array.isArray(subServices) ? subServices.length : 0;
        modalSubtitle.textContent = count + ' administrative options available';

        // Render iOS list items
        modalBody.innerHTML = '';
        if (count > 0) {
            subServices.forEach(function(sub) {
                var item = document.createElement('a');
                item.href = '{{ Auth::check() ? route("client.book") : route("login") }}';
                item.className = 'ios-sub-item';
                
                var priceHtml = '';
                @auth
                    if (sub.price && parseFloat(sub.price) > 0) {
                        var fmtPrice = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', maximumFractionDigits: 0 }).format(sub.price);
                        priceHtml = '<span class="badge bg-light text-dark border extra-small fw-bold px-2 py-1">' + fmtPrice + '</span>';
                    } else {
                        priceHtml = '<span class="badge bg-light text-secondary border extra-small px-2 py-1">Direct Quote</span>';
                    }
                @else
                    priceHtml = '';
                @endauth

                item.innerHTML = 
                    '<div class="ios-sub-item-icon">' +
                        '<i class="bi ' + (sub.icon || 'bi-file-earmark-check') + '"></i>' +
                    '</div>' +
                    '<div class="flex-grow-1 min-w-0">' +
                        '<div class="ios-sub-item-name text-truncate">' + sub.name + '</div>' +
                        '<div class="ios-sub-item-price">Official verification &amp; fast-track</div>' +
                    '</div>' +
                    '<div class="d-flex align-items-center gap-2 flex-shrink-0">' +
                        priceHtml +
                        '<i class="bi bi-chevron-right text-muted" style="font-size: 13px;"></i>' +
                    '</div>';

                modalBody.appendChild(item);
            });
        } else {
            modalBody.innerHTML = '<div class="text-center py-4 text-secondary small"><i class="bi bi-info-circle fs-3 d-block mb-2"></i>No sub-services listed for this item yet.</div>';
        }

        // Trigger opening animation
        modal.classList.add('ios-open');
        document.body.style.overflow = 'hidden';
    }

    function closeSubModal() {
        var modal = document.getElementById('iosSubModal');
        if (modal) {
            modal.classList.remove('ios-open');
            document.body.style.overflow = '';
        }
    }

    function closeSubModalOnBg(e) {
        if (e.target.id === 'iosSubModal') {
            closeSubModal();
        }
    }

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeSubModal();
        }
    });

    // ==========================================
    // PROMO VIDEO MODAL HANDLERS
    // ==========================================
    function openPromoModal() {
        var el = document.getElementById('promoVideoModal');
        if (el) {
            var modal = bootstrap.Modal.getOrCreateInstance(el);
            modal.show();
            // Track event
            trackTelemetry('video_promo_click', { action: 'opened_modal' });
        }
    }

    function closePromoModal() {
        var iframe = document.getElementById('promoIframe');
        if (iframe) {
            // Stop video on modal close
            var src = iframe.src;
            iframe.src = src;
        }
    }

    // Telemetry tracking helper
    function trackTelemetry(eventType, metadata) {
        try {
            fetch('{{ route("analytics.track") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    event_type: eventType,
                    metadata: metadata || {}
                })
            }).catch(function() {});
        } catch(e) {}
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Restore Disclaimer state
        if (sessionStorage.getItem('disclaimerDismissed') === 'true') {
            var banner = document.getElementById('disclaimerBanner');
            if (banner) banner.style.display = 'none';
        }

        // ==========================================
        // HIGH-PERFORMANCE 3D PARTICLE NETWORK CANVAS
        // ==========================================
        var canvas = document.getElementById('heroParticleCanvas');
        if (canvas) {
            var ctx = canvas.getContext('2d');
            var heroSection = document.getElementById('heroSection');
            var particles = [];
            var particleCount = window.innerWidth < 768 ? 26 : 50;
            var mouse = { x: null, y: null, maxDist: 130 };

            function resizeCanvas() {
                var dpr = window.devicePixelRatio || 1;
                canvas.width = canvas.parentElement.offsetWidth * dpr;
                canvas.height = canvas.parentElement.offsetHeight * dpr;
                ctx.scale(dpr, dpr);
            }
            resizeCanvas();
            window.addEventListener('resize', resizeCanvas);

            // Track mouse inside hero
            if (heroSection) {
                heroSection.addEventListener('mousemove', function(e) {
                    var rect = canvas.getBoundingClientRect();
                    mouse.x = e.clientX - rect.left;
                    mouse.y = e.clientY - rect.top;
                });
                heroSection.addEventListener('mouseleave', function() {
                    mouse.x = null;
                    mouse.y = null;
                });
            }

            // Create particles
            for (var i = 0; i < particleCount; i++) {
                particles.push({
                    x: Math.random() * (canvas.width / (window.devicePixelRatio || 1)),
                    y: Math.random() * (canvas.height / (window.devicePixelRatio || 1)),
                    vx: (Math.random() - 0.5) * 0.7,
                    vy: (Math.random() - 0.5) * 0.7,
                    radius: Math.random() * 2 + 1.2,
                    isGold: Math.random() > 0.35,
                    alpha: Math.random() * 0.5 + 0.3
                });
            }

            function animateParticles() {
                var w = canvas.width / (window.devicePixelRatio || 1);
                var h = canvas.height / (window.devicePixelRatio || 1);
                ctx.clearRect(0, 0, w, h);

                // Update & draw particles
                for (var i = 0; i < particles.length; i++) {
                    var p = particles[i];
                    p.x += p.vx;
                    p.y += p.vy;

                    // Bounce off edges smoothly
                    if (p.x < 0 || p.x > w) p.vx *= -1;
                    if (p.y < 0 || p.y > h) p.vy *= -1;

                    // Mouse interaction
                    if (mouse.x !== null && mouse.y !== null) {
                        var dx = p.x - mouse.x;
                        var dy = p.y - mouse.y;
                        var dist = Math.sqrt(dx * dx + dy * dy);
                        if (dist < mouse.maxDist) {
                            var force = (1 - dist / mouse.maxDist) * 0.6;
                            p.x += (dx / dist) * force;
                            p.y += (dy / dist) * force;

                            // Draw line to mouse
                            ctx.beginPath();
                            ctx.moveTo(p.x, p.y);
                            ctx.lineTo(mouse.x, mouse.y);
                            ctx.strokeStyle = 'rgba(212, 175, 55, ' + (0.25 * (1 - dist / mouse.maxDist)) + ')';
                            ctx.lineWidth = 0.8;
                            ctx.stroke();
                        }
                    }

                    // Draw particle circle
                    ctx.beginPath();
                    ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
                    ctx.fillStyle = p.isGold 
                        ? 'rgba(212, 175, 55, ' + p.alpha + ')' 
                        : 'rgba(52, 211, 153, ' + (p.alpha * 0.7) + ')';
                    ctx.fill();

                    // Connect nearby particles
                    for (var j = i + 1; j < particles.length; j++) {
                        var p2 = particles[j];
                        var dist2 = Math.hypot(p.x - p2.x, p.y - p2.y);
                        if (dist2 < 110) {
                            ctx.beginPath();
                            ctx.moveTo(p.x, p.y);
                            ctx.lineTo(p2.x, p2.y);
                            var lineAlpha = (1 - dist2 / 110) * 0.18;
                            ctx.strokeStyle = 'rgba(212, 175, 55, ' + lineAlpha + ')';
                            ctx.lineWidth = 0.6;
                            ctx.stroke();
                        }
                    }
                }

                requestAnimationFrame(animateParticles);
            }
            animateParticles();
        }

        // ==========================================
        // KINETIC WORD ROTATOR
        // ==========================================
        var rotatingWords = [
            "Passports",
            "Visas & Consular Permits",
            "Identity Records",
            "Court Affidavits",
            "Legal Attestations",
            "Consular Vetting"
        ];
        var wordIndex = 0;
        var rotatingEl = document.getElementById("heroRotatingWord");
        if (rotatingEl) {
            setInterval(function() {
                wordIndex = (wordIndex + 1) % rotatingWords.length;
                rotatingEl.style.opacity = '0';
                rotatingEl.style.transform = 'translateY(14px)';
                setTimeout(function() {
                    rotatingEl.textContent = rotatingWords[wordIndex];
                    rotatingEl.style.opacity = '1';
                    rotatingEl.style.transform = 'translateY(0)';
                }, 260);
            }, 3000);
        }

        // ==========================================
        // 3D INTERACTIVE PERSPECTIVE TILT
        // ==========================================
        var heroPipeline = document.querySelector('.hero-pipeline-wrap');
        var heroCard = document.getElementById('heroPanelCard');
        if (heroPipeline && heroCard && window.innerWidth >= 992) {
            heroPipeline.addEventListener('mousemove', function(e) {
                var rect = heroPipeline.getBoundingClientRect();
                var x = e.clientX - rect.left - (rect.width / 2);
                var y = e.clientY - rect.top - (rect.height / 2);
                var rotX = -(y / 28).toFixed(2);
                var rotY = (x / 28).toFixed(2);
                heroCard.style.transform = 'perspective(1000px) rotateX(' + rotX + 'deg) rotateY(' + rotY + 'deg)';
            });

            heroPipeline.addEventListener('mouseleave', function() {
                heroCard.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg)';
                heroCard.style.transition = 'transform 0.55s cubic-bezier(0.34, 1.56, 0.64, 1)';
            });

            heroPipeline.addEventListener('mouseenter', function() {
                heroCard.style.transition = 'transform 0.12s ease-out';
            });
        }

        // ==========================================
        // ANIMATED NUMBER COUNTER (0 -> 15,000+)
        // ==========================================
        var statDocsEl = document.getElementById('statDocs');
        if (statDocsEl) {
            var targetCount = 15000;
            var currentCount = 0;
            var duration = 2000; // 2 seconds
            var startTime = null;

            function animateCounter(timestamp) {
                if (!startTime) startTime = timestamp;
                var progress = Math.min((timestamp - startTime) / duration, 1);
                // Ease out expo
                var easeProgress = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
                currentCount = Math.floor(easeProgress * targetCount);
                statDocsEl.textContent = currentCount.toLocaleString() + '+';

                if (progress < 1) {
                    requestAnimationFrame(animateCounter);
                }
            }
            requestAnimationFrame(animateCounter);
        }

        // ==========================================
        // HERO SEARCH SYNC TO SERVICES
        // ==========================================
        var heroSearch = document.getElementById('heroQuickSearch');
        var serviceSearch = document.getElementById('serviceSearch');
        if (heroSearch && serviceSearch) {
            heroSearch.addEventListener('input', function(e) {
                serviceSearch.value = e.target.value;
                currentSearch = e.target.value.toLowerCase().trim();
                filterServices();
            });
            heroSearch.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    var servSec = document.getElementById('services');
                    if (servSec) servSec.scrollIntoView({ behavior: 'smooth' });
                }
            });
        }

        // ==========================================
        // CUSTOM MOBILE NAV TOGGLE
        // ==========================================
        var navToggleBtn = document.getElementById('navToggleBtn');
        var mobileNavDrawer = document.getElementById('mobileNavDrawer');
        var isNavOpen = false;

        if (navToggleBtn && mobileNavDrawer) {
            navToggleBtn.addEventListener('click', function() {
                isNavOpen = !isNavOpen;
                mobileNavDrawer.style.display = isNavOpen ? 'block' : 'none';
                var icon = navToggleBtn.querySelector('i');
                if (icon) {
                    icon.className = isNavOpen ? 'bi bi-x fs-3' : 'bi bi-list fs-3';
                }
            });

            mobileNavDrawer.querySelectorAll('a').forEach(function(link) {
                link.addEventListener('click', function() {
                    isNavOpen = false;
                    mobileNavDrawer.style.display = 'none';
                    var icon = navToggleBtn.querySelector('i');
                    if (icon) icon.className = 'bi bi-list fs-3';
                });
            });
        }

        // ==========================================
        // CUSTOM FAQ ACCORDION
        // ==========================================
        document.querySelectorAll('#customFaqAccordion .faq-question').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var answer = this.nextElementSibling;
                var icon = this.querySelector('.faq-icon');
                var isOpen = answer.style.display === 'block';

                document.querySelectorAll('#customFaqAccordion .faq-answer').forEach(function(a) {
                    a.style.display = 'none';
                });
                document.querySelectorAll('#customFaqAccordion .faq-icon').forEach(function(i) {
                    i.style.transform = 'rotate(0deg)';
                });

                if (!isOpen) {
                    answer.style.display = 'block';
                    if (icon) icon.style.transform = 'rotate(180deg)';
                }
            });
        });

        // ==========================================
        // SERVICES CATALOG SEARCH & FILTER
        // ==========================================
        var searchInput = document.getElementById('serviceSearch');
        var filterButtons = document.querySelectorAll('#serviceCategories button');
        var cardContainers = document.querySelectorAll('.service-card-container');

        var currentSearch = "";
        var currentCategory = "all";

        function filterServices() {
            var visibleCount = 0;
            cardContainers.forEach(function(container) {
                var name = container.getAttribute('data-name') || '';
                var category = container.getAttribute('data-category') || '';
                
                var matchesSearch = name.includes(currentSearch);
                var matchesCategory = currentCategory === "all" || category === currentCategory;

                if (matchesSearch && matchesCategory) {
                    container.style.display = "block";
                    visibleCount++;
                } else {
                    container.style.display = "none";
                }
            });
            
            var noResultsMsg = document.getElementById('noServicesFound');
            if (visibleCount === 0) {
                if (!noResultsMsg) {
                    noResultsMsg = document.createElement('div');
                    noResultsMsg.id = 'noServicesFound';
                    noResultsMsg.className = 'col-12 text-center py-5 text-secondary';
                    noResultsMsg.innerHTML = '<i class="bi bi-search fs-1 d-block mb-2"></i><p class="small">No services match your criteria. Please try another search.</p>';
                    var servicesGrid = document.querySelector('#services .row.g-4');
                    if (servicesGrid) servicesGrid.appendChild(noResultsMsg);
                }
            } else if (noResultsMsg) {
                noResultsMsg.remove();
            }
        }

        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                currentSearch = e.target.value.toLowerCase().trim();
                filterServices();
            });
        }

        filterButtons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                filterButtons.forEach(function(b) {
                    b.classList.remove('btn-dark', 'active');
                    b.classList.add('btn-outline-dark');
                });
                
                this.classList.remove('btn-outline-dark');
                this.classList.add('btn-dark', 'active');
                
                currentCategory = this.getAttribute('data-category');
                filterServices();
            });
        });

        // ==========================================
        // ON-SCROLL REVEAL ANIMATIONS (IntersectionObserver)
        // ==========================================
        (function initScrollReveal() {
            var targets = [];

            // Section headings & intro blocks
            document.querySelectorAll('#services .text-center, #faq .text-center, #reviews .row.align-items-end').forEach(function(el) {
                el.classList.add('scroll-reveal');
                targets.push(el);
            });

            // Feature cards ("All Services In One Place", "Fast Processing", "Secure & Trackable")
            document.querySelectorAll('.modern-feature-card').forEach(function(el, idx) {
                el.classList.add('scroll-reveal', 'reveal-scale');
                el.style.transitionDelay = (idx * 90) + 'ms';
                targets.push(el);
            });

            // Service cards in Services Catalog
            document.querySelectorAll('#services .service-item').forEach(function(el, idx) {
                el.classList.add('scroll-reveal');
                el.style.transitionDelay = ((idx % 3) * 85) + 'ms';
                targets.push(el);
            });

            // FAQ accordion items
            document.querySelectorAll('#faq .accordion-item').forEach(function(el, idx) {
                el.classList.add('scroll-reveal');
                el.style.transitionDelay = (Math.min(idx, 5) * 65) + 'ms';
                targets.push(el);
            });

            // Reviews horizontal marquee
            var reviewsTrack = document.querySelector('#reviews .reviews-marquee-viewport');
            if (reviewsTrack) {
                reviewsTrack.classList.add('scroll-reveal', 'reveal-scale');
                targets.push(reviewsTrack);
            }

            // Footer / Contact columns
            document.querySelectorAll('#contact .col-lg-4, #contact .col-lg-3, #contact .col-lg-5').forEach(function(el, idx) {
                el.classList.add('scroll-reveal');
                el.style.transitionDelay = (idx * 90) + 'ms';
                targets.push(el);
            });

            if (!('IntersectionObserver' in window)) {
                targets.forEach(function(el) { el.classList.add('is-visible'); });
                return;
            }

            var observer = new IntersectionObserver(function(entries, obs) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        obs.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.08,
                rootMargin: '0px 0px -40px 0px'
            });

            targets.forEach(function(el) { observer.observe(el); });
        })();

        // ==========================================
        // STAR RATING PICKER FOR REVIEWS
        // ==========================================
        var starPickerStars = document.querySelectorAll('#starRatingPicker .rating-star-picker');
        var ratingInput = document.getElementById('reviewRatingInput');
        var ratingLabel = document.getElementById('ratingScoreLabel');
        if (starPickerStars.length && ratingInput) {
            starPickerStars.forEach(function(star) {
                star.addEventListener('click', function() {
                    var val = parseInt(this.getAttribute('data-rating')) || 5;
                    ratingInput.value = val;
                    if (ratingLabel) ratingLabel.textContent = val.toFixed(1) + ' / 5 Stars';
                    starPickerStars.forEach(function(s) {
                        var sVal = parseInt(s.getAttribute('data-rating'));
                        if (sVal <= val) {
                            s.classList.remove('bi-star');
                            s.classList.add('bi-star-fill', 'text-warning');
                        } else {
                            s.classList.remove('bi-star-fill', 'text-warning');
                            s.classList.add('bi-star');
                        }
                    });
                });
            });
        }

        // ==========================================
        // AUTOMATIC TELEMETRY TRACKING
        // ==========================================
        // Track page view
        trackTelemetry('page_view', { path: window.location.pathname, referrer: document.referrer });

        // Track social media clicks
        document.querySelectorAll('.footer-social-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var title = this.getAttribute('title') || 'Social Media Link';
                var href = this.getAttribute('href') || '#';
                trackTelemetry('social_click', { platform: title, url: href });
            });
        });
    });
</script>

{{-- ════════════════════════════════════════════════════════════
     GEMINI AI CHATBOT WIDGET + LIVE SUPPORT CHAT WIDGET
════════════════════════════════════════════════════════════ --}}

<!-- Single Unified Assistant Button (beside WhatsApp in layouts/app) -->
<div id="chatStack" style="position:fixed;bottom:25px;right:92px;z-index:9991;display:flex;flex-direction:row;align-items:center;">

    {{-- Unified AI + Live Support Button --}}
    <button id="geminiBotBtn" onclick="toggleGeminiChat()" title="DOOTOR Assistant &amp; Live Support"
        style="width:52px;height:52px;border-radius:50%;border:none;cursor:pointer;
               background:linear-gradient(135deg,#004225 60%,#1a7a4a);
               box-shadow:0 6px 24px rgba(0,66,37,.35);
               display:flex;align-items:center;justify-content:center;
               transition:transform .2s,box-shadow .2s;position:relative;">
        <i class="bi bi-chat-dots-fill" style="font-size:22px;color:#D4AF37;"></i>
        <span style="position:absolute;top:-2px;right:-2px;width:12px;height:12px;background:#22c55e;border-radius:50%;border:2px solid #fff;animation:gPulse 2s ease-in-out infinite;"></span>
    </button>
</div>

<style>
@keyframes gPulse { 0%,100%{transform:scale(1);opacity:1} 50%{transform:scale(1.5);opacity:.6} }
#chatStack button:hover { transform:scale(1.1) !important; box-shadow:0 10px 32px rgba(0,0,0,.3) !important; }
#chatStack a:hover    { transform:scale(1.1) !important; box-shadow:0 10px 32px rgba(37,211,102,.4) !important; }

/* ── Gemini Chat Drawer ───────────────────────────────────── */
#geminiDrawer {
    position:fixed; bottom:90px; right:24px; z-index:1049;
    width:360px; max-width:calc(100vw - 32px);
    background:#fff; border-radius:20px;
    box-shadow:0 20px 60px rgba(0,0,0,.18), 0 4px 16px rgba(0,66,37,.12);
    display:none; flex-direction:column; overflow:hidden;
}
@keyframes drawerIn { from{opacity:0;transform:translateY(30px) scale(.95)} to{opacity:1;transform:none} }
#geminiDrawer.show { display:flex; animation:drawerIn .35s cubic-bezier(.34,1.56,.64,1) both; }

.gd-header { background:linear-gradient(135deg,#004225,#1a7a4a); color:#fff; padding:14px 16px; display:flex; align-items:center; gap:10px; }
.gd-avatar { width:36px;height:36px;border-radius:50%;background:rgba(212,175,55,.2);border:2px solid #D4AF37;display:flex;align-items:center;justify-content:center; }
.gd-messages { flex:1; overflow-y:auto; padding:16px 14px; display:flex; flex-direction:column; gap:10px; min-height:220px; max-height:320px; background:#fff; }
.gd-messages::-webkit-scrollbar { width:4px; }
.gd-messages::-webkit-scrollbar-thumb { background:#dee2e6; border-radius:2px; }
.gd-bubble { max-width:85%; padding:9px 13px; border-radius:16px; font-size:13.5px; line-height:1.5; word-break:break-word; }
.gd-bubble.bot  { background:#f1f5f9; color:#1e293b; border-bottom-left-radius:4px; align-self:flex-start; }
.gd-bubble.user { background:#004225; color:#fff; border-bottom-right-radius:4px; align-self:flex-end; }
.gd-bubble.typing { background:#f1f5f9; align-self:flex-start; padding:12px 16px; }
.gd-bubble.typing span { display:inline-block; width:7px; height:7px; background:#94a3b8; border-radius:50%; margin:0 1px; animation:tDot 1.2s ease-in-out infinite; }
.gd-bubble.typing span:nth-child(2) { animation-delay:.2s; }
.gd-bubble.typing span:nth-child(3) { animation-delay:.4s; }
@keyframes tDot { 0%,80%,100%{transform:scale(0.6);opacity:.4} 40%{transform:scale(1);opacity:1} }
.gd-quick-replies { display:flex; flex-wrap:wrap; gap:5px; padding:0 14px 10px; background:#fff; }
.gd-qr-btn { background:#f8fafc; border:1px solid #e2e8f0; border-radius:20px; padding:4px 10px; font-size:11.5px; cursor:pointer; color:#334155; transition:all .15s; }
.gd-qr-btn:hover { background:#004225; color:#fff; border-color:#004225; }
.gd-footer { border-top:1px solid #f1f5f9; padding:10px 12px; display:flex; gap:8px; align-items:flex-end; background:#fff; }
.gd-footer input { flex:1; border:1.5px solid #e2e8f0; border-radius:12px; padding:8px 12px; font-size:13px; outline:none; transition:border-color .2s; }
.gd-footer input:focus { border-color:#004225; }
.gd-send { width:36px;height:36px;border-radius:50%;background:#004225;color:#D4AF37;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:background .2s; }
.gd-send:hover { background:#1a7a4a; }

/* ── Live Support Drawer ─────────────────────────────────── */
#liveChatDrawer {
    position:fixed; bottom:90px; right:24px; z-index:1049;
    width:360px; max-width:calc(100vw - 32px);
    background:#fff; border-radius:20px;
    box-shadow:0 20px 60px rgba(0,0,0,.18);
    display:none; flex-direction:column; overflow:hidden;
}
#liveChatDrawer.show { display:flex; animation:drawerIn .35s cubic-bezier(.34,1.56,.64,1) both; }
.lcd-header { background:linear-gradient(135deg,#1e293b,#334155); color:#fff; padding:14px 16px; display:flex; align-items:center; gap:10px; }
.lcd-messages { flex:1; overflow-y:auto; padding:16px 14px; display:flex; flex-direction:column; gap:10px; min-height:180px; max-height:260px; }
.lcd-messages::-webkit-scrollbar { width:4px; }
.lcd-messages::-webkit-scrollbar-thumb { background:#dee2e6; border-radius:2px; }
.lcd-bubble { max-width:85%; padding:9px 13px; border-radius:16px; font-size:13.5px; line-height:1.5; word-break:break-word; }
.lcd-bubble.client { background:#f1f5f9; color:#1e293b; border-bottom-left-radius:4px; align-self:flex-start; }
.lcd-bubble.agent  { background:#1e293b; color:#fff; border-bottom-right-radius:4px; align-self:flex-end; }
.lcd-bubble.system { background:rgba(212,175,55,.12); color:#92400e; border-radius:10px; align-self:center; font-size:11.5px; font-style:italic; text-align:center; padding:5px 12px; max-width:90%; }
.lcd-footer { border-top:1px solid #f1f5f9; padding:10px 12px; display:flex; gap:8px; align-items:flex-end; }
.lcd-footer input { flex:1; border:1.5px solid #e2e8f0; border-radius:12px; padding:8px 12px; font-size:13px; outline:none; transition:border-color .2s; }
.lcd-footer input:focus { border-color:#1e293b; }
.lcd-send { width:36px;height:36px;border-radius:50%;background:#1e293b;color:#fff;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.lcd-queue-badge { background:rgba(212,175,55,.12); border:1px solid rgba(212,175,55,.3); color:#92400e; border-radius:20px; padding:6px 14px; font-size:12px; text-align:center; margin:12px 14px 4px; }
</style>

{{-- ── GEMINI CHATBOT DRAWER ──────────────────────────────── --}}
<div id="geminiDrawer" role="dialog" aria-label="AI Assistant">
    <div class="gd-header">
        <div class="gd-avatar">
            <svg width="18" height="18" viewBox="0 0 36 36" fill="none">
                <path d="M18 8c-5.52 0-10 4.48-10 10s4.48 10 10 10 10-4.48 10-10S23.52 8 18 8zm-1 15h-2v-6h2v6zm0-8h-2v-2h2v2zm4 8h-2v-6h2v6zm0-8h-2v-2h2v2z" fill="#D4AF37"/>
            </svg>
        </div>
        <div class="flex-grow-1">
            <div class="fw-bold small">DOOTOR AI Assistant</div>
            <div style="font-size:11px;opacity:.7;">Powered by Gemini &middot; 24/7 Available</div>
        </div>
        <button onclick="toggleGeminiChat()" style="background:none;border:none;color:rgba(255,255,255,.8);cursor:pointer;font-size:18px;line-height:1;" aria-label="Close">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <div class="gd-messages" id="gdMessages">
        <div class="gd-bubble bot">
            👋 Hello! I'm DOOTOR's AI Assistant. I can help with questions about passports, visas, identity records, court affidavits, and all our consular services.<br><br>How can I assist you today?
        </div>
    </div>
    <div class="gd-quick-replies" id="gdQuickReplies">
        <button class="gd-qr-btn" onclick="gdQuickSend('What services do you offer?')">📋 Our Services</button>
        <button class="gd-qr-btn" onclick="gdQuickSend('How long does passport processing take?')">⏱ Processing Time</button>
        <button class="gd-qr-btn" onclick="gdQuickSend('What documents do I need for a visa application?')">📄 Visa Requirements</button>
        <button class="gd-qr-btn" onclick="gdQuickSend('How do I book an appointment?')">📅 Book Appointment</button>
    </div>
    <div class="px-3 pb-2 bg-white">
        <button type="button" onclick="switchToLiveSupport()" class="btn btn-sm w-100 rounded-pill fw-semibold d-flex align-items-center justify-content-center gap-2" style="background:rgba(30,41,59,0.06);color:#1e293b;border:1px solid #e2e8f0;font-size:12px;">
            <i class="bi bi-headset text-primary"></i>
            <span>Need human help? Chat with Live Support Agent</span>
        </button>
    </div>
    <div class="gd-footer">
        <input type="text" id="gdInput" placeholder="Ask me anything…" onkeydown="if(event.key==='Enter')gdSend()" maxlength="500" autocomplete="off">
        <button class="gd-send" onclick="gdSend()" aria-label="Send">
            <i class="bi bi-send-fill" style="font-size:13px;"></i>
        </button>
    </div>
</div>

{{-- ── LIVE SUPPORT CHAT DRAWER ───────────────────────────── --}}
<div id="liveChatDrawer" role="dialog" aria-label="Live Support Chat">
    <div class="lcd-header">
        <div style="width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,.1);border:2px solid rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;">
            <i class="bi bi-headset text-white"></i>
        </div>
        <div class="flex-grow-1">
            <div class="fw-bold small">Live Support</div>
            <div style="font-size:11px;opacity:.7;" id="lcdStatusText">Ready to connect</div>
        </div>
        <button onclick="switchBackToAi()" title="Back to AI Assistant" style="background:rgba(255,255,255,.12);border:none;color:#fff;cursor:pointer;font-size:11px;padding:4px 10px;border-radius:999px;margin-right:4px;">
            <i class="bi bi-robot me-1"></i>AI Bot
        </button>
        <button onclick="toggleLiveChat()" style="background:none;border:none;color:rgba(255,255,255,.8);cursor:pointer;font-size:18px;line-height:1;" aria-label="Close">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    @guest
    <div class="p-4 text-center">
        <i class="bi bi-lock-fill text-muted mb-3 d-block" style="font-size:36px;"></i>
        <p class="fw-bold mb-1 small">Login Required</p>
        <p class="text-muted mb-3" style="font-size:12px;">Please log in to start a live support session with our team.</p>
        <a href="{{ route('login') }}" class="btn btn-dark rounded-pill px-4 fw-bold btn-sm">
            <i class="bi bi-box-arrow-in-right me-1"></i>Log In to Chat
        </a>
        <p class="mt-3 mb-0" style="font-size:11px;color:#9ca3af;">Don't have an account? <a href="{{ route('register') }}" class="text-dark fw-semibold">Register free</a></p>
    </div>
    @endguest

    @auth
    <div id="lcdQueueBadge" class="lcd-queue-badge" style="display:none;">
        <i class="bi bi-clock-history me-1"></i><span id="lcdQueueText">Connecting to queue…</span>
    </div>
    <div class="lcd-messages" id="lcdMessages"></div>
    <div class="lcd-footer" id="lcdFooter" style="display:none;">
        <input type="text" id="lcdInput" placeholder="Type your message…" onkeydown="if(event.key==='Enter')lcdSend()" maxlength="500" autocomplete="off">
        <button class="lcd-send" onclick="lcdSend()" aria-label="Send">
            <i class="bi bi-send-fill" style="font-size:13px;"></i>
        </button>
    </div>
    <div class="p-3" id="lcdStartArea">
        <button onclick="lcdStart()" class="btn w-100 fw-bold rounded-pill"
            style="background:linear-gradient(135deg,#1e293b,#334155);color:#fff;border:none;padding:10px 0;">
            <i class="bi bi-chat-dots me-2"></i>Start Live Support Chat
        </button>
        <p class="text-center text-muted mt-2 mb-0" style="font-size:11px;">Average wait: &lt;5 minutes</p>
    </div>
    @endauth
</div>

<script>
// ═══════════════════════════════════════════════════════════
//  GEMINI AI CHATBOT
// ═══════════════════════════════════════════════════════════
var gdHistory  = [];
var gdBusy     = false;

function toggleGeminiChat() {
    var gd  = document.getElementById('geminiDrawer');
    var lcd = document.getElementById('liveChatDrawer');
    if (lcd.classList.contains('show')) { lcd.classList.remove('show'); lcd.style.display = 'none'; return; }
    if (gd.classList.contains('show')) { gd.classList.remove('show'); gd.style.display = 'none'; }
    else { gd.style.display = 'flex'; setTimeout(function() { gd.classList.add('show'); document.getElementById('gdInput').focus(); }, 10); }
}

function switchToLiveSupport() {
    var gd  = document.getElementById('geminiDrawer');
    var lcd = document.getElementById('liveChatDrawer');
    gd.classList.remove('show'); gd.style.display = 'none';
    lcd.style.display = 'flex';
    setTimeout(function() { lcd.classList.add('show'); }, 10);
}

function switchBackToAi() {
    var gd  = document.getElementById('geminiDrawer');
    var lcd = document.getElementById('liveChatDrawer');
    lcd.classList.remove('show'); lcd.style.display = 'none';
    gd.style.display = 'flex';
    setTimeout(function() { gd.classList.add('show'); document.getElementById('gdInput').focus(); }, 10);
}

function gdAddBubble(text, cls) {
    var area = document.getElementById('gdMessages');
    var el   = document.createElement('div');
    el.className = 'gd-bubble ' + cls;
    el.textContent = text;
    area.appendChild(el);
    area.scrollTop = area.scrollHeight;
    return el;
}

function gdSend() {
    var inp = document.getElementById('gdInput');
    var msg = inp.value.trim();
    if (!msg || gdBusy) return;
    inp.value = '';
    document.getElementById('gdQuickReplies').style.display = 'none';
    gdAddBubble(msg, 'user');
    gdDispatch(msg);
}

function gdQuickSend(msg) {
    document.getElementById('gdQuickReplies').style.display = 'none';
    gdAddBubble(msg, 'user');
    gdDispatch(msg);
}

function gdDispatch(msg) {
    gdBusy = true;
    gdHistory.push({ role: 'user', parts: [{ text: msg }] });

    // typing indicator
    var area  = document.getElementById('gdMessages');
    var tEl   = document.createElement('div');
    tEl.className = 'gd-bubble typing'; tEl.id = 'gdTyping';
    tEl.innerHTML = '<span></span><span></span><span></span>';
    area.appendChild(tEl); area.scrollTop = area.scrollHeight;

    fetch('{{ route("chat.gemini") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': (document.querySelector('meta[name=csrf-token]') || {}).content || '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ message: msg, history: gdHistory.slice(-8) })
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        var t = document.getElementById('gdTyping'); if(t) t.remove();
        gdBusy = false;
        var reply = (d.reply || 'Apologies — something went wrong. Please try again.').trim();
        gdHistory.push({ role: 'model', parts: [{ text: reply }] });
        gdAddBubble(reply, 'bot');
    })
    .catch(function() {
        var t = document.getElementById('gdTyping'); if(t) t.remove();
        gdBusy = false;
        gdAddBubble('Connection error. Please check your internet and try again.', 'bot');
    });
}

// ═══════════════════════════════════════════════════════════
//  LIVE SUPPORT CHAT
// ═══════════════════════════════════════════════════════════
@auth
var lcdChatId  = null;
var lcdLastId  = 0;
var lcdPollTmr = null;
var lcdPhase   = 'idle'; // idle|waiting|active|closed

function toggleLiveChat() {
    var lcd = document.getElementById('liveChatDrawer');
    var gd  = document.getElementById('geminiDrawer');
    if (gd.classList.contains('show')) { gd.classList.remove('show'); gd.style.display = 'none'; }
    if (lcd.classList.contains('show')) { lcd.classList.remove('show'); lcd.style.display = 'none'; }
    else { lcd.style.display = 'flex'; setTimeout(function() { lcd.classList.add('show'); }, 10); }
}

function lcdAddBubble(msg, cls) {
    var area = document.getElementById('lcdMessages');
    var el   = document.createElement('div');
    el.className = 'lcd-bubble ' + cls;
    el.textContent = msg;
    area.appendChild(el);
    area.scrollTop = area.scrollHeight;
}

function lcdStart() {
    document.getElementById('lcdStartArea').style.display = 'none';
    document.getElementById('lcdStatusText').textContent = 'Connecting…';

    fetch('{{ route("live-chat.start") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ subject: 'General Enquiry from homepage' })
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        lcdChatId = d.chat_id;
        lcdPhase  = d.status;
        if (d.status === 'waiting') {
            document.getElementById('lcdQueueBadge').style.display = '';
            document.getElementById('lcdQueueText').textContent = 'You are #' + d.queue_position + ' in queue — an agent will join you shortly.';
            document.getElementById('lcdStatusText').textContent = '#' + d.queue_position + ' in queue';
        } else {
            document.getElementById('lcdQueueBadge').style.display = 'none';
            document.getElementById('lcdFooter').style.display = '';
            document.getElementById('lcdStatusText').textContent = 'Connected to agent';
        }
        lcdPollTmr = setInterval(lcdPoll, 3000);
        lcdPoll();
    })
    .catch(function() {
        document.getElementById('lcdStatusText').textContent = 'Failed to connect';
        lcdAddBubble('Unable to reach support. Please try WhatsApp or email instead.', 'system');
    });
}

function lcdPoll() {
    if (!lcdChatId) return;
    fetch('{{ url("/live-chat") }}/' + lcdChatId + '/messages?after=' + lcdLastId, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        (d.messages || []).forEach(function(m) {
            if (m.id > lcdLastId) {
                lcdLastId = m.id;
                var cls = m.sender_type === 'system' ? 'system' : (m.sender_type === 'agent' ? 'agent' : 'client');
                lcdAddBubble(m.message, cls);
            }
        });
        if (d.status === 'active' && lcdPhase !== 'active') {
            lcdPhase = 'active';
            document.getElementById('lcdQueueBadge').style.display = 'none';
            document.getElementById('lcdFooter').style.display = '';
            document.getElementById('lcdStatusText').textContent = 'Connected — Agent ' + (d.agent_name || '') + ' is here';
        }
        if (d.status === 'waiting' && d.queue_position) {
            document.getElementById('lcdQueueText').textContent = 'You are #' + d.queue_position + ' in queue — an agent will join shortly.';
        }
        if (d.status === 'closed') {
            lcdPhase = 'closed';
            clearInterval(lcdPollTmr);
            document.getElementById('lcdFooter').style.display = 'none';
            document.getElementById('lcdStatusText').textContent = 'Chat ended';
        }
    });
}

function lcdSend() {
    var inp = document.getElementById('lcdInput');
    var msg = inp.value.trim();
    if (!msg || !lcdChatId || lcdPhase !== 'active') return;
    inp.value = '';
    lcdAddBubble(msg, 'client');
    fetch('{{ url("/live-chat") }}/' + lcdChatId + '/message', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ message: msg })
    });
}
@endauth
</script>
@endsection
