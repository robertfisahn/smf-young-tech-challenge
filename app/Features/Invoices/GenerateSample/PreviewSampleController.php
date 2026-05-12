<?php

declare(strict_types=1);

namespace App\Features\Invoices\GenerateSample;

use App\Features\BaseController;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class PreviewSampleController
{
    public function __invoke(Request $request): View
    {
        // Jeśli dane przychodzą jako JSON (z fetch), dekodujemy je
        $data = $request->has('json_data') 
            ? json_decode($request->input('json_data'), true) 
            : $request->all();

        // Przekazujemy dane jako pojedyncze zmienne, aby pasowały do szablonu blade
        return view('invoices.template', $data ?? []);
    }
}
