<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OFFICIAL PRE-ENROLLMENT FORM - <?php echo e($service->name); ?> (<?php echo e($referenceNumber); ?>)</title>
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
                    <?php if(!empty($logoUrl)): ?>
                        <img src="<?php echo e($logoUrl); ?>" alt="Logo">
                    <?php else: ?>
                        <div style="font-weight: 800; font-size: 16px; color: #004225; line-height: 1.1; text-align: center;">
                            <?php echo e(strtoupper($companyName)); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </td>
            <!-- Center: Header Title & Service Meta -->
            <td style="width: 56%; text-align: center; vertical-align: middle; padding: 0 10px;">
                <h4 style="margin: 0; font-weight: 800; color: #004225; font-size: 17px; text-transform: uppercase; letter-spacing: 0.5px;"><?php echo e($companyName); ?></h4>
                <div style="font-size: 13px; font-weight: 700; color: #d4af37; margin-top: 3px; text-transform: uppercase; letter-spacing: 0.5px;">OFFICIAL PRE-ENROLLMENT FORM</div>
                <div style="font-size: 12.5px; font-weight: 600; color: #334155; margin-top: 2px;"><?php echo e($service->name); ?></div>
                <div style="font-size: 10.5px; color: #64748b; margin-top: 4px;">
                    Issued: <?php echo e($generatedDate); ?> &nbsp;|&nbsp; <strong style="color: #004225; letter-spacing: 0.5px;">REF: <?php echo e($referenceNumber); ?></strong>
                </div>
            </td>
            <!-- Right: Passport Photo Box -->
            <td style="width: 22%; text-align: right; vertical-align: middle;">
                <div class="photo-box">
                    <?php if($user && $user->avatar_url): ?>
                        <img src="<?php echo e(app_file_url($user->avatar_url)); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        AFFIX PASSPORT<br>PHOTOGRAPH<br>HERE<br>(White BG)
                    <?php endif; ?>
                </div>
            </td>
        </tr>
    </table>

    <!-- Section 1: Applicant Details -->
    <div class="section-title">SECTION 1: APPLICANT PERSONAL INFORMATION</div>
    <div class="row g-3">
        <div class="col-6">
            <div class="form-label-print">Full Name (Surname First)</div>
            <div class="field-fill-line"><?php echo e($user ? strtoupper($user->name) : '____________________________________'); ?></div>
        </div>
        <div class="col-6">
            <div class="form-label-print">Email Address</div>
            <div class="field-fill-line"><?php echo e($user ? $user->email : '____________________________________'); ?></div>
        </div>
        <div class="col-4">
            <div class="form-label-print">Phone Number</div>
            <div class="field-fill-line"><?php echo e($user ? ($user->phone ?? '__________________') : '__________________'); ?></div>
        </div>
        <div class="col-4">
            <div class="form-label-print">Country of Residence</div>
            <div class="field-fill-line"><?php echo e($user ? ($user->country ?? 'Nigeria') : 'Nigeria'); ?></div>
        </div>
        <div class="col-4">
            <div class="form-label-print">State / Province</div>
            <div class="field-fill-line"><?php echo e($user ? ($user->state ?? '__________________') : '__________________'); ?></div>
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
    <div class="section-title">SECTION 2: SERVICE SPECIFIC QUESTIONNAIRE — <?php echo e(strtoupper($service->name)); ?></div>
    
    <!-- Dynamic Service Details Summary Bar -->
    <div class="p-2 mb-3 rounded border bg-light d-flex justify-content-between align-items-center flex-wrap gap-2" style="font-size: 11px; border-color: #cbd5e1 !important;">
        <div>
            <strong class="text-uppercase" style="color: #004225;">Selected Service:</strong> <?php echo e($service->name); ?>

        </div>
        <div>
            <strong>Category:</strong> <?php echo e($service->category ?? 'General Service'); ?>

        </div>
        <div>
            <strong>Processing Timeline:</strong> <?php echo e($service->processing_days ? ($service->processing_days . ' Business Days') : 'Standard Processing'); ?>

        </div>
    </div>

    <?php if($fields && $fields->count() > 0): ?>
        <div class="row g-3">
            <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-<?php echo e(in_array($f->field_type, ['textarea', 'address', 'instructions', 'declaration']) ? '12' : '6'); ?>">
                    <?php if($f->field_type === 'instructions'): ?>
                        <div class="p-2 mb-1 bg-light border rounded" style="font-size: 11px;">
                            <strong style="color: #004225;"><?php echo e($f->field_label); ?></strong>
                            <p class="mb-0 text-muted" style="font-size: 10px;"><?php echo e($f->help_text ?: $f->placeholder); ?></p>
                        </div>
                    <?php elseif($f->field_type === 'declaration'): ?>
                        <div class="p-2 mb-1 border rounded bg-light" style="font-size: 11px;">
                            <span class="checkbox-box"></span> <strong><?php echo e($f->field_label); ?></strong>
                            <p class="mb-0 text-muted" style="font-size: 10px;"><?php echo e($f->help_text ?: $f->placeholder); ?></p>
                        </div>
                    <?php else: ?>
                        <div class="form-label-print"><?php echo e($f->field_label); ?> <?php echo e($f->is_required ? '*' : ''); ?></div>
                        <?php if($f->field_type === 'checkbox' || $f->field_type === 'yes_no'): ?>
                            <div class="field-fill-line">
                                <span class="checkbox-box"></span> YES &nbsp;&nbsp;&nbsp;&nbsp;
                                <span class="checkbox-box"></span> NO
                            </div>
                        <?php elseif($f->field_type === 'country'): ?>
                            <div class="field-fill-line">
                                [ &nbsp; ] Nigeria &nbsp;&nbsp; [ &nbsp; ] Ghana &nbsp;&nbsp; [ &nbsp; ] Kenya &nbsp;&nbsp; [ &nbsp; ] Other: __________________
                            </div>
                        <?php elseif($f->field_type === 'passport'): ?>
                            <div class="field-fill-line" style="font-size: 11px; font-weight: 500;">
                                <i class="bi bi-camera me-1"></i> Passport Photograph Attachment Required (White Background)
                            </div>
                        <?php elseif($f->field_type === 'file'): ?>
                            <div class="field-fill-line" style="font-size: 11px; font-weight: 500;">
                                Supporting File Upload: ____________________________________
                            </div>
                        <?php elseif($f->field_type === 'dropdown' && is_array($f->options)): ?>
                            <div class="field-fill-line">
                                Options: <?php echo e(implode(' / ', array_slice($f->options, 0, 4))); ?>

                            </div>
                        <?php elseif($f->field_type === 'date'): ?>
                            <div class="field-fill-line border-0 pt-1">
                                <div class="date-fill-box">
                                    <span class="date-unit">DD</span>
                                    <span class="date-separator">/</span>
                                    <span class="date-unit">MM</span>
                                    <span class="date-separator">/</span>
                                    <span class="date-unit-year">YYYY</span>
                                </div>
                            </div>
                        <?php elseif(in_array($f->field_type, ['textarea', 'address'])): ?>
                            <div class="field-fill-line" style="min-height: 48px;">
                                ___________________________________________________________________________________________________<br>
                                ___________________________________________________________________________________________________
                            </div>
                        <?php else: ?>
                            <div class="field-fill-line">____________________________________</div>
                        <?php endif; ?>
                        <?php if($f->help_text): ?>
                            <div style="font-size: 9.5px; color: #64748b;"><?php echo e($f->help_text); ?></div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <div class="col-12">
                <div class="form-label-print">Specific Purpose / Details of <?php echo e($service->name); ?> Request</div>
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
    <?php endif; ?>

    <!-- Section 3: Document Checklist -->
    <div class="section-title">SECTION 3: REQUIRED SUPPORTING DOCUMENTS CHECKLIST</div>
    <p style="font-size: 11px; color: #475569; margin-bottom: 8px;">Attach clear photocopies/scans of all ticked documents when uploading or submitting this completed form:</p>
    <div class="row g-2">
        <div class="col-6"><div style="font-size: 11px;"><span class="checkbox-box"></span> International Passport (Bio-Data Page)</div></div>
        <div class="col-6"><div style="font-size: 11px;"><span class="checkbox-box"></span> National Identification Number (NIN) Slip / Card</div></div>
        <div class="col-6"><div style="font-size: 11px;"><span class="checkbox-box"></span> Recent Passport Photograph (White Background)</div></div>
        <div class="col-6"><div style="font-size: 11px;"><span class="checkbox-box"></span> Proof of Address / Utility Bill / Affidavit</div></div>
        <?php if(!empty($requiredDocuments) && is_array($requiredDocuments)): ?>
            <?php $__currentLoopData = $requiredDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(!in_array(strtolower($doc), ['passport', 'nin', 'international passport', 'passport photograph'])): ?>
                    <div class="col-6">
                        <div style="font-size: 11px; font-weight: 500;">
                            <span class="checkbox-box"></span> <?php echo e($doc); ?>

                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
        <div class="col-6"><div style="font-size: 11px;"><span class="checkbox-box"></span> Other Supporting Document: ___________________________</div></div>
    </div>

    <!-- Section 4: Declaration & Signature -->
    <div class="section-title">SECTION 4: APPLICANT DECLARATION & CONSENT</div>
    <p style="font-size: 10.5px; color: #334155; text-align: justify; line-height: 1.4; margin-bottom: 15px;">
        I hereby declare that all information provided in this pre-enrollment form is true, correct, and complete to the best of my knowledge. 
        I authorize <strong><?php echo e($companyName); ?></strong> and its designated verification officers to process, verify, and authenticate my submitted documents with the relevant official authorities for the fulfillment of the requested service.
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
                <td style="width: 33%;"><strong>Tracking Ref:</strong> <?php echo e($referenceNumber); ?></td>
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
<?php /**PATH C:\xampp\htdocs\DOOTOR ENTERPRISES\resources\views\services\printable-form.blade.php ENDPATH**/ ?>