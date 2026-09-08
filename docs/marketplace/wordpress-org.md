# WordPress.org notes (Acreline)

Seller-only. Do **not** ship this file in the ThemeForest buyer zip.

WordPress.org is a **lite / traffic** listing for this repo, not a cash register and not a guaranteed first-pass approval. Money there is services or a separate plugin — never an in-theme upgrade nag.

## What to upload

Upload **only** `dist-theme/acreline.zip` (inner theme zip).

Do **not** upload:

- The outer pack (`acreline-1.2.3.zip`)
- `acreline-child.zip` or `acreline-core.zip` inside the theme
- `SELLING.md`, this file, or Unsplash binaries
- `.git`, `.cursor/`, tests, or `bin/*.sh`

The theme folder inside the zip must be `acreline/` with `style.css` at `acreline/style.css`.

## Required parser files (already in the theme)

| File | Role |
| --- | --- |
| `style.css` | Theme Name **Acreline**, License GNU GPL v2 or later, License URI gnu.org, Text Domain `acreline`, Requires PHP 8.3, Requires at least 6.6, Tested up to 7.0.1 |
| `readme.txt` | WP.org readme parser (Stable tag **1.2.3**) |
| `screenshot.png` | **1200×900**, 4:3, homepage capture — not an ad |
| `license.txt` | GPLv2 notice + full GPLv2 text (Theme Check) |
| `languages/acreline.pot` | Translation-ready |
| `CREDITS.md` | Third-party resources |

## Gutenberg optimized

**No.** Do not check “Gutenberg optimized” on any form. The theme disables the block editor and uses classic metaboxes. Enabling Gutenberg just to pass a checklist is out of scope.

## Plugin territory

A WP.org theme zip should not be the only place that registers `listing` / `agent` / `booking`.

- Ship **Acreline Core** as a **separate** plugin (its own wp.org listing, or only in the ThemeForest pack).
- For a future WP.org-only theme zip, define `KS_DISABLE_THEME_CPTS` so the theme is presentation-only (`bin/build-theme-zip.sh` does **not** do this today — general buyers need listings without the plugin).
- Do not bundle the plugin zip inside the theme folder (forbidden).

## Theme Check on a stock install

Expected warnings on Sage 11 / Acorn:

- `vendor/` size and third-party PHP (Laravel / Illuminate)
- Blade templates instead of classic PHP templates
- Compiled Vite `public/build` instead of `style.css` CSS
- PHP 8.3+ requirement (directory often prefers lower)

Do **not** hide those with a dummy license, a fake `rtl.css`, or Gutenberg patterns. Document them. A future “Acreline Lite” without Acorn would have a real shot at the picker. This repo is the premium / Envato / own-site product.

## Footer and credits

- Front-end copyright is the **buyer’s site name + year**
- Author credit is optional (Customize → Identity) and `rel="nofollow"`
- No Freemius, Envato banners, TGMPA, or “upgrade to Pro”

## Screenshot

Keep `screenshot.png` at 1200×900. Recapture if the homepage layout changes. Do not overlay “SALE” or Envato chrome on the WP.org screenshot.

## Submission copy (if you try anyway)

Use `readme.txt` Description as the directory blurb. Short:

```
Acreline is a classic (non-block) WordPress theme for land, farms, and historic-home inventory: searchable listings, agents, and showing requests. Customizer identity and eight color styles. Concept demo — not a live MLS.
```

## Honest blockers (do not paper over)

1. Sage / Blade / `vendor` (Acorn) is not what the Themes Team usually ships
2. Block editor is disabled on purpose
3. PHP 8.3+ and a compiled Vite manifest
4. Listings are plugin territory if you want a directory-clean theme zip

Do not claim the theme is “WordPress.org approved” or “in the picker” until a reviewer says so.
