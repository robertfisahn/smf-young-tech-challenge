<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Domyślny użytkownik
        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Rekruter',
                'password' => Hash::make('user1234'),
            ]
        );

        // 2. Kontrahenci
        $c1 = \App\Models\Contractor::firstOrCreate(
            ['tax_id' => '525-222-11-00'],
            ['name' => 'TechForge Solutions', 'address' => 'ul. Cybernetyki 7, 02-677 Warszawa']
        );

        $c2 = \App\Models\Contractor::firstOrCreate(
            ['tax_id' => '777-888-99-11'],
            ['name' => 'GreenEnergy Polska Sp. z o.o.', 'address' => 'Al. Pokoju 1, 31-548 Kraków']
        );

        $c3 = \App\Models\Contractor::firstOrCreate(
            ['tax_id' => '999-000-11-22'],
            ['name' => 'Creative Mind Agency', 'address' => 'ul. Piotrkowska 10, 90-001 Łódź']
        );

        // 3. Przykładowe Faktury zsynchronizowane z plikami
        try {
            if (\App\Models\Invoice::count() === 0) {
                // Faktura 1 (PDF) - TechForge
                $i1 = \App\Models\Invoice::create([
                    'contractor_id' => $c1->id,
                    'invoice_number' => 'INV/2026/001',
                    'date' => '2026-03-20',
                    'file_path' => 'invoices/invoice_1.pdf',
                ]);
                $i1->items()->createMany([
                    ['name' => 'Subskrypcja Serwera Cloud (M-1)', 'quantity' => 1, 'unit_price' => 450.00, 'total_price' => 450.00],
                    ['name' => 'Wsparcie techniczne 24/7', 'quantity' => 5, 'unit_price' => 150.00, 'total_price' => 750.00],
                ]);
                $i1->payments()->create(['amount' => 1200.00, 'currency' => 'PLN', 'paid_at' => '2026-03-20', 'method' => 'przelew']);

                // Faktura 2 (JPG) - GreenEnergy
                $i2 = \App\Models\Invoice::create([
                    'contractor_id' => $c2->id,
                    'invoice_number' => 'INV/2026/055',
                    'date' => '2026-03-25',
                    'file_path' => 'invoices/invoice_2.jpg',
                ]);
                $i2->items()->createMany([
                    ['name' => 'Audyt energetyczny IT', 'quantity' => 1, 'unit_price' => 600.00, 'total_price' => 600.00],
                    ['name' => 'Kabel Sieciowy CAT6 (100m)', 'quantity' => 1, 'unit_price' => 250.50, 'total_price' => 250.50],
                ]);
                $i2->payments()->create(['amount' => 850.50, 'currency' => 'PLN', 'paid_at' => '2026-03-25', 'method' => 'karta']);

                // Faktura 3 (PNG) - Creative Mind
                $i3 = \App\Models\Invoice::create([
                    'contractor_id' => $c3->id,
                    'invoice_number' => 'INV/2026/099',
                    'date' => '2026-04-02',
                    'file_path' => 'invoices/invoice_3.png',
                ]);
                $i3->items()->createMany([
                    ['name' => 'Projekt Interfejsu UI/UX', 'quantity' => 1, 'unit_price' => 2800.00, 'total_price' => 2800.00],
                    ['name' => 'Konsultacje UX', 'quantity' => 2, 'unit_price' => 1500.00, 'total_price' => 3000.00],
                ]);
                $i3->payments()->create(['amount' => 5800.00, 'currency' => 'PLN', 'paid_at' => '2026-04-02', 'method' => 'gotówka']);
            }
        } catch (\Exception $e) {
            echo "SEED ERROR: " . $e->getMessage() . "\n";
        }
    }
}
