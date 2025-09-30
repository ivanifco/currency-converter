# Currency Converter API (Laravel)

## Project Overview

This is a **Laravel-based currency conversion API** that:

- Converts currencies using the Fixer.io free API (`/latest`).
- Calculates conversions manually (free plan compatible).
- Stores conversions in a **MySQL** database.
- Provides a **RESTful JSON API endpoint**.
- Includes **unit and feature tests**.

## Tech Stack

- **Backend:** Laravel 10 (PHP 8.2 via Sail)
- **Database:** MySQL (via Docker/Sail)
- **HTTP Client:** Laravel `Http` facade
- **Logging:** Laravel `Log`
- **Testing:** Laravel Feature Tests

## Requirements

- Docker & Docker Compose
- PHP 8.2+
- Composer

## Setup Instructions

### 1. Clone Project

```bash
git clone https://github.com/ivanifco/currency-converter
cd currency-converter
```
### 2. Environment Configuration

Copy `.env.example` to `.env`:

```bash
cp .env.example .env
```

In .env file, set database credentials (compatible with Sail):

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=<your_db_name>
DB_USERNAME=<username>                 
DB_PASSWORD=<password>
```

Also, add your Fixer.io API key:

```env
FIXER_API_KEY=your_fixer_api_key_here
```

### 3. Install Laravel & Sail dependencies

```bash
composer install
```

### 4. Start Docker Containers

```bash
./vendor/bin/sail up -d
```

This starts:
- **Laravel app** on port `80`
- **MySQL** on port `3306`
### 5. Run Migrations

```bash
./vendor/bin/sail artisan migrate
```

Generate APP_KEY

```bash
./vendor/bin/sail artisan key:generate
```

## API Endpoint

### POST `/api/convert`

**Payload:**

```json
{
  "source_currency": "USD",
  "target_currency": "EUR",
  "value": 100
}
```

**Response:**

```json
{
  "message": "Conversion successful",
  "data": {
    "id": 1,
    "source_currency": "USD",
    "target_currency": "EUR",
    "value": 100,
    "converted_value": 95,
    "rate": 0.95,
    "created_at": "2025-09-28T20:00:00Z",
    "updated_at": "2025-09-28T20:00:00Z"
  }
}
```

### Testing the API

Using `curl`:

```bash
curl -X POST http://localhost/api/convert \
  -H "Content-Type: application/json" \
  -d '{"source_currency":"USD","target_currency":"EUR","value":100}'
```

## Testing

Run all feature tests:

```bash
./vendor/bin/sail artisan test
```

- Tests include:
  - Success case → stores conversion
  - API failure → proper error JSON
  - Validation → required fields check

## Logging

- Logs are stored in:

```
storage/logs/laravel.log
```

- Successful conversion: `Log::info("Conversion success", ...)`
- API failure: `Log::error("Fixer API failed", ...)`

## Project Structure

```
app/
├── Http/Controllers/CurrencyController.php
├── Models/Conversion.php
routes/
├── api.php
database/
├── migrations/
tests/
├── Feature/ConversionTest.php
```

## Notes

- Uses **Fixer free plan**, `/latest` endpoint only.
- Conversion is calculated manually (`target_rate / source_rate * value`).
- Database refresh for testing handled via `RefreshDatabase` trait.

