# 🧾 SMF Young Tech Challenge — Invoice Processing System

> ### ⚡ **SZYBKIE URUCHOMIENIE (Docker Hub)**
> ```bash
> git clone https://github.com/robertfisahn/smf-young-tech-challenge.git
> cd smf-young-tech-challenge
> cp .env.example .env
> # Uzupełnij GROQ_API_KEY w pliku .env
> docker-compose -f docker-compose.hub.yml up -d
> ```

Aplikacja Laravel do automatycznego zarządzania fakturami, zintegrowana z silnikiem OCR oraz Agentem AI do inteligentnej ekstrakcji danych.

---

## 🚀 Realizacja zadań (Zgodnie z wymaganiami)

### **1. Aplikacja CRUD (Laravel)**
System posiada kompletny moduł zarządzania fakturami (Invoices):
- Lista wszystkich faktur z informacją o statusie płatności.
- Szczegółowy podgląd faktury wraz z pozycjami i danymi kontrahenta.
- Formularz tworzenia i edycji faktur (ręczny oraz automatyczny przez OCR).
- Bezpieczne usuwanie faktur wraz z załącznikami.

### **2. Upload pliku – faktura/paragon**
- Możliwość przesłania plików w formatach **PDF, JPG, PNG**.
- Pliki są bezpiecznie przechowywane na serwerze (`storage/app/invoices`).
- System obsługuje przesyłanie plików przez Drag & Drop oraz tradycyjny wybór pliku.

### **3. Silnik OCR (Open-Source PHP)**
Do ekstrakcji tekstu z dokumentów wykorzystano wyłącznie darmowe rozwiązania open-source:
- **smalot/pdfparser** — do szybkiego wyciągania tekstu bezpośrednio z plików PDF.
- **thiagoalessio/tesseract-ocr** — jako wrapper dla silnika Tesseract OCR (JPG/PNG oraz fallback dla PDF).

### **4. Kategoryzacja danych + Agent AI**
Aplikacja wykorzystuje inteligentnego agenta AI, który analizuje surowy tekst z OCR i zamienia go na JSON.
- **Multi-Provider**: Wsparcie dla **Groq API** oraz **Ollama**.
- **AiPromptService**: Centralny system reguł (promptów) zapewniający precyzję danych.

### **5. Baza danych — SQLite**
- Wykorzystano lekką bazę danych **SQLite** (plik `database/faktury.sqlite`).
- Schemat bazy obejmuje tabele m.in.: `contractors`, `invoices`, `invoice_items`, `payments`.

### **6. Prezentacja wyników (REST API + Swagger)**
- Pełne REST API wspierające metody GET, POST, PUT, PATCH, DELETE.
- **Swagger / OpenAPI**: Interaktywna dokumentacja dostępna pod adresem: `/api-docs.html`.

---

## 🌟 Punkty dodatkowe (Zrealizowane)

- [x] **Docker** — pełna konteneryzacja (PHP, Nginx, Tesseract).
- [x] **Kolejkowanie OCR** — przetwarzanie zadań OCR w tle (Laravel Queues).
- [x] **Testy** — pakiet testów jednostkowych i integracyjnych (`phpunit`).
- [x] **Autoryzacja** — system logowania (Simple Token Auth dla API).
- [x] **Prosty UI** — nowoczesny, responsywny interfejs zbudowany w Tailwind CSS.
- [x] **Generator Faktur** — wbudowane narzędzie do generowania testowych dokumentów PDF/IMG.

---

## ⚙️ Instrukcja uruchomienia

### **Metoda 1: Docker (Zalecane)**
```bash
# 1. Skopiuj env
cp .env.example .env

# 2. Uruchom kontenery
docker-compose up -d --build

# 3. Instalacja (wewnątrz kontenera)
docker exec -it smf-app composer install
docker exec -it smf-app php artisan key:generate
docker exec -it smf-app php artisan migrate --seed
```
Aplikacja dostępna pod: `http://localhost:8000`

### **Metoda 2: Lokalnie (PHP + Composer)**
1. Zainstaluj **Tesseract OCR** w systemie.
2. Skonfiguruj `.env` (klucz GROQ_API_KEY lub Ollama).
3. `composer install`
4. `php artisan migrate --seed`
5. `php artisan serve`

---

## 📄 Generator Faktur (Testy)
Aplikacja jest wyposażona we **wbudowany generator faktur**, który pozwala na tworzenie testowych plików PDF oraz obrazów (JPG/PNG). 
- Pozwala to na błyskawiczne przetestowanie modułu OCR bez konieczności szukania własnych faktur.
- Link do generatora znajduje się w górnym menu nawigacyjnym aplikacji.

---

## 🏗️ Architektura
Projekt podąża za wzorcem **Service Layer**:
- `OcrService`: Zarządza procesem OCR.
- `AiParserFactory`: Wybiera dostawcę AI (Groq/Ollama).
- `InvoiceService`: Logika biznesowa dotycząca zapisu i relacji.
- `ProcessOcrJob`: Kolejkowanie zadań OCR w tle (Bonus points).

---

## 🧪 Testowanie
Aby uruchomić testy:
```bash
php artisan test
```
lub bezpośrednio przez PHPUnit:
```bash
vendor/bin/phpunit
```
