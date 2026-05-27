# 🐉 Kobold As A Service (KAAS)

A procedural generation HTTP API that conjures whimsical Kobold RPG character sheets on demand. Every name, origin, and backstory is woven from [Polygen](https://polygen.org/) grammars — fetch a fresh kobold with a single request.

**Live at:** https://kaas.procionegobbo.it

## API

Send a `POST` request to `/api/generate-kobold` with a `language` body parameter.

```bash
curl -X POST https://kaas.procionegobbo.it/api/generate-kobold \
  -H "Content-Type: application/json" \
  -d '{"language": "en"}'
```

**Supported languages:** `en` (English), `it` (Italian, default)

**Rate limit:** 1 request per second per IP. Excess requests return `429 Too Many Requests`.

### Code examples

**PHP**
```php
$response = (new GuzzleHttp\Client())->post('https://kaas.procionegobbo.it/api/generate-kobold', [
    'json' => ['language' => 'en'],
]);
$kobold = json_decode((string) $response->getBody(), true);
```

**JavaScript**
```js
const response = await fetch('https://kaas.procionegobbo.it/api/generate-kobold', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ language: 'en' }),
});
const kobold = await response.json();
```

**Python**
```python
import requests
kobold = requests.post(
    'https://kaas.procionegobbo.it/api/generate-kobold',
    json={'language': 'en'},
).json()
```

### Throttle bypass

If you need to exceed the 1 req/s rate limit, add an `X-Bypass-Key` header with a pre-shared secret:

```bash
curl -X POST https://kaas.procionegobbo.it/api/generate-kobold \
  -H "Content-Type: application/json" \
  -H "X-Bypass-Key: your-secret-key" \
  -d '{"language": "en"}'
```

Requests carrying a valid key bypass the rate limiter entirely. Keys are configured server-side via the `THROTTLE_BYPASSERS` environment variable (comma-separated list). Contact the operator to request a key.

## Tech stack

- **PHP 8.4** / **Laravel 13**
- **[polygen-php](https://github.com/procionegobbo/polygen-php)** — PHP port of the Polygen grammar engine
- **Tailwind CSS v4** / **Vite**
- **Pest v4** for testing

## Local setup

```bash
composer run setup
```

This installs dependencies, copies `.env.example` to `.env`, generates an app key, runs migrations, and builds frontend assets.

To start the development server:

```bash
composer run dev
```

## Testing

```bash
php artisan test --compact
```

## Credits

Powered by [polygen-php](https://github.com/procionegobbo/polygen-php), a PHP port of [Polygen](https://polygen.org/) by Ulisse Spanò.
Inspired by the tabletop adventures and podcasts of [FumbleGDR](https://www.fumblegdr.it).
Made with ❤️ by [Federico "Procionegobbo" Maiorini](https://procionegobbo.it).
