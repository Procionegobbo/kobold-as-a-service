# 002 — Generate Kobold Form Request

## Description

**As a** developer,
**I want** a `GenerateKoboldRequest` Form Request that validates the optional `language` input,
**So that** invalid language values are rejected with a structured 422 response before reaching any business logic.

## Background

The endpoint accepts a single optional field, `language`. Because the set of supported languages is defined by which `.grm` files exist on disk (not a fixed enum), validation only enforces format constraints — exactly 2 alphabetic characters. Unknown-but-valid codes (e.g. `fr`) fall back to Italian in the service layer.

## Acceptance Criteria

- [ ] `app/Http/Requests/GenerateKoboldRequest.php` is created using `php artisan make:request GenerateKoboldRequest`.
- [ ] `authorize()` returns `true` unconditionally.
- [ ] `rules()` returns `['language' => ['sometimes', 'string', 'alpha', 'size:2']]`.
- [ ] A `POST /api/generate-kobold` request with `{"language": "it"}` passes validation.
- [ ] A `POST /api/generate-kobold` request with `{"language": "en"}` passes validation.
- [ ] A `POST /api/generate-kobold` request with no body passes validation (field is optional).
- [ ] A `POST /api/generate-kobold` request with `{"language": "ita"}` returns HTTP 422 with a `language` key in `errors`.
- [ ] A `POST /api/generate-kobold` request with `{"language": "1t"}` returns HTTP 422 with a `language` key in `errors`.
- [ ] A `POST /api/generate-kobold` request with `{"language": "42"}` returns HTTP 422 with a `language` key in `errors`.
- [ ] `vendor/bin/pint --dirty` reports no formatting issues on the new file.

## Story Points

2

## Priority

High

## Dependencies

- Story 001 (API routing setup) — the route must exist for HTTP-level tests to work.

## Notes

- The `size:2` rule counts string length, not numeric value — `"42"` fails `alpha` before `size:2` is even evaluated.
- A language code that passes validation but has no grammar file (e.g. `fr`) is intentionally allowed through; the service layer handles the fallback silently.
- Return type for `rules()` should be documented as `array<string, array<string>>` per project PHP conventions.
