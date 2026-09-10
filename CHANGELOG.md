# Changelog

## 1.5.0

- **Home & Areas** — New Region Coverage block: split map + neighborhood cards, or a bento/grid layout. Neighborhoods, methods, and CTAs are editable in the block inspector. Section bands follow the active color scheme.
- **Blocks** — Pricing plans, logo strip, and a newsletter signup (demo form — nothing is emailed). Area Grid now supports photos, card styles, and a section CTA. How We Work steps are editable.
- **Copy** — Marketing pages and FAQs speak to a general real-estate office: homes, neighborhoods, and local agents.
- **Seed** — Home, Areas, Agents, Listings, Guide, and Blog stacks include the new blocks. After you install the zip, run Tools → Migrate to Blocks → Force rebuild so existing pages pick them up.

## 1.4.7

- **Home** — The marketing CTA band follows the active color scheme (`ks_accent` / `ks_paper` / `ks_ink`). Text and buttons stay readable on that surface.
- **Header** — The desktop top bar follows the active color scheme. Custom top-bar colors still override.
- **Settings** — Appearance → Acreline Settings → General: pick the site color style (same eight styles as Customize → Colors) and optionally hide the front-end color chip. On by default for the concept demo.

## 1.4.6

- **Header** — The mobile-menu contact / social / “new listing” panel follows the active color scheme. Text and icons stay readable on that surface.

## 1.4.5

- **Header** — Primary menu, hamburger, and mobile drawer follow the active color scheme (`ks_accent` / `ks_paper` / `ks_ink`). Link, hover, and current colors stay readable on the header.

## 1.4.4

- **Settings** — Homepage hero motion (Ken Burns and mobile search tilt) is on Appearance → Acreline Settings → General. Values stay saved when you leave and return. Customize → Header stays in sync.
- **Top bar** — Desktop bar and the mobile-nav copy use the active color scheme (`ks_accent` / `ks_paper` / `ks_ink`). Text and icons auto-pick a readable pair when accent-on-paper would fail. Custom colors still override.

## 1.4.3

- **Homepage hero** — The hero photo slowly pans and zooms. Headlines, the dark overlay, and the listing search stay still so they stay readable. Visitors who prefer reduced motion see a still photo.
- **Listing search on phones** — Optional: the search panel can gently follow the phone’s tilt. Off by default (battery and privacy). Does nothing if the device has no motion sensors or the visitor declines access.
- **Customizer** — Appearance → Customize → Header: “Animate homepage hero image” (on) and “Tilt listing search on mobile” (off). The two checkboxes are independent.

## 1.4.2

- **Home** — `acreline/home-hero` always paints a full-bleed rural farmhouse photo behind the copy and search form (theme default Unsplash still already used on the featured listing). Ink veil keeps white type readable; Gutenberg image picker still replaces it.
- **Seed** — `wp ks seed` / Tools → Seed / force-rebuild write `imageUrl` on the home page so older installs without a hero attachment pick up the photo.

## 1.4.1

- **Content** — Marketing pages seed the full Gutenberg stacks that `BlockMigration` already defined: agent-list + reviews on Agents; market-stats, reviews, and FAQ on Listings; how-it-works + reviews on Guide; richer Blog (topic cards, post grid, reviews).
- **Block** — New editor-editable blocks for the former Blade-only sections: `acreline/trust-strip`, `acreline/checklist`, `acreline/prep-checklist`, `acreline/compare-table`, `acreline/topic-cards`, `acreline/post-grid`.
- **Block** — `acreline/agent-list` is now registered in the Gutenberg inserter (it already rendered in PHP).
- **Templates** — Guide, Areas, Contact, and Book output only `the_content()`. Blog (`home.blade.php`) renders the posts-page block content so seeded stacks are not stranded behind hardcoded Blade.
- **Seed** — `wp ks seed` / Tools → Seed Acreline demo force-rebuilds all eight marketing page stacks. Tools → Migrate to Blocks has a Force rebuild button; `wp ks rebuild-blocks` does the same from CLI.

## 1.4.0

- **Feature** — Top bar: a configurable slim bar above the header, desktop-only (hidden at < 900 px via CSS). Controls in Appearance → Customize → Top Bar: enable toggle, four color styles (Dark / Accent / Light / Custom), custom background + text colors, announcement badge + message + optional link, CTA pill button, show/hide toggles for phone, email, address, and hours (values pulled from Identity), social icon toggles (Facebook, Instagram, YouTube, LinkedIn, X), and an optional "Allow visitors to dismiss" mode (state stored in sessionStorage).
- **Feature** — Mobile nav fallback: when the top bar is enabled all of its active items (announcement, contact details, social icons, CTA) surface automatically at the foot of the mobile slide-out nav so no content is hidden from smaller screens.
- **Accessibility** — Top bar carries `role="complementary"` and `aria-label`; announcement slot uses `role="status"`; dismiss button has a descriptive `aria-label`; all interactive elements expose `:focus-visible` rings.
- **Accessibility** — Focus trap and `aria-live` announcements added to listing modal, saved-listings drawer, and compare modal for full keyboard navigation.
- **Accessibility** — Screen-reader announcer live region injected by `listings.js`; save and compare actions announce to assistive technology.
- **Accessibility** — `aria-label` on each save and compare button updates dynamically to include the listing title.
- **Responsive** — Listing detail grid stacks to one column on narrow phones (< 380 px); mortgage calc grid stacks below 480 px; compare table enforces horizontal scroll below 600 px; compare bar wraps at 480 px; saved drawer goes full-width below 360 px.
- **Content** — Seed listings updated with richer details: open house date/time, virtual tour URL, condition, garage type, basement, utilities (water/sewer/heating/cooling), HOA, school district, flood zone, green features, outbuildings, tillable/pasture acres.
- **Content** — Seed agents updated with performance stats (homes sold, volume, avg DOM, list-to-sale ratio, review count), social links, personal website, booking/calendar URL, bio video, certifications, awards, and agent photo URL.
- **Block** — New `acreline/agent-list` Gutenberg block renders the full agent grid on the Agents page with photo/avatar, name, job title, designations, specialties, and mini-stats.
- **Docs** — `README.md` completely rewritten: screenshot gallery, top bar feature, all major features documented, matthummel.com product page coexistence section.
- **Docs** — `readme.txt`, `CHANGELOG.md`, `docs/marketplace/themeforest-listing.md`, `docs/marketplace/changelog.html`, and `docs/marketplace/buyer-guide.html` all updated.

## 1.3.3

- **Admin** — Theme Settings page completely redesigned: tabbed navigation (Listings, Agents, Bookings, Market, Labels, General), iOS-style toggle switches, sticky save bar, unsaved-changes guard, and live Market snapshot preview
- **Admin** — New Theme Support page (Appearance → Acreline Support) with quick-start guide, feature reference cards, shortcode cheatsheet, FAQ accordion, and changelog
- **Feature** — Listing comparison modal: compare up to 3 listings side-by-side from the listings grid
- **Feature** — Persistent saved listings drawer: heart icon on every card saves to `localStorage`; floating button opens the slide-out panel
- **Feature** — Sticky CTA bar on single listing pages: price + "Book a showing" appears after hero scrolls out of view
- **Feature** — Share button with Web Share API (mobile) and URL clipboard-copy fallback
- **Feature** — Print-friendly property flyer layout (`window.print()` with clean print CSS)
- **Feature** — Recently viewed listings (up to 5 in `localStorage`) rendered as chips at the bottom of each listing page
- **Feature** — Nearby listings section on single listing pages: server-rendered, same township, no API required
- **Feature** — Market snapshot shortcode `[acreline_market_snapshot]` with Market tab in Theme Settings
- **Feature** — Built-in mortgage calculator on listing detail pages: editable rate, term, and down payment
- **Feature** — Media picker for all image URL inputs in listing and agent metaboxes (native WP media library)
- **Feature** — Rich listing fields: property details, utilities, HOA, land & farm, flood zone, school district, green/eco features, smart home feature chips
- **Feature** — Advanced agent profiles: stats grid, social links, certifications/designations, intro video link, calendar booking URL, mobile number
- **Content** — Updated seeded blog post "Acreline features" covering all new differentiating capabilities
- **Docs** — `README.md` updated with new feature summary and admin screenshots section
- **Docs** — `DEVELOPMENT.md` updated with new file locations and JS module descriptions

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
