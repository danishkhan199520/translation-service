# Translation Service -- Laravel 12

A high-performance translation management API built with Laravel 12,
supporting translations, tags, caching, export, and Sanctum
authentication.

## Features

-   Create / Update translations per locale
-   Fetch translation for one or all locales
-   Tag filtering
-   Export translations as JSON
-   Sanctum authentication
-   Optimized MySQL indexing
-   Supports large datasets

## Installation

``` bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
```

## Authentication (Login)

POST /api/v1/auth/login

``` json
{
  "email": "admin@example.com",
  "password": "password"
}
```

## Create Translation

POST /api/v1/translations

``` json
{
  "key": "welcome_message",
  "locale": "en",
  "value": "Welcome!",
  "tags": ["web"]
}
```

## Get Translation

GET /api/v1/translations/welcome_message\
GET /api/v1/translations/welcome_message?locale=en

## Export Locale

GET /api/v1/export/en
