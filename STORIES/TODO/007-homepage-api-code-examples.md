# 007 - Homepage API Code Examples Section

## Story

**As a** developer evaluating KAAS,
**I want** to see ready-to-copy code snippets in my preferred language for calling the API,
**So that** I can integrate KAAS into my project with minimal friction.

## Background

This story adds the tabbed code examples section to `home.blade.php`. Five languages are supported — PHP, JavaScript, Go, Python, and cURL — each showing a realistic snippet targeting `POST /api/generate-kobold`. Tab switching is implemented with a small vanilla JS `<script>` block; no external library is introduced.

## Acceptance Criteria

### Content

- [ ] The section is identified with the anchor `id="api"` so the nav link and CTA button scroll to it correctly.
- [ ] Five tab buttons are rendered: **PHP**, **JavaScript**, **Go**, **Python**, **cURL** — in that order.
- [ ] Each tab button is associated with exactly one `<pre><code>` block.
- [ ] All five code blocks are present in the DOM at page load time (not dynamically injected); only visibility is toggled.

### Code snippets (hardcoded strings)

- [ ] **PHP tab** contains `GuzzleHttp\Client` and targets `https://kaas.procionegobbo.it/api/generate-kobold` with `'json' => ['language' => 'en']`.
- [ ] **JavaScript tab** contains `fetch(` targeting `/api/generate-kobold` with `body: JSON.stringify({ language: 'en' })`.
- [ ] **Go tab** contains `http.Post` and `"https://kaas.procionegobbo.it/api/generate-kobold"` with `{"language":"en"}`.
- [ ] **Python tab** contains `import requests` and `requests.post(` with `json={"language": "en"}`.
- [ ] **cURL tab** contains `curl -X POST https://kaas.procionegobbo.it/api/generate-kobold` with `-H "Content-Type: application/json"`.

### Tab switcher behaviour (vanilla JS)

- [ ] On page load the PHP tab is active and its code block is visible; all other code blocks are hidden.
- [ ] Clicking a tab button shows that tab's code block and hides the other four — without a page reload.
- [ ] The active tab button carries a visually distinct style (e.g., bottom border, background highlight) to indicate selection.
- [ ] The tab switcher works without any external JS library (inline `<script>` block only).
- [ ] The section degrades gracefully if JS is disabled: all five `<pre><code>` blocks remain in the DOM and can be scrolled to (only the active/hidden toggle is a JS enhancement).

### Styling

- [ ] The section is responsive and usable on a 375 px mobile viewport (tabs may wrap or scroll horizontally).
- [ ] Dark mode variants are applied to the code blocks and tab buttons.

## Out of Scope

- Syntax highlighting (plain `<pre><code>` is sufficient; no highlight.js or Prism dependency).
- Live API calls from the browser (Future Consideration in the spec).
- The output example card (story 008).

## Technical Notes

- The section anchor `id="api"` connects to the `#api` nav link from story 006.
- All five `<pre><code>` blocks must be in the DOM at render time to satisfy the accessibility and no-JS-framework constraints.
- The spec explicitly forbids adding a front-end JS framework; keep the switcher under ~20 lines of plain JS.

## Story Points: 5

## Priority: High

## Dependencies

- Story 006 — nav `#api` anchor link must exist for scroll behaviour to be meaningful.
