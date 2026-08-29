<?php

use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\ClientController;
use App\Http\Controllers\Web\PublicController;
use App\Http\Controllers\Web\VendorController;
use Illuminate\Support\Facades\Route;

// Public Welcome Homepage
Route::get('/', function () {
    return view('welcome');
});

// Public Contact Form Submission
Route::post('/contact', [PublicController::class, 'sendContact'])->name('contact.send');


// Public Vendor Storefront Link
Route::get('/v/{vendor}', [PublicController::class, 'vendorStorefront'])->name('vendor.storefront');

// Web Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'loginWeb']);

    Route::get('/register', [AuthController::class, 'showRegisterVendor'])->name('register');
    Route::post('/register', [AuthController::class, 'registerWeb']);

    Route::get('/register-client', [AuthController::class, 'showRegisterClient'])->name('register.client');
    Route::post('/register-client', [AuthController::class, 'registerClientWeb']);

    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'forgotPasswordWeb'])->name('password.email');
});

Route::post('/logout', [AuthController::class, 'logoutWeb'])->name('logout')->middleware('auth');

// Protected Monolith Dashboard Sections
Route::middleware('auth')->group(function () {

    // Admin Dashboard Routes
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        
        Route::get('/approvals', [AdminController::class, 'approvals'])->name('admin.approvals');
        Route::post('/approvals/{vendor}/approve', [AdminController::class, 'approveVendor'])->name('admin.approvals.approve');
        Route::post('/approvals/{vendor}/reject', [AdminController::class, 'rejectVendor'])->name('admin.approvals.reject');
        
        Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
        Route::patch('/users/{user}', [AdminController::class, 'updateUser'])->name('admin.user.update');
        Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.user.delete');
        
        Route::get('/services', [AdminController::class, 'services'])->name('admin.services');
        Route::post('/services', [AdminController::class, 'storeService'])->name('admin.services.store');
        Route::patch('/services/{service}', [AdminController::class, 'updateService'])->name('admin.service.update');
        Route::delete('/services/{service}', [AdminController::class, 'deleteService'])->name('admin.service.delete');
        
        Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
        Route::post('/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
    });

    // Vendor Dashboard Routes
    Route::prefix('vendor')->middleware('role:vendor')->group(function () {
        // KYC view is accessible even if status is Pending/Rejected
        Route::get('/kyc', [VendorController::class, 'kyc'])->name('vendor.kyc');
        Route::post('/kyc', [VendorController::class, 'storeKyc'])->name('vendor.kyc.store');
        
        // Dashboard and operations are protected by Approved state check
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
        
        Route::get('/book/{vendorService}', [ClientController::class, 'book'])->name('client.book');
        Route::post('/book', [ClientController::class, 'storeBooking'])->name('client.book.store');
        
        Route::get('/requests', [ClientController::class, 'requests'])->name('client.requests');
        Route::get('/requests/{serviceRequest}', [ClientController::class, 'requestDetails'])->name('client.request.details');
        Route::post('/requests/{serviceRequest}/payment', [ClientController::class, 'payRequest'])->name('client.request.payment');
        Route::delete('/requests/{serviceRequest}', [ClientController::class, 'deleteRequest'])->name('client.request.delete');
        
        Route::get('/settings', [ClientController::class, 'settings'])->name('client.settings');
        Route::post('/settings', [ClientController::class, 'updateSettings'])->name('client.settings.update');
    });
});
