<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use App\Models\RequestDocument;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function dashboard()
    {
        $total_users = User::where('role', 'client')->count();
        $active_services = Service::where('status', 'Active')->count();
        $total_requests = ServiceRequest::count();
        $pending_requests = ServiceRequest::whereIn('status', ['Awaiting Payment', 'Documents Under Review', 'Processing', 'Awaiting External Agency'])->count();
        $recent_requests = ServiceRequest::with('client')->latest()->take(5)->get();

        return view('admin.dashboard', [
            'total_users' => $total_users,
            'active_services' => $active_services,
            'total_requests' => $total_requests,
            'pending_requests' => $pending_requests,
            'recent_requests' => $recent_requests,
        ]);
    }

    public function approvals()
    {
        $pendingVendors = User::where('role', 'vendor')
            ->where('status', 'Pending')
            ->with('kycProfile')
            ->latest()
            ->get();

        return view('admin.approvals', [
            'pending_vendors' => $pendingVendors,
        ]);
    }

    public function approveVendor(Request $request, User $vendor)
    {
        $vendor->update(['status' => 'Approved']);
        if ($vendor->kycProfile) {
            $vendor->kycProfile->update(['status' => 'Approved']);
        }

        return redirect()->back()->with('success', 'Vendor has been approved successfully.');
    }

    public function rejectVendor(Request $request, User $vendor)
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $vendor->update(['status' => 'Rejected']);
        if ($vendor->kycProfile) {
            $vendor->kycProfile->update([
                'status' => 'Rejected',
                'rejection_reason' => $request->rejection_reason,
            ]);
        }

        return redirect()->back()->with('success', 'Vendor application rejected.');
    }

    public function users(Request $request)
    {
        $query = User::query()->with(['kycProfile', 'storefrontSetting', 'roles'])->latest();

        if ($role = $request->query('role')) {
            $query->where(function ($q) use ($role) {
                $q->where('role', $role)
                  ->orWhereHas('roles', function ($rQ) use ($role) {
                      $rQ->where('slug', $role)->orWhere('name', $role)->orWhere('roles.id', $role);
                  });
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $users = $query->paginate(20)->withQueryString();
        $roles = \App\Models\Role::where('is_active', true)->orderBy('name', 'asc')->get();

        return view('admin.users', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    public function updateUser(Request $request, User $user)
    {
        $allRoleSlugs = \App\Models\Role::pluck('slug')->toArray();
        $allRoleIds = \App\Models\Role::pluck('id')->toArray();
        $allowedRoles = array_unique(array_merge(['admin', 'vendor', 'client', 'super_admin', 'manager', 'processing_officer', 'finance_officer'], $allRoleSlugs, array_map('strval', $allRoleIds)));

        $data = $request->validate([
            'status' => ['sometimes', Rule::in(['Approved', 'Pending', 'Rejected', 'Disabled'])],
            'role' => ['sometimes', Rule::in($allowedRoles)],
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', Rule::in(\App\Services\AfricanLocationService::countryNames())],
            'state' => ['nullable', 'string', 'max:255'],
        ]);

        if (!empty($data['country']) && !empty($data['state'])) {
            if (!\App\Services\AfricanLocationService::isValidPair($data['country'], $data['state'])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'state' => ["The selected state/region does not belong to {$data['country']}."],
                ]);
            }
        }

        if (isset($data['role'])) {
            $selectedVal = $data['role'];
            $roleModel = null;
            if (is_numeric($selectedVal)) {
                $roleModel = \App\Models\Role::find((int) $selectedVal);
            } else {
                $roleModel = \App\Models\Role::where('slug', $selectedVal)->orWhere('name', $selectedVal)->first();
            }

            if ($roleModel) {
                $user->roles()->sync([$roleModel->id]);
                if (in_array($roleModel->slug, ['client', 'vendor'])) {
                    $data['role'] = $roleModel->slug;
                } else {
                    $data['role'] = 'admin';
                }
            } else {
                if (in_array($selectedVal, ['client', 'vendor'])) {
                    $user->roles()->detach();
                }
            }
        }

        $user->update($data);

        return redirect()->back()->with('success', 'User profile and role updated successfully.');
    }

    public function deleteUser(User $user)
    {
        $user->delete();
        return redirect()->back()->with('success', 'User deleted successfully.');
    }

    public function services()
    {
        $services = Service::latest()->get();
        return view('admin.services', [
            'services' => $services,
        ]);
    }

    public function storeService(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'service_fee' => ['nullable', 'numeric', 'min:0'],
            'processing_fee' => ['nullable', 'numeric', 'min:0'],
            'processing_days' => ['nullable', 'integer', 'min:1'],
            'description' => ['required', 'string'],
            'status' => ['sometimes', Rule::in(['Active', 'Inactive'])],
            'required_documents' => ['nullable', 'array'],
            'custom_fields' => ['nullable', 'array'],
        ]);

        Service::create($data);

        return redirect()->back()->with('success', 'Service created successfully.');
    }

    public function updateService(Request $request, Service $service)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'service_fee' => ['nullable', 'numeric', 'min:0'],
            'processing_fee' => ['nullable', 'numeric', 'min:0'],
            'processing_days' => ['nullable', 'integer', 'min:1'],
            'description' => ['required', 'string'],
            'status' => ['sometimes', Rule::in(['Active', 'Inactive'])],
            'required_documents' => ['nullable', 'array'],
            'custom_fields' => ['nullable', 'array'],
        ]);

        $service->update($data);

        return redirect()->back()->with('success', 'Service updated successfully.');
    }

    public function deleteService(Service $service)
    {
        $service->delete();
        return redirect()->back()->with('success', 'Service deleted successfully.');
    }

    public function settings()
    {
        $settings = SystemSetting::firstOrCreate([]);
        return view('admin.settings', [
            'settings' => $settings,
        ]);
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'platform_name' => ['nullable', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'logo_url' => ['nullable', 'string', 'max:2048'],
            'logo_file' => ['nullable', 'image', 'mimes:png,jpg,jpeg,gif,svg,webp', 'max:5120'],
            'brand_template' => ['nullable', Rule::in(['classic', 'editorial', 'compact', 'showcase'])],
            'brand_theme' => ['nullable', Rule::in(['light', 'dark'])],
            'brand_color_palette' => ['nullable', Rule::in(['default', 'blue', 'green', 'purple', 'red', 'orange', 'yellow', 'teal'])],
            'brand_primary_color' => ['nullable', 'string', 'max:20'],
            'brand_secondary_color' => ['nullable', 'string', 'max:20'],
            'brand_gradient_from' => ['nullable', 'string', 'max:20'],
            'brand_gradient_to' => ['nullable', 'string', 'max:20'],
            'hero_badge_text' => ['nullable', 'string', 'max:255'],
            'hero_title' => ['nullable', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string'],
            'hero_bg_file' => ['nullable', 'image', 'mimes:png,jpg,jpeg,gif,webp', 'max:10240'],
            'hero_glass_style' => ['nullable', Rule::in(['light_glass', 'dark_glass', 'emerald_glass', 'subtle_glass'])],
            'remove_hero_bg' => ['nullable', 'boolean'],
            'default_currency' => ['nullable', 'string', 'max:10'],
            'maintenance_mode' => ['nullable', 'boolean'],
            'payment_gateway' => ['nullable', Rule::in(['paystack', 'credo'])],
            'payments_enabled' => ['nullable', 'boolean'],
            'payment_mode' => ['nullable', Rule::in(['test', 'live'])],
            'paystack_public_key' => ['nullable', 'string'],
            'paystack_secret_key' => ['nullable', 'string'],
            'paystack_base_url' => ['nullable', 'string'],
            'credo_public_key' => ['nullable', 'string'],
            'credo_secret_key' => ['nullable', 'string'],
            'credo_base_url' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('logo_file')) {
            $path = \App\Helpers\FileUploadHelper::store($request->file('logo_file'), 'branding');
            $data['logo_url'] = '/storage/' . $path;
        }

        if ($request->hasFile('hero_bg_file')) {
            $path = \App\Helpers\FileUploadHelper::store($request->file('hero_bg_file'), 'branding');
            $data['hero_bg_image'] = '/storage/' . $path;
        } elseif ($request->boolean('remove_hero_bg')) {
            $data['hero_bg_image'] = null;
        }

        unset($data['logo_file'], $data['hero_bg_file'], $data['remove_hero_bg']);

        // Auto-run pending migrations if hero columns are missing on server
        if (!\Illuminate\Support\Facades\Schema::hasColumn('system_settings', 'hero_badge_text')) {
            try {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Auto-migration failed: ' . $e->getMessage());
            }
        }

        // Filter data to only include columns that exist in the system_settings table
        if (\Illuminate\Support\Facades\Schema::hasTable('system_settings')) {
            $tableColumns = \Illuminate\Support\Facades\Schema::getColumnListing('system_settings');
            $data = array_intersect_key($data, array_flip($tableColumns));
        }

        $settings = SystemSetting::firstOrCreate([]);

        $oldCurrency = strtoupper((string) ($settings->default_currency ?: config('services.payment.currency') ?: 'NGN'));
        $newCurrency = strtoupper((string) ($data['default_currency'] ?? $oldCurrency));

        if (!empty($newCurrency) && $oldCurrency !== $newCurrency) {
            \App\Helpers\CurrencyConverter::convertPrices($oldCurrency, $newCurrency);
        }

        $settings->update($data);

        return redirect()->back()->with('success', 'System settings updated successfully. Service prices updated to match new billing currency!');
    }

    public function subscriptions(Request $request)
    {
        $search = $request->query('search');
        $perPage = (int) $request->query('per_page', 1);
        if (!in_array($perPage, [1, 2, 5, 10, 20, 50])) {
            $perPage = 1;
        }

        $query = ServiceRequest::query()
            ->with(['client', 'service', 'requestDocuments', 'assignedStaff', 'currentStage', 'stageHistories'])
            ->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('payment_reference', 'like', "%{$search}%")
                  ->orWhere('service_name', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($qc) use ($search) {
                      $qc->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $serviceRequests = $query->paginate($perPage)->withQueryString();
        $staffMembers = User::whereIn('role', ['admin', 'manager', 'processing_officer', 'super_admin'])->get();

        return view('admin.subscriptions', [
            'serviceRequests' => $serviceRequests,
            'search' => $search,
            'perPage' => $perPage,
            'staffMembers' => $staffMembers,
        ]);
    }

    public function updateSubscriptionStatus(Request $request, ServiceRequest $serviceRequest)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in([
                'Awaiting Payment', 'Payment Confirmed', 'Documents Under Review', 
                'Verification in Progress', 'Processing', 'Awaiting External Agency', 
                'Action Required', 'Ready for Collection', 'Completed', 'Cancelled', 'Rejected'
            ])],
            'assigned_staff_id' => ['nullable', 'exists:users,id'],
            'assigned_role_id' => ['nullable', 'exists:roles,id'],
            'stage_id' => ['nullable', 'exists:service_workflow_stages,id'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
            'is_user_visible' => ['nullable', 'boolean'],
        ]);

        $oldStatus = $serviceRequest->status;
        $oldStaffId = $serviceRequest->assigned_staff_id;

        $updateData = [
            'status' => $data['status'],
            'assigned_staff_id' => $data['assigned_staff_id'] ?? $serviceRequest->assigned_staff_id,
            'assigned_role_id' => $data['assigned_role_id'] ?? $serviceRequest->assigned_role_id,
        ];

        if (!empty($data['stage_id'])) {
            $stage = \App\Models\ServiceWorkflowStage::find($data['stage_id']);
            if ($stage) {
                $updateData['current_stage_id'] = $stage->id;
                $updateData['current_stage_name'] = $stage->stage_name;
            }
        }

        $serviceRequest->update($updateData);

        // Record Stage History
        $isPublic = $request->has('is_user_visible');
        \App\Models\ApplicationStageHistory::create([
            'service_request_id' => $serviceRequest->id,
            'stage_id' => $serviceRequest->current_stage_id,
            'stage_name' => $serviceRequest->current_stage_name ?? $serviceRequest->status,
            'status' => $serviceRequest->status,
            'changed_by_user_id' => auth()->id(),
            'notes' => $data['admin_notes'] ?? null,
            'is_user_visible' => $isPublic,
        ]);

        // Record Internal or Public Note
        if (!empty($data['admin_notes'])) {
            \App\Models\ApplicationNote::create([
                'service_request_id' => $serviceRequest->id,
                'user_id' => auth()->id(),
                'note' => $data['admin_notes'],
                'is_internal' => !$isPublic,
            ]);
        }

        // Record Audit Log
        \App\Services\AuditLogger::log(
            'application_status_updated',
            'ServiceRequest',
            (string) $serviceRequest->id,
            $serviceRequest->reference_number,
            ['status' => $oldStatus, 'assigned_staff_id' => $oldStaffId],
            ['status' => $serviceRequest->status, 'assigned_staff_id' => $serviceRequest->assigned_staff_id]
        );

        // Send Email Notification to Applicant
        \App\Services\EmailNotificationService::send(
            'stage_updated_user',
            $serviceRequest->client_email,
            [
                'full_name' => $serviceRequest->client_name,
                'service_name' => $serviceRequest->service_name,
                'reference_number' => $serviceRequest->reference_number,
                'current_stage' => $serviceRequest->current_stage_name ?? $serviceRequest->status,
                'status' => $serviceRequest->status,
                'notes' => $isPublic && !empty($data['admin_notes']) ? "<p><strong>Notes from Officer:</strong> " . e($data['admin_notes']) . "</p>" : '',
            ],
            $serviceRequest,
            $serviceRequest->client
        );

        return redirect()->back()->with('success', 'Application stage, status, and notes updated successfully.');
    }

    public function updateDocumentStatus(Request $request, \App\Models\RequestDocument $document)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['Pending', 'Received', 'Accepted', 'Requires Correction', 'Rejected'])],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $oldStatus = $document->status;
        $document->update([
            'status' => $data['status'],
            'admin_notes' => $data['admin_notes'] ?? $document->admin_notes,
        ]);

        $serviceRequest = $document->serviceRequest;
        if ($serviceRequest && $data['status'] === 'Accepted') {
            // Auto update request status to Documents Under Review if still in initial state
            if ($serviceRequest->status === 'Application Submitted') {
                $serviceRequest->update(['status' => 'Documents Under Review']);
            }
        }

        \App\Services\AuditLogger::log(
            'document_status_updated',
            'RequestDocument',
            (string) $document->id,
            $document->document_name,
            ['status' => $oldStatus],
            ['status' => $document->status]
        );

        return redirect()->back()->with('success', 'Document status for "' . $document->document_name . '" updated to ' . $data['status'] . '.');
    }

    // --- Dynamic Service Form Builder ---
    public function formBuilder(Service $service)
    {
        $fields = $service->fields()->orderBy('sort_order', 'asc')->get();
        return view('admin.form-builder', compact('service', 'fields'));
    }

    public function saveFormFields(Request $request, Service $service)
    {
        $data = $request->validate([
            'fields' => ['nullable', 'array'],
            'fields.*.id' => ['nullable', 'integer'],
            'fields.*.field_label' => ['required', 'string', 'max:255'],
            'fields.*.field_type' => ['required', 'string'],
            'fields.*.placeholder' => ['nullable', 'string', 'max:255'],
            'fields.*.help_text' => ['nullable', 'string', 'max:1000'],
            'fields.*.is_required' => ['nullable', 'boolean'],
            'fields.*.options' => ['nullable', 'string'], // Comma-separated or lines
            'fields.*.allowed_file_types' => ['nullable', 'string', 'max:255'],
            'fields.*.max_file_size' => ['nullable', 'integer', 'min:100'],
            'fields.*.sort_order' => ['nullable', 'integer'],
            'fields.*.is_enabled' => ['nullable', 'boolean'],
        ]);

        $existingIds = [];
        if (!empty($data['fields'])) {
            foreach ($data['fields'] as $index => $fData) {
                $optionsArray = null;
                if (!empty($fData['options'])) {
                    $optionsArray = array_values(array_filter(array_map('trim', explode("\n", str_replace(',', "\n", $fData['options'])))));
                }

                $fieldSlug = \Illuminate\Support\Str::slug($fData['field_label'], '_');

                $fieldRecord = \App\Models\ServiceField::updateOrCreate(
                    [
                        'id' => $fData['id'] ?? null,
                        'service_id' => $service->id,
                    ],
                    [
                        'field_label' => $fData['field_label'],
                        'field_name' => $fieldSlug,
                        'field_type' => $fData['field_type'],
                        'placeholder' => $fData['placeholder'] ?? null,
                        'help_text' => $fData['help_text'] ?? null,
                        'is_required' => isset($fData['is_required']),
                        'options' => $optionsArray,
                        'allowed_file_types' => $fData['allowed_file_types'] ?? null,
                        'max_file_size' => $fData['max_file_size'] ?? 10240,
                        'sort_order' => $fData['sort_order'] ?? ($index + 1),
                        'is_enabled' => isset($fData['is_enabled']),
                    ]
                );
                $existingIds[] = $fieldRecord->id;
            }
        }

        \App\Services\AuditLogger::log('service_form_updated', 'Service', (string) $service->id, null, null, ['field_count' => count($existingIds)]);

        return redirect()->back()->with('success', 'Form fields configured successfully for ' . $service->name);
    }

    public function deleteFormField(\App\Models\ServiceField $field)
    {
        $serviceId = $field->service_id;
        $field->delete();
        \App\Services\AuditLogger::log('service_field_deleted', 'ServiceField', (string) $field->id);
        return redirect()->back()->with('success', 'Form field deleted.');
    }

    // --- Service Workflow Builder ---
    public function workflowBuilder(Service $service)
    {
        $stages = $service->workflowStages()->orderBy('sort_order', 'asc')->get();
        $roles = \App\Models\Role::where('is_active', true)->get();
        $staffUsers = User::whereIn('role', ['admin', 'vendor'])->get();

        return view('admin.workflow-builder', compact('service', 'stages', 'roles', 'staffUsers'));
    }

    public function saveWorkflowStages(Request $request, Service $service)
    {
        $data = $request->validate([
            'stages' => ['nullable', 'array'],
            'stages.*.id' => ['nullable', 'integer'],
            'stages.*.stage_name' => ['required', 'string', 'max:255'],
            'stages.*.description' => ['nullable', 'string'],
            'stages.*.status_key' => ['required', 'string'],
            'stages.*.assigned_role_id' => ['nullable', 'exists:roles,id'],
            'stages.*.assigned_user_id' => ['nullable', 'exists:users,id'],
            'stages.*.estimated_days' => ['nullable', 'integer', 'min:1'],
            'stages.*.sort_order' => ['nullable', 'integer'],
            'stages.*.is_user_visible' => ['nullable', 'boolean'],
            'stages.*.notification_enabled' => ['nullable', 'boolean'],
        ]);

        if (!empty($data['stages'])) {
            foreach ($data['stages'] as $index => $sData) {
                \App\Models\ServiceWorkflowStage::updateOrCreate(
                    [
                        'id' => $sData['id'] ?? null,
                        'service_id' => $service->id,
                    ],
                    [
                        'stage_name' => $sData['stage_name'],
                        'description' => $sData['description'] ?? null,
                        'status_key' => $sData['status_key'],
                        'assigned_role_id' => $sData['assigned_role_id'] ?? null,
                        'assigned_user_id' => $sData['assigned_user_id'] ?? null,
                        'estimated_days' => $sData['estimated_days'] ?? null,
                        'sort_order' => $sData['sort_order'] ?? ($index + 1),
                        'is_user_visible' => isset($sData['is_user_visible']),
                        'notification_enabled' => isset($sData['notification_enabled']),
                    ]
                );
            }
        }

        \App\Services\AuditLogger::log('service_workflow_updated', 'Service', (string) $service->id);

        return redirect()->back()->with('success', 'Workflow stages updated for ' . $service->name);
    }

    public function deleteWorkflowStage(\App\Models\ServiceWorkflowStage $stage)
    {
        $stage->delete();
        \App\Services\AuditLogger::log('workflow_stage_deleted', 'ServiceWorkflowStage', (string) $stage->id);
        return redirect()->back()->with('success', 'Workflow stage deleted.');
    }

    // --- Roles & Dynamic Granular RBAC ---
    public function rolesIndex()
    {
        $roles = \App\Models\Role::with('permissions', 'users')->get();
        $permissions = \App\Models\Permission::all()->groupBy('module');
        $allUsers = User::orderBy('first_name')->get();

        return view('admin.roles.index', compact('roles', 'permissions', 'allUsers'));
    }

    public function rolesStore(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $slug = \Illuminate\Support\Str::slug($data['name']);
        $role = \App\Models\Role::create([
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'is_active' => true,
        ]);

        if (!empty($data['permissions'])) {
            $role->permissions()->sync($data['permissions']);
        }

        \App\Services\AuditLogger::log('role_created', 'Role', (string) $role->id, null, null, ['name' => $role->name]);

        return redirect()->back()->with('success', 'Role ' . $role->name . ' created successfully.');
    }

    public function rolesUpdate(Request $request, \App\Models\Role $role)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $oldVal = $role->toArray();
        $role->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        $role->permissions()->sync($data['permissions'] ?? []);

        \App\Services\AuditLogger::log('role_updated', 'Role', (string) $role->id, null, $oldVal, $role->toArray());

        return redirect()->back()->with('success', 'Role ' . $role->name . ' updated successfully.');
    }

    public function rolesDelete(\App\Models\Role $role)
    {
        if ($role->slug === 'super-admin') {
            return redirect()->back()->with('error', 'Super Admin role cannot be deleted.');
        }

        \App\Services\AuditLogger::log('role_deleted', 'Role', (string) $role->id, null, ['name' => $role->name], null);
        $role->delete();

        return redirect()->back()->with('success', 'Role deleted successfully.');
    }

    public function assignUserPermissions(Request $request, User $user)
    {
        $data = $request->validate([
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'],
            'direct_permissions' => ['nullable', 'array'],
            'direct_permissions.*' => ['exists:permissions,id'],
        ]);

        $user->roles()->sync($data['roles'] ?? []);
        $user->directPermissions()->sync($data['direct_permissions'] ?? []);

        \App\Services\AuditLogger::log('user_permissions_updated', 'User', (string) $user->id, null, null, [
            'roles' => $data['roles'] ?? [],
            'direct_permissions' => $data['direct_permissions'] ?? [],
        ]);

        return redirect()->back()->with('success', 'User roles and direct permissions updated for ' . $user->name);
    }

    // --- Staff Work Queue ---
    public function workQueue(Request $request)
    {
        $user = auth()->user();
        $roleIds = $user->roles->pluck('id')->toArray();

        $query = ServiceRequest::with(['client', 'service', 'currentStage', 'assignedStaff', 'assignedRole'])
            ->where(function ($q) use ($user, $roleIds) {
                $q->where('assigned_staff_id', $user->id);
                if (!empty($roleIds)) {
                    $q->orWhereIn('assigned_role_id', $roleIds);
                }
            })
            ->latest();

        $myTasks = $query->paginate(15);
        $pendingDocVerifications = RequestDocument::where('status', 'Pending')->count();

        return view('admin.work-queue', compact('myTasks', 'pendingDocVerifications'));
    }

    // --- Email Template Management ---
    public function emailTemplates()
    {
        $templates = \App\Models\EmailTemplate::all();
        return view('admin.email-templates', compact('templates'));
    }

    public function updateEmailTemplate(Request $request, \App\Models\EmailTemplate $template)
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'body_html' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $oldVal = $template->toArray();
        $template->update([
            'subject' => $data['subject'],
            'body_html' => $data['body_html'],
            'is_active' => $request->has('is_active'),
        ]);

        \App\Services\AuditLogger::log('email_template_updated', 'EmailTemplate', (string) $template->id, null, $oldVal, $template->toArray());

        return redirect()->back()->with('success', 'Email template (' . $template->title . ') updated successfully.');
    }

    // --- Audit Logs View ---
    public function auditLogs(Request $request)
    {
        $query = \App\Models\AuditLog::with('user')->latest();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(25)->withQueryString();

        return view('admin.audit-logs', compact('logs'));
    }

    // --- Service Document Checklists ---
    public function serviceDocuments(Request $request)
    {
        $services = Service::latest()->get();

        $selectedService = null;
        if ($serviceId = $request->query('service_id')) {
            $selectedService = Service::find($serviceId);
        }
        if (!$selectedService && $services->isNotEmpty()) {
            $selectedService = $services->first();
        }

        $documentTypes = DocumentType::latest()->get()->groupBy(function ($type) {
            return $type->category ?: 'General';
        });

        return view('admin.service-documents', compact('services', 'selectedService', 'documentTypes'));
    }

    public function updateServiceDocuments(Request $request, Service $service)
    {
        $data = $request->validate([
            'required_documents' => ['nullable', 'array'],
            'required_documents.*' => ['string'],
        ]);

        $requiredDocs = $data['required_documents'] ?? [];

        $service->update([
            'required_documents' => $requiredDocs,
        ]);

        return redirect()->back()->with('success', 'Required documents checklist updated for ' . $service->name . '.');
    }

    // --- Master Document Requirement Types ---
    public function documentTypes(Request $request)
    {
        $documentTypes = DocumentType::latest()->get();
        $allServices = Service::latest()->get();

        return view('admin.document-types', compact('documentTypes', 'allServices'));
    }

    public function storeDocumentType(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:document_types,name'],
            'category' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $data['is_active'] = true;

        $docType = DocumentType::create($data);

        \App\Services\AuditLogger::log('document_type_created', 'DocumentType', (string) $docType->id, null, null, $docType->toArray());

        return redirect()->back()->with('success', 'Master document type "' . $docType->name . '" created successfully.');
    }

    public function updateDocumentType(Request $request, DocumentType $documentType)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('document_types', 'name')->ignore($documentType->id)],
            'category' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $oldVal = $documentType->toArray();
        $documentType->update([
            'name' => $data['name'],
            'category' => $data['category'],
            'description' => $data['description'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        \App\Services\AuditLogger::log('document_type_updated', 'DocumentType', (string) $documentType->id, null, $oldVal, $documentType->toArray());

        return redirect()->back()->with('success', 'Master document type "' . $documentType->name . '" updated successfully.');
    }

    public function assignDocumentToServices(Request $request, DocumentType $documentType)
    {
        $serviceIds = $request->input('service_ids', []);
        $allServices = Service::all();

        foreach ($allServices as $service) {
            $raw = $service->required_documents;
            if (is_string($raw)) {
                $raw = json_decode($raw, true) ?: [];
            }
            $currentDocs = is_array($raw) ? array_map('trim', $raw) : [];

            $shouldHave = in_array((string) $service->id, array_map('strval', $serviceIds));

            if ($shouldHave) {
                if (!in_array($documentType->name, $currentDocs)) {
                    $currentDocs[] = $documentType->name;
                }
            } else {
                $currentDocs = array_values(array_filter($currentDocs, function ($docName) use ($documentType) {
                    return strtolower(trim($docName)) !== strtolower(trim($documentType->name));
                }));
            }

            $service->update(['required_documents' => $currentDocs]);
        }

        return redirect()->back()->with('success', 'Service assignments updated for document type "' . $documentType->name . '".');
    }

    public function deleteDocumentType(DocumentType $documentType)
    {
        $oldVal = $documentType->toArray();
        $docName = $documentType->name;
        $documentType->delete();

        \App\Services\AuditLogger::log('document_type_deleted', 'DocumentType', (string) $documentType->id, null, $oldVal, null);

        return redirect()->back()->with('success', 'Master document type "' . $docName . '" deleted successfully.');
    }

    /**
     * Credo Direct Payment Verification & Re-query List View.
     */
    public function paymentVerification(Request $request)
    {
        $query = ServiceRequest::with(['service', 'client'])->latest();

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('payment_reference', 'like', "%{$search}%")
                  ->orWhere('client_name', 'like', "%{$search}%")
                  ->orWhere('client_email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status_filter')) {
            $statusFilter = $request->input('status_filter');
            if ($statusFilter === 'unpaid') {
                $query->where(function ($q) {
                    $q->where('payment_status', 'Unpaid')
                      ->orWhere('payment_status', 'Pending');
                });
            } elseif ($statusFilter === 'paid') {
                $query->where('payment_status', 'Paid');
            }
        }

        $requests = $query->paginate(20)->withQueryString();
        $search = $request->input('search', '');
        $statusFilter = $request->input('status_filter', '');

        return view('admin.payment-verification', compact('requests', 'search', 'statusFilter'));
    }

    /**
     * Re-query Credo API directly for a specific transaction reference.
     */
    public function queryCredoPayment(Request $request)
    {
        $data = $request->validate([
            'reference' => ['required', 'string', 'max:255'],
        ]);

        $reference = trim($data['reference']);

        $serviceRequest = ServiceRequest::where('reference_number', $reference)
            ->orWhere('payment_reference', $reference)
            ->orWhere('id', $reference)
            ->first();

        if (!$serviceRequest) {
            return redirect()->back()->with('error', "No application found matching reference '{$reference}'.");
        }

        $refToQuery = $serviceRequest->payment_reference ?: $serviceRequest->reference_number;

        $paystackService = new \App\Services\PaystackService();
        $credoService = new \App\Services\CredoService();

        // 1. Query Paystack API
        $paystackRes = $paystackService->verifyTransaction($refToQuery);
        $paystackOk = !empty($paystackRes['is_successful']);

        // 2. Query Credo API
        $credoRes = $credoService->verifyTransaction($refToQuery);
        $credoOk = !empty($credoRes['is_successful']);

        // Fallback check: If payment_reference was different from reference_number, test reference_number as well
        if (!$paystackOk && !$credoOk && $serviceRequest->payment_reference && $serviceRequest->payment_reference !== $serviceRequest->reference_number) {
            $altRef = $serviceRequest->reference_number;
            $paystackResAlt = $paystackService->verifyTransaction($altRef);
            if (!empty($paystackResAlt['is_successful'])) {
                $paystackRes = $paystackResAlt;
                $paystackOk = true;
            } else {
                $credoResAlt = $credoService->verifyTransaction($altRef);
                if (!empty($credoResAlt['is_successful'])) {
                    $credoRes = $credoResAlt;
                    $credoOk = true;
                }
            }
        }

        // Handle SUCCESSFUL verification on either gateway
        if ($paystackOk || $credoOk) {
            $usedGateway = $paystackOk ? 'Paystack' : 'Credo';

            $serviceRequest->update([
                'payment_status' => 'Paid',
                'amount_paid' => $serviceRequest->price,
                'outstanding_balance' => 0,
                'status' => 'Payment Confirmed',
                'payment_gateway' => $usedGateway,
            ]);

            $serviceRequest->syncStatusToWorkflowStage('Payment Confirmed', "Payment verified directly via {$usedGateway} Gateway API by Admin.");

            \App\Services\AuditLogger::log(
                'payment_verified',
                'ServiceRequest',
                (string) $serviceRequest->id,
                $serviceRequest->reference_number,
                ['old_status' => $serviceRequest->getOriginal('status'), 'old_payment_status' => $serviceRequest->getOriginal('payment_status')],
                ['payment_status' => 'Paid', 'status' => 'Payment Confirmed', 'payment_gateway' => $usedGateway]
            );

            return redirect()->back()->with('success', "{$usedGateway} Verification SUCCESSFUL! Payment confirmed for application {$serviceRequest->reference_number}. Database record updated to Paid & Payment Confirmed.");
        }

        // Handle UNSUCCESSFUL verification on both gateways
        $paystackMsg = $paystackRes['message'] ?? 'Transaction reference not found on Paystack.';
        $credoMsg = $credoRes['message'] ?? 'Transaction reference not found on Credo.';

        $wasPaidInDb = ($serviceRequest->payment_status === 'Paid' || $serviceRequest->status === 'Payment Confirmed');

        if ($wasPaidInDb) {
            // Update database record to Unpaid / Payment Pending because gateway verification failed
            $serviceRequest->update([
                'payment_status' => 'Unpaid',
                'amount_paid' => 0,
                'outstanding_balance' => $serviceRequest->price,
                'status' => 'Payment Pending',
            ]);

            $serviceRequest->syncStatusToWorkflowStage('Payment Pending', 'Payment status reverted to Unpaid after gateway API re-query failed to confirm payment.');

            \App\Services\AuditLogger::log(
                'payment_unconfirmed_reverted',
                'ServiceRequest',
                (string) $serviceRequest->id,
                $serviceRequest->reference_number,
                ['old_status' => 'Payment Confirmed', 'old_payment_status' => 'Paid'],
                ['payment_status' => 'Unpaid', 'status' => 'Payment Pending']
            );

            return redirect()->back()->with('error', "Gateway API Verification FAILED! Neither Paystack nor Credo confirmed payment for '{$refToQuery}'. Database record has been UPDATED from Paid to Unpaid / Payment Pending. Details: [Paystack: {$paystackMsg}] | [Credo: {$credoMsg}]");
        }

        return redirect()->back()->with('error', "Gateway API Verification Failed: Payment for '{$refToQuery}' is NOT confirmed on Paystack or Credo. Details: [Paystack: {$paystackMsg}] | [Credo: {$credoMsg}]");
    }

    /**
     * Admin Profile View.
     */
    public function profile()
    {
        $user = auth()->user();
        return view('admin.profile', compact('user'));
    }

    /**
     * Update Admin Profile.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
        ]);

        if ($request->hasFile('avatar')) {
            $path = \App\Helpers\FileUploadHelper::store($request->file('avatar'), 'uploads');
            $data['avatar_url'] = '/storage/' . $path;
        }

        $data['name'] = trim($data['first_name'] . ' ' . $data['last_name']);

        $user->update($data);

        \App\Services\AuditLogger::log('admin_profile_updated', 'User', (string) $user->id, $user->name, null, $data);

        return redirect()->back()->with('success', 'Admin profile updated successfully.');
    }

    /**
     * Update Admin Password.
     */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!\Hash::check($data['current_password'], $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'The current password entered is incorrect.']);
        }

        $user->update([
            'password' => \Hash::make($data['new_password']),
        ]);

        \App\Services\AuditLogger::log('admin_password_updated', 'User', (string) $user->id, $user->name, null, null);

        return redirect()->back()->with('success', 'Password updated successfully!');
    }

    /**
     * Display Email Broadcast Composer page.
     */
    public function emailBroadcast()
    {
        $users = User::orderBy('first_name')->get(['id', 'first_name', 'last_name', 'email', 'role']);
        return view('admin.email-broadcast', compact('users'));
    }

    /**
     * Process & Send Broadcast Email.
     */
    public function sendEmailBroadcast(Request $request)
    {
        $request->validate([
            'notice_category' => ['required', Rule::in(['maintenance', 'announcement', 'service_update', 'custom'])],
            'recipient_target' => ['required', Rule::in(['all_clients', 'all_vendors', 'all_users', 'specific_user', 'external_emails'])],
            'specific_user_id' => ['required_if:recipient_target,specific_user', 'nullable', 'exists:users,id'],
            'external_emails' => ['required_if:recipient_target,external_emails', 'nullable', 'string'],
            'email_subject' => ['required', 'string', 'max:255'],
            'email_headline' => ['nullable', 'string', 'max:255'],
            'email_body' => ['required', 'string'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'button_url' => ['nullable', 'url', 'max:255'],
        ]);

        $recipients = [];

        switch ($request->recipient_target) {
            case 'all_clients':
                $recipients = User::where('role', 'client')->get();
                break;

            case 'all_vendors':
                $recipients = User::where('role', 'vendor')->get();
                break;

            case 'all_users':
                $recipients = User::whereIn('role', ['client', 'vendor', 'admin', 'manager', 'processing_officer', 'super_admin'])->get();
                break;

            case 'specific_user':
                $user = User::find($request->specific_user_id);
                if ($user) {
                    $recipients = [$user];
                }
                break;

            case 'external_emails':
                $rawEmails = array_map('trim', explode(',', $request->external_emails));
                foreach ($rawEmails as $email) {
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $recipients[] = (object) [
                            'email' => $email,
                            'name' => explode('@', $email)[0],
                            'first_name' => explode('@', $email)[0],
                            'id' => null
                        ];
                    }
                }
                break;
        }

        if (empty($recipients)) {
            return redirect()->back()->with('error', 'No valid recipient email addresses found for the selected target group.')->withInput();
        }

        $settings = SystemSetting::first();
        $companyName = $settings->platform_name ?? 'DOOTOR ENTERPRISES';
        $logoUrl = $settings->logo_url ? app_file_url($settings->logo_url) : null;

        $sentCount = 0;
        $failedCount = 0;

        foreach ($recipients as $recipient) {
            $userObj = is_a($recipient, User::class) ? $recipient : null;
            $recipientEmail = is_a($recipient, User::class) ? $recipient->email : $recipient->email;
            $recipientName = is_a($recipient, User::class) ? ($recipient->first_name . ' ' . $recipient->last_name) : $recipient->name;

            // Render email body template
            $renderedHtml = view('emails.broadcast', [
                'category' => $request->notice_category,
                'subject' => $request->email_subject,
                'headline' => $request->email_headline ?? $request->email_subject,
                'bodyContent' => $request->email_body,
                'buttonText' => $request->button_text,
                'buttonUrl' => $request->button_url,
                'recipientName' => $recipientName,
                'companyName' => $companyName,
                'logoUrl' => $logoUrl,
            ])->render();

            $success = \App\Services\EmailNotificationService::sendBroadcast(
                $recipientEmail,
                $request->email_subject,
                $renderedHtml,
                $userObj
            );

            if ($success) {
                $sentCount++;
            } else {
                $failedCount++;
            }
        }

        \App\Services\AuditLogger::log('broadcast_email_sent', 'EmailLog', null, "Category: {$request->notice_category}", null, [
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
            'subject' => $request->email_subject,
            'target' => $request->recipient_target,
        ]);

        $msg = "Broadcast notice dispatched successfully to {$sentCount} recipient(s).";
        if ($failedCount > 0) {
            $msg .= " ({$failedCount} failed to deliver)";
        }

        return redirect()->back()->with('success', $msg);
    }
}

