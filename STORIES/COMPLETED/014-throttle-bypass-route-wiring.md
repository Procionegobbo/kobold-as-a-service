## Throttle Bypass Route Wiring

**As a** developer,
**I want** the `throttle.bypass` middleware to run before `throttle:kobold-api` on the generate-kobold route,
**So that** a valid bypass key can remove the throttle check before it fires.

**Acceptance Criteria:**
- [ ] The `generate-kobold` route in `routes/api.php` declares its middleware chain as `['throttle.bypass', 'throttle:kobold-api']`, with `throttle.bypass` first
- [ ] Routes that do not include the `generate-kobold` route are not modified
- [ ] The route name `kobold.generate` is preserved

**Story Points:** 1
**Priority:** High
**Dependencies:** 013-throttle-bypass-middleware
**Notes:** Middleware order is critical — `throttle.bypass` must precede `throttle:kobold-api` so the bypass can call `forgetMiddleware` before the rate-limit counter is checked or incremented.
