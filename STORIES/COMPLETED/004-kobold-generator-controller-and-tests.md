# 004 — Kobold Generator Controller and Feature Tests

## Description

**As a** developer consuming the API,
**I want** a controller that ties the Form Request and Service together and returns a JSON response,
**So that** `POST /api/generate-kobold` produces a fully formed kobold character sheet or a structured error, and a complete Pest feature test suite verifies every defined behaviour.

## Background

With the route (001), Form Request (002), and Service (003) in place, this story delivers the final wiring: the controller that handles the request and the Pest test file that proves the entire stack works end-to-end. This is the story where all success criteria from the spec are verified.

## Acceptance Criteria

### Controller

- [ ] `app/Http/Controllers/KoboldGeneratorController.php` is created using `php artisan make:controller KoboldGeneratorController`.
- [ ] The `generate(GenerateKoboldRequest $request, KoboldGeneratorService $service): JsonResponse` method is the only public action.
- [ ] The `language` value is extracted with `$request->string('language', 'it')->lower()->value()` to normalise casing and supply the default.
- [ ] On success, the controller returns `response()->json($kobold)` with HTTP 200.
- [ ] When the service throws `\RuntimeException`, the controller returns HTTP 500 with body `{"error": "Grammar file could not be loaded."}`.
- [ ] When the service throws `\JsonException`, the controller returns HTTP 500 with body `{"error": "Generated output was not valid JSON."}`.
- [ ] No other exceptions are caught — unexpected exceptions propagate to Laravel's exception handler.

### Feature Tests

- [ ] `tests/Feature/KoboldGeneratorApiTest.php` is created using `php artisan make:test --pest KoboldGeneratorApiTest`.
- [ ] All tests are grouped inside `describe('POST /api/generate-kobold', ...)`.
- [ ] `RefreshDatabase` is NOT used (no database interaction).

**Happy path tests:**

- [ ] `POST /api/generate-kobold` with empty body returns HTTP 200 and all 9 Italian field keys are present in the response.
- [ ] `POST /api/generate-kobold` with `{"language": "it"}` returns HTTP 200 and Italian field keys are present.
- [ ] `POST /api/generate-kobold` with `{"language": "en"}` returns HTTP 200 and all 9 English field keys are present.
- [ ] `POST /api/generate-kobold` with `{"language": "fr"}` returns HTTP 200 (falls back to Italian grammar, Italian keys present).

**Validation failure tests:**

- [ ] `POST /api/generate-kobold` with `{"language": "ita"}` returns HTTP 422 with `assertJsonValidationErrors(['language'])`.
- [ ] `POST /api/generate-kobold` with `{"language": "1t"}` returns HTTP 422 with `assertJsonValidationErrors(['language'])`.
- [ ] `POST /api/generate-kobold` with `{"language": "42"}` returns HTTP 422 with `assertJsonValidationErrors(['language'])`.

**Error handling tests:**

- [ ] When `KoboldGeneratorService::generate()` is mocked to throw `\RuntimeException`, the endpoint returns HTTP 500 with `{"error": "Grammar file could not be loaded."}`.
- [ ] When `KoboldGeneratorService::generate()` is mocked to throw `\JsonException`, the endpoint returns HTTP 500 with `{"error": "Generated output was not valid JSON."}`.

### Quality Gates

- [ ] `php artisan test --compact` passes with no failures or warnings.
- [ ] `vendor/bin/pint --dirty` reports no formatting issues on all new PHP files.
- [ ] Existing `tests/Feature/GenerateKoboldTest.php` remains unmodified and continues to pass.

## Story Points

5

## Priority

High

## Dependencies

- Story 001 — API routing setup (route must be registered).
- Story 002 — Form Request (controller depends on `GenerateKoboldRequest`).
- Story 003 — Service class (controller depends on `KoboldGeneratorService`).

## Notes

- Use `$this->mock(KoboldGeneratorService::class)->shouldReceive('generate')->andThrow(...)` for the error-handling tests (Pest's built-in mock helper, available via the `Tests\TestCase` base).
- Response structure assertions should use `->assertJsonStructure([...keys...])` for presence checks and `->assertJson([...])` for exact value checks.
- The controller should not contain any grammar or language resolution logic — that belongs entirely in the service.

## Italian Grammar Keys

`Nomekoboldo`, `CogNomekoboldo`, `Originekoboldo`, `Colorekoboldo`, `SegnoParticolare`, `ParentelaKoboldo`, `PiattoKoboldo`, `StoriaKoboldo`, `MestiereKoboldo`

## English Grammar Keys

`KoboldName`, `KoboldSurname`, `KoboldOrigin`, `KoboldColor`, `SpecialTrait`, `KoboldKinship`, `KoboldDish`, `KoboldStory`, `KoboldJob`
