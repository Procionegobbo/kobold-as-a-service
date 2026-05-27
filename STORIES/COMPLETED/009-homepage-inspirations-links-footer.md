# 009 - Homepage Inspirations, Links, and Footer Sections

## Story

**As a** visitor who wants to learn more about the project's origins and ecosystem,
**I want** to find attribution to Polygen and FumbleGDR and a curated list of related resources,
**So that** I can explore the tools and community behind KAAS.

## Background

This story completes the lower half of `home.blade.php` by adding three remaining sections: the Inspirations & Credits block, the Ecosystem links grid, and the branded footer. Together these sections satisfy attribution obligations and provide onward navigation for curious visitors.

## Acceptance Criteria

### Inspirations & Credits section

- [ ] The section has a heading (e.g., "Standing on the shoulders of Kobolds" or equivalent creative phrasing).
- [ ] Body copy acknowledges that KAAS is built on the **Polygen** generative grammar engine.
- [ ] Body copy acknowledges that KAAS is inspired by **FumbleGDR**'s podcasts (not a character generator — just inspired by their podcast content).
- [ ] "Polygen" is an external link to `https://polygen.org/` with `target="_blank" rel="noopener noreferrer"`.
- [ ] "FumbleGDR" is an external link to `https://www.fumblegdr.it` with `target="_blank" rel="noopener noreferrer"`.
- [ ] The section is visually separated from adjacent sections (e.g., card, bordered block, or sufficient whitespace).

### Links / Ecosystem section

- [ ] The section has a heading (e.g., "Ecosystem").
- [ ] The section has the anchor `id="links"` so the hero secondary CTA (story 006) scrolls to it.
- [ ] The following four links are present, each opening in `_blank` with `rel="noopener noreferrer"`:

  | Label | URL |
  |---|---|
  | `polygen-php` on GitHub | `https://github.com/procionegobbo/polygen-php` |
  | `polygen-php` on Packagist | `https://packagist.org/packages/procionegobbo/polygen-php` |
  | Laravel | `https://laravel.com` |
  | procionegobbo.it | `https://procionegobbo.it` |

- [ ] The links are arranged in a grid or list that is readable on both mobile (375 px) and desktop (1280 px) viewports.

### Footer

- [ ] A `<footer>` element is present at the bottom of the page.
- [ ] The footer displays: `Made with ❤️ by Federico "Procionegobbo" Maiorini` (exact wording; emoji is required per spec).
- [ ] "Federico 'Procionegobbo' Maiorini" (or the full attribution string) is wrapped in an `<a>` tag linking to `https://procionegobbo.it` with `target="_blank" rel="noopener noreferrer"`.

### Security — all external links

- [ ] Every external link on the entire page (including those added in stories 006–008) carries both `target="_blank"` and `rel="noopener noreferrer"` to prevent reverse tabnapping.

### Styling

- [ ] All three sections use Tailwind CSS v4 and are responsive at 375 px and 1280 px.
- [ ] Dark mode variants are applied consistently.

## Out of Scope

- SEO meta tags and Open Graph tags (Future Consideration in spec).
- Any backend changes; this story is purely view markup.

## Story Points: 3

## Priority: Medium

## Dependencies

- Story 006 — the `#links` anchor connects to the secondary CTA in the hero section.
