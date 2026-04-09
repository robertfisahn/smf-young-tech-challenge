<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Http\Requests\ProcessOcrRequest;
use App\Models\Invoice;
use App\Services\InvoiceService;
use App\Services\FileUploadService;
use App\Services\OcrService;
use App\Services\AiParserFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceApiController extends Controller
{
    public function __construct(
        protected InvoiceService $invoiceService,
        protected FileUploadService $fileUploadService,
        protected OcrService $ocrService,
        protected AiParserFactory $aiParserFactory,
    ) {}

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (auth()->attempt($credentials)) {
            $user = auth()->user();
            return response()->json([
                'access_token' => 'user-id-' . $user->id,
                'token_type' => 'Bearer',
                'message' => 'To jest uproszczony token Bearer do celów rekrutacyjnych.'
            ]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => $this->invoiceService->getAll(),
        ]);
    }

    public function show(Invoice $invoice): JsonResponse
    {
        return response()->json([
            'data' => $this->invoiceService->getWithDetails($invoice),
        ]);
    }

    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        $invoice = $this->invoiceService->create($request->validated());

        return response()->json(['data' => $invoice], 201);
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice): JsonResponse
    {
        $updated = $this->invoiceService->update($invoice, $request->validated());

        return response()->json(['data' => $updated]);
    }

    public function destroy(Invoice $invoice): JsonResponse
    {
        $this->invoiceService->delete($invoice);

        return response()->json(['message' => 'Faktura usunięta.'], 200);
    }

    public function upload(ProcessOcrRequest $request): JsonResponse
    {
        $fullPath = $this->fileUploadService->storeInvoiceFile($request->file('file'));

        \App\Jobs\ProcessOcrJob::dispatch($fullPath, auth()->id());

        $text = $this->ocrService->extractText($fullPath);
        $aiData = $this->aiParserFactory->make()->parse($text);

        return response()->json([
            'ocr_text' => $text,
            'ai_parsed' => $aiData,
            'file_path' => 'invoices/' . basename($fullPath),
        ]);
    }
}
