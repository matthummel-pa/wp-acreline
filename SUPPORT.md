# Support — Acreline

Acreline is a **concept** WordPress theme for farms, land, and historic homes. Sample listings, agents, phones, and showing requests are fiction — not a live MLS, licensed brokerage, or booking desk.

Created by [Matt Hummel](https://matthummel.com/). House mark and lockup: `public/images/brand/` (GPLv2).

## Documentation (theme shops)

Open **`Documentation/index.html`** in the seller pack first. That hub is what ThemeForest / TemplateMonster reviewers expect (install, branding, screenshots, sources, changelog, listing-form paste).

| Need | Go here |
| --- | --- |
| Contents | [docs/marketplace/index.html](docs/marketplace/index.html) |
| Install, Customizer, custom fields, demo, child theme, updates, FAQ | [Buyer guide](docs/marketplace/buyer-guide.html) |
| Logos, Forest palette, fair housing note | [Branding](docs/marketplace/branding.html) |
| Host versions + listing form fields | [Requirements](docs/marketplace/requirements.html) |
| Pack support page | [support.html](docs/marketplace/support.html) |
| Sources and credits | [sources.html](docs/marketplace/sources.html) |
| Changelog | [changelog.html](docs/marketplace/changelog.html) |
| Customizer | [customizer.html](docs/marketplace/customizer.html) |
| Listings & bookings | [listings.html](docs/marketplace/listings.html) |
| How the public site looks | [Concept demo](https://acreline.matthummel.com/) |
| Theme bugs | [GitHub issues](https://github.com/matthummel-pa/wp-acreline/issues) |
| Author | [matthummel.com](https://matthummel.com/) |
| Concept office form (demo only) | [Contact on the demo](https://acreline.matthummel.com/contact/) |

There is no ticket desk or live chat in the theme. Envato item support follows that marketplace’s channel. Demo phones are `555` numbers (office `(555) 010-0455`). Demo emails use `@acreline-concept.test` and do not reach a real inbox.

## Almost no plugins

The theme zip is enough. Optional **Acreline Core** (listings survive a theme switch) and **Acreline Child** (CSS survives parent updates). Native SEO tags yield to Yoast / Rank Math / SEOPress / AIOSEO. Do not install ACF or Elementor to make fields appear — they are already metaboxes.

## Custom fields, not the design

Identity, colors, header, typography, and logo: **Appearance → Customize**. Page / listing / agent / post copy: classic metaboxes. The block editor is off. Editing theme files does not change live posts.

## Before you file an issue

1. Confirm the theme folder is still `acreline` and you ran `npm run build` (or installed a zip that already includes `public/build`).
2. After Blade edits, clear Acorn views: `wp acorn view:clear`.
3. Identity, colors, and the header live under **Appearance → Customize**.
4. Include WordPress version, PHP version, theme version, and whether Acreline Core is active.

## What this theme does not do

- Sync a live MLS or IDX feed
- Process payments or hold real appointments
- Replace a licensed brokerage or CRM
- Ship Gutenberg / block-editor patterns
- Require ACF, Elementor, or a page builder

## Marketplace buyers

If you bought a pack (theme + child + Acreline Core), start with **Documentation/index.html**. ThemeForest / own-site purchase questions go through that marketplace’s support channel; GitHub issues are still welcome for reproducible theme bugs.

## Security

Do not commit GitHub tokens. The updater PAT lives in Customizer → GitHub, the Update Theme screen, or `KS_GITHUB_TOKEN` on the host. **Contents: Read** to install the zip. Add **Actions: Read and write** only to trigger rebuilds. Repo: `matthummel-pa/wp-acreline`.
