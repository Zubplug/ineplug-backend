<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletTransferController;
use App\Http\Controllers\ServiceFormController;
use App\Http\Controllers\PartnerJobController;
use App\Http\Controllers\API\AdminRequestController as APIAdminRequestController;
use App\Http\Controllers\AdminServiceController;
use App\Http\Controllers\Admin\ServiceManagerController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\Admin\AdminRequestController;
use App\Http\Controllers\AdminFormController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\PartnerAuthController;
use App\Http\Controllers\AggregatorAuthController;
use App\Http\Controllers\VtpassController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\VirtualAccountController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\KycLimitController;
use App\Http\Controllers\Admin\KycComplianceController;
use App\Http\Controllers\VtuServiceController;
use App\Http\Controllers\VtuAdminSyncController;

// ✅ Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/webhook/monnify', [WebhookController::class, 'handle']);

// ✅ Public Forms
Route::get('/forms/{slug}', [ServiceFormController::class, 'loadForm']);
Route::post('/submit-request', [ServiceFormController::class, 'submitForm']);

// ✅ VTU Services
Route::post('/vtu/airtime', [VtpassController::class, 'airtime']);
Route::post('/vtu/data', [VtpassController::class, 'data']);
Route::post('/vtu/electricity', [VtpassController::class, 'electricity']);
Route::post('/vtu/electricity/verify', [VtpassController::class, 'verifyMeter']);
Route::post('/vtu/cabletv', [VtpassController::class, 'tvSubscription']);
Route::post('/vtu/cabletv/verify', [VtpassController::class, 'verifySmartCard']);
Route::post('/vtu/education', [VtpassController::class, 'education']);
Route::post('/vtu/insurance', [VtpassController::class, 'insurance']);
Route::post('/vtu/requery', [VtpassController::class, 'requery']);
Route::get('/vtu/variations/{serviceID}', [VtpassController::class, 'serviceVariations']);

// ✅ VTU Admin Sync
Route::post('/admin/vtu/sync', [VtuAdminSyncController::class, 'sync']);

// ✅ General Authenticated Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/ineplug-transfer', [WalletTransferController::class, 'transfer']);
    Route::get('/user/transactions', [TransactionController::class, 'userTransactions']);
    Route::post('/generate-account', [VirtualAccountController::class, 'generate']);
    Route::get('/user', fn(Request $request) => $request->user());
});

// ✅ Partner Routes
Route::prefix('partner')->group(function () {
    Route::post('/register', [PartnerAuthController::class, 'register']);
    Route::post('/login', [PartnerAuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [PartnerAuthController::class, 'logout']);
        Route::get('/jobs/available', [PartnerJobController::class, 'availableJobs']);
        Route::post('/jobs/request', [PartnerJobController::class, 'requestAssignment']);
        Route::get('/transactions', [TransactionController::class, 'partnerTransactions']);
    });
});

// ✅ Aggregator Routes
Route::prefix('aggregator')->group(function () {
    Route::post('/register', [AggregatorAuthController::class, 'register']);
    Route::post('/login', [AggregatorAuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AggregatorAuthController::class, 'logout']);
        Route::get('/transactions', [TransactionController::class, 'aggregatorTransactions']);
    });
});

// ✅ Agent Routes
Route::middleware('auth:sanctum')->prefix('agent')->group(function () {
    Route::get('/transactions', [TransactionController::class, 'agentTransactions']);
});

// ✅ Admin Routes
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    // Admin Services
    Route::get('/services', [ServiceManagerController::class, 'index']);
    Route::post('/services/create', [AdminServiceController::class, 'store']);
    Route::put('/services/update/{type}/{id}', [ServiceManagerController::class, 'update']);
    Route::delete('/services/delete/{type}/{id}', [ServiceManagerController::class, 'destroy']);
    Route::put('/services/toggle/{type}/{id}', [ServiceManagerController::class, 'toggleStatus']);
    Route::get('/services/all', [AdminServiceController::class, 'all']);
    Route::get('/services/{id}/sub-sub-categories', [AdminServiceController::class, 'getSubSubCategories']);
    Route::get('/forms/{slug}', [AdminServiceController::class, 'getForm']);
    Route::post('/forms/save', [AdminServiceController::class, 'saveForm']);

    // Admin Transactions
    Route::get('/transactions', [TransactionController::class, 'adminTransactions']);
    Route::get('/transactions/{reference}', [TransactionController::class, 'show']);
    Route::post('/transactions/requery', [TransactionController::class, 'requery']);
    Route::post('/transactions/refund', [TransactionController::class, 'manualRefund']);

    // KYC & Compliance
    Route::get('/kyc-limits', [KycLimitController::class, 'index']);
    Route::post('/kyc-limits', [KycLimitController::class, 'update']);
    Route::get('/kyc-compliance', [KycComplianceController::class, 'index']);
    Route::post('/address-verifications/{id}/reject', [KycComplianceController::class, 'rejectAddress']);
    Route::post('/address-verifications/{id}/approve', [KycComplianceController::class, 'approveAddress']);

    // Admin Requests
    Route::get('/requests', [AdminRequestController::class, 'all']);
    Route::get('/requests/pending-verification', [APIAdminRequestController::class, 'pending']);
    Route::post('/requests/verify', [APIAdminRequestController::class, 'verify']);
});

// ✅ Debug (Token Tester)
Route::middleware('auth:sanctum')->get('/debug-token', function (Request $request) {
    return response()->json([
        'status' => 'authenticated',
        'user_id' => $request->user()->id,
        'email' => $request->user()->email,
        'token_guard' => auth()->guard()->getName()
    ]);
});
