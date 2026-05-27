## Throttle Bypass Documentation

**As an** API consumer,
**I want** to read about the `X-Bypass-Key` option in the home page and README,
**So that** I know how to request a bypass key and how to use it.

**Acceptance Criteria:**
- [ ] `resources/views/home.blade.php` contains a paragraph in the `#api` section describing the `X-Bypass-Key` header and directing the reader to contact the operator to request a key
- [ ] The new paragraph appears after the existing rate-limit notice and matches the surrounding typographic style (same text colour, `<code>` styling for the header name)
- [ ] `README.md` contains a `### Throttle bypass` subsection under `## API` and before `## Tech stack`
- [ ] The README subsection includes a `curl` example that shows `X-Bypass-Key: your-secret-key` alongside `Content-Type: application/json`
- [ ] The README subsection explains that keys are configured via the `THROTTLE_BYPASSERS` environment variable and that callers should contact the operator to request a key
- [ ] No new section heading is added to the home page; the information is inline with the rate-limit description

**Story Points:** 1
**Priority:** Medium
**Dependencies:** 012-throttle-bypass-config
**Notes:** Documentation wording should make clear that the header name is not secret but the key values must be kept out of version control. The README example should use the production domain `kaas.procionegobbo.it`.
