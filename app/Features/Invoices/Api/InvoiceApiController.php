<?php

declare(strict_types=1);



namespace App\Features\Invoices\Api;

use App\Models\Invoice;
use App\Features\Invoices\ListInvoices\ListInvoicesAction;
use App\Features\Invoices\ShowInvoice\ShowInvoiceAction;
use App\Features\Invoices\CreateInvoice\CreateInvoiceAction;
use App\Features\Invoices\UpdateInvoice\UpdateInvoiceAction;
use App\Features\Invoices\DeleteInvoice\DeleteInvoiceAction;
use App\Features\Invoices\ProcessOcr\ProcessOcrAction;
use App\Features\Invoices\CreateInvoice\CreateInvoiceRequest;
use App\Features\Invoices\UpdateInvoice\UpdateInvoiceRequest;
use App\Features\Invoices\ProcessOcr\ProcessOcrRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

final class InvoiceApiController
{
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

    public function index(ListInvoicesAction $action): JsonResponse
    {
        return response()->json([
            'data' => $action->execute(),
        ]);
    }

    public function show(int $id, ShowInvoiceAction $action): JsonResponse
    {
        try {
            return response()->json([
                'data' => $action->execute($id),
            ]);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function store(CreateInvoiceRequest $request, CreateInvoiceAction $action): JsonResponse
    {
        try {
            $invoice = $action->execute($request->validated());
            return response()->json(['data' => $invoice], 201);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice, UpdateInvoiceAction $action): JsonResponse
    {
        try {
            $updated = $action->execute($invoice, $request->validated());
            return response()->json(['data' => $updated]);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function destroy(Invoice $invoice, DeleteInvoiceAction $action): JsonResponse
    {
        try {
            $action->execute($invoice);
            return response()->json(['message' => 'Faktura usunięta.'], 200);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function upload(ProcessOcrRequest $request, ProcessOcrAction $action): JsonResponse
    {
        try {
            $result = $action->execute(
                $request->file('file'),
                $request->input('existing_file'),
                $request->input('ai_provider'),
                $request->boolean('auto_save'),
                (int) auth()->id()
            );

            return response()->json([
                'ocr_text' => $result['text'],
                'ai_parsed' => $result['ai_data'],
                'file_path' => $result['file_path'],
            ]);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}

