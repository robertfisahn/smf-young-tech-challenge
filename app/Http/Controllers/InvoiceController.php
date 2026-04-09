<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Http\Requests\ProcessOcrRequest;
use App\Models\Invoice;
use App\Services\InvoiceService;
use App\Services\FileUploadService;
use App\Services\OcrService;
use App\Services\AiParserFactory;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(
        protected InvoiceService $invoiceService,
        protected FileUploadService $fileUploadService,
        protected OcrService $ocrService,
        protected AiParserFactory $aiParserFactory,
    ) {}

    public function index()
    {
        return view('invoices.index', [
            'invoices' => $this->invoiceService->getAll(),
        ]);
    }

    public function create()
    {
        return view('invoices.create', [
            'contractors' => $this->invoiceService->getAllContractors(),
        ]);
    }

    public function store(StoreInvoiceRequest $request)
    {
        $this->invoiceService->create($request->validated());

        return redirect()->route('invoices.index')->with('success', 'Faktura dodana pomyślnie.');
    }

    public function show(Invoice $invoice)
    {
        return view('invoices.show', [
            'invoice' => $this->invoiceService->getWithDetails($invoice),
        ]);
    }

    public function showFile(Invoice $invoice)
    {
        if (!$invoice->file_path) {
            abort(404, 'Do tej faktury nie przypisano żadnego pliku.');
        }

        $path = storage_path('app/' . $invoice->file_path);

        if (!file_exists($path)) {
            abort(404, 'Plik nie istnieje na dysku.');
        }

        return response()->file($path);
    }

    public function edit(Invoice $invoice)
    {
        return view('invoices.edit', [
            'invoice' => $invoice,
            'contractors' => $this->invoiceService->getAllContractors(),
        ]);
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        $this->invoiceService->update($invoice, $request->validated());

        return redirect()->route('invoices.show', $invoice)->with('success', 'Faktura zaktualizowana pomyślnie.');
    }

    public function destroy(Invoice $invoice)
    {
        $this->invoiceService->delete($invoice);

        return redirect()->route('invoices.index')->with('success', 'Faktura usunięta pomyślnie.');
    }

    public function ocr()
    {
        return view('invoices.ocr');
    }

    public function processOcr(ProcessOcrRequest $request)
    {
        if ($request->hasFile('file')) {
            $fullPath = $this->fileUploadService->storeInvoiceFile($request->file('file'));
        } else {
            $fullPath = $request->input('existing_file');
        }

        \App\Jobs\ProcessOcrJob::dispatch($fullPath, auth()->id());

        $text = $this->ocrService->extractText($fullPath);
        $aiData = $this->aiParserFactory->make($request->input('ai_provider'))->parse($text);

        if ($request->boolean('auto_save') && !isset($aiData['error'])) {
            $permanentPath = $this->fileUploadService->moveToPermanent($fullPath);
            try {
                $invoice = $this->invoiceService->createFromAiData($aiData, $permanentPath);
                return redirect()->route('invoices.show', $invoice)->with('success', 'Faktura przetworzona i zapisana automatycznie.');
            } catch (\Exception $e) {
            }
        }

        return view('invoices.ocr_result', [
            'text' => $text,
            'ai_data' => $aiData,
            'file_path' => $fullPath,
            'ai_provider' => $request->input('ai_provider'),
        ]);
    }

    public function showGenerateForm()
    {
        return view('invoices.generate');
    }

    public function preview(Request $request)
    {
        $request->validate(['json_data' => 'required|json']);

        return view('invoices.template', json_decode($request->json_data, true));
    }

    public function storeFromOcr(Request $request)
    {
        $aiData = json_decode($request->input('ai_data'), true);
        $filePath = $request->input('file_path');

        if (!$aiData) {
            return back()->with('error', 'Nieprawidłowe dane AI.');
        }

        $permanentRelativePath = $this->fileUploadService->moveToPermanent($filePath);

        try {
            $invoice = $this->invoiceService->createFromAiData($aiData, $permanentRelativePath);
            return redirect()->route('invoices.show', $invoice)->with('success', 'Faktura została zapisana pomyślnie na podstawie analizy AI.');
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            return redirect()->route('invoices.ocr')->with('error', 'BŁĄD: Faktura o tym numerze (' . ($aiData['invoice_number'] ?? '---') . ') już istnieje w bazie danych!');
        } catch (\Exception $e) {
            return redirect()->route('invoices.ocr')->with('error', 'Wystąpił nieoczekiwany błąd podczas zapisu: ' . $e->getMessage());
        }
    }
}
