# Acreline — WordPress theme for farms, land, and historic homes

[![Deploy theme zip](https://github.com/matthummel-pa/wp-acreline/actions/workflows/deploy.yml/badge.svg)](https://github.com/matthummel-pa/wp-acreline/actions/workflows/deploy.yml)

**Acreline** is a WordPress real estate theme for rural land offices: searchable listings, agent profiles, and showing requests — built for farms, acreage, and historic homes, not a generic luxury brokerage skin.

| | |
| --- | --- |
| **Live demo** | [acreline.matthummel.com](https://acreline.matthummel.com/) |
| **Product page** | [matthummel.com/projects/acreline/](https://matthummel.com/projects/acreline/) |
| **Buy / checkout** | [matthummel.com/product/acreline/](https://matthummel.com/product/acreline/) |
| **GitHub** | [`matthummel-pa/wp-acreline`](https://github.com/matthummel-pa/wp-acreline) |
| **Support** | [SUPPORT.md](SUPPORT.md) · [GitHub Issues](https://github.com/matthummel-pa/wp-acreline/issues) |
| **Author** | [Matt Hummel](https://matthummel.com/) |
| **Install folder** | **`acreline`** — leave this name, Vite asset URLs depend on it |
| **Version** | See `style.css` |

> **Fiction only.** Listings, market stats, contact details, and appointments in the demo are sample data. Not a live MLS, licensed brokerage, or booking system. Demo phones use the `555` exchange; emails use `@acreline-concept.test`.

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
| **Block editor** | Core Gutenberg — 21 custom dynamic blocks, no page builder |
| **Admin** | Custom Gutenberg blocks for pages; classic metaboxes for listings, agents, and bookings |

---

## What's included

### Gutenberg block system (21 custom blocks)

All marketing pages are built with actual WordPress blocks — no custom HTML blocks, no shortcodes. Every block renders server-side so the editor canvas matches the live page exactly.

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
| `Custom Block` | Renders blocks created in the Block Generator |

**Block Generator** (Tools → Block Generator) lets admins create new custom blocks — name, fields, icon — without writing PHP or JavaScript. Field types: text, textarea, URL, image (media picker), toggle.

**Migrate to Blocks** (Tools → Migrate to Blocks) converts any existing page copy from the legacy `ks_*` meta format to block content in one click.

### Block customization

Every block has a live **ServerSideRender** preview in the editor so what you see in the canvas matches what visitors see. The **Inspector Controls** sidebar exposes:

- **Typography** — heading size (sm / default / lg / xl), heading weight, body text size, alignment
- **Images** — WordPress media library picker with thumbnail preview and remove button
- **Hero options** — focal point, overlay strength (slider + preset), hero height, text alignment
- **CTA Band** — background (light / forest accent / dark ink), content alignment
- **FAQ List** — format (definition list / flat / accordion with details/summary), list icon (none / → / ✓), numbered questions
- **Area Grid** — column count (2 / 3 / 4), show/hide card numbers
- **Spotlight** — listing count, grid columns
- **Reviews** — column count, show/hide star rating, show/hide photo
- **How We Work** — step marker style (numbered / checkmark / arrow / dot), vertical or horizontal layout
- Per-block show/hide toggles for optional sub-sections (side photo, tools, map, etc.)

### Pages and templates

| Page | Blocks pre-built on install |
| --- | --- |
| **Home** | Home Hero · Intent Cards · Spotlight · How It Works · Booking Section · Market Stats · Agent Tools · SEO Content · FAQ · Reviews · CTA Band |
| **Listings** | Page Hero · Listing Grid · CTA Band |
| **Areas** | Page Hero · Intro · Area Grid · CTA Band |
| **Guide** | Page Hero · Tools Section · FAQ · CTA Band |
| **Agents** | Page Hero · Intro · How We Work · CTA Band |
| **Contact** | Page Hero · Contact Form · CTA Band |
| **Book a showing** | Page Hero · Booking Note + Form · FAQ |
| **Blog** | Page Hero · CTA Band |

### Listings, agents, and showings

- **Listing** CPT — type (home / farm / land / historic), price, beds, baths, sqft, acres, township, MLS, virtual tour, map pin, status (active / pending / sold)
- **Agent** CPT — job title, license state + number, MLS/NRDS, phone, email, bio, specialties, service areas, social links
- **Booking** CPT — showing type (in-person / virtual), date, time, assigned agent, client contact; pipeline (Requested → Confirmed → Completed); WP-CLI and list-row advance actions
- Listing filter: type, price, acreage, area, status, sort; grid and map views

### Customizer (Appearance → Customize)

| Section | Controls |
| --- | --- |
| **Identity** | Brand name, tagline, phone, email, address, hours, header CTA label + URL, footer blurb, demo banner toggle, author credit toggle |
| **Colors** | Eight presets (Forest, Clay, Navy, Burgundy, Harvest, Lake, Orchard, Charcoal) + per-color accent, paper, and ink pickers |
| **Header** | Sticky toggle, standard / compact size |
| **Typography** | Five font families for display, body, nav, button, mono; base size (14–20 px); heading weight (500 / 600 / 700) |
| **Social links** | Facebook, Instagram, YouTube, LinkedIn, X |
| **GitHub** | Token for the one-click theme updater |

### Other included features

- Native `<title>`, `<meta name="description">`, canonical, Open Graph, and Twitter tags — no SEO plugin required; yields automatically when Yoast, Rank Math, SEOPress, or AIOSEO is active
- Eight named color schemes; live preview in Customizer
- WordPress Primary + Footer nav menus, custom logo, footer widgets, breadcrumbs
- Translation-ready `acreline` text domain
- GPLv2+ license; Sage / Acorn remain MIT

---

## Minimal plugin footprint

| Need | What to install |
| --- | --- |
| Public site | **Theme zip only** — listings, agents, and bookings are registered in the theme |
| Listings that survive a theme switch | **Acreline Core** (`acreline-core.zip`) — the store-correct home for those post types |
| CSS that survives parent updates | **Acreline Child** (`acreline-child.zip`) |
| SEO | **Nothing** — native tags are built in; add Yoast or Rank Math if you prefer a plugin, the theme yields |

Do **not** install ACF, a page builder, or an IDX plugin. The fields are already there.

---

## System requirements

| | |
| --- | --- |
| **WordPress** | 6.6 or later |
| **PHP** | 8.3 or later |
| **Server** | Apache or Nginx (no special modules) |
| **Composer / npm** | **Not required on the host** — the theme zip includes compiled assets |

---

## Install from a marketplace zip

**Do not clone this repo onto the host.** Buyers receive a built zip that needs no Composer or npm.

1. Extract the outer `acreline-*.zip`. Do not upload the outer file in wp-admin.
2. **Appearance → Themes → Add New → Upload Theme** → choose the inner **`acreline.zip`**. Activate. The folder must stay **`acreline`**.
3. **Appearance → Acreline Setup** — run the setup wizard (office identity, color style, optional demo content).
4. **Appearance → Customize → Identity** — fine-tune brand name, phone, email, address, hours, and the header CTA. Upload a logo under **Site Identity**.
5. Optional: upload `acreline-child.zip`, then `acreline-core.zip`.
6. **Tools → Seed Acreline demo** remains available if you prefer to load inventory outside the wizard.

Full walkthrough, screenshots, and field reference: open `Documentation/index.html` from your seller pack.

---

## One-click update from GitHub

CI publishes a built zip on the `theme-latest` GitHub release after every push to `main`.

1. **Appearance → Update Theme** in wp-admin.
2. Paste a fine-grained GitHub PAT with **Contents: Read** (or set `KS_GITHUB_TOKEN` in `wp-config.php` or Customizer → GitHub).
3. Click **Install latest zip from GitHub**. Pages, posts, and uploads are not touched.

---

## Documentation

| File | For |
| --- | --- |
| **This README.md** | GitHub visitors, theme stores |
| [`readme.txt`](readme.txt) | WordPress / ThemeForest parser |
| [`SUPPORT.md`](SUPPORT.md) | Support links |
| [`CHANGELOG.md`](CHANGELOG.md) | User-facing history |
| [`CREDITS.md`](CREDITS.md) · [`LICENSE.md`](LICENSE.md) | Fonts, third-party licenses, GPL |
| [`BRAND.md`](BRAND.md) | Brand kit — name, palette, logo marks |
| [`DEVELOPMENT.md`](DEVELOPMENT.md) | Local dev setup, build commands, deploy notes |
| [`AGENTS.md`](AGENTS.md) | Cursor Cloud agent setup (VM bootstrap, WP-CLI) |
| [`docs/marketplace/index.html`](docs/marketplace/index.html) | Buyer docs hub (start here) |
| [`docs/marketplace/buyer-guide.html`](docs/marketplace/buyer-guide.html) | Install, Customizer, fields, FAQ |
| [`docs/marketplace/themeforest-listing.md`](docs/marketplace/themeforest-listing.md) | Paste-ready store listing copy |

---

## License

GPLv2 or later. See [`license.txt`](license.txt), [`LICENSE.md`](LICENSE.md), and [`CREDITS.md`](CREDITS.md). Sage / Acorn remain MIT.

Author: [Matt Hummel](https://matthummel.com/)
