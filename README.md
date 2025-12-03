# Translation Service -- Laravel 12

A scalable and high-performance **Translation Management API** built
using **Laravel 12**, with features such as localization, caching,
tagging, exporting, and dynamic translation key management.

This service is designed for multilingual applications handling **large
datasets** (100k+ records) with near-instant response times through
Redis caching, optimized queries, and clean separation of translation
keys, translations, languages, and tags.

------------------------------------------------------------------------

## 📑 Table of Contents

-   Features\
-   Requirements\
-   Installation\
-   Environment Configuration\
-   Database Migrations & Seeders\
-   Authentication (Sanctum)\
-   API Documentation\
-   Caching System\
-   Project Structure\
-   Optimization & Scaling\
-   License

------------------------------------------------------------------------

## 🚀 Features

### Translation Management

-   Create translation keys\
-   Add/update translations per locale\
-   Optional tagging system\
-   Context support for phrases\
-   Export translations by locale

### Performance Enhancements

-   Redis-based caching for fast responses\
-   Indexed queries\
-   Optimized for large datasets

### Authentication

-   Laravel Sanctum (token-based auth)

------------------------------------------------------------------------

## 🧰 Requirements

-   PHP 8.2+\
-   Composer 2.x\
-   MySQL / MariaDB\
-   Redis (recommended)\
-   Laravel 12.x

------------------------------------------------------------------------

## 🏗 Installation

### 1️⃣ Clone repository

``` bash
git clone https://github.com/your-repo/translation-service.git
cd translation-service
```

### 2️⃣ Install dependencies

``` bash
composer install
```

### 3️⃣ Create environment file

``` bash
cp .env.example .env
```

### 4️⃣ Generate application key

``` bash
php artisan key:generate
```

------------------------------------------------------------------------

## ⚙ Environment Configuration

### Configure database

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=translation_db
    DB_USERNAME=root
    DB_PASSWORD=

### Enable Redis cache (optional but recommended)

    CACHE_DRIVER=redis
    REDIS_CLIENT=phpredis

------------------------------------------------------------------------

## 🗄 Database Migrations & Seeders

### Run migrations

``` bash
php artisan migrate
```

### Seed required data

``` bash
php artisan db:seed
```

Or separately:

``` bash
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=LanguageSeeder
php artisan db:seed --class=LargeTranslationSeeder
```

------------------------------------------------------------------------

## 🔐 Authentication (Sanctum)

### Install Sanctum

``` bash
composer require laravel/sanctum
```

### Publish Sanctum

``` bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

### Add trait in User model

``` php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable;
}
```

------------------------------------------------------------------------

# 📘 API Documentation

Base URL:

    /api/v1

All protected routes require:

    Authorization: Bearer {token}
    Accept: application/json

------------------------------------------------------------------------

## 1. Authentication (Login)

### POST /auth/login

``` json
{
    "email": "admin@example.com",
    "password": "password"
}
```

Response:

``` json
{
    "token": "1|xxxx",
    "user": {
        "id": 1,
        "email": "admin@example.com"
    }
}
```

------------------------------------------------------------------------

## 2. Create Translation

### POST /translations

``` json
{
    "key": "welcome_message",
    "locale": "en",
    "value": "Welcome to our platform!",
    "tags": ["web", "home"]
}
```

------------------------------------------------------------------------

## 3. Update Translation

### PUT /translations/{key}

``` json
{
    "locale": "fr",
    "value": "Bienvenue sur notre plateforme!",
    "tags": ["web"]
}
```

------------------------------------------------------------------------

## 4. Get Translation

### All locales

GET `/translations/welcome_message`

### Specific locale

GET `/translations/welcome_message?locale=en`

------------------------------------------------------------------------

## 5. List Translations (tag filter)

GET `/translations?tags=mobile,auth`

------------------------------------------------------------------------

## 6. Export Locale

GET `/export/en`

Response:

``` json
{
    "welcome_message": "Welcome",
    "login_button": "Login"
}
```

------------------------------------------------------------------------

## ⚡ Caching System

### Cache Keys

-   `translations_en`
-   `translations_fr`
-   `export_en`

### Auto-invalidated when:

-   A translation is created\
-   A translation is updated\
-   Tags change

------------------------------------------------------------------------

## 📂 Project Structure

    app/
     ├── Http/Controllers/Api/V1/
     ├── Models/
     ├── Services/
    database/
     ├── migrations/
     ├── seeders/
    routes/
     └── api.php
    bootstrap/
     └── app.php

------------------------------------------------------------------------

## 🚀 Optimization & Scaling

-   Redis for 2--10ms response time\
-   Database indexing on:
    -   translation_key_id\
    -   language_id\
    -   key\
    -   code\
-   Bulk insert for large seeding\
-   Zero N+1 queries with eager loading

------------------------------------------------------------------------

## 📄 License

MIT License.
