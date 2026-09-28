<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\User;
use App\Services\ReportExportService;
use App\Services\ReportQueryService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display Reports & Records Dashboard for Admin / Staff.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $availableReportTypes = ReportQueryService::getReportTypes($user);

        $reportType = $request->query('report_type', 'all');
        if (!array_key_exists($reportType, $availableReportTypes)) {
            // Default to 'all' if invalid or unauthorized report type requested
            if (array_key_exists('all', $availableReportTypes)) {
                $reportType = 'all';
            } else {
                $reportType = array_key_first($availableReportTypes) ?? 'all';
            }
        }

        if ($reportType && !ReportQueryService::canAccessReportType($user, $reportType)) {
            abort(403, 'Unauthorized access to the requested report type.');
        }

        $filters = [
            'report_type' => $reportType,
            'date_preset' => $request->query('date_preset', 'all_time'),
            'date_from' => $request->query('date_from'),
            'date_to' => $request->query('date_to'),
            'status' => $request->query('status'),
            'service_id' => $request->query('service_id'),
            'staff_id' => $request->query('staff_id'),
            'payment_status' => $request->query('payment_status'),
            'country' => $request->query('country'),
            'search' => $request->query('search'),
            'per_page' => (int) $request->query('per_page', 25),
        ];

        // Ensure per_page is one of [25, 50, 100, 250]
        if (!in_array($filters['per_page'], [25, 50, 100, 250])) {
            $filters['per_page'] = 25;
        }

        // Build query and get paginated results
        $query = ReportQueryService::buildQuery($reportType, $filters, $user);
        $records = $query->paginate($filters['per_page'])->withQueryString();

        // Summary metrics
        $metrics = ReportQueryService::getSummaryMetrics($reportType, $filters, $user);

        // Supporting dropdown lists for filters
        $services = Service::orderBy('name')->get(['id', 'name']);
        $staffMembers = User::whereIn('role', ['admin', 'super_admin', 'manager', 'processing_officer', 'staff'])
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'staff_file_number', 'role']);

        $activeReportInfo = $availableReportTypes[$reportType] ?? ['label' => 'Records'];

        return view('admin.reports', compact(
            'availableReportTypes',
            'reportType',
            'activeReportInfo',
            'filters',
            'records',
            'metrics',
            'services',
            'staffMembers'
        ));
    }

    /**
     * Export Reports to CSV.
     */
    public function exportCsv(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasPermission('reports.export') && !$user->hasPermission('reports.view') && !$user->hasRole('super-admin') && !$user->hasRole('admin')) {
            abort(403, 'Unauthorized to export report data.');
        }

        $availableReportTypes = ReportQueryService::getReportTypes($user);
        $reportType = $request->query('report_type', 'all');

        if (!array_key_exists($reportType, $availableReportTypes) || !ReportQueryService::canAccessReportType($user, $reportType)) {
            abort(403, 'Unauthorized access to the requested report type.');
        }

        $filters = $request->only([
            'report_type', 'date_preset', 'date_from', 'date_to', 'status',
            'service_id', 'staff_id', 'payment_status', 'country', 'search'
        ]);

        return ReportExportService::exportCsv($reportType, $filters, $user);
    }

    /**
     * Export Reports to PDF.
     */
    public function exportPdf(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasPermission('reports.export') && !$user->hasPermission('reports.view') && !$user->hasRole('super-admin') && !$user->hasRole('admin')) {
            abort(403, 'Unauthorized to export report data.');
        }

        $availableReportTypes = ReportQueryService::getReportTypes($user);
        $reportType = $request->query('report_type', 'all');

        if (!array_key_exists($reportType, $availableReportTypes) || !ReportQueryService::canAccessReportType($user, $reportType)) {
            abort(403, 'Unauthorized access to the requested report type.');
        }

        $filters = $request->only([
            'report_type', 'date_preset', 'date_from', 'date_to', 'status',
            'service_id', 'staff_id', 'payment_status', 'country', 'search'
        ]);

        return ReportExportService::exportPdf($reportType, $filters, $user);
    }
}
