<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OFFICIAL SERVICE APPLICATION FORM - {{ $service->name }} ({{ $referenceNumber }})</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #1e293b;
            background-color: #f8fafc;
            padding: 20px;
        }
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            border: 2px solid #004225;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            border-radius: 4px;
        }
        .header-table {
            width: 100%;
            border-bottom: 3px double #004225;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .logo-box {
            width: 105px;
            height: 115px;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            padding: 4px;
            border-radius: 4px;
        }
        .logo-box img {
            max-height: 105px;
            max-width: 97px;
            object-fit: contain;
        }
        .photo-box {
            width: 105px;
            height: 115px;
            border: 1.5px dashed #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 9.5px;
            line-height: 1.3;
            color: #64748b;
            background: #f1f5f9;
            margin-left: auto;
            border-radius: 4px;
        }
        .section-title {
            background-color: #004225;
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 6px 12px;
            margin-top: 20px;
            margin-bottom: 12px;
            letter-spacing: 0.5px;
        }
        .form-label-print {
            font-size: 11px;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .field-fill-line {
            border-bottom: 1px solid #94a3b8;
            min-height: 28px;
            font-size: 13px;
            font-weight: 600;
            color: #0f172a;
            padding-top: 4px;
        }
        .date-fill-box {
            display: inline-flex;
            align-items: center;
            color: #94a3b8;
            font-size: 12px;
            font-weight: 400;
            letter-spacing: 0.5px;
        }
        .date-unit {
            display: inline-block;
            border-bottom: 1.5px solid #cbd5e1;
            min-width: 36px;
            text-align: center;
            color: #94a3b8;
            padding: 0 4px 2px 4px;
            font-weight: 500;
        }
        .date-unit-year {
            display: inline-block;
            border-bottom: 1.5px solid #cbd5e1;
            min-width: 55px;
            text-align: center;
            color: #94a3b8;
            padding: 0 4px 2px 4px;
            font-weight: 500;
        }
        .date-separator {
            color: #94a3b8;
            font-weight: 600;
            margin: 0 4px;
        }
        .checkbox-box {
            width: 14px;
            height: 14px;
            border: 1.5px solid #334155;
            display: inline-block;
            margin-right: 6px;
            vertical-align: middle;
        }
        .print-btn-bar {
            max-width: 800px;
            margin: 0 auto 20px auto;
        }
        @media print {
            .print-btn-bar {
                display: none !important;
            }
            body {
                background: #ffffff;
                padding: 0;
            }
            .form-container {
                box-shadow: none;
                border: 2px solid #000;
            }
        }
    </style>
</head>
<body>

<div class="print-btn-bar d-flex justify-content-between align-items-center">
    <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">&larr; Back to Portal</a>
    <button onclick="window.print()" class="btn btn-success btn-sm px-4 fw-bold" style="background-color: #004225; border: none;">
        <i class="bi bi-printer"></i> Print / Save as PDF
    </button>
</div>

<div class="form-container">
    <!-- Header Section (Requirements 3: Header format) -->
    <table class="header-table">
        <tr>
            <!-- Left: Company Logo Box (Matching Passport Photo Box height/width) -->
            <td style="width: 22%; vertical-align: middle;">
                <div class="logo-box">
                    @if(!empty($logoUrl))
                        <img src="{{ $logoUrl }}" alt="Logo">
                    @else
                        <div style="font-weight: 800; font-size: 16px; color: #004225; line-height: 1.1; text-align: center;">
                            {{ strtoupper($companyName) }}
                        </div>
                    @endif
                </div>
            </td>
            <!-- Center: Header Title & Service Meta -->
            <td style="width: 56%; text-align: center; vertical-align: middle; padding: 0 10px;">
                <h4 style="margin: 0; font-weight: 800; color: #004225; font-size: 17px; text-transform: uppercase; letter-spacing: 0.5px;">{{ $companyName }}</h4>
                <div style="font-size: 13px; font-weight: 700; color: #d4af37; margin-top: 3px; text-transform: uppercase; letter-spacing: 0.5px;">OFFICIAL SERVICE APPLICATION FORM</div>
                <div style="font-size: 12.5px; font-weight: 600; color: #334155; margin-top: 2px;">{{ $service->name }}</div>
                <div style="font-size: 10.5px; color: #64748b; margin-top: 4px;">
                    Issued: {{ $generatedDate }} &nbsp;|&nbsp; <strong style="color: #004225; letter-spacing: 0.5px;">REF: {{ $referenceNumber }}</strong>
                </div>
            </td>
            <!-- Right: Passport Photo Box -->
            <td style="width: 22%; text-align: right; vertical-align: middle;">
                <div class="photo-box">
                    @if($user && $user->avatar_url)
                        <img src="{{ app_file_url($user->avatar_url) }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        AFFIX PASSPORT<br>PHOTOGRAPH<br>HERE<br>(White BG)
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- Section 1: Applicant Details -->
    <div class="section-title">SECTION 1: APPLICANT PERSONAL INFORMATION</div>
    <div class="row g-3">
        <div class="col-6">
            <div class="form-label-print">Full Name (Surname First)</div>
            <div class="field-fill-line">{{ $user ? strtoupper($user->name) : '____________________________________' }}</div>
        </div>
        <div class="col-6">
            <div class="form-label-print">Email Address</div>
            <div class="field-fill-line">{{ $user ? $user->email : '____________________________________' }}</div>
        </div>
        <div class="col-4">
            <div class="form-label-print">Phone Number</div>
            <div class="field-fill-line">{{ $user ? ($user->phone ?? '__________________') : '__________________' }}</div>
        </div>
        <div class="col-4">
            <div class="form-label-print">Country of Residence</div>
            <div class="field-fill-line">{{ $user ? ($user->country ?? 'Nigeria') : 'Nigeria' }}</div>
        </div>
        <div class="col-4">
            <div class="form-label-print">State / Province</div>
            <div class="field-fill-line">{{ $user ? ($user->state ?? '__________________') : '__________________' }}</div>
        </div>
        <div class="col-6">
            <div class="form-label-print">Local Government Area (LGA)</div>
            <div class="field-fill-line">____________________________________</div>
        </div>
        <div class="col-3">
            <div class="form-label-print">Date of Birth</div>
            <div class="field-fill-line border-0 pt-1">
                <div class="date-fill-box">
                    <span class="date-unit">DD</span>
                    <span class="date-separator">/</span>
                    <span class="date-unit">MM</span>
                    <span class="date-separator">/</span>
                    <span class="date-unit-year">YYYY</span>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="form-label-print">Gender</div>
            <div class="field-fill-line">
                <span class="checkbox-box"></span> Male &nbsp;&nbsp;
                <span class="checkbox-box"></span> Female
            </div>
        </div>
        <div class="col-12">
            <div class="form-label-print">Residential / Postal Address</div>
            <div class="field-fill-line">___________________________________________________________________________________________________</div>
        </div>
        <div class="col-6">
            <div class="form-label-print">Means of Identification</div>
            <div class="field-fill-line" style="font-size: 11px; font-weight: 500;">
                <span class="checkbox-box"></span> NIN &nbsp;&nbsp;
                <span class="checkbox-box"></span> Passport &nbsp;&nbsp;
                <span class="checkbox-box"></span> Driver's License &nbsp;&nbsp;
                <span class="checkbox-box"></span> Voter's Card
            </div>
        </div>
        <div class="col-6">
            <div class="form-label-print">Identification Number (NIN / Passport No / ID Ref)</div>
            <div class="field-fill-line">____________________________________</div>
        </div>
    </div>

    <!-- Section 2: Service Questionnaire -->
    <div class="section-title">SECTION 2: SERVICE SPECIFIC QUESTIONNAIRE — {{ strtoupper($service->name) }}</div>
    
    <!-- Dynamic Service Details Summary Bar -->
    <div class="p-2 mb-3 rounded border bg-light d-flex justify-content-between align-items-center flex-wrap gap-2" style="font-size: 11px; border-color: #cbd5e1 !important;">
        <div>
            <strong class="text-uppercase" style="color: #004225;">Selected Service:</strong> {{ $service->name }}
        </div>
        <div>
            <strong>Category:</strong> {{ $service->category ?? 'General Service' }}
        </div>
        <div>
            <strong>Processing Timeline:</strong> {{ $service->processing_days ? ($service->processing_days . ' Business Days') : 'Standard Processing' }}
        </div>
    </div>

    @if($fields && $fields->count() > 0)
        <div class="row g-3">
            @foreach($fields as $f)
                <div class="col-{{ in_array($f->field_type, ['textarea', 'address', 'instructions']) ? '12' : '6' }}">
                    <div class="form-label-print">{{ $f->field_label }} {{ $f->is_required ? '*' : '' }}</div>
                    @if($f->field_type === 'checkbox' || $f->field_type === 'yes_no')
                        <div class="field-fill-line">
                            <span class="checkbox-box"></span> YES &nbsp;&nbsp;&nbsp;&nbsp;
                            <span class="checkbox-box"></span> NO
                        </div>
                    @elseif($f->field_type === 'dropdown' && is_array($f->options))
                        <div class="field-fill-line">
                            Options: {{ implode(' / ', array_slice($f->options, 0, 4)) }}
                        </div>
                    @elseif($f->field_type === 'date')
                        <div class="field-fill-line border-0 pt-1">
                            <div class="date-fill-box">
                                <span class="date-unit">DD</span>
                                <span class="date-separator">/</span>
                                <span class="date-unit">MM</span>
                                <span class="date-separator">/</span>
                                <span class="date-unit-year">YYYY</span>
                            </div>
                        </div>
                    @elseif(in_array($f->field_type, ['textarea', 'address']))
                        <div class="field-fill-line" style="min-height: 48px;">
                            ___________________________________________________________________________________________________<br>
                            ___________________________________________________________________________________________________
                        </div>
                    @else
                        <div class="field-fill-line">____________________________________</div>
                    @endif
                    @if($f->help_text)
                        <div style="font-size: 9.5px; color: #64748b;">{{ $f->help_text }}</div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="row g-3">
            <div class="col-12">
                <div class="form-label-print">Specific Purpose / Details of {{ $service->name }} Request</div>
                <div class="field-fill-line">___________________________________________________________________________________________________</div>
            </div>
            <div class="col-6">
                <div class="form-label-print">Reference / Registration Number (If any)</div>
                <div class="field-fill-line">____________________________________</div>
            </div>
            <div class="col-6">
                <div class="form-label-print">Preferred Processing Mode</div>
                <div class="field-fill-line"><span class="checkbox-box"></span> Standard &nbsp;&nbsp; <span class="checkbox-box"></span> Expedited</div>
            </div>
        </div>
    @endif

    <!-- Section 3: Document Checklist -->
    <div class="section-title">SECTION 3: REQUIRED SUPPORTING DOCUMENTS CHECKLIST</div>
    <p style="font-size: 11px; color: #475569; margin-bottom: 8px;">Attach clear photocopies/scans of all ticked documents when uploading or submitting this completed form:</p>
    <div class="row g-2">
        <div class="col-6"><div style="font-size: 11px;"><span class="checkbox-box"></span> International Passport (Bio-Data Page)</div></div>
        <div class="col-6"><div style="font-size: 11px;"><span class="checkbox-box"></span> National Identification Number (NIN) Slip / Card</div></div>
        <div class="col-6"><div style="font-size: 11px;"><span class="checkbox-box"></span> Recent Passport Photograph (White Background)</div></div>
        <div class="col-6"><div style="font-size: 11px;"><span class="checkbox-box"></span> Proof of Address / Utility Bill / Affidavit</div></div>
        @if(!empty($requiredDocuments) && is_array($requiredDocuments))
            @foreach($requiredDocuments as $doc)
                @if(!in_array(strtolower($doc), ['passport', 'nin', 'international passport', 'passport photograph']))
                    <div class="col-6">
                        <div style="font-size: 11px; font-weight: 500;">
                            <span class="checkbox-box"></span> {{ $doc }}
                        </div>
                    </div>
                @endif
            @endforeach
        @endif
        <div class="col-6"><div style="font-size: 11px;"><span class="checkbox-box"></span> Other Supporting Document: ___________________________</div></div>
    </div>

    <!-- Section 4: Declaration & Signature -->
    <div class="section-title">SECTION 4: APPLICANT DECLARATION & CONSENT</div>
    <p style="font-size: 10.5px; color: #334155; text-align: justify; line-height: 1.4; margin-bottom: 15px;">
        I hereby declare that all information provided in this application form is true, correct, and complete to the best of my knowledge. 
        I authorize <strong>{{ $companyName }}</strong> and its designated verification officers to process, verify, and authenticate my submitted documents with the relevant official authorities for the fulfillment of the requested service.
    </p>

    <table style="width: 100%; margin-top: 15px;">
        <tr>
            <td style="width: 60%; vertical-align: bottom;">
                <div class="form-label-print">Applicant Signature</div>
                <div class="field-fill-line" style="min-height: 40px;">X __________________________________________</div>
            </td>
            <td style="width: 40%; vertical-align: bottom; text-align: right;">
                <div class="form-label-print" style="text-align: right;">Submission Date</div>
                <div class="field-fill-line border-0 pt-1 align-items-center" style="text-align: right; min-height: 36px;">
                    <div class="date-fill-box">
                        <span class="date-unit">DD</span>
                        <span class="date-separator">/</span>
                        <span class="date-unit">MM</span>
                        <span class="date-separator">/</span>
                        <span class="date-unit-year">20__</span>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Section 5: Administrative Use Section -->
    <div style="border: 2px dashed #004225; padding: 12px; margin-top: 25px; background: #f8fafc; border-radius: 4px;">
        <div style="font-size: 11px; font-weight: 800; color: #004225; text-transform: uppercase; margin-bottom: 6px;">
            FOR OFFICIAL ADMINISTRATIVE USE ONLY
        </div>
        <table style="width: 100%; font-size: 11px;">
            <tr>
                <td style="width: 33%;"><strong>Tracking Ref:</strong> {{ $referenceNumber }}</td>
                <td style="width: 33%;"><strong>Assigned Officer:</strong> ________________</td>
                <td style="width: 34%;"><strong>Stage Status:</strong> ________________</td>
            </tr>
            <tr>
                <td style="padding-top: 6px;">
                    <strong>Date Received:</strong> 
                    <span class="date-fill-box ms-1">
                        <span class="date-unit">DD</span>
                        <span class="date-separator">/</span>
                        <span class="date-unit">MM</span>
                        <span class="date-separator">/</span>
                        <span class="date-unit-year">20__</span>
                    </span>
                </td>
                <td style="padding-top: 6px;"><strong>Doc Verification:</strong> [  ] Passed [  ] Fail</td>
                <td style="padding-top: 6px;"><strong>Payment Status:</strong> [  ] Paid [  ] Pending</td>
            </tr>
        </table>
    </div>
</div>

</body>
</html>
