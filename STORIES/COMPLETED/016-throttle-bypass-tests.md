## Throttle Bypass Feature Tests

**As a** developer,
**I want** a Pest feature test suite that covers every meaningful path through the bypass middleware,
**So that** I can refactor with confidence and prevent regressions.

**Acceptance Criteria:**
- [ ] `tests/Feature/ThrottleBypassTest.php` is created using `php artisan make:test --pest ThrottleBypassTest`
- [ ] Tests follow the `describe()` + `test()` structure used in `KoboldGeneratorApiTest.php`
- [ ] All seven test cases below are present and green when running `php artisan test --compact --filter=ThrottleBypass`:

  | # | Test name | What it proves |
  |---|-----------|----------------|
  | 1 | Valid bypass key returns 200 | Bypass triggers on first request |
  | 2 | Second consecutive request with valid key also returns 200 | Bypass actually removes the throttle (without bypass, second same-second request returns 429) |
  | 3 | Invalid bypass key does not skip the rate limiter (first request still 200) | Wrong key falls through to normal throttle |
  | 4 | Absent `X-Bypass-Key` header does not skip the rate limiter | Missing header falls through to normal throttle |
  | 5 | Bypass is impossible when `THROTTLE_BYPASSERS` is empty | Empty config = opt-out |
  | 6 | One of multiple configured keys is accepted | Multi-key list works |
  | 7 | Config value is already trimmed and matched correctly | Trimming logic in config parsing is validated end-to-end |

- [ ] Each test overrides config inline via `config([...])` — no `.env` changes required
- [ ] No existing tests are broken after the new test file is added (`php artisan test --compact` passes in full)

**Story Points:** 3
**Priority:** High
**Dependencies:** 013-throttle-bypass-middleware, 014-throttle-bypass-route-wiring
**Notes:** Test 2 (consecutive requests) is the definitive proof of correctness — the rate limiter fires at 1 req/s per IP, so two requests in the same test function within the same second would produce a 429 without the bypass. The `array` cache driver active in the test environment means no additional cache mocking is needed.
