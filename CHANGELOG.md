# Changelog

## 1.3.2

- **Accessibility** — mobile nav close button is now a full 44×44 px touch target (WCAG 2.2 Target Size)
- **Accessibility** — concept-badge raised to 44 px min-height; light-outline buttons show a white focus ring on dark hero backgrounds
- **Accessibility** — footer column headings (`Office`, `Hours`, `Explore`) demoted from `h2` to `h3` for a correct document outline
- **Accessibility** — `<main>` gets `tabindex="-1"` so keyboard skip links land and move focus correctly
- **Accessibility** — header phone link carries a full accessible name (`Call 555…`) when the number is visually hidden at tablet widths
- **Contrast** — form notes and booking time-slot legend raised from `--ink-faint` to `--ink-soft` for WCAG AA compliance
- **Contrast** — footer copyright text opacity raised from 45 % to 58 %; calculator label from 60 % to 78 %; testimonial location and average promoted to `--ink-soft`
- **Contrast** — hero search panel gets explicit `color-scheme: light` to prevent OS dark-mode from inverting field colours
- **i18n** — hardcoded English strings in single listing, single agent, blog post single, and footer templates wrapped in `__()`; listing image alt text now uses the listing title
- **Docs** — `SUPPORT.md` block-editor note corrected (Gutenberg is enabled for pages and posts)
- **Docs** — `DEVELOPMENT.md` CSS architecture table updated with `responsive-forms.css` and `form-contrast.css`

## 1.3.1

- Single blog posts no longer fatal: adjacent / related post data is passed as arrays, not invokable view variables

## 1.3.0

- **Setup wizard** under Appearance → Acreline Setup: Welcome → Identity → Colors → Demo → Done
- Saves brand, phone, email, hours, color style, and optional demo seed without leaving wp-admin
- Opens automatically after theme activation until you finish or skip (no upsells)

## 1.2.3

- Concept demo URL is https://acreline.matthummel.com/
- readme.txt and marketplace docs point at matthummel.com/projects/acreline/ and the 01–07 screenshot set

## 1.2.2

- GitHub repository, updater, support links, and Composer package name are `matthummel-pa/wp-acreline`

## 1.2.1

- Inner marketing pages (listings, areas, guide, agents, book, contact, blog) share a taller photo hero, brand pill, and two-column lead
- Areas use numbered cards; guide and agents use a five- and three-card topic grid instead of a wall of prose
- Stored Keystone Real Estate / Adams County page fields fall back to Acreline sample-market copy
- Empty CTA fields now pick up the page defaults (PHP `+` was keeping a blank title)
- Agent emails still on `@keystone-concept.test` rewrite to `@acreline-concept.test`

## 1.2.0

- Theme folder, text domain, and Appearance → Upload Theme zip are **acreline** (not keystone-homes)
- Child theme is `acreline-child`; companion plugin is **Acreline Core** (`acreline-core`)
- Sample office default and seed site name are Acreline; concept emails `@acreline-concept.test`
- Seller pack: `acreline-1.2.0.zip` → extract, then upload inner `acreline.zip`
- Copies Customizer settings from a previous `keystone-homes` install when you first activate Acreline

## 1.1.0

- Eight color styles (Forest, Clay, Navy, Burgundy, Harvest, Lake, Orchard, Charcoal) plus accent, paper, and ink
- Header: sticky/static and standard/compact
- Front-end color switcher on the concept demo (Customize → Colors for buyers)
- Sample page, FAQ, SEO, and listing copy is generic market language for store previews

## 1.0.2

- Theme Author and Author URI are Matt Hummel / https://matthummel.com/ for ThemeForest and WordPress.org
- Footer credit default, child theme, and Acreline Core plugin header match

## 1.0.1

- Theme Name is **Acreline** (what buyers see in Appearance → Themes)
- Admin: Appearance → Acreline Setup, Tools → Seed Acreline demo
- Sample office in the preview stays Acreline until Identity is changed
- Install folder and text domain stay `acreline`

## 1.0.0

- Marketplace packaging: Customizer identity, accent color, social URLs, removable footer credit
- WordPress Primary + Footer menus (concept slugs only as fallback)
- Breadcrumbs + BreadcrumbList JSON-LD
- Appearance → Acreline Setup checklist (no upsells)
- Acreline Core companion plugin for listings / agents / bookings
- Child theme starter and Envato-style pack script
- `readme.txt`, `CREDITS.md`, text domain `acreline`
- Footer copyright is the site name; author line is optional and `rel="nofollow"`
