<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Display Reports Dashboard for Admin.
     */
    public function index(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $status = $request->query('status');

        $query = ServiceRequest::query();

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        if ($status) {
            $query->where('status', $status);
        }

        $requests = $query->latest()->get();

        $totalRevenue = $query->sum('amount_paid');
        $totalOutstanding = $query->sum('outstanding_balance');
        $totalApplications = $requests->count();
        $completedApplications = $requests->where('status', 'Completed')->count();
        $pendingApplications = $requests->whereIn('status', ['Awaiting Payment', 'Documents Under Review', 'Processing', 'Awaiting External Agency'])->count();

        $servicesBreakdown = ServiceRequest::selectRaw('service_name, count(*) as total, sum(amount_paid) as revenue')
            ->groupBy('service_name')
            ->get();

        return view('admin.reports', compact(
            'requests', 'totalRevenue', 'totalOutstanding', 'totalApplications',
            'completedApplications', 'pendingApplications', 'servicesBreakdown'
        ));
    }

    /**
     * Export Reports to CSV.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $fileName = 'dooter_enterprise_report_' . date('Y-m-d') . '.csv';
        $requests = ServiceRequest::latest()->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function () use ($requests) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Reference Number', 'Client Name', 'Client Email', 'Service Name', 'Total Price', 'Amount Paid', 'Outstanding Balance', 'Payment Status', 'Application Status', 'Date']);

            foreach ($requests as $req) {
                fputcsv($file, [
                    $req->reference_number ?? ('DE-' . $req->id),
                    $req->client_name,
                    $req->client_email,
                    $req->service_name,
                    $req->price,
                    $req->amount_paid,
                    $req->outstanding_balance,
                    $req->payment_status,
                    $req->status,
                    $req->created_at->format('Y-m-d H:i'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
