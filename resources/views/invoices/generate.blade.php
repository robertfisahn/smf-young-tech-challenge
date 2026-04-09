@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-200">
        <div class="px-8 py-8 border-b border-gray-100">
            <h1 class="text-2xl font-bold text-gray-800 tracking-tight text-center">Generator Faktur Testowych 📄</h1>
            <p class="mt-1 text-gray-500 text-sm text-center">Wprowadź dane JSON lub polusuj gotowy zestaw, aby pobrać plik do testów OCR.</p>
        </div>

        <div class="p-8 space-y-8">
            <div class="flex justify-center">
                <button type="button" onclick="loadSampleData()" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-lg shadow-md transition-all hover:scale-105 active:scale-95">
                    🎲 Losuj nowe dane do testów
                </button>
            </div>

            <div class="space-y-6">
                <div>
                    <label for="json_data" class="block text-sm font-semibold text-gray-700 mb-2">Dane faktury (JSON):</label>
                    <textarea 
                        id="json_data" 
                        name="json_data" 
                        rows="12" 
                        class="w-full rounded-2xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 font-mono text-sm p-4 bg-gray-50"
                        placeholder='Wpisz tutaj JSON...'
                    ></textarea>
                </div>

                <div class="flex items-center justify-between pt-4">
                    <a href="{{ route('invoices.index') }}" class="text-gray-500 hover:text-gray-700 font-medium transition-colors">
                        ← Wróć do listy
                    </a>
                    <div class="flex space-x-3">
                        <button type="button" onclick="downloadImage('png')" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-full font-bold text-white uppercase text-xs tracking-widest hover:bg-green-700 transition ease-in-out duration-150 shadow-lg shadow-green-500/30">
                            PNG 🖼️
                        </button>
                        <button type="button" onclick="downloadImage('jpg')" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-full font-bold text-white uppercase text-xs tracking-widest hover:bg-yellow-700 transition ease-in-out duration-150 shadow-lg shadow-yellow-500/30">
                            JPG 🖼️
                        </button>
                        <button type="button" onclick="downloadImage('pdf')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-full font-bold text-white uppercase text-xs tracking-widest hover:bg-blue-700 transition ease-in-out duration-150 shadow-lg shadow-blue-500/30">
                            PDF 📄
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ukryty kontener na podgląd do przechwycenia -->
<div id="capture-container" style="position: absolute; left: -9999px; top: 0;">
    <iframe id="preview-iframe" style="width: 800px; border: none;"></iframe>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
    function loadSampleData(index = null) {
        const scenarios = [
            { 
                name: "TechForge Solutions", nip: "525-222-11-00", address: "ul. Cybernetyki 7, 02-677 Warszawa", nr: "INV/2026/001", date: "2026-03-20", 
                method: "przelew",
                items: [
                    { name: "Subskrypcja Serwera Cloud (M-1)", qty: 1, price: 450.00 },
                    { name: "Wsparcie techniczne 24/7", qty: 5, price: 150.00 }
                ]
            },
            { 
                name: "GreenEnergy Polska Sp. z o.o.", nip: "777-888-99-11", address: "Al. Pokoju 1, 31-548 Kraków", nr: "INV/2026/055", date: "2026-03-25", 
                method: "karta",
                items: [
                    { name: "Audyt energetyczny IT", qty: 1, price: 600.00 },
                    { name: "Kabel Sieciowy CAT6 (100m)", qty: 1, price: 250.50 }
                ]
            },
            { 
                name: "Creative Mind Agency", nip: "999-000-11-22", address: "ul. Piotrkowska 10, 90-001 Łódź", nr: "INV/2026/099", date: "2026-04-02", 
                method: "gotówka",
                items: [
                    { name: "Projekt Interfejsu UI/UX", qty: 1, price: 2800.00 },
                    { name: "Konsultacje UX", qty: 2, price: 1500.00 }
                ]
            },
            { 
                name: "ByteCloud Services", nip: "111-333-55-77", address: "ul. Chmielna 89, 00-801 Warszawa", nr: "INV/2026/120", date: "2026-04-05", 
                method: "przelew",
                items: [
                    { name: "Hosting WWW - Pakiet Pro", qty: 1, price: 800.00 },
                    { name: "Certyfikat SSL Wildcard", qty: 1, price: 450.00 }
                ]
            },
            { 
                name: "Nexus Secure", nip: "222-444-66-88", address: "ul. Graniczna 4, 50-001 Wrocław", nr: "INV/2026/210", date: "2026-04-12", 
                method: "karta",
                items: [
                    { name: "Audyt Bezpieczeństwa (Pentest)", qty: 1, price: 4200.00 }
                ]
            },
            { 
                name: "AppLogic Solutions", nip: "444-555-66-77", address: "ul. Długa 15, 80-001 Gdańsk", nr: "INV/2026/305", date: "2026-04-14", 
                method: "przelew",
                items: [
                    { name: "Serwis aplikacji webowej", qty: 1, price: 1800.00 }
                ]
            },
            { 
                name: "DevOps Master", nip: "777-111-22-33", address: "ul. Krótka 2, 60-001 Poznań", nr: "INV/2026/415", date: "2026-04-15", 
                method: "gotówka",
                items: [
                    { name: "Optymalizacja CI/CD", qty: 1, price: 2500.00 }
                ]
            },
            { 
                name: "SoftLicence Sp. z o.o.", nip: "888-999-00-11", address: "ul. Jasna 10, 00-001 Warszawa", nr: "INV/2026/502", date: "2026-04-18", 
                method: "karta",
                items: [
                    { name: "Licencja IDE JetBrains", qty: 1, price: 950.00 }
                ]
            },
            { 
                name: "Security Watch Inc.", nip: "333-222-11-00", address: "ul. Obrońców 12, 01-234 Warszawa", nr: "INV/2026/601", date: "2026-04-20", 
                method: "karta",
                items: [
                    { name: "Konfiguracja Firewall", qty: 1, price: 3500.00 }
                ]
            },
            { 
                name: "Data Analytics Lab", nip: "555-444-33-22", address: "ul. Statystyczna 1, 30-100 Kraków", nr: "INV/2026/705", date: "2026-04-22", 
                method: "przelew",
                items: [
                    { name: "Analiza Big Data (Kwartalna)", qty: 1, price: 6200.00 }
                ]
            },
            { 
                name: "Mobile Flow", nip: "123-456-78-90", address: "ul. Mobilna 5, 50-123 Wrocław", nr: "INV/2026/810", date: "2026-04-25", 
                method: "przelew",
                items: [
                    { name: "Poprawki w aplikacji Flutter", qty: 1, price: 2150.00 }
                ]
            }
        ];
        
        // Jeśli index jest null (przycisk "Losuj"), losujemy tylko z nowych (od indexu 3 wzwyż)
        const idx = (index !== null) ? index : Math.floor(Math.random() * (scenarios.length - 3)) + 3;
        const s = scenarios[idx];
        
        const total = s.items.reduce((sum, item) => sum + (item.qty * item.price), 0);
        
        const sample = {
            "contractor_name": s.name,
            "contractor_nip": s.nip,
            "contractor_address": s.address,
            "invoice_number": s.nr,
            "date": s.date,
            "total_amount": total,
            "currency": "PLN",
            "payment_method": s.method,
            "payment_date": s.date,
            "items": s.items.map(item => ({
                "name": item.name,
                "quantity": item.qty,
                "unit_price": item.price,
                "total_price": item.qty * item.price
            }))
        };
        document.getElementById('json_data').value = JSON.stringify(sample, null, 2);
    }

    async function downloadImage(format) {
        const jsonData = document.getElementById('json_data').value;
        if (!jsonData) {
            alert('Wprowadź dane JSON!');
            return;
        }
        let data;
        try {
            data = JSON.parse(jsonData);
        } catch (e) {
            alert('Niepoprawny format JSON!');
            return;
        }

        const btn = event.currentTarget || event.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = 'Generowanie... ⏳';
        btn.disabled = true;

        if (format === 'pdf') {
            try {
                const docDefinition = {
                    pageSize: 'A4',
                    pageMargins: [40, 40, 40, 40],
                    content: [
                        // Nagłówek: Sprzedawca i Daty
                        {
                            columns: [
                                {
                                    stack: [
                                        { text: 'SMF Young Tech Ltd.', style: 'headerMain' },
                                        { text: 'ul. Wynalazek 1, 02-677 Warszawa\nNIP: 111-222-33-44', style: 'headerSub' }
                                    ]
                                },
                                {
                                    stack: [
                                        { text: 'Data wystawienia: ' + data.date, alignment: 'right', style: 'headerSub' },
                                        { text: 'Miejsce: Warszawa', alignment: 'right', style: 'headerSub' }
                                    ]
                                }
                            ]
                        },
                        // Pasek z numerem faktury
                        {
                            canvas: [{ type: 'rect', x: 0, y: 0, w: 515, h: 40, color: '#f3f4f6' }],
                            margin: [0, 20, 0, 0]
                        },
                        {
                            text: [
                                { text: 'DOKUMENT: FAKTURA VAT\n', style: 'docTypeLabel' },
                                { text: 'nr ' + data.invoice_number, style: 'invoiceNumber' }
                            ],
                            alignment: 'center',
                            margin: [0, -32, 0, 0]
                        },
                        {
                            canvas: [{ type: 'line', x1: 0, y1: 0, x2: 515, y2: 0, lineWidth: 2, lineColor: '#374151' }],
                            margin: [0, 8, 0, 30]
                        },
                        // Sprzedawca i Nabywca side-by-side
                        {
                            columns: [
                                {
                                    width: '45%',
                                    stack: [
                                        { text: 'SPRZEDAWCA:', style: 'sectionLabel' },
                                        { text: 'SMF Young Tech Ltd.', style: 'participantName' },
                                        { text: 'ul. Wynalazek 1\n02-677 Warszawa\nNIP: 111-222-33-44', style: 'participantInfo' }
                                    ]
                                },
                                { width: '10%', text: '' },
                                {
                                    width: '45%',
                                    stack: [
                                        { text: 'NABYWCA:', style: 'sectionLabel' },
                                        { text: data.contractor_name || "Brak nazwy", style: 'participantName' },
                                        { text: (data.contractor_address || "Brak adresu") + '\nNIP: ' + (data.contractor_nip || "Brak NIP"), style: 'participantInfo' }
                                    ]
                                }
                            ]
                        },
                        // Płatność i dostawa
                        {
                            margin: [0, 20, 0, 20],
                            columns: [
                                { text: 'Sposób płatności: ' + (data.payment_method || 'Przelew'), style: 'infoText' },
                                { text: (data.payment_method && data.payment_method.toLowerCase().includes('gotów') ? '' : 'Termin płatności: 2026-04-14\n') + 'Waluta: ' + (data.currency || 'PLN'), alignment: 'right', style: 'infoText' }
                            ]
                        },
                        // Tabela produktów
                        {
                            style: 'tableExample',
                            table: {
                                headerRows: 1,
                                widths: [30, '*', 50, 70, 80],
                                body: [
                                    [
                                        { text: 'LP', style: 'tableHeader' },
                                        { text: 'NAZWA USŁUGI / TOWARU', style: 'tableHeader' },
                                        { text: 'ILOŚĆ', style: 'tableHeader', alignment: 'center' },
                                        { text: 'CENA JEDN.', style: 'tableHeader', alignment: 'right' },
                                        { text: 'WARTOŚĆ BRUTTO', style: 'tableHeader', alignment: 'right' }
                                    ],
                                    ...(data.items || []).map((item, index) => [
                                        { text: (index + 1).toString(), alignment: 'center' },
                                        { text: item.name },
                                        { text: item.quantity + ' szt', alignment: 'center' },
                                        { text: item.unit_price.toFixed(2), alignment: 'right' },
                                        { text: item.total_price.toFixed(2) + ' ' + (data.currency || 'PLN'), alignment: 'right', bold: true }
                                    ])
                                ]
                            },
                            layout: 'lightHorizontalLines'
                        },
                        // Suma
                        {
                            margin: [0, 20, 0, 0],
                            columns: [
                                { text: '' },
                                {
                                    width: 'auto',
                                    stack: [
                                        {
                                            columns: [
                                                { text: 'DO ZAPŁATY:', style: 'totalLabel', width: 120 },
                                                { text: (data.total_amount || 0).toFixed(2) + ' ' + (data.currency || 'PLN'), style: 'totalValue', alignment: 'right', width: 100 }
                                            ]
                                        }
                                    ]
                                }
                            ]
                        }
                    ],
                    styles: {
                        headerMain: { fontSize: 16, bold: true, color: '#111827' },
                        headerSub: { fontSize: 9, color: '#4b5563', lineHeight: 1.3 },
                        docTypeLabel: { fontSize: 8, color: '#6b7280', characterSpacing: 1 },
                        invoiceNumber: { fontSize: 18, bold: true, color: '#111827' },
                        sectionLabel: { fontSize: 8, bold: true, color: '#6b7280', margin: [0, 0, 0, 5] },
                        participantName: { fontSize: 12, bold: true, color: '#111827' },
                        participantInfo: { fontSize: 10, color: '#374151', lineHeight: 1.3 },
                        infoText: { fontSize: 9, color: '#374151', lineHeight: 1.4 },
                        tableHeader: { fontSize: 9, bold: true, color: '#4b5563', fillColor: '#f9fafb', margin: [0, 5, 0, 5] },
                        totalLabel: { fontSize: 14, bold: true, color: '#111827', margin: [0, 10, 0, 0] },
                        totalValue: { fontSize: 16, bold: true, color: '#111827', margin: [0, 10, 0, 0] }
                    },
                    footer: function(currentPage, pageCount) {
                        return {
                            stack: [
                                { canvas: [{ type: 'line', x1: 40, y1: 0, x2: 555, y2: 0, lineWidth: 0.5, lineColor: '#e5e7eb' }] },
                                { text: 'Dokument wystawiony automatycznie nr ' + data.invoice_number + '. Dzi\u0119kujemy za zakupy w SMF Young Tech Challenge!', alignment: 'center', fontSize: 8, color: '#9ca3af', margin: [0, 10, 0, 0] }
                            ]
                        };
                    }
                };

                pdfMake.createPdf(docDefinition).download(`faktura_${data.invoice_number}.pdf`);
                btn.innerHTML = originalText;
                btn.disabled = false;
            } catch (e) {
                console.error(e);
                alert("Błąd PDF: " + e.message);
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
            return;
        }

        // PNG / JPG
        try {
            const response = await fetch('{{ route("invoices.preview") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ json_data: jsonData })
            });

            const html = await response.text();
            const iframe = document.getElementById('preview-iframe');
            const doc = iframe.contentDocument || iframe.contentWindow.document;
            doc.open(); doc.write(html); doc.close();

            setTimeout(() => {
                html2canvas(doc.body, { scale: 2, useCORS: true }).then(canvas => {
                    const link = document.createElement('a');
                    link.download = `faktura_${Date.now()}.${format}`;
                    link.href = canvas.toDataURL(`image/${format === 'png' ? 'png' : 'jpeg'}`, 0.9);
                    link.click();
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
            }, 600);
        } catch (error) {
            console.error(error);
            alert('Błąd generowania obrazu.');
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }
</script>
@endsection
