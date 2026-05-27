# 010 - Homepage Feature Tests

## Story

**As a** developer on the KAAS project,
**I want** a comprehensive Pest feature test suite for the homepage,
**So that** regressions in routing, view rendering, content, caching, and external links are caught automatically in CI.

## Background

Stories 005–009 implement the homepage end-to-end. This story creates `tests/Feature/HomepageTest.php` covering every behavioural requirement from the spec. The test file is the single source of truth for automated verification; no tinker scripts or manual verification scripts should duplicate this coverage.

## Acceptance Criteria

### Test file

- [ ] `tests/Feature/HomepageTest.php` is created using `herd php artisan make:test --pest HomepageTest --no-interaction`.
- [ ] The file follows the Pest conventions in `tests/Pest.php`: feature tests extend `Tests\TestCase` via `pest()->extend(TestCase::class)->in('Feature')`.
- [ ] `RefreshDatabase` is NOT used (no database is touched by the homepage).

### Required test cases — all must pass

| Test name | Assertion |
|---|---|
| `homepage returns 200 OK` | `assertOk()` on `GET route('home')` |
| `homepage view is home` | `assertViewIs('home')` |
| `homepage contains the project name` | `assertSee('Kobold As A Service')` |
| `homepage contains a link to the API endpoint` | `assertSee('/api/generate-kobold')` |
| `homepage contains PHP code example` | `assertSee('GuzzleHttp', false)` |
| `homepage contains JavaScript code example` | `assertSee('fetch(', false)` |
| `homepage contains Go code example` | `assertSee('http.Post', false)` |
| `homepage contains Python code example` | `assertSee('import requests', false)` |
| `homepage contains cURL code example` | `assertSee('curl -X POST', false)` |
| `homepage contains a live generated kobold with all nine fields` | `assertSee` for each of: `KoboldName`, `KoboldSurname`, `KoboldOrigin`, `KoboldColor`, `SpecialTrait`, `KoboldKinship`, `KoboldDish`, `KoboldStory`, `KoboldJob` (all with `false` as second arg) |
| `homepage passes a kobold array to the view` | `assertViewHas('kobold')`, `assertViewHas('kobold.KoboldName')`, `assertViewHas('kobold.KoboldJob')` |
| `kobold output is cached and the service is only called once per cache window` | Mock `KoboldGeneratorService` with `->once()`, make two requests, both return 200 |
| `homepage links to Polygen` | `assertSee('https://polygen.org/')` |
| `homepage links to FumbleGDR` | `assertSee('https://www.fumblegdr.it')` |
| `homepage links to polygen-php on GitHub` | `assertSee('https://github.com/procionegobbo/polygen-php')` |
| `homepage links to polygen-php on Packagist` | `assertSee('https://packagist.org/packages/procionegobbo/polygen-php')` |
| `homepage links to Laravel` | `assertSee('https://laravel.com')` |
| `homepage links to procionegobbo.it` | `assertSee('https://procionegobbo.it')` |
| `homepage footer contains attribution` | `assertSee('Federico')` and `assertSee('Procionegobbo')` |

### Caching mock test — specific requirements

- [ ] `KoboldGeneratorService` is mocked using `$this->mock(KoboldGeneratorService::class)`.
- [ ] `->shouldReceive('generate')->with('en')->once()` — the service must be called exactly once across two requests.
- [ ] The mock returns a valid array with all nine kobold fields.
- [ ] Both `$this->get(route('home'))->assertOk()` calls succeed (second call is a cache hit).

### Test execution

- [ ] All tests pass when run with `herd php artisan test --compact`.
- [ ] No test relies on a real database connection.
- [ ] The `array` cache driver behaviour (per-request) is acceptable in the test environment; the caching mock test manages service call counts explicitly via Mockery.

## Out of Scope

- Browser/Dusk tests for the JS tab switcher (vanilla JS behaviour is not covered by HTTP tests; manual verification is acceptable for this sprint).
- Load or performance tests.

## Technical Notes

- Use `herd php artisan` for all Artisan commands (system PHP 8.4 is below the required 8.5).
- Run `vendor/bin/pint --dirty --format agent` after creating the test file.
- The mock test interacts with the cache; ensure `Cache::flush()` is not needed by using the `array` driver (configured by default in `phpunit.xml` for tests).

## Story Points: 3

## Priority: High

## Dependencies

- Stories 005–009 must be complete before all tests can pass (tests will fail on missing view content).
- Story 003 — `KoboldGeneratorService` must be mockable (no `final` keyword; constructor-injected into the controller).
