<?php

namespace App\Services;

use App\Models\ApplicationStageHistory;
use App\Models\AssignmentHistory;
use App\Models\AuditLog;
use App\Models\EmailLog;
use App\Models\RequestDocument;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ReportQueryService
{
    /**
     * Get all available report types with permissions & descriptions.
     */
    public static function getReportTypes(?User $user = null): array
    {
        $allTypes = [
            'all' => [
                'code' => 'all',
                'label' => 'All Records (Master Applications Ledger)',
                'category' => 'General',
                'description' => 'Complete master report of all application requests, clients, services, and payments.',
                'permission' => 'reports.view',
            ],
            'applications' => [
                'code' => 'applications',
                'label' => 'All Applications',
                'category' => 'Applications',
                'description' => 'List of all system applications regardless of current status.',
                'permission' => 'reports.view',
            ],
            'draft_applications' => [
                'code' => 'draft_applications',
                'label' => 'Draft Applications',
                'category' => 'Applications',
                'description' => 'Incomplete or step-in-progress draft applications.',
                'permission' => 'reports.view',
            ],
            'submitted_applications' => [
                'code' => 'submitted_applications',
                'label' => 'Submitted Applications',
                'category' => 'Applications',
                'description' => 'Applications submitted by clients awaiting vetting.',
                'permission' => 'reports.view',
            ],
            'awaiting_assignment_applications' => [
                'code' => 'awaiting_assignment_applications',
                'label' => 'Applications Awaiting Assignment',
                'category' => 'Applications',
                'description' => 'Unassigned applications requiring staff allocation.',
                'permission' => 'reports.view',
            ],
            'assigned_applications' => [
                'code' => 'assigned_applications',
                'label' => 'Assigned Applications',
                'category' => 'Applications',
                'description' => 'Applications assigned to processing officers.',
                'permission' => 'reports.view',
            ],
            'under_review_applications' => [
                'code' => 'under_review_applications',
                'label' => 'Applications Under Review',
                'category' => 'Applications',
                'description' => 'Applications undergoing document or agency review.',
                'permission' => 'reports.view',
            ],
            'completed_applications' => [
                'code' => 'completed_applications',
                'label' => 'Completed Applications',
                'category' => 'Applications',
                'description' => 'Successfully processed and completed applications.',
                'permission' => 'reports.view',
            ],
            'rejected_applications' => [
                'code' => 'rejected_applications',
                'label' => 'Rejected Applications',
                'category' => 'Applications',
                'description' => 'Applications rejected due to invalid documents or criteria.',
                'permission' => 'reports.view',
            ],
            'cancelled_applications' => [
                'code' => 'cancelled_applications',
                'label' => 'Cancelled Applications',
                'category' => 'Applications',
                'description' => 'Applications cancelled by client or administrator.',
                'permission' => 'reports.view',
            ],
            'client_records' => [
                'code' => 'client_records',
                'label' => 'Client Records',
                'category' => 'Users & Accounts',
                'description' => 'Registered client profiles, contact information, and statistics.',
                'permission' => 'reports.view',
            ],
            'staff_records' => [
                'code' => 'staff_records',
                'label' => 'Staff & User Records',
                'category' => 'Users & Accounts',
                'description' => 'System staff, managers, officers, and admin accounts.',
                'permission' => 'reports.view',
            ],
            'service_records' => [
                'code' => 'service_records',
                'label' => 'Service Catalog Records',
                'category' => 'Services & Pricing',
                'description' => 'Service catalog listings, pricing, and application counts.',
                'permission' => 'reports.view',
            ],
            'payment_records' => [
                'code' => 'payment_records',
                'label' => 'Payment & Financial Records',
                'category' => 'Financials',
                'description' => 'Financial transactions, revenue collected, and balances.',
                'permission' => 'reports.view',
            ],
            'document_records' => [
                'code' => 'document_records',
                'label' => 'Document Upload Records',
                'category' => 'Documents & Files',
                'description' => 'Uploaded client supporting documents and verification status.',
                'permission' => 'reports.view',
            ],
            'staff_activity_records' => [
                'code' => 'staff_activity_records',
                'label' => 'Staff Activity & Audit Logs',
                'category' => 'Auditing & Compliance',
                'description' => 'Detailed system action logs, security audit, and user actions.',
                'permission' => 'reports.view',
            ],
            'assignment_records' => [
                'code' => 'assignment_records',
                'label' => 'Application Assignment History',
                'category' => 'Auditing & Compliance',
                'description' => 'History of staff task allocations and reassignments.',
                'permission' => 'reports.view',
            ],
            'status_history_records' => [
                'code' => 'status_history_records',
                'label' => 'Application Status History',
                'category' => 'Auditing & Compliance',
                'description' => 'Workflow stage transitions and status update logs.',
                'permission' => 'reports.view',
            ],
            'notification_records' => [
                'code' => 'notification_records',
                'label' => 'Notification & Email Logs',
                'category' => 'Communication',
                'description' => 'History of email broadcasts and automated notification dispatches.',
                'permission' => 'reports.view',
            ],
        ];

        if (!$user) {
            return $allTypes;
        }

        // Filter based on user RBAC permissions
        $authorizedTypes = [];
        foreach ($allTypes as $code => $info) {
            if (self::canAccessReportType($user, $code)) {
                $authorizedTypes[$code] = $info;
            }
        }

        return $authorizedTypes;
    }

    /**
     * Check if user is authorized for specific report type.
     */
    public static function canAccessReportType(User $user, string $type): bool
    {
        if ($user->isAdmin() || $user->role === 'admin' || $user->role === 'super_admin') {
            return true;
        }

        // Default base permission
        if (!$user->hasPermission('reports.view')) {
            return false;
        }

        switch ($type) {
            case 'staff_records':
            case 'staff_activity_records':
                return $user->hasPermission('reports.staff') || $user->hasPermission('users.view');

            case 'client_records':
                return $user->hasPermission('reports.clients') || $user->hasPermission('applications.view');

            case 'payment_records':
                return $user->hasPermission('reports.payments') || $user->hasPermission('services.pricing.update');

            case 'assignment_records':
            case 'status_history_records':
            case 'document_records':
                return $user->hasPermission('reports.applications') || $user->hasPermission('applications.view');

            default:
                return true;
        }
    }

    /**
     * Build the filtered Eloquent query based on report type & request filters.
     */
    public static function buildQuery(string $reportType, array $filters, User $user): Builder
    {
        // Resolve date range from preset if provided
        $dateFrom = $filters['date_from'] ?? null;
        $dateTo = $filters['date_to'] ?? null;

        if (!empty($filters['date_preset']) && $filters['date_preset'] !== 'custom' && $filters['date_preset'] !== 'all_time') {
            [$dateFrom, $dateTo] = self::resolveDatePreset($filters['date_preset']);
        }

        $search = isset($filters['search']) ? trim($filters['search']) : null;
        $status = $filters['status'] ?? null;
        $serviceId = $filters['service_id'] ?? null;
        $staffId = $filters['staff_id'] ?? null;
        $paymentStatus = $filters['payment_status'] ?? null;
        $country = $filters['country'] ?? null;

        switch ($reportType) {
            case 'client_records':
                $query = User::where('role', 'client')->latest();
                if ($dateFrom) $query->whereDate('created_at', '>=', $dateFrom);
                if ($dateTo) $query->whereDate('created_at', '<=', $dateTo);
                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
                }
                if ($country) {
                    $query->where(function ($q) use ($country) {
                        $q->where('country_applying_from', $country)
                            ->orWhere('country_service_requested', $country);
                    });
                }
                if ($status) {
                    $query->where('status', $status);
                }
                return $query;

            case 'staff_records':
                $query = User::whereIn('role', ['admin', 'super_admin', 'manager', 'processing_officer', 'staff', 'vendor'])->latest();
                if ($dateFrom) $query->whereDate('created_at', '>=', $dateFrom);
                if ($dateTo) $query->whereDate('created_at', '<=', $dateTo);
                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('staff_file_number', 'like', "%{$search}%");
                    });
                }
                if ($status) {
                    $query->where('status', $status);
                }
                return $query;

            case 'service_records':
                $query = Service::withCount('requests')->latest();
                if ($dateFrom) $query->whereDate('created_at', '>=', $dateFrom);
                if ($dateTo) $query->whereDate('created_at', '<=', $dateTo);
                if ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                }
                if ($status) {
                    $query->where('status', $status);
                }
                return $query;

            case 'document_records':
                $query = RequestDocument::with(['serviceRequest', 'serviceRequest.client'])->latest();
                if ($dateFrom) $query->whereDate('created_at', '>=', $dateFrom);
                if ($dateTo) $query->whereDate('created_at', '<=', $dateTo);
                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('document_name', 'like', "%{$search}%")
                            ->orWhere('original_filename', 'like', "%{$search}%")
                            ->orWhereHas('serviceRequest', function ($sq) use ($search) {
                                $sq->where('reference_number', 'like', "%{$search}%")
                                    ->orWhere('client_name', 'like', "%{$search}%");
                            });
                    });
                }
                if ($status) {
                    $query->where('status', $status);
                }
                return $query;

            case 'staff_activity_records':
                $query = AuditLog::with('user')->latest();
                if ($dateFrom) $query->whereDate('created_at', '>=', $dateFrom);
                if ($dateTo) $query->whereDate('created_at', '<=', $dateTo);
                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('action', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%")
                            ->orWhere('ip_address', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($uq) use ($search) {
                                $uq->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->orWhere('staff_file_number', 'like', "%{$search}%");
                            });
                    });
                }
                if ($staffId) {
                    $query->where('user_id', $staffId);
                }
                return $query;

            case 'assignment_records':
                $query = AssignmentHistory::with(['serviceRequest', 'assignedBy', 'assignedTo'])->latest();
                if ($dateFrom) $query->whereDate('created_at', '>=', $dateFrom);
                if ($dateTo) $query->whereDate('created_at', '<=', $dateTo);
                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('notes', 'like', "%{$search}%")
                            ->orWhereHas('serviceRequest', function ($sq) use ($search) {
                                $sq->where('reference_number', 'like', "%{$search}%")
                                    ->orWhere('client_name', 'like', "%{$search}%");
                            });
                    });
                }
                if ($staffId) {
                    $query->where('assigned_to_id', $staffId);
                }
                return $query;

            case 'status_history_records':
                $query = ApplicationStageHistory::with(['serviceRequest', 'updatedBy'])->latest();
                if ($dateFrom) $query->whereDate('created_at', '>=', $dateFrom);
                if ($dateTo) $query->whereDate('created_at', '<=', $dateTo);
                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('stage_name', 'like', "%{$search}%")
                            ->orWhere('status_key', 'like', "%{$search}%")
                            ->orWhere('notes', 'like', "%{$search}%")
                            ->orWhereHas('serviceRequest', function ($sq) use ($search) {
                                $sq->where('reference_number', 'like', "%{$search}%")
                                    ->orWhere('client_name', 'like', "%{$search}%");
                            });
                    });
                }
                if ($status) {
                    $query->where('status_key', $status);
                }
                return $query;

            case 'notification_records':
                $query = EmailLog::with('user')->latest();
                if ($dateFrom) $query->whereDate('sent_at', '>=', $dateFrom);
                if ($dateTo) $query->whereDate('sent_at', '<=', $dateTo);
                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('subject', 'like', "%{$search}%")
                            ->orWhere('recipient_email', 'like', "%{$search}%");
                    });
                }
                if ($status) {
                    $query->where('status', $status);
                }
                return $query;

            // Application & Payment-based Reports (Default/All, Applications, Specific Statuses)
            default:
                $query = ServiceRequest::with(['client', 'assignedStaff', 'service'])->latest();

                // Specific Application Sub-types
                switch ($reportType) {
                    case 'draft_applications':
                        $query->where(function ($q) {
                            $q->where('status', 'Draft')->orWhere('current_step', '<', 4);
                        });
                        break;
                    case 'submitted_applications':
                        $query->where('status', 'Submitted');
                        break;
                    case 'awaiting_assignment_applications':
                        $query->whereNull('assigned_staff_id')->whereNotIn('status', ['Draft', 'Cancelled']);
                        break;
                    case 'assigned_applications':
                        $query->whereNotNull('assigned_staff_id');
                        break;
                    case 'under_review_applications':
                        $query->whereIn('status', ['Documents Under Review', 'In Review', 'Verification']);
                        break;
                    case 'completed_applications':
                        $query->where('status', 'Completed');
                        break;
                    case 'rejected_applications':
                        $query->where('status', 'Rejected');
                        break;
                    case 'cancelled_applications':
                        $query->where('status', 'Cancelled');
                        break;
                    case 'payment_records':
                        $query->where('amount_paid', '>', 0);
                        break;
                }

                // Date Filtering
                if ($dateFrom) $query->whereDate('created_at', '>=', $dateFrom);
                if ($dateTo) $query->whereDate('created_at', '<=', $dateTo);

                // Search Filtering
                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('reference_number', 'like', "%{$search}%")
                            ->orWhere('client_name', 'like', "%{$search}%")
                            ->orWhere('client_email', 'like', "%{$search}%")
                            ->orWhere('service_name', 'like', "%{$search}%")
                            ->orWhere('transaction_reference', 'like', "%{$search}%");
                    });
                }

                // Status Filter
                if ($status) {
                    $query->where('status', $status);
                }

                // Service Filter
                if ($serviceId) {
                    $query->where('service_id', $serviceId);
                }

                // Staff Filter
                if ($staffId) {
                    $query->where('assigned_staff_id', $staffId);
                }

                // Payment Status Filter
                if ($paymentStatus) {
                    $query->where('payment_status', $paymentStatus);
                }

                // Country Filter
                if ($country) {
                    $query->where(function ($q) use ($country) {
                        $q->where('country_applying_from', $country)
                            ->orWhere('country_service_requested', $country);
                    });
                }

                return $query;
        }
    }

    /**
     * Resolve date preset helper.
     */
    public static function resolveDatePreset(string $preset): array
    {
        $today = Carbon::today();

        switch ($preset) {
            case 'today':
                return [$today->toDateString(), $today->toDateString()];
            case 'yesterday':
                $y = Carbon::yesterday();
                return [$y->toDateString(), $y->toDateString()];
            case 'last_7_days':
                return [Carbon::now()->subDays(6)->toDateString(), $today->toDateString()];
            case 'last_30_days':
                return [Carbon::now()->subDays(29)->toDateString(), $today->toDateString()];
            case 'this_month':
                return [Carbon::now()->startOfMonth()->toDateString(), Carbon::now()->endOfMonth()->toDateString()];
            case 'last_month':
                return [Carbon::now()->subMonth()->startOfMonth()->toDateString(), Carbon::now()->subMonth()->endOfMonth()->toDateString()];
            case 'this_year':
                return [Carbon::now()->startOfYear()->toDateString(), Carbon::now()->endOfYear()->toDateString()];
            default:
                return [null, null];
        }
    }

    /**
     * Compute summary metrics cards data based on report type & filters.
     */
    public static function getSummaryMetrics(string $reportType, array $filters, User $user): array
    {
        $query = self::buildQuery($reportType, $filters, $user);
        $totalRecords = (clone $query)->count();

        if (in_array($reportType, ['client_records', 'staff_records'])) {
            $activeCount = (clone $query)->where('status', 'Active')->count();
            $inactiveCount = (clone $query)->where('status', '!=', 'Active')->count();
            return [
                'total' => $totalRecords,
                'active' => $activeCount,
                'inactive' => $inactiveCount,
                'label1' => 'Total Users',
                'label2' => 'Active Accounts',
                'label3' => 'Inactive Accounts',
            ];
        }

        if ($reportType === 'service_records') {
            $activeCount = (clone $query)->where('status', 'Active')->count();
            return [
                'total' => $totalRecords,
                'active' => $activeCount,
                'label1' => 'Total Services',
                'label2' => 'Active Services',
            ];
        }

        if ($reportType === 'staff_activity_records' || $reportType === 'assignment_records' || $reportType === 'status_history_records' || $reportType === 'notification_records' || $reportType === 'document_records') {
            return [
                'total' => $totalRecords,
                'label1' => 'Total Activity Logs',
            ];
        }

        // Default Application & Payment metrics
        $totalRevenue = (clone $query)->sum('amount_paid');
        $totalOutstanding = (clone $query)->sum('outstanding_balance');
        $completed = (clone $query)->where('status', 'Completed')->count();
        $pending = (clone $query)->whereIn('status', ['Awaiting Payment', 'Payment Confirmed', 'Documents Under Review', 'Processing', 'In Review', 'Verification'])->count();

        return [
            'total' => $totalRecords,
            'revenue' => (float) $totalRevenue,
            'outstanding' => (float) $totalOutstanding,
            'completed' => $completed,
            'pending' => $pending,
            'label1' => 'Total Applications',
            'label2' => 'Revenue Collected',
            'label3' => 'Outstanding Balance',
            'label4' => 'Completed',
        ];
    }
}
