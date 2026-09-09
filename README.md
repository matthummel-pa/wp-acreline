# Acreline — WordPress Theme for Farms, Land & Historic Homes

[![Deploy theme zip](https://github.com/matthummel-pa/wp-acreline/actions/workflows/deploy.yml/badge.svg)](https://github.com/matthummel-pa/wp-acreline/actions/workflows/deploy.yml)
[![Version](https://img.shields.io/badge/version-1.4.0-155539?style=flat-square)](CHANGELOG.md)
[![License: GPLv2+](https://img.shields.io/badge/license-GPLv2%2B-3c763d?style=flat-square)](license.txt)
[![PHP 8.3+](https://img.shields.io/badge/PHP-8.3%2B-777bb4?style=flat-square)](https://www.php.net/)
[![WordPress 6.6+](https://img.shields.io/badge/WordPress-6.6%2B-21759b?style=flat-square)](https://wordpress.org/)

**Acreline** is a WordPress real estate theme built for rural land offices — searchable listings, agent profiles, and showing requests for farms, acreage, and historic homes. Not a generic luxury brokerage skin.

|  |  |
| --- | --- |
| **Live demo** | [acreline.matthummel.com](https://acreline.matthummel.com/) |
| **Product page** | [matthummel.com/projects/acreline/](https://matthummel.com/projects/acreline/) |
| **Buy / checkout** | [matthummel.com/product/acreline/](https://matthummel.com/product/acreline/) |
| **ThemeForest** | [themeforest.net/item/acreline](https://themeforest.net/user/matthummel) *(listing in review)* |
| **GitHub** | [`matthummel-pa/wp-acreline`](https://github.com/matthummel-pa/wp-acreline) |
| **Support** | [SUPPORT.md](SUPPORT.md) · [GitHub Issues](https://github.com/matthummel-pa/wp-acreline/issues) |
| **Author** | [Matt Hummel](https://matthummel.com/) |
| **Install folder** | **`acreline`** — leave this name, Vite asset URLs depend on it |
| **Version** | 1.4.0 |

> **Fiction only.** Listings, market stats, contact details, and appointments in the demo are sample data. Not a live MLS, licensed brokerage, or booking system. Demo phones use the `555` exchange; emails use `@acreline-concept.test`.

---

## Screenshots

> Captured from the seeded concept demo. The same images are used on the [matthummel.com Acreline product page](https://matthummel.com/projects/acreline/).

| | |
|---|---|
| ![Homepage](docs/marketplace/screenshots/01-homepage.png) | ![Listings](docs/marketplace/screenshots/02-listings.png) |
| **Homepage** — Hero search, Intent cards, Featured listings | **Listings** — Filter bar (type, price, acreage, area), grid + map |
| ![Listing detail](docs/marketplace/screenshots/03-listing.png) | ![Agents](docs/marketplace/screenshots/04-agents.png) |
| **Listing detail** — Photo, stats, calculator, agent, book CTA | **Agents** — Stats grid, designations, social links |
| ![Contact](docs/marketplace/screenshots/05-contact.png) | ![Areas](docs/marketplace/screenshots/06-areas.png) |
| **Contact** — Office info from Customizer + message form | **Areas** — Numbered cards, local market copy |
| ![Book a Showing](docs/marketplace/screenshots/07-book.png) | |
| **Book a Showing** — Showing request form, agent selector | |

*Full-resolution screenshots and brand assets: `docs/marketplace/screenshots/` and `public/images/brand/`.*

---

## What it is

A WordPress theme that gives a land or farm real estate office everything it needs in one zip — no page builder, no ACF, no IDX plugin required. Buyers customize identity, colors, and content from the WordPress admin. Developers get a clean [Sage 11](https://roots.io/sage/) codebase with Blade, Tailwind v4, and Vite 8.

---

## Built with

| Layer | Technology |
| --- | --- |
| **Theme framework** | [Roots Sage 11](https://roots.io/sage/) + [Acorn](https://roots.io/acorn/) |
| **Templating** | Blade (Laravel views inside WordPress) |
| **CSS** | Tailwind CSS v4 + custom design system (`keystone.css`) |
| **Build tool** | Vite 8 |
| **WordPress** | 6.6+, PHP 8.3+, GPLv2+ |
| **Block editor** | Core Gutenberg — 22 custom dynamic blocks, no page builder |
| **Admin** | Custom Gutenberg blocks for pages; classic metaboxes for listings, agents, and bookings |

---

## What's included

### Gutenberg block system (22 custom blocks)

All marketing pages are built with WordPress blocks. Every block renders server-side so the editor canvas matches the live page exactly.

| Block | Purpose |
| --- | --- |
| `Home Hero` | Full-width hero with integrated listing search (type, price, acreage, area) |
| `Page Hero` | Standard page hero with photo, eyebrow, CTA buttons |
| `Intent Cards` | Buy / Sell / Tour three-card section with help notes |
| `Featured Listings Spotlight` | Dynamic grid of featured listings from WP |
| `Booking Form Section` | Showing request form with editable header |
| `Market Stats` | Four editable market statistic tiles |
| `How It Works` | Four-step tour process |
| `Agent Tools` | Home value estimator + listing alert forms |
| `Area Grid` | Up to six area cards with local market copy |
| `CTA Band` | Full-width call-to-action with two buttons, three background styles |
| `Intro Section` | Eyebrow / title / lede text |
| `Listing Grid` | Filter toolbar, card grid, and map view |
| `Tools Section` | Land loan + pre-qual calculators |
| `How We Work` | Three-step office process section |
| `Office Info` | Address, phone, email, hours from Customizer |
| `Contact Form` | Office info + message form side by side |
| `FAQ List` | FAQ with accordion, plain, or numbered display |
| `Reviews` | Demo review cards |
| `SEO Content Block` | Buying guide copy and scan cards |
| `Booking Note + Form` | Standalone booking page note and form |
| `Agent List` | Agent grid with photo, stats, designations, and social links |
| `Custom Block` | Renders blocks created in the Block Generator |

**Block Generator** (Tools → Block Generator) lets admins create new custom blocks — name, fields, icon — without writing PHP or JavaScript. Field types: text, textarea, URL, image (media picker), toggle.

**Migrate to Blocks** (Tools → Migrate to Blocks) converts any existing page copy from the legacy `ks_*` meta format to block content in one click.

### Block customization

Every block has a live **ServerSideRender** preview in the editor so what you see in the canvas matches what visitors see. The **Inspector Controls** sidebar exposes:

- **Typography** — heading size (sm / default / lg / xl), heading weight, body text size, alignment
- **Images** — WordPress media library picker with thumbnail preview and remove button
- **Hero options** — focal point, overlay strength (slider + preset), hero height, text alignment
- **CTA Band** — background (light / forest accent / dark ink), content alignment
- **FAQ List** — format (definition list / flat / accordion with details/summary), list icon, numbered questions
- **Area Grid** — column count (2 / 3 / 4), show/hide card numbers
- **Spotlight** — listing count, grid columns
- **Reviews** — column count, show/hide star rating, show/hide photo
- **How We Work** — step marker style (numbered / checkmark / arrow / dot), vertical or horizontal layout
- Per-block show/hide toggles for optional sub-sections

### Pages and templates

| Page | Blocks pre-built on install |
| --- | --- |
| **Home** | Home Hero · Intent Cards · Spotlight · How It Works · Booking Section · Market Stats · Agent Tools · SEO Content · FAQ · Reviews · CTA Band |
| **Listings** | Page Hero · Listing Grid · CTA Band |
| **Areas** | Page Hero · Intro · Area Grid · CTA Band |
| **Guide** | Page Hero · Tools Section · FAQ · CTA Band |
| **Agents** | Page Hero · Intro · Agent List · How We Work · CTA Band |
| **Contact** | Page Hero · Contact Form · CTA Band |
| **Book a showing** | Page Hero · Booking Note + Form · FAQ |
| **Blog** | Page Hero · CTA Band |

### Listings, agents, and showings

- **Listing** CPT — type, price, beds, baths, sqft, acres, township, MLS, status; plus property details, utilities (water, sewer, heating, cooling), HOA, land & farm fields (tillable/pasture acres, outbuildings), flood zone, school district, virtual tour, video tour, floor plan, open house date/time, green/eco features, smart home chips
- **Agent** CPT — photo, bio, performance stats (homes sold, volume, avg DOM, list-to-sale ratio, review count), certifications/designations, social links (Facebook, Instagram, LinkedIn, YouTube), intro video URL, calendar booking URL, mobile number, awards, team name
- **Booking** CPT — showing type, date, time, assigned agent, client contact; buyer type, attendees, comm preference; pipeline (Requested → Confirmed → Completed)
- Listing filter: type, price, acreage, area, status, sort; grid and map views
- **Listing comparison** — compare up to 3 listings side-by-side in a full-screen modal
- **Saved listings** — heart icon saves to `localStorage`; floating button opens slide-out drawer
- **Sticky CTA bar** — price + "Book a showing" after hero scrolls out of view on single listing pages
- **Share & print** — Web Share API with clipboard fallback; print-friendly property flyer layout
- **Recently viewed** — up to 5 listings tracked in `localStorage`, shown as chips on detail pages
- **Nearby listings** — server-rendered same-township listings, no API required
- **Market snapshot** — `[acreline_market_snapshot]` shortcode powered by Theme Settings → Market tab
- **Mortgage calculator** — built-in, no plugin or API; editable rate, term, and down payment
- **Media picker** — native WP media library picker for all image URL fields (listing photos, floor plans, agent photos)

### Customizer (Appearance → Customize)

| Section | Controls |
| --- | --- |
| **Identity** | Brand name, tagline, phone, email, address, hours, header CTA label + URL, footer blurb, demo banner toggle, author credit toggle |
| **Colors** | Eight presets (Forest, Clay, Navy, Burgundy, Harvest, Lake, Orchard, Charcoal) + per-color accent, paper, and ink pickers |
| **Header** | Sticky toggle, standard / compact size |
| **Top Bar** | Enable toggle; color style (Dark / Accent / Light / Custom); custom bg + text colors; announcement badge, message, and link; CTA pill button; show/hide for phone, email, address, hours; social icon toggles (Facebook, Instagram, YouTube, LinkedIn, X); allow-dismiss toggle |
| **Typography** | Five font families for display, body, nav, button, mono; base size (14–20 px); heading weight (500 / 600 / 700) |
| **Social links** | Facebook, Instagram, YouTube, LinkedIn, X |
| **GitHub** | Token for the one-click theme updater |

### Top bar

Acreline 1.4.0 adds a configurable slim bar above the header for desktop visitors (hidden at < 900 px via CSS). On mobile, every enabled item — announcement, contact details, social icons, and CTA — surfaces automatically at the foot of the slide-out mobile nav so nothing is hidden on small screens.

| Slot | What it can show |
| --- | --- |
| Left | Social icon links (platform toggles + URL from Social Links section) |
| Centre | Announcement badge pill + message text, optionally hyperlinked |
| Right | Phone, email, address, hours (pulled from Identity); optional CTA pill button |
| Dismiss | Optional × button — stores dismissed state in `sessionStorage` |

Color style presets: **Dark** (ink bg), **Accent** (brand green), **Light** (paper bg), **Custom** (pick any bg + text colors). The sticky header shifts its `top` offset automatically so it sits flush below the bar.

### Admin pages

| Page | Path | Description |
| --- | --- | --- |
| **Acreline Setup** | Appearance → Acreline Setup | Five-step wizard: brand, phone, colors, demo content |
| **Acreline Settings** | Appearance → Acreline Settings | Tabbed settings page — listing, agent, booking, market, labels, and general options with iOS-style toggles |
| **Acreline Support** | Appearance → Acreline Support | Quick start, feature reference, shortcode cheatsheet, FAQ accordion, and changelog |

### Other features

- Native `<title>`, `<meta name="description">`, canonical, Open Graph, and Twitter tags — no SEO plugin required; yields automatically when Yoast, Rank Math, SEOPress, or AIOSEO is active
- Eight named color schemes; live preview in Customizer
- WordPress Primary + Footer nav menus, custom logo, footer widgets, breadcrumbs
- BreadcrumbList JSON-LD structured data
- Translation-ready `acreline` text domain
- GPLv2+ license; Sage / Acorn remain MIT

---

## What makes Acreline different

| Feature | Acreline | Generic real-estate themes |
| --- | --- | --- |
| No page builder required | ✅ | Page builder license often required |
| No ACF required | ✅ | ACF Pro often bundled or required |
| No IDX/MLS plugin | ✅ | IDX subscription often needed |
| Listing comparison modal | ✅ | Rare without premium add-ons |
| Saved listings in localStorage | ✅ | Usually requires a plugin or login |
| Built-in mortgage calculator | ✅ | Usually a plugin |
| Market snapshot shortcode | ✅ | Usually a plugin |
| Open house date + virtual tour fields | ✅ | Often paid add-ons |
| Agent performance stats | ✅ | Rarely included |
| Desktop-only top bar with mobile fallback | ✅ | Rarely included |
| Block Generator (no-code custom blocks) | ✅ | Unique to Acreline |
| Clean Sage 11 codebase | ✅ | Most themes use custom OOP or Classic |
| 22 custom server-side Gutenberg blocks | ✅ | Most use Classic or shortcodes only |
| Fiction-only concept demo | ✅ | Required for honest marketplace sales |

---

## Minimal plugin footprint

| Need | What to install |
| --- | --- |
| Public site | **Theme zip only** — listings, agents, and bookings are registered in the theme |
| Listings that survive a theme switch | **Acreline Core** (`acreline-core.zip`) — store-correct home for those post types |
| CSS that survives parent updates | **Acreline Child** (`acreline-child.zip`) |
| SEO | **Nothing** — native tags are built in; add Yoast or Rank Math if preferred, the theme yields |

Do **not** install ACF, a page builder, or an IDX plugin. The fields are already there.

---

## System requirements

|  |  |
| --- | --- |
| **WordPress** | 6.6 or later |
| **PHP** | 8.3 or later |
| **Server** | Apache or Nginx (no special modules) |
| **Composer / npm** | **Not required on the host** — the theme zip includes compiled assets and production vendor |

---

## Install from a marketplace zip

**Do not clone this repo onto the host.** Buyers receive a built zip that needs no Composer or npm.

1. Extract the outer `acreline-*.zip`. Do not upload the outer file in wp-admin.
2. **Appearance → Themes → Add New → Upload Theme** → choose the inner **`acreline.zip`**. Activate. The folder must stay **`acreline`**.
3. **Appearance → Acreline Setup** — run the setup wizard (office identity, color style, optional demo content).
4. **Appearance → Customize → Identity** — fine-tune brand name, phone, email, address, hours, and the header CTA. Upload a logo under **Site Identity**.
5. **Appearance → Customize → Top Bar** — optionally enable the announcement/contact bar above the header.
6. Optional: upload `acreline-child.zip`, then `acreline-core.zip`.
7. **Tools → Seed Acreline demo** remains available if you prefer to load inventory outside the wizard.

Full walkthrough, screenshots, and field reference: open `Documentation/index.html` from your seller pack.

---

## Purchasing on matthummel.com

The Acreline product page at [matthummel.com/projects/acreline/](https://matthummel.com/projects/acreline/) shows the same screenshot set used in this README (`docs/marketplace/screenshots/01–07`). The checkout is at [matthummel.com/product/acreline/](https://matthummel.com/product/acreline/).

After purchase you receive the full seller pack (`acreline-<version>.zip`) — the same file built by `bin/build-install-pack.sh`. This contains:

| File / folder | What it is |
| --- | --- |
| `acreline.zip` | Upload this in Appearance → Themes |
| `acreline-child.zip` | Optional child theme |
| `acreline-core.zip` | Optional companion plugin (keeps listings if you switch themes) |
| `Documentation/` | HTML docs — open `index.html` first |
| `Demos/` | Seed notes (Tools → Seed Acreline demo) |
| `Licensing/` | GPLv2 notice and credits |

The product page screenshots are generated from the live concept demo and stored in `docs/marketplace/screenshots/`. If you embed them in your own site or store listing, use the paths from that folder — they are updated with every release.

---

## One-click update from GitHub

CI publishes a built zip on the `theme-latest` GitHub release after every push to `main`.

1. **Appearance → Update Theme** in wp-admin.
2. Paste a fine-grained GitHub PAT with **Contents: Read** (or set `KS_GITHUB_TOKEN` in `wp-config.php` or Customizer → GitHub).
3. Click **Install latest zip from GitHub**. Pages, posts, and uploads are not touched.

---

## Local development (this git repo)

The git repo is the Sage 11 **source**. It does not include compiled assets or `vendor/`.

```bash
# Bootstrap WordPress + install deps + build + seed (idempotent)
bin/setup-wp.sh

# Run the dev server (after setup)
wp server --path="$HOME/wp" --host=0.0.0.0 --port=8080 --allow-root

# Build Vite assets (required — Vite manifest not found if skipped)
npm run build

# PHP lint
./vendor/bin/pint --test   # check only
./vendor/bin/pint          # auto-fix
```

Browse `http://localhost:8080/` and its pages: `/listings`, `/areas`, `/guide`, `/agents`, `/contact`, `/blog`.

Clear Blade cache after editing templates:
```bash
wp acorn view:clear --path="$HOME/wp" --allow-root
```

### Build scripts

| Script | Output |
| --- | --- |
| `bin/build-theme-zip.sh` | `dist-theme/acreline.zip` — upload in Appearance → Themes |
| `bin/build-install-pack.sh` | `dist-install/acreline-<version>.zip` — full seller pack |
| `bin/build-marketplace-pack.sh` | `dist-marketplace/` — seller pack + `SELLING.md` (repo only) |

### Non-obvious gotchas

- **You must run `npm run build`** or every page shows "Vite manifest not found."
- **Do not symlink `vendor/`** to another git worktree. Blade maps to the tree's `app/`; a mismatch causes 500 errors.
- **Blade template changes are cached.** Run `wp acorn view:clear` after editing `.blade.php` files.
- **Vite `base` path** is hard-coded to `/wp-content/themes/acreline/public/build/`. If you rename the folder, update `vite.config.js`.

---

## Template map

| Template | File | Used for |
| --- | --- | --- |
| `page-home` | `resources/views/template-home.blade.php` | Homepage |
| `page-listings` | `template-listings.blade.php` | /listings |
| `page-areas` | `template-areas.blade.php` | /areas |
| `page-guide` | `template-guide.blade.php` | /guide |
| `page-agents` | `template-agents.blade.php` | /agents |
| `page-contact` | `template-contact.blade.php` | /contact |
| `page-book` | `template-book.blade.php` | /book-a-showing |
| `page-blog` | `template-blog.blade.php` | /blog |
| `single-listing` | `single-listing.blade.php` | Listing detail |
| `single-agent` | `single-agent.blade.php` | Agent profile |
| `single` | `single.blade.php` | Blog posts |
| `index` | `index.blade.php` | Fallback archive |

---

## Documentation

| File | For |
| --- | --- |
| **This README.md** | GitHub visitors, theme stores |
| [`readme.txt`](readme.txt) | WordPress / ThemeForest parser |
| [`SUPPORT.md`](SUPPORT.md) | Support links |
| [`CHANGELOG.md`](CHANGELOG.md) | User-facing history |
| [`CREDITS.md`](CREDITS.md) · [`LICENSE.md`](LICENSE.md) | Fonts, third-party licenses, GPL |
| [`BRAND.md`](BRAND.md) | Brand kit — name, Forest palette, logo marks |
| [`DEVELOPMENT.md`](DEVELOPMENT.md) | Local dev setup, build commands, deploy notes |
| [`AGENTS.md`](AGENTS.md) | Cursor Cloud agent setup (VM bootstrap, WP-CLI) |
| [`docs/marketplace/index.html`](docs/marketplace/index.html) | Buyer docs hub — open this first |
| [`docs/marketplace/buyer-guide.html`](docs/marketplace/buyer-guide.html) | Install, Customizer, top bar, fields, menus, FAQ |
| [`docs/marketplace/themeforest-listing.md`](docs/marketplace/themeforest-listing.md) | Paste-ready store listing copy |
| [`docs/marketplace/screenshots/`](docs/marketplace/screenshots/) | 01–07 item images used on matthummel.com |
| [`public/images/brand/`](public/images/brand/) | House mark + horizontal lockup (SVG, GPLv2) |

---

## Branding

| Asset | Path |
| --- | --- |
| House mark (48×48) | `public/images/brand/acreline-mark.svg` |
| Horizontal lockup | `public/images/brand/acreline-lockup.svg` |
| Forest palette ink | `#141210` |
| Forest palette paper | `#f5f4f1` |
| Forest palette accent | `#1f6b4a` |

Upload your office logo under **Appearance → Customize → Site Identity**. The Acreline house mark and lockup are GPLv2 originals — not NAR, HUD, or MLS artwork.

Eight built-in color styles: **Forest** (default) · Clay · Navy · Burgundy · Harvest · Lake · Orchard · Charcoal. All swappable from Customizer → Colors.

---

## License

GPLv2 or later. See [`license.txt`](license.txt), [`LICENSE.md`](LICENSE.md), and [`CREDITS.md`](CREDITS.md). Sage / Acorn remain MIT.

Author: [Matt Hummel](https://matthummel.com/) · Theme URI: [acreline.matthummel.com](https://acreline.matthummel.com/)
