<?php

declare(strict_types=1);



namespace App\Features\Invoices\Shared;

use App\Models\Contractor;
use Illuminate\Support\Collection;

final readonly class GetContractorsForSelectionAction
{
    /**
     * @return Collection<int, Contractor>
     */
    public function execute(): Collection
    {
        return Contractor::query()
            ->select(['id', 'name', 'tax_id'])
            ->orderBy('name')
            ->get();
    }
}

