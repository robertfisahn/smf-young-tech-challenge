<?php

declare(strict_types=1);

use App\Features\Invoices\Api\InvoiceApiController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [InvoiceApiController::class, 'login']);

Route::middleware('simple.token')->group(function () {
    Route::get('/invoices', [InvoiceApiController::class, 'index']);
    Route::get('/invoices/{invoice}', [InvoiceApiController::class, 'show']);
    Route::post('/invoices', [InvoiceApiController::class, 'store']);
    Route::match(['put', 'patch'], '/invoices/{invoice}', [InvoiceApiController::class, 'update']);
    Route::delete('/invoices/{invoice}', [InvoiceApiController::class, 'destroy']);
    Route::post('/invoices/upload', [InvoiceApiController::class, 'upload']);
});
