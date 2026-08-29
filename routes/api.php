<?php

use App\Http\Controllers\Api\Admin\ApprovalController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Api\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Client\BookController as ClientBookController;
use App\Http\Controllers\Api\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\Api\Client\RequestController as ClientRequestController;
use App\Http\Controllers\Api\Client\ServiceController as ClientServiceController;
use App\Http\Controllers\Api\PublicController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\Vendor\ClientController as VendorClientController;
use App\Http\Controllers\Api\Vendor\DashboardController as VendorDashboardController;
use App\Http\Controllers\Api\Vendor\KycController;
use App\Http\Controllers\Api\Vendor\RequestController as VendorRequestController;
use App\Http\Controllers\Api\Vendor\SettingsController as VendorSettingsController;
use App\Http\Controllers\Api\Vendor\VendorServiceController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::patch('/me', [AuthController::class, 'updateProfile']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::prefix('public')->group(function () {
    Route::get('/settings', [PublicController::class, 'settings']);
    Route::get('/vendors/{vendor}/storefront', [PublicController::class, 'vendorStorefront']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/uploads', [UploadController::class, 'store']);

    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', AdminDashboardController::class);
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::patch('/users/{user}', [AdminUserController::class, 'update']);
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy']);
        Route::get('/vendors', [AdminUserController::class, 'index'])->defaults('role', 'vendor');
        Route::get('/approvals', [ApprovalController::class, 'index']);
        Route::get('/approvals/{vendor}', [ApprovalController::class, 'show']);
        Route::post('/approvals/{vendor}/approve', [ApprovalController::class, 'approve']);
        Route::post('/approvals/{vendor}/reject', [ApprovalController::class, 'reject']);
        Route::apiResource('/services', AdminServiceController::class)->except(['show'])->names('api.admin.services');
        Route::get('/settings', [AdminSettingsController::class, 'show']);
        Route::patch('/settings', [AdminSettingsController::class, 'update']);
    });

    Route::prefix('vendor')->middleware('role:vendor')->group(function () {
        Route::get('/dashboard', VendorDashboardController::class);
        Route::get('/kyc', [KycController::class, 'show']);
        Route::post('/kyc', [KycController::class, 'store']);
        Route::get('/clients', [VendorClientController::class, 'index']);
        Route::post('/clients/{client}/archive', [VendorClientController::class, 'archive']);
        Route::get('/requests', [VendorRequestController::class, 'index']);
        Route::patch('/requests/{serviceRequest}', [VendorRequestController::class, 'update']);
        Route::get('/settings', [VendorSettingsController::class, 'show']);
        Route::patch('/settings/profile', [VendorSettingsController::class, 'updateProfile']);
        Route::patch('/settings/storefront', [VendorSettingsController::class, 'updateStorefront']);
        Route::get('/platform-services', [VendorServiceController::class, 'platformServices']);
        Route::apiResource('/services', VendorServiceController::class)
            ->except(['show'])
            ->parameters(['services' => 'vendorService'])
            ->names('api.vendor.services');
    });

    Route::prefix('client')->middleware('role:client')->group(function () {
        Route::get('/dashboard', ClientDashboardController::class);
        Route::get('/services', [ClientServiceController::class, 'index']);
        Route::get('/requests', [ClientRequestController::class, 'index']);
        Route::post('/requests', [ClientRequestController::class, 'store']);
        Route::get('/requests/{serviceRequest}', [ClientRequestController::class, 'show']);
        Route::delete('/requests/{serviceRequest}', [ClientRequestController::class, 'destroy']);
        Route::post('/requests/{serviceRequest}/payment', [ClientRequestController::class, 'updatePayment']);
        Route::get('/book/retry/{serviceRequest}', [ClientBookController::class, 'retry']);
        Route::get('/book/{vendor}/{vendorService}', [ClientBookController::class, 'show']);
        Route::patch('/settings', [AuthController::class, 'updateProfile']);
    });
});
