# Kobold Generator API — Implementation Specification

## Feature Name & Description

**Summary:** A single HTTP endpoint that accepts an optional language code and generates a random kobold character sheet as a JSON object, using the `procionegobbo/polygen-php` grammar engine.

**Current state:** The project contains two Polygen grammar files (`grm/kobold_json_it.grm`, `grm/kobold_json_en.grm`) and a passing test suite that exercises both grammars directly. There is no HTTP surface at all — `routes/web.php` only serves the default welcome view, and no controller or service class exists yet.

**In scope:**
- `POST /api/generate-kobold` route.
- A dedicated service class that wraps `Polygen\Polygen` and resolves the correct grammar file.
- A Form Request class for input validation.
- A controller that returns a JSON response.
- Feature tests covering the happy paths and all error states.

**Out of scope:**
- Authentication or API keys.
- Persisting generated kobolds to a database.
- A seed parameter (see "Open Questions" resolution below).
- Any front-end or UI changes.

---

## Open Questions — Resolved

**Seed parameter.** The draft mentions an optional `seed` to make generation deterministic. `Polygen\Polygen::generate()` and `Generator::run()` accept no seed argument and rely entirely on PHP's internal `rand()`. The library provides no way to supply a seed that would guarantee reproducibility. The seed parameter is **removed from this spec**. It is documented under "Future Considerations" at the end.

**Route prefix.** The project has only `routes/web.php` with no `api.php`. Because the endpoint is stateless and returns JSON, it belongs on an `api` route group. The spec registers it in `routes/api.php`, which is the standard Laravel convention. The `bootstrap/app.php` `withRouting()` call must be updated to include this file.

**HTTP verb.** `POST` is kept as specified. Generation is not idempotent (same input, different output), but `POST` is semantically correct for "perform an action and return a result."

**Default language.** When no `language` is provided, or when no grammar file exists for the requested language, the endpoint falls back to `grm/kobold_json_it.grm`. This matches the draft's stated intent.

---

## Architecture / Design Overview

The feature sits entirely within Laravel's HTTP layer. No queue, no persistence, no event.

```
HTTP POST /api/generate-kobold
        |
        v
GenerateKoboldRequest   (validates 'language' input)
        |
        v
KoboldGeneratorController::generate()
        |
        v
KoboldGeneratorService::generate(string $language): array
        |  - resolves grammar file path
        |  - instantiates Polygen::fromFile()
        |  - calls ->generate()
        |  - JSON-decodes the string returned by Polygen
        v
JsonResponse  (the decoded associative array, HTTP 200)
```

**Key design decisions:**
- The service class is thin and single-purpose. It is not bound through the service container because it has no constructor dependencies and no interface is required for testing at this scale — it can be newed directly in the controller or injected via type hint (Laravel will auto-resolve it).
- `Polygen::fromFile()` throws a `\RuntimeException` if the file cannot be read. The controller catches this and returns a 500 with a structured error body.
- `json_decode()` is called on Polygen's output. If the grammar ever produces malformed JSON (which the existing test suite guards against), the controller returns a 500.

---

## Configuration

No new environment variables or feature flags are introduced. The grammar directory path is fixed at `base_path('grm')` and is not configurable. The supported language codes are implicitly defined by which `kobold_json_{code}.grm` files exist on disk.

---

## Data Model

No persistence is required. No database tables, migrations, or models are created.

---

## Impact on Existing Code

| Action | File path |
|--------|-----------|
| Create | `routes/api.php` |
| Modify | `bootstrap/app.php` — add `api:` key to `withRouting()` |
| Create | `app/Http/Requests/GenerateKoboldRequest.php` |
| Create | `app/Http/Controllers/KoboldGeneratorController.php` |
| Create | `app/Services/KoboldGeneratorService.php` |
| Create | `tests/Feature/KoboldGeneratorApiTest.php` |
| No change | `tests/Feature/GenerateKoboldTest.php` — existing grammar tests remain untouched |

---

## Routes

**File:** `routes/api.php` (new file)

```php
<?php

use App\Http\Controllers\KoboldGeneratorController;
use Illuminate\Support\Facades\Route;

Route::post('/generate-kobold', [KoboldGeneratorController::class, 'generate'])
    ->name('kobold.generate');
```

The route is available at `POST /api/generate-kobold`. Laravel's default `api` route group applies no middleware beyond `SubstituteBindings`.

**`bootstrap/app.php` change:** Add the `api:` key inside `withRouting()`:

```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
)
```

---

## Form Request

**File:** `app/Http/Requests/GenerateKoboldRequest.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateKoboldRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'language' => ['sometimes', 'string', 'alpha', 'size:2'],
        ];
    }
}
```

---

## Service Class

**File:** `app/Services/KoboldGeneratorService.php`

```php
<?php

namespace App\Services;

use Polygen\Polygen;

class KoboldGeneratorService
{
    private const DEFAULT_LANGUAGE = 'it';
    private const GRAMMAR_DIRECTORY = 'grm';
    private const GRAMMAR_PREFIX = 'kobold_json_';

    /**
     * Generate a kobold character sheet.
     *
     * Resolves the grammar file for $language; falls back to the default
     * Italian grammar when the language-specific file does not exist.
     *
     * @return array<string, string>
     *
     * @throws \RuntimeException  If the resolved grammar file cannot be read.
     * @throws \JsonException     If Polygen produces output that is not valid JSON.
     */
    public function generate(string $language = self::DEFAULT_LANGUAGE): array
    {
        $grammarPath = $this->resolveGrammarPath($language);

        $polygen = Polygen::fromFile($grammarPath);
        $raw = $polygen->generate();

        $decoded = json_decode($raw, associative: true, flags: JSON_THROW_ON_ERROR);

        return $decoded;
    }

    private function resolveGrammarPath(string $language): string
    {
        $requested = base_path(
            self::GRAMMAR_DIRECTORY.'/'.self::GRAMMAR_PREFIX.strtolower($language).'.grm'
        );

        if (file_exists($requested)) {
            return $requested;
        }

        return base_path(
            self::GRAMMAR_DIRECTORY.'/'.self::GRAMMAR_PREFIX.self::DEFAULT_LANGUAGE.'.grm'
        );
    }
}
```

---

## Controller

**File:** `app/Http/Controllers/KoboldGeneratorController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateKoboldRequest;
use App\Services\KoboldGeneratorService;
use Illuminate\Http\JsonResponse;

class KoboldGeneratorController extends Controller
{
    public function generate(
        GenerateKoboldRequest $request,
        KoboldGeneratorService $service,
    ): JsonResponse {
        $language = $request->string('language', 'it')->lower()->value();

        try {
            $kobold = $service->generate($language);
        } catch (\RuntimeException $e) {
            return response()->json(
                ['error' => 'Grammar file could not be loaded.'],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        } catch (\JsonException $e) {
            return response()->json(
                ['error' => 'Generated output was not valid JSON.'],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }

        return response()->json($kobold);
    }
}
```

---

## Validation Rules

| Field | Type | Required | Rules | Behaviour when absent / invalid |
|-------|------|----------|-------|---------------------------------|
| `language` | string | No | 2 alphabetic characters only (e.g. `it`, `en`) | Absent: defaults to `it`. Invalid: 422 Unprocessable Content with field error message. |

A value that passes validation but has no matching grammar file (e.g. `fr`) will silently fall back to `it` — this is handled in the service, not in validation, because the set of supported languages is defined by the filesystem rather than a fixed enum.

---

## Authorization & Security

The endpoint is public. No authentication or authorization is required. The `authorize()` method in `GenerateKoboldRequest` returns `true` unconditionally.

**CSRF:** The route lives under `/api`, which Laravel excludes from CSRF verification by default in Laravel 13 (the `VerifyCsrfToken` middleware is not applied to the `api` route group).

**Rate limiting:** Laravel 13 applies the `throttle:api` middleware to the `api` route group by default (60 requests per minute per IP). No changes to this default are required.

**Input safety:** The `language` field is constrained to exactly 2 alphabetic characters and lowercased before use in a file path. The grammar directory is fixed and constructed via `base_path()`. This eliminates path traversal risk.

---

## Testing

**File:** `tests/Feature/KoboldGeneratorApiTest.php`

Create with: `php artisan make:test --pest KoboldGeneratorApiTest`

All tests use `pest()->extend(TestCase::class)->in('Feature')` (already configured in `tests/Pest.php`). No database interaction is needed; `RefreshDatabase` is not required.

### Test cases to implement

**Happy path — Italian (default)**
```
POST /api/generate-kobold  {}
→ 200, body is valid JSON, contains all expected kobold fields
```

**Happy path — explicit Italian**
```
POST /api/generate-kobold  {"language": "it"}
→ 200, body is valid JSON
```

**Happy path — English**
```
POST /api/generate-kobold  {"language": "en"}
→ 200, body is valid JSON, response contains English field names
  (e.g. "KoboldName" key, not "Nomekoboldo")
```

**Language fallback — unknown language**
```
POST /api/generate-kobold  {"language": "fr"}
→ 200, body is valid JSON (falls back to Italian grammar)
```

**Language fallback — no language provided**
```
POST /api/generate-kobold  (empty body)
→ 200, body is valid JSON
```

**Validation failure — language too long**
```
POST /api/generate-kobold  {"language": "ita"}
→ 422, body contains {"errors": {"language": [...]}}
```

**Validation failure — language is non-alpha**
```
POST /api/generate-kobold  {"language": "1t"}
→ 422
```

**Validation failure — language is numeric**
```
POST /api/generate-kobold  {"language": "42"}
→ 422
```

**Response structure — Italian field names present**

Assert the 200 response body contains all nine keys produced by the Italian grammar:
`Nomekoboldo`, `CogNomekoboldo`, `Originekoboldo`, `Colorekoboldo`, `SegnoParticolare`, `ParentelaKoboldo`, `PiattoKoboldo`, `StoriaKoboldo`, `MestiereKoboldo`.

**Response structure — English field names present**

Assert the 200 response body for `language=en` contains:
`KoboldName`, `KoboldSurname`, `KoboldOrigin`, `KoboldColor`, `SpecialTrait`, `KoboldKinship`, `KoboldDish`, `KoboldStory`, `KoboldJob`.

**Service layer — grammar file not found (mock test)**

Mock `KoboldGeneratorService::generate()` to throw `\RuntimeException`. Assert the endpoint returns 500 with `{"error": "Grammar file could not be loaded."}`.

### Example test file structure

```php
<?php

use App\Services\KoboldGeneratorService;

describe('POST /api/generate-kobold', function () {
    it('returns a valid kobold JSON with no parameters', function () {
        $response = $this->postJson('/api/generate-kobold');

        $response->assertOk()
            ->assertJsonStructure([
                'Nomekoboldo',
                'CogNomekoboldo',
                'Originekoboldo',
                'Colorekoboldo',
                'SegnoParticolare',
                'ParentelaKoboldo',
                'PiattoKoboldo',
                'StoriaKoboldo',
                'MestiereKoboldo',
            ]);
    });

    it('returns Italian kobold when language is "it"', function () {
        $this->postJson('/api/generate-kobold', ['language' => 'it'])
            ->assertOk()
            ->assertJsonStructure(['Nomekoboldo']);
    });

    it('returns English kobold when language is "en"', function () {
        $this->postJson('/api/generate-kobold', ['language' => 'en'])
            ->assertOk()
            ->assertJsonStructure(['KoboldName', 'KoboldJob']);
    });

    it('falls back to Italian when language grammar does not exist', function () {
        $this->postJson('/api/generate-kobold', ['language' => 'fr'])
            ->assertOk()
            ->assertJsonStructure(['Nomekoboldo']);
    });

    it('returns 422 when language has more than 2 characters', function () {
        $this->postJson('/api/generate-kobold', ['language' => 'ita'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['language']);
    });

    it('returns 422 when language contains non-alpha characters', function () {
        $this->postJson('/api/generate-kobold', ['language' => '1t'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['language']);
    });

    it('returns 500 when the grammar service throws a RuntimeException', function () {
        $this->mock(KoboldGeneratorService::class)
            ->shouldReceive('generate')
            ->andThrow(new \RuntimeException('File not found'));

        $this->postJson('/api/generate-kobold')
            ->assertInternalServerError()
            ->assertJson(['error' => 'Grammar file could not be loaded.']);
    });
});
```

---

## Success Criteria

- [ ] `POST /api/generate-kobold` with an empty body returns HTTP 200 and a JSON object whose keys match the Italian grammar's top-level symbols.
- [ ] `POST /api/generate-kobold` with `{"language": "en"}` returns HTTP 200 and a JSON object whose keys match the English grammar's top-level symbols.
- [ ] `POST /api/generate-kobold` with `{"language": "fr"}` returns HTTP 200 (falls back to Italian grammar).
- [ ] `POST /api/generate-kobold` with `{"language": "ita"}` returns HTTP 422 with a `language` validation error.
- [ ] `POST /api/generate-kobold` with `{"language": "1t"}` returns HTTP 422 with a `language` validation error.
- [ ] When `KoboldGeneratorService::generate()` throws `\RuntimeException`, the endpoint returns HTTP 500 with `{"error": "Grammar file could not be loaded."}`.
- [ ] `php artisan test --compact` passes with no failures or warnings.
- [ ] `vendor/bin/pint --dirty` reports no formatting issues on all new PHP files.

---

## Future Considerations

These items are explicitly out of scope for this story. They should not be built now.

**Seed-based deterministic generation.** The draft requests an optional `seed` parameter. This would require either: (a) patching `procionegobbo/polygen-php` to accept a seed and call `mt_srand()` before generation, or (b) wrapping the generation in a `mt_srand()` / `mt_rand()` context. Neither is feasible without changes to the library, and reproducibility cannot be guaranteed in all environments. Track as a separate story after the library supports it.

**Additional language grammars.** Only `it` and `en` are present. Adding `de`, `es`, etc. requires only placing new `.grm` files in `grm/` — no code changes.

**Caching.** Each request parses and compiles the grammar from disk. For high-throughput scenarios, the parsed `$decls` array could be cached in the application cache keyed by grammar file and its `filemtime`. Track as a performance story.

**GET variant.** Generation with no side effects could be served via `GET /api/generate-kobold?language=en` for easier browser consumption. Track as a separate story if needed.
