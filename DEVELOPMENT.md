# Acreline — Developer reference

This file covers everything you need to work on the theme **source** locally, build release zips, and push updates to a live host. For cloud agent bootstrapping see [`AGENTS.md`](AGENTS.md). For the public product description see [`README.md`](README.md).

---

## Stack

| Layer | Technology |
| --- | --- |
| Theme framework | [Roots Sage 11](https://roots.io/sage/) + [Acorn](https://roots.io/acorn/) |
| Templating | Blade |
| CSS | Tailwind CSS v4 + `resources/css/keystone.css` design system |
| Build tool | Vite 8 |
| Block editor | Core Gutenberg — 21 custom dynamic blocks registered in `app/blocks.php` |
| Block editor JS | `resources/js/blocks/index.js` (separate Vite entry) |
| PHP style | Laravel Pint (`./vendor/bin/pint`) |
| WordPress | 6.6+, PHP 8.3+, SQLite (local) or MySQL (host) |

---

## Local setup

```bash
# One-time bootstrap (idempotent — safe to re-run)
bin/setup-wp.sh

# Start the dev server
wp server --path="$HOME/wp" --host=0.0.0.0 --port=8080 --allow-root
```

Admin: `http://localhost:8080/wp-admin` — user `admin`, password `admin123`.

`bin/setup-wp.sh`:
- Runs `composer install` + `npm run build` in the theme directory
- Downloads WordPress core and creates a SQLite-backed install at `~/wp`
- Symlinks the theme into `~/wp/wp-content/themes/acreline`
- Activates the theme and seeds demo content (pages, listings, agents, blog posts)

---

## Daily build commands

```bash
npm run build            # production build (required before any page loads)
npm run dev              # Vite HMR alongside wp server

./vendor/bin/pint        # auto-fix PHP style
./vendor/bin/pint --test # check only (used in CI)
```

After editing `.blade.php` files, clear the Blade cache if changes don't appear:

```bash
wp acorn view:clear --path="$HOME/wp" --allow-root
```

---

## Key file locations

| Path | Purpose |
| --- | --- |
| `resources/css/keystone.css` | Design tokens and component styles |
| `resources/css/card-lock.css` | Card chrome — **do not delete or move** |
| `resources/css/block-options.css` | CSS modifier classes for block advanced settings |
| `resources/css/editor.css` | Editor-only overrides (hero height caps, form pointer-events) |
| `resources/css/app.css` | Entry — imports keystone, block-options, card-lock |
| `resources/js/app.js` | Frontend JS entry |
| `resources/js/blocks/index.js` | Gutenberg block registrations (separate Vite entry) |
| `app/blocks.php` | Block `register_block_type()` + all PHP render callbacks |
| `app/block-generator.php` | Tools → Block Generator admin page |
| `app/Support/BlockMigration.php` | Migrate `ks_*` meta → block markup |
| `app/Support/PageCopy.php` | Legacy schema (kept for SEO fallbacks and migration) |
| `app/Support/DemoContent.php` | Demo seed — pages, listings, agents, posts |
| `app/Support/Identity.php` | Customizer → Identity values |
| `app/customizer.php` | All Customizer sections + controls |
| `app/admin.php` | Classic metaboxes for listing / agent / booking CPTs |
| `app/setup.php` | Theme setup, Gutenberg enablement, editor CSS injection |
| `app/post-types.php` | CPT registrations (fallback when Acreline Core is absent) |
| `resources/views/` | Blade templates |
| `resources/seed/` | Seeded agent + listing JSON, blog post HTML |
| `vite.config.js` | Vite config — `base` is `/wp-content/themes/acreline/public/build/` |
| `public/build/` | Compiled assets (gitignored, built by CI) |
| `.github/workflows/deploy.yml` | CI: Pint + Node → deploy theme zip + seller pack |

---

## Gutenberg blocks overview

Gutenberg is enabled for `page` and `post`. The `listing`, `booking`, and `agent` CPTs continue using classic metaboxes.

### PHP side (`app/blocks.php`)

- `ks_register_blocks()` — registers all 21 blocks via `register_block_type()`
- Shared attribute groups: `$typo` (heading size/weight/body/align), `$heroBase` (imageId, overlayPreset, imagePosition, heroHeight, textAlign)
- Helper functions: `ks_head_class()`, `ks_hero_class()`, `ks_hero_image_url()`, `ks_veil_style()`
- Each block has a `ks_render_*()` function that outputs the section HTML using `ob_start()`
- CSS modifier classes applied at render time via `block-options.css` (e.g., `ks-band--accent`, `ks-faq--accordion`, `ks-cols--2`)

### JS side (`resources/js/blocks/index.js`)

- Separate Vite entry built to `public/build/assets/index-*.js`
- Loaded via `enqueue_block_editor_assets` in `app/blocks.php`
- Each block uses `ServerSideRender` for a live canvas preview (PHP render output via REST)
- Shared components: `MediaPicker` (WP media library), `SsrEdit` (SSR + sidebar), `TypographyPanel`, `HeroImagePanel`
- Attributes edited in **Inspector Controls** sidebar panels

### Editor CSS (`resources/css/editor.css`)

Loaded after `app.css` in the block editor. Provides:
- Hero height cap (prevents full-viewport height in canvas)
- Frozen Ken-Burns animation during editing
- `pointer-events: none` on interactive forms (booking, contact, listing filter)
- `.reveal` opacity disabled so blocks are immediately visible

### Block Generator

**Tools → Block Generator** — create custom blocks without code. Definitions stored in `wp_options` as `ks_custom_blocks`. Served via REST at `acreline/v1/custom-blocks`.

### Migration

**Tools → Migrate to Blocks** — runs `BlockMigration::migrateAll()` to convert legacy `ks_*` post meta to serialized Gutenberg block markup. Idempotent; skips pages that already have block content.

---

## Content vs. theme

Theme files and WordPress **content** are separate surfaces.

| Surface | What lives there | How it updates |
| --- | --- | --- |
| **Theme** | Blade, PHP, CSS, JS, Gutenberg blocks | `git push main` → CI → `acreline.zip` on `theme-latest` release |
| **Content** | Pages, block attributes, listings, agents, posts, Customizer mods | Lives in WordPress; editing theme files does **not** change live content |

Pushing to `main` does **not** rewrite live pages, listings, or uploads. CI does **not** FTP to the host.

---

## Package for distribution

```bash
bin/build-theme-zip.sh          # → dist-theme/acreline.zip  (WP Appearance → Upload Theme)
bin/build-install-pack.sh       # → dist-install/acreline-<version>.zip  (seller pack)
bin/build-marketplace-pack.sh   # same seller pack + SELLING.md (for Matt only)
```

**Theme zip** (`dist-theme/acreline.zip`): compiled `public/build/`, production `vendor/`, PHP/Blade source, `style.css`, `readme.txt`, `screenshot.png`. The host needs no Composer or npm.

**Seller pack** (`dist-install/acreline-<version>.zip`): inner theme zip + `acreline-child/` + `acreline-core/` + `Documentation/` HTML hub. Buyers extract the outer zip; they upload only the inner `acreline.zip` in wp-admin.

CI (`.github/workflows/deploy.yml`) runs these scripts and publishes both zips on the `theme-latest` release after every push to `main`.

---

## Seeding demo content

```bash
wp ks seed --path="$HOME/wp" --allow-root
```

Or from wp-admin: **Tools → Seed Acreline demo**. Idempotent — existing posts are updated, not duplicated.

`DemoContent::seed()` now calls `buildPageBlocks()` after `attachHeroImages()`, which writes serialized Gutenberg block markup into `post_content` for every marketing page. Re-seeding preserves existing block content (it skips pages that already contain `<!-- wp:` markup).

---

## Deploying to a live host

### Push and wait for CI

```
git push origin main
# Wait for "Deploy theme zip" Actions workflow to finish
```

Assets: `acreline.zip` and `acreline-<version>.zip` on the `theme-latest` GitHub release.

### Install on the host

**Appearance → Update Theme** → **Install latest zip from GitHub**. Needs a fine-grained PAT:
- **Contents: Read** — to download the zip
- **Actions: Read and write** — only if you want to trigger a rebuild from wp-admin

Or set the token in `wp-config.php` as `define('KS_GITHUB_TOKEN', 'ghp_...')`, in Customizer → GitHub, or run:

```bash
wp ks theme-update --path="/path/to/wp"  # download + install latest zip
wp ks theme-build  --path="/path/to/wp"  # trigger a GitHub Actions rebuild
```

Theme files only — pages, posts, and uploads are never touched.

### Live demo cache

After a theme update, `/?fresh=1` or purge **Site Tools → Speed → Caching** if nginx serves a stale `/`. `wp cache flush` only clears the object cache.

---

## Keeping versions in sync

Bump all three in the same commit:

1. **`style.css`** → `Version:`
2. **`readme.txt`** → `Stable tag:` + changelog entry
3. **`CHANGELOG.md`** → user-facing note

Versions that disagree across these files will confuse the WP.org parser and the theme updater.

---

## Gotchas

| Symptom | Cause | Fix |
| --- | --- | --- |
| White screen "Vite manifest not found" | `public/build/` is missing | `npm run build` |
| Homepage 500 `Undefined array key "intent_eyebrow"` | `vendor/` symlinked from another worktree | `composer install` in the theme directory you symlinked |
| Block editor not loading on `page`/`post` | Acorn not booted yet | Check `bin/setup-wp.sh` ran; `wp acorn key:generate` if needed |
| Blade edits not visible | Acorn view cache | `wp acorn view:clear --path="$HOME/wp" --allow-root` |
| Old hero images showing in editor | SSR fetches via REST; browser-cached response | Hard-refresh or wait for the `stale-while-revalidate` window |
| Block Generator block doesn't render | `acreline/custom` needs a valid `blockId` | Choose a block in the editor canvas dropdown |
| `wp ks theme-update` 404s | Running PHP still has old repo slug | Upload inner `acreline.zip` once manually; then the updater works |

---

## Design system notes

- **`keystone.css`** — source of all tokens and component CSS. Edit layout/component CSS here.
- **`card-lock.css`** — locked card chrome (white faces, thin shadow, no green bars). Imported **last** in `app.css`; do not delete or move it above `keystone.css`.
- **`block-options.css`** — CSS modifier classes for block advanced settings. Class names must match what the PHP render callbacks emit.
- **`editor.css`** — editor-only overrides, loaded after `app.css` in the block editor canvas.
- Color tokens come from `Identity::cssVariables()` (Customizer-driven), overriding the defaults in `keystone.css :root`.

---

## Architecture diagram

```
wp-acreline (git)
├── app/
│   ├── blocks.php          ← block registration + render callbacks
│   ├── block-generator.php ← Tools → Block Generator admin page
│   ├── admin.php           ← CPT metaboxes (listing / agent / booking)
│   ├── setup.php           ← theme setup, Gutenberg enable, editor CSS
│   ├── customizer.php      ← Customizer sections + controls
│   ├── post-types.php      ← CPT fallback when Acreline Core absent
│   └── Support/
│       ├── Identity.php    ← Customizer → Identity values
│       ├── Catalog.php     ← CPT data layer
│       ├── PageCopy.php    ← legacy schema (SEO + migration reference)
│       ├── BlockMigration.php ← meta → block markup converter
│       └── DemoContent.php ← demo seed (pages + blocks + CPTs)
├── resources/
│   ├── css/
│   │   ├── app.css         ← entry (imports below)
│   │   ├── keystone.css    ← design system + components
│   │   ├── block-options.css ← block advanced-setting CSS classes
│   │   ├── editor.css      ← editor-only overrides
│   │   └── card-lock.css   ← locked card chrome (last import)
│   ├── js/
│   │   ├── app.js          ← frontend JS entry
│   │   └── blocks/
│   │       └── index.js    ← Gutenberg block registrations (editor)
│   ├── views/              ← Blade templates (simplified to the_content())
│   └── seed/               ← demo JSON + HTML
├── bin/
│   ├── setup-wp.sh         ← local WP bootstrap (idempotent)
│   ├── build-theme-zip.sh  ← dist-theme/acreline.zip
│   ├── build-install-pack.sh   ← dist-install/acreline-<ver>.zip
│   └── build-marketplace-pack.sh ← + SELLING.md for Matt
├── plugins/
│   └── acreline-core/      ← store-correct CPT registrar
└── .github/workflows/
    └── deploy.yml          ← CI: Pint + Node + build + publish zips
```

---

## License

GPLv2 or later. See [`license.txt`](license.txt) and [`CREDITS.md`](CREDITS.md). Sage / Acorn remain MIT.
