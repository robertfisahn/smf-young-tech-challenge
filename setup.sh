#!/bin/bash
echo "Silo Invoices: Inicjalizacja srodowiska na Linux/macOS..."

echo "Tworzenie wymaganych folderow..."
folders=(
    "bootstrap/cache"
    "storage/logs"
    "storage/framework/cache"
    "storage/framework/sessions"
    "storage/framework/views"
    "database"
)

for folder in "${folders[@]}"; do
    if [ ! -d "$folder" ]; then
        mkdir -p "$folder"
        echo "  Utworzono: $folder"
    fi
done

chmod -R 775 storage bootstrap/cache

echo "Przygotowanie .env..."
if [ ! -f ".env" ]; then
    cp .env.example .env
    echo "  Utworzono .env z .env.example"
fi

if command -v composer &> /dev/null; then
    echo "Instalowanie zaleznosci (composer install)..."
    composer install
    
    echo "Generowanie klucza aplikacji..."
    php artisan key:generate --force
    
    echo "Migracje i seed bazy danych..."
    php artisan migrate:fresh --seed
    
    echo "Gotowe! Uruchom aplikacje: php artisan serve"
else
    echo "UWAGA: Nie znaleziono composer. Zainstaluj go i uruchom skrypt ponownie."
fi