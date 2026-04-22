Write-Host "Silo Invoices: Inicjalizacja srodowiska na Windows..." -ForegroundColor Cyan

Write-Host "Tworzenie wymaganych folderow..." -ForegroundColor Yellow
$folders = @(
    "bootstrap/cache",
    "storage/logs",
    "storage/framework/cache",
    "storage/framework/sessions",
    "storage/framework/views",
    "database"
)

foreach ($folder in $folders) {
    if (-not (Test-Path $folder)) {
        New-Item -ItemType Directory -Force $folder | Out-Null
        Write-Host "  Utworzono: $folder" -ForegroundColor Green
    }
}

Write-Host "Przygotowanie .env..." -ForegroundColor Yellow
if (-not (Test-Path .env)) {
    Copy-Item .env.example .env
    Write-Host "  Utworzono .env z .env.example" -ForegroundColor Green
}

if (Get-Command "composer" -ErrorAction SilentlyContinue) {
    Write-Host "Instalowanie zaleznosci (composer install)..." -ForegroundColor Cyan
    composer install
    
    Write-Host "Generowanie klucza aplikacji..." -ForegroundColor Cyan
    php artisan key:generate --force
    
    Write-Host "Migracje i seed bazy danych..." -ForegroundColor Cyan
    php artisan migrate:fresh --seed
    
    Write-Host "Gotowe! Uruchom aplikacje: php artisan serve" -ForegroundColor Green
} else {
    Write-Host "UWAGA: Nie znaleziono composer. Zainstaluj go i uruchom skrypt ponownie." -ForegroundColor Red
}