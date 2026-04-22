# 🏗️ Architektura — Invoice Processing System

Projekt realizuje automatyczne przetwarzanie faktur w trzech etapach: ekstrakcja tekstu przez OCR, parsowanie danych przez agenta AI, zapis do bazy przez warstwę serwisową. Poniżej opis kluczowych komponentów.

---

## 📦 Warstwa serwisowa (Service Layer)

- `OcrService` — zarządza procesem ekstrakcji tekstu z dokumentów (PDF/JPG/PNG).
- `AiParserFactory` — wybiera dostawcę AI (Groq lub Ollama) i deleguje parsowanie danych.
- `AiPromptService` — centralne reguły promptów zapewniające precyzję ekstrakcji.
- `InvoiceService` — logika biznesowa zapisu faktur i relacji między modelami.
- `ProcessOcrJob` — kolejkowanie zadań OCR w tle (Laravel Queues).

---

## 🗄️ Baza danych — SQLite

Schemat bazy obejmuje tabele: `contractors`, `invoices`, `invoice_items`, `payments`.

---

## 🌐 REST API + Swagger

Pełne REST API wspierające metody GET, POST, PUT, PATCH, DELETE.

Interaktywna dokumentacja Swagger / OpenAPI dostępna pod: `/api-docs.html`.

---

## 🔧 Silnik OCR

Do ekstrakcji tekstu wykorzystano wyłącznie darmowe rozwiązania open-source:

- **smalot/pdfparser** — szybkie wyciąganie tekstu bezpośrednio z plików PDF.
- **thiagoalessio/tesseract-ocr** — wrapper dla Tesseract OCR (JPG/PNG oraz fallback dla PDF).

---

## 🤖 Agent AI — inteligentna ekstrakcja

Proces analizy danych przebiega w trzech krokach:

1. **Multi-Provider Layer** — wsparcie dla Groq API (Llama 3.1 70b, chmura) oraz Ollama (Llama 3.1 8b, lokalnie). Wybór dostawcy odbywa się w interfejsie aplikacji.
2. **Contextual Prompting** — `AiPromptService` dostarcza precyzyjne instrukcje dostosowane do specyfiki polskich faktur, minimalizując halucynacje modeli.
3. **Smart Environment Bridge**: Projekt posiada wbudowany mechanizm wykrywania środowiska Docker (`/.dockerenv`). Dzięki temu aplikacja inteligentnie przełącza się między `localhost` a `host.docker.internal`, co rozwiązuje problemy z łącznością sieciową bez ingerencji użytkownika w plik `.env`. Szczegółowy opis problemu i rozwiązania: [docs/DOCKER_ENV_GUIDE.md](docs/DOCKER_ENV_GUIDE.md)

> **Mappowanie ustawień:** Wszystkie parametry AI (nazwy modeli, klucze API) są centralnie zarządzane w pliku `config/services.php`. Wartości te są pobierane z pliku `.env`, co pozwala na łatwe przełączanie modeli (np. Llama 3.1 vs Qwen) bez dotykania kodu źródłowego.

---

## 🧪 Generator faktur testowych

Aplikacja zawiera wbudowany generator faktur testowych (PDF, JPG, PNG) — dostępny z górnego menu nawigacyjnego. Gotowa faktura testowa znajduje się w `public/invoice_sample.pdf`.