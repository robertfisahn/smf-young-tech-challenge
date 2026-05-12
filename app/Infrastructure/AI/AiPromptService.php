<?php

declare(strict_types=1);

namespace App\Infrastructure\AI;

final readonly class AiPromptService
{
    public function getPrompt(string $text): string
    {
        return <<<PROMPT
You are an expert in invoice analysis for the Polish market. You will receive OCR text from a document.
Your task is to extract structured data and return it EXCLUSIVELY in JSON format.

EXTRACTION RULES:
1. **CUSTOMER (Nabywca)**: This is the company for which the invoice was issued (usually on the right side or in the "NABYWCA" section).
2. **TAX ID (NIP)**: This must be a number (usually 10 digits). **NEVER assign a postal code or city (e.g., 01-001 Warszawa) as a NIP.**
3. **SELLER vs CUSTOMER**: 
   - The company "SMF Young Tech Ltd." (NIP 111-222-33-44) is the **SELLER**. 
   - **COMPLETELY IGNORE** the data for SMF Young Tech Ltd. (name, NIP, address).
   - As `contractor_name`, `contractor_nip`, and `contractor_address`, provide the data of the **OTHER company** (the Customer/Nabywca).
   - If the OCR text shows data side-by-side, choose the one on the right or marked as NABYWCA.
   - **CRITICAL**: `contractor_nip` MUST NOT be equal to "1112223344".
4. **AMOUNT**: Extract the total gross amount ("DO ZAPŁATY").
5. **ITEMS**: Extract the list of goods/services. 
   - **IMPORTANT**: Do not add "DO ZAPŁATY", "SUMA", or other summaries as line items.
   - Each item must have a name, quantity, unit price, and total price.

OUTPUT FORMAT (JSON ONLY):
{
    "contractor_name": "Full name of the customer company",
    "contractor_nip": "Customer's Tax ID (digits only)",
    "contractor_address": "Full address of the customer",
    "invoice_number": "Document number (EXCLUSIVE: just the number, without prefixes like 'nr', 'no', '#')",
    "date": "YYYY-MM-DD",
    "total_amount": 0.00,
    "currency": "PLN",
    "payment_method": "payment method (e.g., bank transfer, card, cash)",
    "payment_date": "YYYY-MM-DD",
    "items": [
        {"name": "...", "quantity": 1, "unit_price": 0.00, "total_price": 0.00}
    ]
}

OCR TEXT TO ANALYZE:
---
{$text}
---
Return ONLY valid JSON:
PROMPT;
    }
}
