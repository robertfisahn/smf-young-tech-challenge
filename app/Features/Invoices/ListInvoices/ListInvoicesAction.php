<?php

declare(strict_types=1);



namespace App\Features\Invoices\ListInvoices;

use App\Models\Invoice;
use Illuminate\Pagination\LengthAwarePaginator;

final readonly class ListInvoicesAction
{
    /**
     * @return LengthAwarePaginator<Invoice>
     */
    public function execute(int $perPage = 10): LengthAwarePaginator
    {
        return Invoice::query()
            ->select(['id', 'contractor_id', 'invoice_number', 'date'])
            ->with([
                'contractor:id,name,tax_id',
            ])
            // Używamy withSum zamiast akcesora w modelu, aby uniknąć problemu N+1
            ->withSum('payments as total_amount', 'amount')
            ->latest()
            ->paginate($perPage);
    }
}

