## Throttle Bypass Middleware

**As a** trusted API caller,
**I want** to present a pre-shared secret in the `X-Bypass-Key` header,
**So that** the `kobold-api` rate limiter is skipped for my request.

**Acceptance Criteria:**
- [ ] A new `ThrottleBypassMiddleware` class is created at `app/Http/Middleware/ThrottleBypassMiddleware.php`
- [ ] The middleware reads the `X-Bypass-Key` request header and compares it against `config('app.throttle_bypassers')` using strict equality
- [ ] When a valid key is matched, the middleware calls `$request->route()->forgetMiddleware('throttle:kobold-api')` before passing the request to the next middleware, preventing the rate limiter from firing
- [ ] When the key is absent, empty, or not in the configured list, the middleware passes through without removing the throttle
- [ ] When `config('app.throttle_bypassers')` is `[]`, no header value can trigger a bypass
- [ ] The middleware is registered as a named alias `throttle.bypass` inside the `->withMiddleware()` callback in `bootstrap/app.php`

**Story Points:** 2
**Priority:** High
**Dependencies:** 012-throttle-bypass-config
**Notes:** `forgetMiddleware` removes the throttle only for the current request; rate-limit counters for other callers are unaffected. Timing-safe comparison is not required at this stage — strict `in_array` is sufficient for the current threat model.
