# 🧾 SMF Young Tech Challenge — Invoice Processing System

Aplikacja Laravel do automatycznego zarządzania fakturami, zintegrowana z silnikiem OCR oraz Agentem AI do inteligentnej ekstrakcji danych.

## 📌 Wersje projektu

| Wersja | Opis | Tag |
|--------|------|-----|
| `v1.0.0` | Wersja z dnia 09.04 — oryginalna wersja rekrutacyjna | [v1.0.0](https://github.com/robertfisahn/smf-young-tech-challenge/releases/tag/v1.0.0) |
| `v1.0.1` | Dodano skrypty setup, poprawki .env.example | [v1.0.1](https://github.com/robertfisahn/smf-young-tech-challenge/releases/tag/v1.0.1) |

> Aby przetestować konkretną wersję:
> ```bash
> git clone https://github.com/robertfisahn/smf-young-tech-challenge.git
> cd smf-young-tech-challenge
> git checkout v1.0.0   # wersja rekrutacyjna z 09.04
> git checkout v1.0.1   # wersja z usprawnieniami
> git checkout main     # powrót do najnowszej
> ```

---

> ### ⚡ **SZYBKIE URUCHOMIENIE (Docker Hub)**
> Nie wymaga budowania obrazu lokalnie — pobiera gotowy obraz z Docker Hub.
> ```bash
> git clone https://github.com/robertfisahn/smf-young-tech-challenge.git
> cd smf-young-tech-challenge
> cp .env.example .env
> # Uzupełnij GROQ_API_KEY w pliku .env
> docker-compose -f docker-compose.hub.yml up -d
> ```
> Aplikacja dostępna pod: `http://localhost:8000`

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
Aplikacja wykorzystuje inteligentnego agenta AI, który analizuje surowy tekst z OCR i zamienia go na ustrukturyzowany JSON.
- **Multi-Provider**: Wsparcie dla **Groq API** oraz **Ollama**.
- **AiPromptService**: Centralny system reguł (promptów) zapewniający precyzję ekstrakcji danych.

### **5. Baza danych — SQLite**
- Schemat bazy obejmuje tabele: `contractors`, `invoices`, `invoice_items`, `payments`.

### **6. Prezentacja wyników (REST API + Swagger)**
- Pełne REST API wspierające metody GET, POST, PUT, PATCH, DELETE.
- **Swagger / OpenAPI**: Interaktywna dokumentacja dostępna pod adresem: `/api-docs.html`.

### **7. Wymagania repozytorium GitHub**
Repozytorium publiczne: **smf-young-tech-challenge**
- ✅ README.md z instrukcją uruchomienia
- ✅ Opis architektury (sekcja poniżej oraz `ARCHITECTURE.md`)
- ✅ Przykładowa faktura testowa (`public/invoice_sample.pdf`)
- ✅ Plik `.env.example`

---

## ⚙️ Instrukcja uruchomienia

```bash
git clone https://github.com/robertfisahn/smf-young-tech-challenge.git
cd smf-young-tech-challenge
```

### **Metoda 1: Docker Lokalnie**

**Wymagania:** Docker Desktop

```bash
# 1. Skopiuj plik środowiskowy
cp .env.example .env

# 2. Uzupełnij GROQ_API_KEY w pliku .env

# 3. Uruchom kontenery
docker-compose up -d --build
```
Aplikacja dostępna pod: `http://localhost:8000`

---

### **Metoda 2: Lokalnie (skrypt setup)**

**Wymagania:**
- PHP >= 8.2
- Composer
- Tesseract OCR z paczką językową `pol` ([instrukcja instalacji](https://github.com/UB-Mannheim/tesseract/wiki))
- SQLite (zazwyczaj wbudowany w PHP)

**Windows (PowerShell):**
```powershell
# 1. Sklonuj repozytorium
git clone https://github.com/robertfisahn/smf-young-tech-challenge.git

cd smf-young-tech-challenge

# 2. Uruchom skrypt setup
./setup.ps1

# 3. Uzupełnij GROQ_API_KEY w pliku .env (skrypt go utworzy automatycznie)

# 4. Uruchom serwer
php artisan serve
```

**Linux / macOS (Bash):**
```bash
# 1. Sklonuj repozytorium
git clone https://github.com/robertfisahn/smf-young-tech-challenge.git

cd smf-young-tech-challenge

# 2. Uruchom skrypt setup
chmod +x setup.sh && ./setup.sh

# 3. Uzupełnij GROQ_API_KEY w pliku .env (skrypt go utworzy automatycznie)

# 4. Uruchom serwer
php artisan serve
```

Aplikacja dostępna pod: `http://localhost:8000`

---

## 🏗️ Architektura

Projekt podąża za wzorcem **Service Layer**:
- `OcrService` — zarządza procesem ekstrakcji tekstu (PDF/JPG/PNG).
- `AiParserFactory` — wybiera dostawcę AI (Groq/Ollama) i deleguje parsowanie.
- `AiPromptService` — centralne reguły promptów zapewniające precyzję danych.
- `InvoiceService` — logika biznesowa zapisu faktur i relacji między modelami.
- `ProcessOcrJob` — kolejkowanie zadań OCR w tle (Laravel Queues).

---

## 📄 Generator Faktur (Testy)

Aplikacja posiada wbudowany generator faktur testowych (PDF, JPG, PNG) — link dostępny w górnym menu nawigacyjnym. Gotowa faktura testowa znajduje się w `public/invoice_sample.pdf`.

---

## 🌟 Punkty dodatkowe (Zrealizowane)

- [x] **Docker** — pełna konteneryzacja (PHP, Nginx, Tesseract).
- [x] **Kolejkowanie OCR** — przetwarzanie zadań OCR w tle (Laravel Queues).
- [x] **Testy** — pakiet testów jednostkowych i integracyjnych (`phpunit`).
- [x] **Autoryzacja** — system logowania (Simple Token Auth dla API).
- [x] **Prosty UI** — nowoczesny, responsywny interfejs zbudowany w Tailwind CSS.
- [x] **Generator Faktur** — wbudowane narzędzie do generowania testowych dokumentów PDF/IMG.

---

## 🧪 Testowanie

```bash
php artisan test
# lub
vendor/bin/phpunit
```