<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('invoices.index'));

Route::get('login', [\App\Features\Auth\LoginController::class, 'show'])->name('login');
Route::post('login', [\App\Features\Auth\LoginController::class, 'login']);
Route::post('logout', \App\Features\Auth\LogoutController::class)->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('invoices/ocr', \App\Features\Invoices\ProcessOcr\ShowOcrFormController::class)->name('invoices.ocr');
    Route::post('invoices/process-ocr', \App\Features\Invoices\ProcessOcr\ProcessOcrController::class)->name('invoices.process-ocr');
    Route::post('invoices/store-ocr', \App\Features\Invoices\StoreOcr\StoreOcrController::class)->name('invoices.store_ocr');

    Route::get('invoices/generate', \App\Features\Invoices\GenerateSample\ShowGenerateFormController::class)->name('invoices.generate_form');
    Route::post('invoices/preview', \App\Features\Invoices\GenerateSample\PreviewSampleController::class)->name('invoices.preview');

    Route::get('invoices/{invoice}/file', \App\Features\Invoices\DownloadFile\DownloadFileController::class)->name('invoices.file');
    
    Route::get('invoices', \App\Features\Invoices\ListInvoices\ListInvoicesController::class)->name('invoices.index');
    Route::get('invoices/create', \App\Features\Invoices\CreateInvoice\ShowCreateFormController::class)->name('invoices.create');
    Route::post('invoices', \App\Features\Invoices\CreateInvoice\StoreInvoiceController::class)->name('invoices.store');
    Route::get('invoices/{invoice}', \App\Features\Invoices\ShowInvoice\ShowInvoiceController::class)->name('invoices.show');
    Route::get('invoices/{invoice}/edit', \App\Features\Invoices\UpdateInvoice\ShowEditFormController::class)->name('invoices.edit');
    Route::match(['put', 'patch'], 'invoices/{invoice}', \App\Features\Invoices\UpdateInvoice\UpdateInvoiceController::class)->name('invoices.update');
    Route::patch('invoices/{invoice}/mark-as-paid', \App\Features\Invoices\MarkAsPaid\MarkAsPaidController::class)->name('invoices.mark-as-paid');
    Route::delete('invoices/{invoice}', \App\Features\Invoices\DeleteInvoice\DeleteInvoiceController::class)->name('invoices.destroy');

});
