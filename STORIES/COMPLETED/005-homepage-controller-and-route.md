# 005 - Homepage Controller and Route

## Story

**As a** visitor to the KAAS site,
**I want** the root URL to serve the branded homepage,
**So that** I land on a meaningful page instead of the Laravel skeleton welcome screen.

## Background

The existing `GET /` route uses an inline closure in `routes/web.php` returning the stock `welcome` view. This story replaces that closure with a dedicated invokable `HomepageController` and renames the view file, establishing the backend foundation that all other homepage stories depend on.

## Acceptance Criteria

- [ ] `HomepageController` exists at `app/Http/Controllers/HomepageController.php` and is invokable (implements `__invoke`).
- [ ] The controller injects `KoboldGeneratorService` via constructor or method injection.
- [ ] The controller calls `Cache::remember('homepage.kobold', 15, fn () => $service->generate('en'))` to retrieve a `$kobold` array, invoking the service at most once per 15-second window.
- [ ] The controller returns `view('home', compact('kobold'))`.
- [ ] `routes/web.php` maps `GET /` to `HomepageController::class` with the route name `home`.
- [ ] `route('home')` resolves to `/`.
- [ ] `resources/views/welcome.blade.php` is deleted from the repository.
- [ ] `resources/views/home.blade.php` is created (can be a minimal placeholder; full content is covered by later stories).
- [ ] `GET /` returns HTTP 200.
- [ ] The route is accessible without authentication.

## Out of Scope

- Full view markup and styling (covered by stories 006–009).
- Feature tests beyond basic HTTP 200 and view assertion (covered by story 010).

## Technical Notes

- Create the controller with: `herd php artisan make:controller HomepageController --invokable --no-interaction`
- The `array` cache driver (used in tests) is per-request only and must not be used in production; any persistent driver (`file`, `redis`) is correct for production.
- Run `vendor/bin/pint --dirty --format agent` after creating the controller.

## Story Points: 2

## Priority: Critical

## Dependencies

- Story 003 — `KoboldGeneratorService` must exist and expose a `generate(string $language): array` method.
