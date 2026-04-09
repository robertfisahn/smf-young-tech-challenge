<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\AuthController;

Route::get('/', fn() => redirect()->route('invoices.index'));

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('invoices/ocr', [InvoiceController::class, 'ocr'])->name('invoices.ocr');
    Route::post('invoices/process-ocr', [InvoiceController::class, 'processOcr'])->name('invoices.process-ocr');
    Route::post('invoices/store-ocr', [InvoiceController::class, 'storeFromOcr'])->name('invoices.store_ocr');

    Route::get('/invoices/generate', [InvoiceController::class, 'showGenerateForm'])->name('invoices.generate_form');
    Route::post('/invoices/preview', [InvoiceController::class, 'preview'])->name('invoices.preview');

    Route::get('/invoices/{invoice}/file', [InvoiceController::class, 'showFile'])->name('invoices.file');
    Route::resource('invoices', InvoiceController::class);

});
