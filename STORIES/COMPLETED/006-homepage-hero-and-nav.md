# 006 - Homepage Hero and Navigation Bar

## Story

**As a** first-time visitor,
**I want** to immediately see the project name, a clear value proposition, and quick navigation links,
**So that** I understand what KAAS is and can jump to the relevant sections without scrolling.

## Background

With the controller and route in place (story 005), this story adds the visual top layer of the page: a slim header/nav bar and a hero section with a headline, description, and call-to-action buttons. All styling uses Tailwind CSS v4 with dark mode variants.

## Acceptance Criteria

### Navigation bar

- [ ] A `<header>` element renders at the top of the page.
- [ ] The left side of the nav displays the project name "Kobold As A Service".
- [ ] The right side contains a link to `#api` labelled "Try the API".
- [ ] The nav is responsive and does not overflow on a 375 px mobile viewport.
- [ ] No authentication links are present (the project has no auth routes).

### Hero section

- [ ] An `<h1>` element contains exactly "Kobold As A Service".
- [ ] A `<p>` below `<h1>` contains a short value proposition referencing procedural generation, Kobold RPG character sheets, the HTTP API, and Polygen grammars (exact wording can be refined; must convey these four concepts).
- [ ] A primary CTA button or link pointing to `#api` with visible label "Try the API".
- [ ] A secondary link pointing to `#links` with visible label "Explore the ecosystem".
- [ ] Both CTA elements are visible and accessible on mobile (375 px) and desktop (1280 px) viewports.
- [ ] Dark mode styles (`dark:` Tailwind variants) are applied so the section is readable when the OS prefers dark colour scheme.

### General

- [ ] The `<title>` tag reads "Kobold As A Service" (sourced from `config('app.name')` or hardcoded; spec permits either).
- [ ] The view loads assets via `@vite(['resources/css/app.css', 'resources/js/app.js'])`.
- [ ] The `@fonts` directive is present if the existing Vite Bunny font integration is configured in `vite.config.js`.

## Out of Scope

- Code examples section (story 007).
- Output example card (story 008).
- Inspirations, Links, and Footer sections (story 009).

## Technical Notes

- Follow Tailwind CSS v4 conventions; check existing welcome view for dark-mode colour tokens already in use (e.g., `bg-[#FDFDFC]`, `dark:bg-[#0a0a0a]`).
- No JavaScript is needed in this story.

## Story Points: 3

## Priority: High

## Dependencies

- Story 005 — `HomepageController` and `home.blade.php` must exist.
