# Acreline — WordPress Theme

A [Roots Sage 11](https://roots.io/sage/) theme (Blade + Tailwind CSS v4 + Vite 8, powered by Acorn) for a land-and-farms realtor concept. GitHub repo: [`matthummel-pa/wp-acreline`](https://github.com/matthummel-pa/wp-acreline). The visual design is ported from the static concept in [`matthummel-pa/realtor-keystone-homes-and-land-theme`](https://github.com/matthummel-pa/realtor-keystone-homes-and-land-theme).

## Project rules (Cursor Agent)

Versioned Cursor rules live in [`.cursor/rules/`](.cursor/rules/) as `.mdc` files:

- `keystone-project.mdc` — always on (fiction-only, theme vs live content)
- `theme-sage.mdc` — PHP / Blade (CPTs, PageCopy, query pitfalls)
- `frontend.mdc` — CSS / JS / views (tokens, Vite, homepage search)
- `ui-aesthetics.mdc` — wrap, cards, type, hero contrast (no slate/dark skin)
- `accessibility.mdc` — WCAG 2.2 AA (contrast, focus, 44px, semantics, reduced motion)
- `conversion-ux.mdc` — labels, autocomplete, button states, `aria-live`
- `seo-native.mdc` — native title / description / OG / Twitter; no SEO plugins
- `marketplace.mdc` — ThemeForest / WP.org (plugin territory, WPCS, demo zip, no admin upsells)
- `theme-shop-readme.mdc` — seller README / `readme.txt` / ThemeForest docs (Sage 11 + Vite zip layout)
- `live-wordpress.mdc` — when editing the live WP site via WPVibe

Edit those files when an agent repeats a mistake. Use `.mdc` only (plain `.md` in that folder is ignored).

## Cursor Cloud specific instructions

### What this repo is
This repository is only the **theme** (`wp-content/themes/acreline`). WordPress core is not part of the repo — a throwaway local WordPress install is created outside the repo to run/test the theme.

### System prerequisites (installed during initial VM setup)
PHP 8.3+ (with `mbstring xml curl zip gd intl sqlite3 bcmath`), Composer 2, WP-CLI (`wp`), and Node 20+/22+. The startup update script only refreshes the theme's own dependencies (`composer install`, `npm install`); it intentionally does **not** install system packages, build assets, or start services.

### First-time run in a fresh clone
Run the bootstrap helper — it is idempotent and safe to re-run:

```bash
bin/setup-wp.sh
```

This installs theme deps + builds assets, then stands up WordPress at `~/wp` using the **SQLite Database Integration** plugin (no MySQL needed), symlinks the theme in, seeds concept pages and sample blog posts, and activates the theme. Admin: `/wp-admin` (user `admin`, pass `admin123`).

### Running the site
```bash
wp server --path="$HOME/wp" --host=0.0.0.0 --port=8080 --allow-root
```
Then browse `http://localhost:8080/` (homepage), `/listings`, `/areas`, `/guide`, `/agents`, `/contact`, and `/blog`.

### Build / lint / dev
- Build assets (required — Vite generates the manifest the theme loads): `npm run build`
- Hot-reload dev server for assets: `npm run dev` (in addition to `wp server`)
- PHP lint (Laravel Pint): `./vendor/bin/pint --test` (drop `--test` to auto-fix)

### Non-obvious gotchas
- **You must build assets** (`npm run build`) or every page dies with a "Vite manifest not found" error. Build artifacts live in `public/build/` and are git-ignored.
- **Do not symlink `vendor/` to another git worktree.** Composer maps `App\\` to that tree's `app/`. Blade is loaded from this theme folder. A mismatch 500s the homepage (`Undefined array key "intent_eyebrow"`). Run `composer install` in the theme directory you symlink into `~/wp`.
- **Blade template changes are cached by Acorn.** After editing `.blade.php` files, if you don't see changes, clear the cache: `wp acorn view:clear --path="$HOME/wp" --allow-root` (or `wp acorn optimize:clear`).
- **Vite `base` path is hard-coded** in `vite.config.js` to `/wp-content/themes/acreline/public/build/`. If the theme is installed under a different folder name, update it or built asset URLs will 404.
- Every marketing page has a named Blade template (`Template Name`) plus slug fallback. `App\Support\DemoContent` creates the pages, assigns `_wp_page_template`, and seeds listings / agents / bookings / blog posts. `bin/setup-wp.sh` and `wp ks seed` both call that. The first public request on a fresh install also auto-seeds once (`ks_demo_content_v1`).
- The block editor and patterns are disabled. Edit pages, listings, agents, bookings, and posts through custom-field metaboxes only.
- Showing requests POST to `keystone/v1/bookings` and create Booking posts. Advance status from WP Admin → Bookings.
- Listing inventory is the `listing` CPT (JS reads `window.ACRELINE.listings`). Fallback sample data remains in `resources/js/listings.js`.
- Shared chrome lives in `resources/views/sections/` and `partials/chat.blade.php`.

### Packaging & deploying the theme
- This is a WordPress theme — it ships as an installable theme package to a WordPress host.
- Theme zip (regular Appearance → Upload Theme): `bin/build-theme-zip.sh` → `dist-theme/acreline.zip`. Ships compiled `public/build` and production `vendor/` so the host needs no composer/npm.
- Seller pack (ThemeForest / Gumroad / own site): `bin/build-install-pack.sh` → `dist-install/acreline-*.zip`. Buyers extract it, then upload the inner `acreline.zip`. Includes child theme, Acreline Core, and `Documentation/requirements.html` (host needs + listing fields). Do not upload the outer zip into WordPress.
- Hosts can also update in place from **Appearance → Update Theme**. `.github/workflows/deploy.yml` publishes both zips on the `theme-latest` GitHub release. Token: Customizer → GitHub, the updater screen, or `KS_GITHUB_TOKEN`.
- `bin/build-marketplace-pack.sh` copies the install pack to `dist-marketplace/` and adds `SELLING.md` for Matt only. WordPress.org is a later lite path — Sage + Gutenberg-off will not sail through first review. See `docs/marketplace/SELLING.md` and `.cursor/rules/marketplace.mdc`.
