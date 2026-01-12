<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Master\CompanyController;
use App\Http\Controllers\Master\BrandController;
use App\Http\Controllers\Master\ProductController;
use App\Http\Controllers\Master\PromotionController;
use App\Http\Controllers\Master\RouterController;
use App\Http\Controllers\CRM\CustomerController;
use App\Http\Controllers\CRM\SubscriptionController;
use App\Http\Controllers\CRM\ProvisioningController;
use App\Http\Controllers\Billing\InvoiceController;
use App\Http\Controllers\Billing\PaymentController;
use App\Http\Controllers\Billing\ReminderController;
use App\Http\Controllers\Support\TicketController;
use App\Http\Controllers\AuditLogController;

Route::prefix('v1')->group(function () {
    // Auth
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/auth/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

    Route::middleware('auth:sanctum')->group(function () {
        // Master
        Route::apiResource('companies', CompanyController::class);
        Route::apiResource('brands', BrandController::class);
        Route::apiResource('products', ProductController::class);
        Route::apiResource('promotions', PromotionController::class);
        Route::apiResource('routers', RouterController::class);

        // CRM
        Route::apiResource('customers', CustomerController::class);
        Route::apiResource('subscriptions', SubscriptionController::class);
        Route::apiResource('provisionings', ProvisioningController::class);

        // Billing
        Route::apiResource('invoices', InvoiceController::class);
        Route::apiResource('payments', PaymentController::class);
        Route::apiResource('reminders', ReminderController::class);

        // Support
        Route::apiResource('tickets', TicketController::class);

        // Audit
        Route::get('audit-logs', [AuditLogController::class, 'index']);
    });
});