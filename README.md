
# 📦 Laravel Idempotency

A professional Laravel package for handling **Idempotency Keys**.  
It ensures that duplicate requests (like hitting the payment button twice) are processed only once, safely returning the same response.

---

## 🚀 Features
- Support for `Idempotency-Key` header in requests (customizable)
- Three storage drivers:
    - Redis → blazing fast, ideal for high‑traffic environments
    - Database → persistent and easy to monitor
    - Cache → simple and flexible using Laravel’s cache system
- TTL (time‑to‑live) support for automatic expiration
- Artisan command to purge expired keys (Database driver only)
- Full Pest test coverage (Feature, Unit, Command tests)

---

## 📥 Installation
```bash
composer require codenaline/laravel-idempotency
```

---

## ⚙️ Configuration
Publish the config file:

```bash
php artisan vendor:publish --tag=idempotency-config
```

### Example `config/idempotency.php`
```php
return [

    'header' => env('IDEMPOTENCY_HEADER', 'Idempotency-Key'),

    'default' => env('IDEMPOTENCY_DRIVER', 'redis'),

    'drivers' => [

        'redis' => [
            'connection' => env('IDEMPOTENCY_REDIS_CONNECTION', 'default'),
            'ttl' => env('IDEMPOTENCY_TTL', 60),
        ],

        'database' => [
            'connection' => env('IDEMPOTENCY_DB_CONNECTION', config('database.default')),
            'table' => env('IDEMPOTENCY_DB_TABLE', 'idempotency_keys'),
            'ttl' => env('IDEMPOTENCY_TTL', 60),
        ],

        'cache' => [
            'store' => env('IDEMPOTENCY_CACHE_STORE'),
            'ttl' => env('IDEMPOTENCY_TTL', 60),
        ],
    ],
];

```

---

## 🔑 Custom Header
By default, the package looks for the header:

```http
Idempotency-Key: abc123
```

You can change the header name in `config/idempotency.php`:

```php
'header' => env('IDEMPOTENCY_HEADER', 'Idempotency-Key'),
```

For example, if you want to use `X-Request-Id` instead:

```env
IDEMPOTENCY_HEADER=X-Request-Id
```

Then your requests should include:

```http
POST /payments
X-Request-Id: abc123
```

---

## 🛠️ Usage
Add the middleware to your routes:


Attach the middleware to your routes to enable idempotency:

```php
Route::post('/payments', [PaymentController::class, 'store'])
    ->middleware('idempotency');
```

By default, the middleware will use the **TTL** value defined in your configuration file (`config/idempotency.php`).

You can override the TTL for a specific route by passing it as a parameter to the middleware:

```php
Route::post('/payments', [PaymentController::class, 'store'])
    ->middleware('idempotency:120'); // TTL = 120 seconds
```

This allows you to fine‑tune expiration times depending on the sensitivity of each endpoint.  
For example:
- Short TTL (e.g. 30 seconds) for lightweight requests.
- Longer TTL (e.g. 600 seconds) for heavy operations like payment processing.


Each request must include the idempotency header.  
If the same key is reused, the previous response will be returned instead of re‑processing.

---

## 🧹 Purging Expired Keys
- Redis / Cache drivers → no purge needed, TTL is handled automatically by the storage engine.
- Database driver → purge is required to clean up expired rows.

Run the Artisan command manually:
```bash
php artisan idempotency:purge
```

Schedule it in `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule): void
{
    // Run every hour
    $schedule->command('idempotency:purge')->hourly();

    // Or daily
    // $schedule->command('idempotency:purge')->daily();
}
```

---

## 🧪 Testing
This package ships with full Pest tests:
- Feature tests for middleware flow
- Unit tests for each driver (Cache, Database, Redis)
- Command tests for purging expired keys (Database driver only)

---

## 📌 Summary
- Prevents duplicate request execution
- Supports three drivers: Redis, Database, Cache
- TTL expiration included
- Header name customizable (`Idempotency-Key` by default)
- Purge command only needed for Database driver, can be scheduled in the Laravel Kernel
- Ready for production with full test coverage

---

## 🤝 Contributing
Pull requests are welcome! Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## 👨🏻‍💻 Credits

- [Mahdi Rezaei](https://github.com/mahdirezaei-dev)

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
