# Selling Acreline (for Matt)

This is the seller brief — not buyer docs. Buyer HTML is `buyer-guide.html`.

## What actually sells

Realtor themes on Envato move when the demo looks like a working office: search → listings → agent → book a showing. You already have that. Buyers pay for **identity Customizer, menus, a child theme, and a plugin that keeps listings after they switch themes** — not for another “luxury brokerage” skin.

Price band for a niche land/farms theme: **$39–$69** on ThemeForest, **$79** on your own checkout ([matthummel.com/product/acreline/](https://matthummel.com/product/acreline/)). Product landing / ads: [matthummel.com/projects/acreline/](https://matthummel.com/projects/acreline/) (legacy `/concept/acreline/` redirects here). Keep the own-site price higher; Envato takes a large cut.

## Channel by channel

| Channel | Ship | Do not claim |
| --- | --- | --- |
| **Own site** | Full pack + WooCommerce on matthummel.com | Nothing. Best margin. Landing: `/projects/acreline/`. |
| **ThemeForest / Creative Market** | Same pack + live preview URL | “#1 realtor theme.” Show the sample-county land/farms concept honestly. |
| **WordPress.org** | Theme zip only. No plugin inside the theme. CPTs off (`KS_DISABLE_THEME_CPTS`). `readme.txt` + 1200×900 screenshot | “Approved” or “in the picker” until a reviewer says so. Sage + Acorn + Gutenberg-off is a common rejection. Use WP.org later as a **lite** traffic listing, or list **Acreline Core** as a free plugin and sell setup. |

WordPress.org is **not** a cash register. It is distribution. Money there is services (install, copy, listing import) or a paid add-on — never an in-theme upgrade nag.

## Pack

```bash
bin/build-install-pack.sh         # general seller zip
bin/build-marketplace-pack.sh     # same files + this brief copied beside them
```

Creates `dist-install/`:

- `acreline-1.2.3.zip` — **upload this to ThemeForest / Gumroad** (folder inside: `acreline-pack/`)
- `acreline-pack/acreline.zip` — what the buyer uploads in Appearance → Themes
- `acreline-pack/acreline-child.zip`
- `acreline-pack/acreline-core.zip`
- `acreline-pack/Documentation/index.html` — Envato-style docs hub (open this first)
- `acreline-pack/Documentation/` — buyer-guide, branding, customizer, templates, listings, child-theme, translation, faq, requirements, support, sources, credits, changelog
- `acreline-pack/Documentation/assets/` and `screenshots/`
- `acreline-pack/Demos/README.txt`
- `acreline-pack/Licensing/CREDITS.md`, `LICENSE.md`, `license.txt`

`SELLING.md`, `themeforest-listing.md`, and `wordpress-org.md` stay in the repo (and `SELLING.md` is copied beside the pack in `dist-marketplace/`). They are **not** inside the buyer zip.

Upload the **outer** `acreline-*.zip` to Envato (“All files & documentation”). Buyers extract it, then upload `acreline.zip`. Upload **only** `acreline.zip` if you try WordPress.org.

Paste-ready ThemeForest title, excerpt, long description, and attributes: `docs/marketplace/themeforest-listing.md`. WP.org upload rules and Theme Check blockers: `docs/marketplace/wordpress-org.md`.

## Before you submit anywhere

1. Live preview with demo chrome **on** (concept honesty) and a second screenshot with chrome **off** (buyer fantasy).
2. Customize → Identity filled with 555 / `@acreline-concept.test` only.
3. No Unsplash files inside the theme zip.
4. No Freemius, Envato banners, or “Pro” admin ads.
5. Footer credit is optional and `rel="nofollow"`.
6. Theme Check on a stock WP install (warnings on Sage/vendor are expected — document them).

### 1.4.3 demo notes

Public release notes: `CHANGELOG.md`, `readme.txt`, and `docs/marketplace/changelog.html`. Paste store copy from `themeforest-listing.md`.

- **Animate homepage hero image** (`ks_hero_ken_burns`) is **on** by default. Leave it on for cinematic homepage screenshots. Headlines and the search form do not move.
- **Tilt listing search on mobile** (`ks_hero_search_tilt`) is **off** by default (battery / privacy). Turn it on only for a phone recording. It no-ops on desktop, without sensors, or if the visitor declines motion access. iPhone may prompt.
- Both checkboxes live under Customize → Header and are independent. They do not change listings or Identity.

## WP.org remaining blockers (do not paper over)

- Sage / Blade / `vendor` (Acorn) is not what the Themes Team usually ships
- Block editor is disabled on purpose (project rule)
- PHP 8.3+ and a compiled Vite manifest
- Listings are plugin territory — Core must be a **separate** WP.org plugin if you go that route

A future “Acreline Lite” without Acorn would have a real shot at the picker. This repo is the premium / Envato / own-site product.

## GitHub repo About (paste in Settings → General)

GitHub repo is **`matthummel-pa/wp-acreline`**. Paste these on https://github.com/matthummel-pa/wp-acreline/settings :

**Description** (under 350 characters; this is the search snippet):

```
Acreline is a WordPress theme for farms, land, and historic homes — searchable listings, agents, and showing requests. Customizer identity, eight color styles, Sage 11. Demo and support by Matt Hummel.
```

**Website** (product landing + support):

```
https://matthummel.com/projects/acreline/
```

**Topics:** `wordpress-theme`, `wordpress`, `real-estate`, `realtor`, `farms`, `land`, `sage`, `acreline`

Leave the live demo URL in `style.css` Theme URI. Brand kit for screenshots and ads: [`BRAND.md`](../../BRAND.md).

## After a sale

Buyer path is Appearance → Acreline Setup (multi-step wizard). Do not add a license server unless a marketplace requires it. Envato purchase codes are their problem, not a theme options lock.
