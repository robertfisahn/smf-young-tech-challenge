<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Faktura {{ $invoice_number }}</title>
    <style>
        body { font-family: 'DejaVu Sans', 'Inter', sans-serif; font-size: 11px; color: #333; line-height: 1.4; background-color: #fff; margin: 0; padding: 0; }
        .container { width: 210mm; min-height: 297mm; margin: 0 auto; padding: 20px; box-sizing: border-box; background-color: white; position: relative; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th { background-color: #f3f4f6; text-align: left; padding: 8px; border: 1px solid #e5e7eb; font-weight: bold; font-size: 10px; color: #4b5563; }
        .table td { padding: 8px; border: 1px solid #e5e7eb; vertical-align: middle; }
        .footer { position: absolute; bottom: 20px; left: 20px; right: 20px; text-align: center; font-size: 9px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <table style="width: 100%; margin-bottom: 20px;">
            <tr>
                <td style="vertical-align: top;">
                    <div style="font-size: 18px; font-weight: bold; margin-bottom: 5px;">SMF Young Tech Ltd.</div>
                    <div style="font-size: 11px; color: #4b5563;">
                        ul. Wynalazek 1, 02-677 Warszawa<br>
                        NIP: 111-222-33-44
                    </div>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <div style="font-size: 11px; color: #4b5563;">
                        Data wystawienia: <strong>{{ $date }}</strong><br>
                        Miejsce wystawienia: Warszawa
                    </div>
                </td>
            </tr>
        </table>

        <div style="background-color: #f3f4f6; padding: 15px; text-align: center; border-radius: 4px; margin-bottom: 30px; border-bottom: 2px solid #374151;">
            <div style="font-size: 10px; color: #6b7280; text-transform: uppercase; letter-spacing: 1px;">Dokument: FAKTURA VAT</div>
            <div style="font-size: 20px; font-weight: bold; color: #111827;">nr {{ $invoice_number }}</div>
        </div>

        <table style="width: 100%; margin-bottom: 40px;">
            <tr>
                <td style="width: 45%; vertical-align: top; border: 1px solid #e5e7eb; padding: 15px; border-radius: 4px;">
                    <span style="font-size: 10px; color: #6b7280; text-transform: uppercase; font-weight: bold;">Sprzedawca:</span><br>
                    <div style="font-size: 13px; font-weight: bold; margin-top: 5px;">SMF Young Tech Ltd.</div>
                    <div style="font-size: 11px; color: #374151; margin-top: 2px;">
                        ul. Wynalazek 1, 02-677 Warszawa<br>
                        NIP: 111-222-33-44
                    </div>
                </td>
                <td style="width: 10%;"></td>
                <td style="width: 45%; vertical-align: top; border: 1px solid #e5e7eb; padding: 15px; border-radius: 4px;">
                    <span style="font-size: 10px; color: #6b7280; text-transform: uppercase; font-weight: bold;">Nabywca:</span><br>
                    <div style="font-size: 13px; font-weight: bold; margin-top: 5px;">{{ $contractor_name }}</div>
                    <div style="font-size: 11px; color: #374151; margin-top: 2px;">
                        {{ $contractor_address ?? 'Brak adresu' }}<br>
                        <strong>NIP: {{ $contractor_nip }}</strong>
                    </div>
                </td>
            </tr>
        </table>

        <table style="width: 100%; margin-bottom: 20px; font-size: 11px;">
            <tr>
                <td>
                    <strong>Sposób płatności:</strong> {{ $payment_method ?? 'Przelew (ECOM)' }}
                </td>
                <td style="text-align: right;">
                    @php
                        $method = strtolower($payment_method ?? '');
                        $isPaid = str_contains($method, 'gotów') || str_contains($method, 'kart') || str_contains($method, 'blik');
                    @endphp
                    @if($isPaid)
                        <strong style="color: #059669;">Status: ZAPŁACONO</strong><br>
                    @else
                        <strong>Termin płatności:</strong> {{ \Carbon\Carbon::parse($date)->addDays(7)->format('Y-m-d') }}<br>
                    @endif
                    <strong>Waluta:</strong> {{ $currency }}
                </td>
            </tr>
        </table>

        <table class="table">
            <thead>
                <tr>
                    <th style="width: 40px;">LP</th>
                    <th>Nazwa usługi / towaru</th>
                    <th style="width: 80px; text-align: center;">Ilość</th>
                    <th style="width: 100px; text-align: right;">Cena jedn.</th>
                    <th style="width: 120px; text-align: right;">Wartość brutto</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $item['name'] }}</td>
                    <td style="text-align: center;">{{ $item['quantity'] }} szt</td>
                    <td style="text-align: right;">{{ number_format($item['unit_price'], 2) }}</td>
                    <td style="text-align: right; font-weight: bold;">{{ number_format($item['total_price'], 2) }} {{ $currency }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 20px; float: right; width: 300px;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr style="font-size: 18px; font-weight: bold; background-color: #f9fafb;">
                    <td style="padding: 10px; border-top: 2px solid #111827;">DO ZAPŁATY:</td>
                    <td style="padding: 10px; text-align: right; border-top: 2px solid #111827;">{{ number_format($total_amount, 2) }} {{ $currency }}</td>
                </tr>
            </table>
        </div>
        <div style="clear: both;"></div>

        <div class="footer">
            Dokument wystawiony automatycznie nr {{ $invoice_number }}.<br>
            Dziękujemy za zakupy w SMF Young Tech Challenge!
        </div>
    </div>
</body>
</html>
