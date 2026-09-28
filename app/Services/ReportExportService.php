<?php

namespace App\Services;

use App\Models\SystemSetting;
use App\Models\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportService
{
    /**
     * Export matching report records to CSV stream.
     */
    public static function exportCsv(string $reportType, array $filters, User $user): StreamedResponse
    {
        $reportTypes = ReportQueryService::getReportTypes($user);
        $reportInfo = $reportTypes[$reportType] ?? ['label' => 'Report'];

        $dateSlug = date('Y-m-d');
        $fileName = 'dootor-' . Str::slug($reportInfo['label']) . '-' . $dateSlug . '.csv';

        // Query ALL matching records (unpaginated)
        $query = ReportQueryService::buildQuery($reportType, $filters, $user);

        // Audit report export
        AuditLogger::log('report_exported_csv', 'Report', null, "Exported {$reportType} CSV", null, [
            'report_type' => $reportType,
            'filters' => $filters,
            'format' => 'csv',
        ]);

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($query, $reportType) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility
            fwrite($file, "\xEF\xBB\xBF");

            $columns = self::getColumnHeaders($reportType);
            fputcsv($file, array_map([self::class, 'sanitizeCsvCell'], $columns));

            // Use chunking to be memory-conscious for large datasets
            $query->chunk(250, function ($records) use ($file, $reportType) {
                foreach ($records as $rec) {
                    $row = self::formatRecordRow($reportType, $rec);
                    fputcsv($file, array_map([self::class, 'sanitizeCsvCell'], $row));
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export matching report records to PDF.
     */
    public static function exportPdf(string $reportType, array $filters, User $user)
    {
        $reportTypes = ReportQueryService::getReportTypes($user);
        $reportInfo = $reportTypes[$reportType] ?? ['label' => 'Report'];

        $dateSlug = date('Y-m-d');
        $fileName = 'dootor-' . Str::slug($reportInfo['label']) . '-' . $dateSlug . '.pdf';

        // Query ALL matching records
        $query = ReportQueryService::buildQuery($reportType, $filters, $user);
        $records = $query->get();
        $totalRecords = $records->count();

        $settings = SystemSetting::first();
        $companyName = $settings->platform_name ?? config('app.name', 'DOOTOR ENTERPRISES');
        $logoUrl = $settings->logo_url ? app_file_url($settings->logo_url) : null;
        $generatedAt = date('d M Y, h:i A');

        // Audit report export
        AuditLogger::log('report_exported_pdf', 'Report', null, "Exported {$reportType} PDF", null, [
            'report_type' => $reportType,
            'filters' => $filters,
            'format' => 'pdf',
            'count' => $totalRecords,
        ]);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Helvetica');
        $options->set('chroot', realpath(base_path()));

        $dompdf = new Dompdf($options);

        $html = view('admin.reports.pdf', [
            'reportType' => $reportType,
            'reportInfo' => $reportInfo,
            'filters' => $filters,
            'records' => $records,
            'totalRecords' => $totalRecords,
            'companyName' => $companyName,
            'logoUrl' => $logoUrl,
            'generatedAt' => $generatedAt,
            'generatedBy' => $user->first_name . ' ' . $user->last_name,
            'columns' => self::getColumnHeaders($reportType),
        ])->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }

    /**
     * CSV Formula Injection Protection Helper.
     */
    public static function sanitizeCsvCell($val): string
    {
        if (is_null($val)) {
            return '';
        }
        $str = strip_tags((string) $val);
        if (preg_match('/^[=\+\-\@\t\r]/', $str)) {
            return "'" . $str;
        }
        return $str;
    }

    /**
     * Define Column Headers for each report type.
     */
    public static function getColumnHeaders(string $reportType): array
    {
        switch ($reportType) {
            case 'client_records':
                return ['Client ID', 'Full Name', 'Email', 'Phone', 'Country Applying From', 'Service Country', 'Status', 'Registration Date'];

            case 'staff_records':
                return ['Staff File No', 'Full Name', 'Email', 'Role', 'Status', 'Created Date'];

            case 'service_records':
                return ['Service Name', 'Category', 'Price ($)', 'Service Fee ($)', 'Processing Fee ($)', 'Processing Days', 'Total Applications', 'Status'];

            case 'document_records':
                return ['Document Name', 'Original Filename', 'Application Ref', 'Client Name', 'Status', 'Upload Date'];

            case 'staff_activity_records':
                return ['Audit ID', 'Staff File No', 'Staff Name', 'Action', 'Description', 'IP Address', 'Date & Time'];

            case 'assignment_records':
                return ['History ID', 'Application Ref', 'Client Name', 'Assigned By', 'Assigned To', 'Notes', 'Assignment Date'];

            case 'status_history_records':
                return ['History ID', 'Application Ref', 'Client Name', 'Stage Name', 'Status Key', 'Updated By', 'Update Date'];

            case 'notification_records':
                return ['Log ID', 'Recipient Email', 'Subject', 'Status', 'Sent Date'];

            default:
                return ['Application Ref', 'Client Name', 'Client Email', 'Service Name', 'Total Price ($)', 'Amount Paid ($)', 'Outstanding ($)', 'Payment Status', 'Application Status', 'Assigned Staff', 'Submission Date'];
        }
    }

    /**
     * Format an individual record row for CSV export.
     */
    public static function formatRecordRow(string $reportType, $rec): array
    {
        switch ($reportType) {
            case 'client_records':
                return [
                    'CL-' . $rec->id,
                    $rec->first_name . ' ' . $rec->last_name,
                    $rec->email,
                    $rec->phone ?? 'N/A',
                    $rec->country_applying_from ?? 'N/A',
                    $rec->country_service_requested ?? 'N/A',
                    $rec->status ?? 'Active',
                    $rec->created_at ? $rec->created_at->format('Y-m-d H:i') : 'N/A',
                ];

            case 'staff_records':
                return [
                    $rec->staff_file_number ?? ('STF-' . $rec->id),
                    $rec->first_name . ' ' . $rec->last_name,
                    $rec->email,
                    ucfirst(str_replace('_', ' ', $rec->role)),
                    $rec->status ?? 'Active',
                    $rec->created_at ? $rec->created_at->format('Y-m-d H:i') : 'N/A',
                ];

            case 'service_records':
                return [
                    $rec->name,
                    $rec->category ?? 'General',
                    number_format($rec->price, 2),
                    number_format($rec->service_fee, 2),
                    number_format($rec->processing_fee, 2),
                    $rec->processing_days ?? 'N/A',
                    $rec->requests_count ?? 0,
                    $rec->status ?? 'Active',
                ];

            case 'document_records':
                return [
                    $rec->document_name,
                    $rec->original_filename ?? 'N/A',
                    $rec->serviceRequest ? ($rec->serviceRequest->reference_number ?? ('DE-' . $rec->serviceRequest->id)) : 'N/A',
                    $rec->serviceRequest ? $rec->serviceRequest->client_name : 'N/A',
                    $rec->status ?? 'Uploaded',
                    $rec->created_at ? $rec->created_at->format('Y-m-d H:i') : 'N/A',
                ];

            case 'staff_activity_records':
                return [
                    'AUD-' . $rec->id,
                    $rec->user ? ($rec->user->staff_file_number ?? ('STF-' . $rec->user->id)) : 'N/A',
                    $rec->user ? ($rec->user->first_name . ' ' . $rec->user->last_name) : 'System/Guest',
                    $rec->action,
                    $rec->description ?? 'N/A',
                    $rec->ip_address ?? 'N/A',
                    $rec->created_at ? $rec->created_at->format('Y-m-d H:i') : 'N/A',
                ];

            case 'assignment_records':
                return [
                    'ASG-' . $rec->id,
                    $rec->serviceRequest ? ($rec->serviceRequest->reference_number ?? ('DE-' . $rec->serviceRequest->id)) : 'N/A',
                    $rec->serviceRequest ? $rec->serviceRequest->client_name : 'N/A',
                    $rec->assignedBy ? ($rec->assignedBy->first_name . ' ' . $rec->assignedBy->last_name) : 'System',
                    $rec->assignedTo ? ($rec->assignedTo->first_name . ' ' . $rec->assignedTo->last_name) : 'Unassigned',
                    $rec->notes ?? 'N/A',
                    $rec->created_at ? $rec->created_at->format('Y-m-d H:i') : 'N/A',
                ];

            case 'status_history_records':
                return [
                    'HST-' . $rec->id,
                    $rec->serviceRequest ? ($rec->serviceRequest->reference_number ?? ('DE-' . $rec->serviceRequest->id)) : 'N/A',
                    $rec->serviceRequest ? $rec->serviceRequest->client_name : 'N/A',
                    $rec->stage_name,
                    $rec->status_key,
                    $rec->updatedBy ? ($rec->updatedBy->first_name . ' ' . $rec->updatedBy->last_name) : 'System',
                    $rec->created_at ? $rec->created_at->format('Y-m-d H:i') : 'N/A',
                ];

            case 'notification_records':
                return [
                    'LOG-' . $rec->id,
                    $rec->recipient_email,
                    $rec->subject,
                    $rec->status,
                    $rec->sent_at ? Carbon::parse($rec->sent_at)->format('Y-m-d H:i') : ($rec->created_at ? $rec->created_at->format('Y-m-d H:i') : 'N/A'),
                ];

            default:
                return [
                    $rec->reference_number ?? ('DE-' . $rec->id),
                    $rec->client_name,
                    $rec->client_email,
                    $rec->service_name,
                    number_format($rec->price, 2),
                    number_format($rec->amount_paid, 2),
                    number_format($rec->outstanding_balance, 2),
                    $rec->payment_status ?? 'Unpaid',
                    $rec->status ?? 'Draft',
                    $rec->assignedStaff ? ($rec->assignedStaff->first_name . ' ' . $rec->assignedStaff->last_name) : 'Unassigned',
                    $rec->created_at ? $rec->created_at->format('Y-m-d H:i') : 'N/A',
                ];
        }
    }
}
