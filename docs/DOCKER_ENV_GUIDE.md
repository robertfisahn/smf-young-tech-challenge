# 🐳 Przewodnik: Rozwiązywanie konfliktów ENV (Docker + Laravel)

Ten przewodnik opisuje problem dziedziczenia zmiennych środowiskowych w Laravelu uruchomionym w Dockerze oraz przedstawia ostateczne rozwiązanie typu "Smart-Config".

## 1. Natura problemu

W złożonych setupach Dockerowych (szczególnie na Windows z `php artisan serve`) często dochodzi do konfliktu między zmiennymi zdefiniowanymi w `docker-compose.yml` (`environment:`) a plikiem `.env` zamontowanym jako wolumen.

### Dlaczego standardowe nadpisywanie zawodzi?
1.  **Izolacja procesu:** `php artisan serve` uruchamia serwer PHP w osobnym procesie, który często nie dziedziczy wszystkich zmiennych systemowych kontenera.
2.  **Priorytet phpdotenv:** Biblioteka `phpdotenv` w Laravelu wczytuje plik `.env`. Jeśli zmienna (np. `OLLAMA_URL`) istnieje w tym pliku, funkcja `env('OLLAMA_URL', 'default')` zawsze zwróci wartość z pliku, ignorując logikę domyślną.

## 2. Rozwiązanie: "Smart-Config" (Auto-detekcja)

Zamiast walczyć z mechanizmami dziedziczenia OS, stosujemy rozwiązanie świadome kontekstu bezpośrednio w kodzie konfiguracyjnym.

### Implementacja (`config/services.php`)

Wymuszamy użycie mostka Dockerowego (`host.docker.internal`), jeśli aplikacja wykryje, że pracuje w kontenerze:

```php
'ollama' => [
    'api_url' => file_exists('/.dockerenv') 
        ? 'http://host.docker.internal:11434' 
        : env('OLLAMA_URL', 'http://localhost:11434'),
    'model' => env('OLLAMA_MODEL', 'llama3.1'),
],
```

### Dlaczego to działa?
- **Pewność:** Każdy kontener Dockerowy posiada automatycznie generowany plik `/.dockerenv`. Jego obecność jest 100% potwierdzeniem środowiska.
- **Priorytet:** Logika `file_exists` znajduje się **poza** funkcją `env()`, więc plik `.env` nie może jej "przekrzyczeć".
- **Uniwersalność:** Ten sam kod działa poprawnie lokalnie (bez Dockera) oraz wewnątrz kontenera bez żadnych zmian w pliku `.env`.

## 3. Diagnostyka (Jak sprawdzać "prawdę"?)

Jeśli masz wątpliwości, co widzi Twoja aplikacja, użyj poniższych metod:

### A. Sprawdzenie wewnątrz kontenera (CLI)
```bash
docker exec smf-app php artisan tinker --execute="echo config('services.ollama.api_url');"
```

### B. Sprawdzenie przez przeglądarkę (Web)
Możesz dodać tymczasowy endpoint w `routes/web.php` do szybkiej weryfikacji stanu.

## 4. Dobre praktyki
- **Nie używaj `config:cache` w Dockerze podczas developmentu:** Cache "zamraża" zmienne i utrudnia debugowanie. Nasza metoda z `file_exists` działa dynamicznie i jest bezpieczniejsza.
- **Extra Hosts:** Pamiętaj, aby w `docker-compose.yml` zawsze znajdował się wpis `host.docker.internal:host-gateway`.

---
*Dokumentacja techniczna projektu.*
