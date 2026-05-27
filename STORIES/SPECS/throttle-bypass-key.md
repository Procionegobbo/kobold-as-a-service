# Throttle Bypass Key — Implementation Specification

## Feature Name & Description

Allow trusted callers to bypass the `kobold-api` rate limiter by presenting a pre-shared secret in an HTTP request header, enabling integrations and automated pipelines that cannot tolerate `429` responses without per-key rate-limit tiers.

**Current state.** The `kobold-api` rate limiter is registered in `AppServiceProvider::boot()` and always applies `Limit::perSecond(1)->by($request->ip())`. There is no escape hatch for trusted callers.

**In scope.**
- A new `THROTTLE_BYPASSERS` environment variable that accepts a comma-separated list of secret strings.
- A dedicated middleware class that inspects the `X-Bypass-Key` request header and, when it matches one of the configured secrets, short-circuits the throttle middleware for that request.
- Documentation on the home page (`resources/views/home.blade.php`) and in `README.md`.
- Full Pest test coverage.

**Out of scope.**
- Key rotation / expiry.
- Per-key rate-limit tiers.
- Any UI for managing keys.
- Admin authentication or database-backed key storage.

---

## Architecture / Design Overview

The `kobold-api` throttle is applied at the route level via `->middleware('throttle:kobold-api')` in `routes/api.php`. Laravel's `ThrottleRequests` middleware checks and increments the rate-limit counter unless the request is short-circuited before it.

The chosen approach is a **guard middleware** (`ThrottleBypassMiddleware`) that runs before `throttle:kobold-api` in the middleware chain. When a valid bypass key is present in the header, it removes `throttle:kobold-api` from the route's middleware stack for that request, so the rate limiter never fires and no counter is decremented.

```
Request
  │
  ▼
ThrottleBypassMiddleware          ← reads X-Bypass-Key header
  │   valid key found?
  ├── YES → strips throttle:kobold-api → passes to controller
  └── NO  → passes through
               │
               ▼
           throttle:kobold-api    ← normal rate-limit check
               │
               ▼
           KoboldGeneratorController
```

Key design decisions:

1. **Separate middleware class, not a closure in AppServiceProvider.** Keeps the rate-limiter definition clean; the bypass concern lives in its own file, matching the single-responsibility conventions already visible in the project.
2. **Header `X-Bypass-Key`.** Conventional `X-` prefix for custom headers; unambiguous name; consistent with the existing `Content-Type` header already shown in all documentation examples.
3. **Config-driven, not checked inline.** Reads from `config('app.throttle_bypassers')` (array) rather than parsing `env()` at request time, so it respects Laravel's config cache.
4. **Empty/unset means bypass is impossible.** When `THROTTLE_BYPASSERS` is absent or blank, the config value is an empty array and no header value can match.

---

## Configuration

### Environment variable

Add to `.env.example` after the existing `APP_` block:

```dotenv
# Comma-separated list of secret keys that skip the kobold-api rate limiter.
# Leave empty (or unset) to disable bypass entirely.
THROTTLE_BYPASSERS=
```

### Config binding

In `config/app.php`, add one entry inside the returned array alongside the other top-level keys:

```php
'throttle_bypassers' => array_filter(
    array_map('trim', explode(',', env('THROTTLE_BYPASSERS', ''))),
),
```

This converts the comma-separated string into a trimmed, filtered array at boot. `array_filter` removes empty strings so an unset variable yields `[]`.

---

## Data Model

No database tables or Eloquent models are introduced. Bypass keys exist only in environment configuration.

---

## Impact on Existing Code

| File | Action | Change |
|---|---|---|
| `config/app.php` | Modify | Add `throttle_bypassers` key (see above). |
| `.env.example` | Modify | Add `THROTTLE_BYPASSERS=` with comment. |
| `app/Http/Middleware/ThrottleBypassMiddleware.php` | Create | New middleware class (full implementation below). |
| `bootstrap/app.php` | Modify | Register `ThrottleBypassMiddleware` as a global API middleware alias. |
| `routes/api.php` | Modify | Prepend `throttle.bypass` to the `generate-kobold` route middleware chain. |
| `resources/views/home.blade.php` | Modify | Add "Throttle Bypass" sub-section inside the `#api` section. |
| `README.md` | Modify | Add "Throttle Bypass" subsection under the `## API` section. |
| `tests/Feature/ThrottleBypassTest.php` | Create | New Pest feature test (see Testing section). |

---

## Middleware

### `app/Http/Middleware/ThrottleBypassMiddleware.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ThrottleBypassMiddleware
{
    /**
     * Skip the kobold-api throttle when the request carries a valid bypass key.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $bypassers = config('app.throttle_bypassers', []);

        if (
            $bypassers !== []
            && in_array($request->header('X-Bypass-Key'), $bypassers, strict: true)
        ) {
            $request->route()?->forgetMiddleware('throttle:kobold-api');
        }

        return $next($request);
    }
}
```

`forgetMiddleware('throttle:kobold-api')` removes the named middleware from the route's resolved middleware list for the current request only, leaving the rate-limit counter untouched for other callers.

### Registration in `bootstrap/app.php`

Add an alias inside the `->withMiddleware()` callback:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'throttle.bypass' => \App\Http\Middleware\ThrottleBypassMiddleware::class,
    ]);
})
```

### Route wiring in `routes/api.php`

Prepend the bypass middleware so it runs before the throttle check:

```php
Route::post('/generate-kobold', [KoboldGeneratorController::class, 'generate'])
    ->middleware(['throttle.bypass', 'throttle:kobold-api'])
    ->name('kobold.generate');
```

---

## Validation Rules

No new request body fields. The `X-Bypass-Key` header is read and compared but never validated through a Form Request — an unrecognised or absent key simply means no bypass occurs. This is intentional: leaking a `422` response on bad header values would confirm the existence of the bypass mechanism to unauthenticated callers.

---

## Authorization & Security

- **Who can bypass.** Any caller that knows one of the `THROTTLE_BYPASSERS` secrets. Keys must be kept out of version control; they live solely in server environment variables.
- **Key strength.** The spec does not enforce minimum length, but documentation should recommend at least 32 random characters (e.g., output of `openssl rand -hex 32`).
- **Timing-safe comparison.** The in-array strict check used above is sufficient for this threat model (keys are not cryptographic MACs), but if a future iteration requires timing-safe behaviour, replace with `hash_equals()` in a loop.
- **Header name is not a secret.** The header name `X-Bypass-Key` may be documented publicly; security relies entirely on the secrecy of the key values.
- **No CSRF concern.** This is a stateless JSON API; CSRF tokens are not involved.
- **Rate limiting not decremented.** Because the throttle middleware is removed before it fires, bypassing a request leaves the rate-limit counters for that IP unchanged, avoiding counter poisoning.
- **Empty config = no bypass possible.** Deployments that do not set `THROTTLE_BYPASSERS` are not affected; the feature is opt-in.

---

## Home Page Changes

Inside `resources/views/home.blade.php`, add a new paragraph below the existing rate-limit notice in the `#api` section (after line 60, the `<p>` that says `Rate limit: 1 request per second`):

```blade
<p class="mt-4 text-sm text-[#706f6c] dark:text-[#A1A09A]">
    Need to exceed the rate limit? Add an
    <code class="rounded bg-[#1b1b18]/5 px-1.5 py-0.5 text-sm dark:bg-white/10">X-Bypass-Key</code>
    header with a pre-shared secret configured on the server to skip throttling entirely.
    Contact the operator to request a key.
</p>
```

No new section or heading is required; the note belongs inline with the existing rate-limit description.

---

## README Changes

In `README.md`, add the following subsection immediately after the closing paragraph of the `## API` section (after the Python code block, before `## Tech stack`):

```markdown
### Throttle bypass

If you need to exceed the 1 req/s rate limit, add an `X-Bypass-Key` header with a pre-shared secret:

```bash
curl -X POST https://kaas.procionegobbo.it/api/generate-kobold \
  -H "Content-Type: application/json" \
  -H "X-Bypass-Key: your-secret-key" \
  -d '{"language": "en"}'
```

Requests carrying a valid key bypass the rate limiter entirely. Keys are configured server-side via the `THROTTLE_BYPASSERS` environment variable (comma-separated list). Contact the operator to request a key.
```

---

## Testing

Create `tests/Feature/ThrottleBypassTest.php` using `php artisan make:test --pest ThrottleBypassTest`.

Follow the `describe()` + `test()` structure already used in `KoboldGeneratorApiTest.php`.

### Test cases

```php
<?php

describe('Throttle bypass middleware', function () {

    test('valid bypass key skips the rate limiter and returns 200', function () {
        config(['app.throttle_bypassers' => ['secret-key']]);

        $response = $this->postJson(
            route('kobold.generate'),
            ['language' => 'en'],
            ['X-Bypass-Key' => 'secret-key'],
        );

        $response->assertOk();
    });

    test('second consecutive request with valid bypass key also returns 200', function () {
        config(['app.throttle_bypassers' => ['secret-key']]);

        $this->postJson(route('kobold.generate'), ['language' => 'en'], ['X-Bypass-Key' => 'secret-key']);

        $response = $this->postJson(
            route('kobold.generate'),
            ['language' => 'en'],
            ['X-Bypass-Key' => 'secret-key'],
        );

        $response->assertOk();
    });

    test('invalid bypass key does not skip the rate limiter', function () {
        config(['app.throttle_bypassers' => ['secret-key']]);

        $response = $this->postJson(
            route('kobold.generate'),
            ['language' => 'en'],
            ['X-Bypass-Key' => 'wrong-key'],
        );

        // First request should still succeed (within rate limit).
        $response->assertOk();
    });

    test('absent X-Bypass-Key header does not skip the rate limiter', function () {
        config(['app.throttle_bypassers' => ['secret-key']]);

        $response = $this->postJson(route('kobold.generate'), ['language' => 'en']);

        $response->assertOk();
    });

    test('bypass is impossible when THROTTLE_BYPASSERS is empty', function () {
        config(['app.throttle_bypassers' => []]);

        $response = $this->postJson(
            route('kobold.generate'),
            ['language' => 'en'],
            ['X-Bypass-Key' => 'any-key'],
        );

        // First request still succeeds; no bypass, but also within rate limit.
        $response->assertOk();
    });

    test('one of multiple configured keys is accepted', function () {
        config(['app.throttle_bypassers' => ['key-one', 'key-two', 'key-three']]);

        $response = $this->postJson(
            route('kobold.generate'),
            ['language' => 'en'],
            ['X-Bypass-Key' => 'key-two'],
        );

        $response->assertOk();
    });

    test('whitespace-padded key in config is trimmed and matched correctly', function () {
        config(['app.throttle_bypassers' => ['trimmed-key']]);

        $response = $this->postJson(
            route('kobold.generate'),
            ['language' => 'en'],
            ['X-Bypass-Key' => 'trimmed-key'],
        );

        $response->assertOk();
    });

});
```

Notes on the test strategy:
- `config([...])` overrides are applied per-test without touching the `.env` file, consistent with how other Laravel feature tests mock config at runtime.
- The rate limiter fires at 1 req/s per IP. In the test environment, consecutive requests in the same test function arrive within the same second, so the second-request test is the canonical proof that bypass actually works. Without the bypass, the second request would return `429`.
- The `ThrottleRequests` middleware uses the cache to track counters; no additional cache mocking is needed because `RefreshDatabase` is not used and the default `array` cache driver is active in the testing environment.

---

## Success Criteria

- [ ] `THROTTLE_BYPASSERS=key1,key2` set in `.env` — a request with `X-Bypass-Key: key1` returns `200` even when sent twice within the same second from the same IP.
- [ ] A request with `X-Bypass-Key: unknown` is subject to the normal 1 req/s throttle.
- [ ] `THROTTLE_BYPASSERS` unset or empty — no header value bypasses the throttle.
- [ ] `config('app.throttle_bypassers')` returns an array (empty when unset, entries trimmed when set).
- [ ] `php artisan test --compact --filter=ThrottleBypass` passes with all test cases green.
- [ ] The home page `#api` section contains a sentence documenting `X-Bypass-Key`.
- [ ] `README.md` contains a `### Throttle bypass` subsection under `## API` with a `curl` example showing `X-Bypass-Key`.
- [ ] No existing tests are broken (`php artisan test --compact` passes in full).

---

## Future Considerations

- **Per-key rate-limit tiers.** The bypass middleware could be extended to apply a higher (but still limited) `Limit` instead of removing throttling entirely, using the key as the `->by()` discriminator.
- **Key rotation.** A `THROTTLE_BYPASSERS_OLD` variable or overlapping validity window would allow zero-downtime key rotation without a deployment window.
- **Database-backed keys.** If the number of trusted callers grows, a `bypass_keys` table and a lightweight admin UI would replace the environment variable approach.
