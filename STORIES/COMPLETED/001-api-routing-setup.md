# 001 — API Routing Setup

## Description

**As a** developer consuming the kobold generator,
**I want** a `POST /api/generate-kobold` route registered under Laravel's `api` route group,
**So that** I can reach the endpoint over HTTP without CSRF concerns and with default rate limiting applied.

## Background

The project currently has only `routes/web.php` serving the default welcome view. No `routes/api.php` exists and `bootstrap/app.php` does not reference one. This story establishes the HTTP surface before any controller or service logic is built.

## Acceptance Criteria

- [ ] `routes/api.php` is created and registers `POST /generate-kobold` pointing at `KoboldGeneratorController@generate` with the route name `kobold.generate`.
- [ ] `bootstrap/app.php` is updated so `withRouting()` includes `api: __DIR__.'/../routes/api.php'`.
- [ ] `php artisan route:list --path=api` shows the new route with method `POST` and name `kobold.generate`.
- [ ] The route is reachable at `POST /api/generate-kobold` (Laravel prepends `/api` automatically).
- [ ] No existing routes or middleware are changed.
- [ ] `vendor/bin/pint --dirty` reports no formatting issues on all modified PHP files.

## Story Points

1

## Priority

High

## Dependencies

None — this is the foundation for all subsequent kobold stories.

## Notes

- CSRF is not a concern: Laravel 13 does not apply `VerifyCsrfToken` to the `api` route group.
- The default `throttle:api` middleware (60 requests/minute per IP) applies automatically and requires no configuration.
- The controller class referenced by the route will be created in a later story; the route file can be created independently.
