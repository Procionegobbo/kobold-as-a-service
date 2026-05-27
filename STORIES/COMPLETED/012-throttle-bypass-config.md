## Throttle Bypass Configuration

**As a** server operator,
**I want** to declare trusted bypass keys in an environment variable,
**So that** I can grant specific callers the ability to skip the rate limiter without modifying code.

**Acceptance Criteria:**
- [ ] `.env.example` contains `THROTTLE_BYPASSERS=` with an inline comment explaining the format (comma-separated secrets, leave empty to disable)
- [ ] `config/app.php` exposes a `throttle_bypassers` key that parses `THROTTLE_BYPASSERS` into a trimmed, filtered array at boot
- [ ] When `THROTTLE_BYPASSERS` is unset or empty, `config('app.throttle_bypassers')` returns `[]`
- [ ] When `THROTTLE_BYPASSERS=key1, key2 , key3`, the config array contains `['key1', 'key2', 'key3']` (each entry trimmed, empty strings removed)
- [ ] The config is read from `config('app.throttle_bypassers')` at request time, not from `env()` directly, so Laravel's config cache is respected

**Story Points:** 1
**Priority:** High
**Dependencies:** None
**Notes:** Uses `array_filter(array_map('trim', explode(',', env('THROTTLE_BYPASSERS', ''))))` — no new config file needed, the key lives alongside existing top-level entries in `config/app.php`.
