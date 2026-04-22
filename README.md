# 🧾 SMF Young Tech Challenge — Invoice Processing System

Aplikacja Laravel do automatycznego przetwarzania faktur. Silnik OCR ekstrahuje tekst z przesłanych dokumentów (PDF, JPG, PNG), a agent AI (Groq lub Ollama) parsuje dane strukturalne — numer faktury, daty, pozycje, kwoty, dane kontrahenta. Wyniki trafiają do zarządzalnego dashboardu z historią dokumentów i pełnym REST API.

🏗️ [Opis architektury](ARCHITECTURE.md)

---

<details open>
<summary>⚡ <strong>Szybkie uruchomienie — Docker Hub (zalecane)</strong></summary>
<br>

Najszybszy sposób — pobiera gotowy, skompilowany obraz z Docker Hub. Nie wymaga budowania lokalnie ani instalacji PHP.

**Wymagania:** Docker Desktop

```bash
git clone https://github.com/robertfisahn/smf-young-tech-challenge.git
cd smf-young-tech-challenge
cp .env.example .env
```

Otwórz plik `.env` i uzupełnij jedną zmienną:

| Zmienna | Co ustawić |
|---------|-----------|
| `GROQ_API_KEY` | Wygeneruj darmowy klucz na [console.groq.com/keys](https://console.groq.com/keys) |

> **Uwaga:** `TESSERACT_PATH`, `DB_DATABASE` i `OLLAMA_URL` są automatycznie ustawiane przez Docker — nie trzeba ich zmieniać.

```bash
docker-compose -f docker-compose.hub.yml up -d
```

✅ Aplikacja dostępna pod: **http://localhost:8000**
🔑 Dane logowania: `user@example.com` / `user1234`

> **💡 Ollama w trybie Docker:** Aby korzystać z lokalnego modelu AI, Ollama musi być uruchomiona z `OLLAMA_HOST=0.0.0.0`. Szczegóły w sekcji [Konfiguracja Ollama](#-konfiguracja-ollama-lokalny-model-ai).

</details>

---

<details>
<summary>🔨 <strong>Uruchomienie — Docker build lokalny</strong></summary>
<br>

Buduje obraz Dockera z kodu źródłowego zamiast pobierania z Docker Hub. Przydatne jeśli chcesz wprowadzić własne zmiany w kodzie przed uruchomieniem.

**Wymagania:** Docker Desktop

```bash
git clone https://github.com/robertfisahn/smf-young-tech-challenge.git
cd smf-young-tech-challenge
cp .env.example .env
# Uzupełnij GROQ_API_KEY w pliku .env
docker-compose up -d --build
```

✅ Aplikacja dostępna pod: **http://localhost:8000**
🔑 Dane logowania: `user@example.com` / `user1234`

</details>

---

<details>
<summary>⚙️ <strong>Uruchomienie — lokalnie bez Dockera</strong></summary>
<br>

Uruchamia aplikację bezpośrednio na hoście, bez kontenerów. Wymaga ręcznej instalacji zależności.

**Wymagania:**
- PHP >= 8.2 z rozszerzeniem SQLite
- Composer
- Tesseract OCR z paczką językową `pol` ([instrukcja instalacji](https://github.com/UB-Mannheim/tesseract/wiki))

**Konfiguracja `.env`**

| Zmienna | Co ustawić | Domyślna wartość |
|---------|-----------|-----------------|
| `GROQ_API_KEY` | Klucz API z [console.groq.com/keys](https://console.groq.com/keys) | `your_groq_api_key_here` |
| `TESSERACT_PATH` | Ścieżka do Tesseract OCR | `C:/Program Files/Tesseract-OCR/tesseract.exe` |

> **Tip:** Na Linux/macOS ustaw `TESSERACT_PATH=tesseract` (jeśli jest w PATH).

**Windows (PowerShell)**
```powershell
git clone https://github.com/robertfisahn/smf-young-tech-challenge.git
cd smf-young-tech-challenge
./setup.ps1
php artisan serve
```

**Linux / macOS**
```bash
git clone https://github.com/robertfisahn/smf-young-tech-challenge.git
cd smf-young-tech-challenge
chmod +x setup.sh && ./setup.sh
php artisan serve
```

✅ Aplikacja dostępna pod: **http://localhost:8000**
🔑 Dane logowania: `user@example.com` / `user1234`

> **💡 Ollama (opcjonalnie):** Zainstaluj [Ollama](https://ollama.com), pobierz model (`ollama pull llama3.1`) i upewnij się, że działa na porcie `11434`. Wybór między Groq a Ollama odbywa się w interfejsie aplikacji.

</details>

---

<details>
<summary>🤖 <strong>Konfiguracja Ollama (lokalny model AI)</strong></summary>
<br>

Aplikacja obsługuje lokalne modele AI przez [Ollama](https://ollama.com) jako alternatywę dla Groq — dane nie opuszczają Twojego komputera.

**1. Instalacja i model**

1. Pobierz Ollama: [ollama.com](https://ollama.com)
2. Pobierz rekomendowany model (najlepszy stosunek szybkości do jakości ekstrakcji):
   ```bash
   ollama pull llama3.1
   ```
3. Ustaw model w `.env`:
   ```
   OLLAMA_MODEL=llama3.1
   ```

> Jeśli `llama3.1` (8b) jest zbyt wolny, spróbuj `llama3.2:3b`. Na mocnym sprzęcie możesz przetestować `qwen2.5:7b`.

**2. Udostępnianie dla Dockera**

Domyślnie Ollama słucha tylko na `127.0.0.1`. Aby kontener Docker mógł się z nią połączyć:

*Windows (PowerShell):*
```powershell
[System.Environment]::SetEnvironmentVariable("OLLAMA_HOST", "0.0.0.0", "User")
```
Po wykonaniu komendy zrestartuj Ollama (Quit w trayu i uruchom ponownie).

*Linux / macOS:*
```bash
OLLAMA_HOST=0.0.0.0 ollama serve
```

**3. Smart-Config — automatyczne wykrywanie środowiska**

Projekt wykrywa automatycznie czy działa w Dockerze i przełącza się między `localhost` a `host.docker.internal`. Nie musisz zmieniać `OLLAMA_URL` przy zmianie trybu uruchomienia.

</details>

---

<details>
<summary>📌 <strong>Wersje projektu</strong></summary>
<br>

| Wersja | Opis | Tag |
|--------|------|-----|
| `v1.0.1` | Skrypty setup, poprawki .env.example | [v1.0.1](https://github.com/robertfisahn/smf-young-tech-challenge/releases/tag/v1.0.1) |
| `v1.0.0` | Oryginalna wersja rekrutacyjna (09.04) | [v1.0.0](https://github.com/robertfisahn/smf-young-tech-challenge/releases/tag/v1.0.0) |

```bash
git checkout v1.0.0   # wersja rekrutacyjna
git checkout main     # powrót do najnowszej
```

</details>