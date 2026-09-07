<?php

use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\ClientController;
use App\Http\Controllers\Web\PaymentController;
use App\Http\Controllers\Web\PublicController;
use App\Http\Controllers\Web\ReportController;
use App\Http\Controllers\Web\SupportController;
use App\Http\Controllers\Web\VendorController;
use App\Http\Controllers\Api\LocationApiController;
use Illuminate\Support\Facades\Route;

// Public Location API Routes
Route::get('/api/location/african-countries', [LocationApiController::class, 'africanCountries']);
Route::get('/api/location/divisions', [LocationApiController::class, 'divisions']);

// Public Welcome Homepage
Route::get('/', function () {
    return view('welcome');
});

// Public Contact Form Submission
Route::post('/contact', [PublicController::class, 'sendContact'])->name('contact.send');

// Public Vendor Storefront Link
Route::get('/v/{vendor}', [PublicController::class, 'vendorStorefront'])->name('vendor.storefront');

// Credo Payment Callback Route
Route::get('/payment/credo/callback', [PaymentController::class, 'callback'])->name('payment.credo.callback');

// Web Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'loginWeb']);

    Route::get('/register', [AuthController::class, 'showRegisterVendor'])->name('register');
    Route::post('/register', [AuthController::class, 'registerWeb']);

    Route::get('/register-client', [AuthController::class, 'showRegisterClient'])->name('register.client');
    Route::post('/register-client', [AuthController::class, 'registerClientWeb']);

    // Multi-Step Password Reset Routes (Alphanumeric Code Verification & New Password Entry)
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'forgotPasswordWeb'])->name('password.email');

    Route::get('/reset-password/verify', [AuthController::class, 'showVerifyCode'])->name('password.code');
    Route::post('/reset-password/verify', [AuthController::class, 'verifyResetCode'])->name('password.verify-code');

    Route::get('/reset-password/new', [AuthController::class, 'showNewPassword'])->name('password.new');
    Route::post('/reset-password/new', [AuthController::class, 'updatePasswordWeb'])->name('password.update-web');
});

Route::post('/logout', [AuthController::class, 'logoutWeb'])->name('logout')->middleware('auth');

// Protected Monolith Dashboard Sections
Route::middleware('auth')->group(function () {

    // Payment Checkout Route
    Route::post('/payment/credo/checkout/{serviceRequest}', [PaymentController::class, 'initiatePayment'])->name('payment.credo.checkout');

    // In-App Support Routes
    Route::get('/support', [SupportController::class, 'index'])->name('support.index');
    Route::post('/support', [SupportController::class, 'store'])->name('support.store');
    Route::get('/support/{ticket}', [SupportController::class, 'show'])->name('support.show');
    Route::post('/support/{ticket}/reply', [SupportController::class, 'reply'])->name('support.reply');

    // Admin Dashboard Routes
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        
        Route::get('/approvals', [AdminController::class, 'approvals'])->name('admin.approvals');
        Route::post('/approvals/{vendor}/approve', [AdminController::class, 'approveVendor'])->name('admin.approvals.approve');
        Route::post('/approvals/{vendor}/reject', [AdminController::class, 'rejectVendor'])->name('admin.approvals.reject');
        
        Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
        Route::patch('/users/{user}', [AdminController::class, 'updateUser'])->name('admin.user.update');
        Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.user.delete');
        
        Route::get('/subscriptions', [AdminController::class, 'subscriptions'])->name('admin.subscriptions');
        Route::patch('/subscriptions/{serviceRequest}', [AdminController::class, 'updateSubscriptionStatus'])->name('admin.subscription.update');
        Route::patch('/documents/{document}/status', [AdminController::class, 'updateDocumentStatus'])->name('admin.document.status');
        
        Route::get('/services', [AdminController::class, 'services'])->name('admin.services');
        Route::post('/services', [AdminController::class, 'storeService'])->name('admin.services.store');
        Route::patch('/services/{service}', [AdminController::class, 'updateService'])->name('admin.service.update');
        Route::delete('/services/{service}', [AdminController::class, 'deleteService'])->name('admin.service.delete');
        
        // Modular Dynamic Form Builder Routes
        Route::get('/services/{service}/form-builder', [AdminController::class, 'formBuilder'])->name('admin.service.form-builder');
        Route::post('/services/{service}/form-builder', [AdminController::class, 'saveFormFields'])->name('admin.service.form-builder.save');
        Route::delete('/form-fields/{field}', [AdminController::class, 'deleteFormField'])->name('admin.service.form-field.delete');

        // Service Workflow Stage Builder Routes
        Route::get('/services/{service}/workflow', [AdminController::class, 'workflowBuilder'])->name('admin.service.workflow');
        Route::post('/services/{service}/workflow', [AdminController::class, 'saveWorkflowStages'])->name('admin.service.workflow.save');
        Route::delete('/workflow-stages/{stage}', [AdminController::class, 'deleteWorkflowStage'])->name('admin.service.workflow.delete');

        // Modular Roles & Dynamic Granular RBAC Routes
        Route::get('/roles', [AdminController::class, 'rolesIndex'])->name('admin.roles');
        Route::post('/roles', [AdminController::class, 'rolesStore'])->name('admin.roles.store');
        Route::patch('/roles/{role}', [AdminController::class, 'rolesUpdate'])->name('admin.roles.update');
        Route::delete('/roles/{role}', [AdminController::class, 'rolesDelete'])->name('admin.roles.delete');
        Route::post('/users/{user}/permissions', [AdminController::class, 'assignUserPermissions'])->name('admin.user.permissions');

        // Staff Work Queue Route
        Route::get('/work-queue', [AdminController::class, 'workQueue'])->name('admin.work-queue');

        // Email Templates & Notification Logs Routes
        Route::get('/email-templates', [AdminController::class, 'emailTemplates'])->name('admin.email-templates');
        Route::patch('/email-templates/{template}', [AdminController::class, 'updateEmailTemplate'])->name('admin.email-templates.update');

        // System Audit Logs Route
        Route::get('/audit-logs', [AdminController::class, 'auditLogs'])->name('admin.audit-logs');
        
        Route::get('/service-documents', [AdminController::class, 'serviceDocuments'])->name('admin.service-documents');
        Route::patch('/service-documents/{service}', [AdminController::class, 'updateServiceDocuments'])->name('admin.service-documents.update');

        Route::get('/document-types', [AdminController::class, 'documentTypes'])->name('admin.document-types');
        Route::post('/document-types', [AdminController::class, 'storeDocumentType'])->name('admin.document-types.store');
        Route::patch('/document-types/{documentType}', [AdminController::class, 'updateDocumentType'])->name('admin.document-types.update');
        Route::post('/document-types/{documentType}/assign-services', [AdminController::class, 'assignDocumentToServices'])->name('admin.document-types.assign-services');
        Route::delete('/document-types/{documentType}', [AdminController::class, 'deleteDocumentType'])->name('admin.document-types.delete');
        
        Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports');
        Route::get('/reports/export-csv', [ReportController::class, 'exportCsv'])->name('admin.reports.export');

        Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
        Route::post('/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');

        // Credo Direct Payment Verification & Re-query Route
        Route::get('/payment-verification', [AdminController::class, 'paymentVerification'])->name('admin.payment-verification');
        Route::post('/payment-verification/query', [AdminController::class, 'queryCredoPayment'])->name('admin.payment-verification.query');

        // Admin Profile Management Routes
        Route::get('/profile', [AdminController::class, 'profile'])->name('admin.profile');
        Route::post('/profile', [AdminController::class, 'updateProfile'])->name('admin.profile.update');
        Route::post('/profile/password', [AdminController::class, 'updatePassword'])->name('admin.profile.password');
    });

    // Vendor Dashboard Routes
    Route::prefix('vendor')->middleware('role:vendor')->group(function () {
        Route::get('/kyc', [VendorController::class, 'kyc'])->name('vendor.kyc');
        Route::post('/kyc', [VendorController::class, 'storeKyc'])->name('vendor.kyc.store');
        
        Route::middleware('vendor.approved')->group(function () {
            Route::get('/dashboard', [VendorController::class, 'dashboard'])->name('vendor.dashboard');
            
            Route::get('/my-services', [VendorController::class, 'myServices'])->name('vendor.my-services');
            Route::get('/services', [VendorController::class, 'services'])->name('vendor.services');
            Route::post('/services', [VendorController::class, 'enableService'])->name('vendor.service.enable');
            Route::patch('/services/{vendorService}', [VendorController::class, 'updateService'])->name('vendor.service.update');
            Route::delete('/services/{vendorService}', [VendorController::class, 'deleteService'])->name('vendor.service.delete');
            
            Route::get('/clients', [VendorController::class, 'clients'])->name('vendor.clients');
            Route::post('/clients/{client}/archive', [VendorController::class, 'archiveClient'])->name('vendor.client.archive');
            
            Route::get('/requests', [VendorController::class, 'requests'])->name('vendor.requests');
            Route::patch('/requests/{serviceRequest}', [VendorController::class, 'updateRequestStatus'])->name('vendor.request.status');
            
            Route::get('/settings', [VendorController::class, 'settings'])->name('vendor.settings');
            Route::post('/settings/storefront', [VendorController::class, 'updateSettings'])->name('vendor.settings.storefront');
            Route::post('/settings/profile', [VendorController::class, 'updateProfile'])->name('vendor.settings.profile');
        });
    });

    // Client Dashboard Routes
    Route::prefix('client')->middleware('role:client')->group(function () {
        Route::get('/dashboard', [ClientController::class, 'dashboard'])->name('client.dashboard');
        Route::get('/services', [ClientController::class, 'services'])->name('client.services');
        
        Route::get('/book/{service}', [ClientController::class, 'book'])->name('client.book');
        Route::post('/book', [ClientController::class, 'storeBooking'])->name('client.book.store');

        // Method B: Manual Form Download and Manual Form Submission Routes
        Route::get('/services/{service}/download-form', [ClientController::class, 'downloadForm'])->name('client.service.download-form');
        Route::post('/services/{service}/submit-manual', [ClientController::class, 'submitManual'])->name('client.service.submit-manual');
        
        Route::get('/requests', [ClientController::class, 'requests'])->name('client.requests');
        Route::get('/requests/{serviceRequest}', [ClientController::class, 'requestDetails'])->name('client.request.details');
        Route::post('/requests/{serviceRequest}/payment', [ClientController::class, 'payRequest'])->name('client.request.payment');
        Route::delete('/requests/{serviceRequest}', [ClientController::class, 'deleteRequest'])->name('client.request.delete');
        
        Route::get('/settings', [ClientController::class, 'settings'])->name('client.settings');
        Route::post('/settings', [ClientController::class, 'updateSettings'])->name('client.settings.update');
    });
});
