# 003 — Kobold Generator Service

## Description

**As a** developer,
**I want** a `KoboldGeneratorService` class that wraps the `Polygen` library and resolves the correct grammar file for a given language code,
**So that** character-sheet generation logic is encapsulated in a single, testable unit with a clear fallback strategy.

## Background

Two Polygen grammar files exist at `grm/kobold_json_it.grm` and `grm/kobold_json_en.grm`. The service must locate the correct file by language code, fall back to Italian when no match is found, invoke `Polygen::fromFile()`, decode the resulting JSON string, and return an associative array. The existing grammar test suite (`tests/Feature/GenerateKoboldTest.php`) must remain untouched.

## Acceptance Criteria

- [ ] `app/Services/KoboldGeneratorService.php` is created.
- [ ] `generate(string $language = 'it'): array` is the single public method.
- [ ] Given `language = 'it'`, the service resolves `grm/kobold_json_it.grm` and returns a non-empty associative array containing `Nomekoboldo` and the other 8 Italian keys.
- [ ] Given `language = 'en'`, the service resolves `grm/kobold_json_en.grm` and returns a non-empty associative array containing `KoboldName` and the other 8 English keys.
- [ ] Given `language = 'fr'` (no matching grammar file), the service falls back to `grm/kobold_json_it.grm` and returns Italian keys.
- [ ] The method throws `\RuntimeException` if the resolved grammar file cannot be read by Polygen (propagated from `Polygen::fromFile()`).
- [ ] The method throws `\JsonException` if Polygen's output is not valid JSON (`json_decode` called with `JSON_THROW_ON_ERROR`).
- [ ] Grammar path resolution uses `base_path('grm')` — the directory is not configurable via environment variables.
- [ ] Language codes are lowercased before use in the file path to prevent case-sensitivity issues.
- [ ] `vendor/bin/pint --dirty` reports no formatting issues on the new file.

## Story Points

3

## Priority

High

## Dependencies

- The `procionegobbo/polygen-php` package must be present in `vendor/` (it is — existing grammar tests pass).
- Story 001 and 002 are independent of this story; all three can be worked in parallel.

## Notes

- The service has no constructor dependencies and no interface is required at this scale. It can be injected via Laravel's auto-resolution by type-hinting it in the controller.
- `private const DEFAULT_LANGUAGE = 'it'`, `GRAMMAR_DIRECTORY = 'grm'`, `GRAMMAR_PREFIX = 'kobold_json_'` are the three internal constants that drive path resolution.
- The fallback is intentionally silent — no log entry or warning is emitted for an unknown language code.

## Italian Grammar Keys (expected response shape)

`Nomekoboldo`, `CogNomekoboldo`, `Originekoboldo`, `Colorekoboldo`, `SegnoParticolare`, `ParentelaKoboldo`, `PiattoKoboldo`, `StoriaKoboldo`, `MestiereKoboldo`

## English Grammar Keys (expected response shape)

`KoboldName`, `KoboldSurname`, `KoboldOrigin`, `KoboldColor`, `SpecialTrait`, `KoboldKinship`, `KoboldDish`, `KoboldStory`, `KoboldJob`
