=== Acreline ===
Contributors: matthummel
Requires at least: 6.6
Tested up to: 7.0.1
Requires PHP: 8.3
Stable tag: 1.4.5
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Tags: blog, custom-colors, custom-logo, custom-menu, featured-images, footer-widgets, theme-options, threaded-comments, translation-ready

WordPress theme for farms, land, and historic homes — listings, agents, showing requests. Almost no plugins.

== Description ==

Acreline is a classic (non-block) Sage 11 WordPress theme for land, farms, and historic-home inventory: searchable listings, agents, and showing requests.

It is meant to set up with almost no plugins. The theme zip alone runs the office. You do not need Advanced Custom Fields, Elementor, a Gutenberg kit, or an IDX plugin. Buyers change brand, phone, colors, and inventory from the Customizer and custom-field metaboxes — they do not edit the design.

The sample office in the preview is named Acreline — replace it under Customize → Identity. This is a concept theme. Sample phones are 555 numbers. Concept emails use @acreline-concept.test. It is not a live MLS, licensed brokerage, or booking system.

Live concept demo: https://acreline.matthummel.com/
Product / concept page: https://matthummel.com/projects/acreline/
House mark and wordmark (original SVG, GPLv2) ship in public/images/brand/. Support and ThemeForest docs: https://github.com/matthummel-pa/wp-acreline/blob/main/SUPPORT.md

= Features =

* Appearance → Acreline Setup wizard (identity, colors, optional demo content)
* Appearance → Acreline Settings: tabbed admin page with iOS-style toggles for every listing, agent, booking, and display option
* Appearance → Acreline Support: getting-started guide, feature reference, shortcode cheatsheet, FAQ, and changelog in one screen
* Almost no plugins — theme zip is enough; optional Acreline Core and child theme
* Gutenberg block editor for pages and posts (28 custom dynamic blocks, no page builder or shortcodes)
* Classic metaboxes for listings, agents, and bookings (no ACF required)
* Rich listing fields: property details, utilities, HOA, land & farm, flood zone, school district, green/eco features, smart home chips
* Built-in mortgage calculator on listing detail pages — no plugin or API required
* Listing comparison modal: compare up to 3 listings side-by-side
* Saved listings drawer: heart icon saves to localStorage; floating button opens the slide-out panel
* Sticky CTA bar on single listing pages (price + book) after hero scrolls out
* Share button with Web Share API and URL clipboard-copy fallback; print-friendly property flyer
* Recently viewed listings and nearby listings sections on listing detail pages
* Market snapshot shortcode [acreline_market_snapshot] — enter stats in Theme Settings, embed anywhere
* Advanced agent profiles: stats grid, social links, certifications, intro video, calendar booking URL
* Media picker for all image URL inputs (listing photos, floor plans, agent photos)
* Customizer identity: brand, phone, email, address, hours, header button, removable author credit
* Custom logo under Site Identity (replaces the Acreline house mark)
* Eight color styles (Forest, Clay, Navy, Burgundy, Harvest, Lake, Orchard, Charcoal) plus accent, paper, and ink
* Header size, sticky toggle, homepage Ken Burns hero animation (on by default), and optional mobile listing-search tilt (off by default)
* Demo color switcher on the concept preview (mid-right)
* Inter-based typography with font choices
* WordPress menus (Primary + Footer) with a concept-page fallback
* Footer widget area
* Listing, agent, and booking templates (types registered by Acreline Core, or in-theme as a concept fallback)
* Native title, description, Open Graph, and Twitter tags (yields to Yoast / Rank Math / SEOPress / AIOSEO)
* Breadcrumbs with BreadcrumbList JSON-LD
* Translation-ready (`acreline`)

= WordPress.org note =

This theme ships compiled Vite assets and an Acorn/Sage vendor tree. The directory prefers simpler classic or block themes. Treat a WP.org upload as a later lite listing, not a guaranteed first-pass approval. ThemeForest and your own store take the full pack.

== Installation ==

1. Upload `acreline` to `/wp-content/themes/` (or use the zip from Appearance → Themes → Add New).
2. Activate Acreline. The folder name must stay `acreline`.
3. Optional: install Acreline Core so listings survive a theme switch; optional child theme for CSS.
4. Go to Appearance → Acreline Setup and walk the setup wizard (identity, colors, optional demo).
5. Appearance → Customize → Identity and Site Identity (logo) for anything you skipped. No page builder.
6. Tools → Seed Acreline demo remains available if you prefer to load inventory outside the wizard.

Store zips already include `public/build` and `vendor`. After a git clone run `npm run build` and `composer install --no-dev`. Full ThemeForest documentation: Documentation/index.html in the seller pack.

== Frequently Asked Questions ==

= Do I need extra plugins? =

No. The theme zip runs listings, agents, and showings. Optional: Acreline Core (inventory survives a theme switch), Acreline Child (CSS survives parent updates), and an SEO plugin (native tags yield to Yoast / Rank Math / SEOPress / AIOSEO). Do not install ACF or Elementor to “make the fields work.”

= Where do I change copy without editing templates? =

Customize → Identity / Colors / Header / Typography. Pages, listings, agents, and posts use custom-field metaboxes. Layout stays in Blade.

= Where do I change the phone number? =

Appearance → Customize → Identity.

= How do I hide the concept demo banner? =

Customize → Identity → uncheck “Show concept demo banner and author badge.” Uncheck the credit box to drop the footer author line (WordPress.org requires the buyer’s copyright only).

= How do I turn off the homepage hero animation? =

Appearance → Acreline Settings → General, or Customize → Header → uncheck “Animate homepage hero image.” The photo stays; it no longer pans. Reduced-motion visitors already see a still cover. Both screens store the same value.

= How do I turn on listing-search tilt on phones? =

Appearance → Acreline Settings → General, or Customize → Header → check “Tilt listing search on mobile.” Off by default. The homepage search panel gently follows device tilt. It does nothing on desktop, without sensors, if permission is denied, or if the visitor prefers reduced motion. iPhone may ask for motion access.

= Where do listings live? =

In the `listing` post type. Acreline Core owns registration on a store install. This repo still registers the types in the theme when the plugin is missing so the live concept site keeps working.

= Can I use a child theme? =

Yes. The marketplace pack includes `acreline-child`.

= How do I book a showing for one listing? =

Link to the Book a showing page with `?listing_id=123` (the listing post ID). Do not use `?listing=` as a public query.

= Does this theme sync a live MLS? =

No. Sample inventory is concept data. There is no IDX/RETS feed.

= How do I update from GitHub? =

Appearance → Update Theme. Fine-grained PAT: Contents: Read. Add Actions: Read and write only to trigger rebuilds. Repo: matthummel-pa/wp-acreline.

== Screenshots ==

Desktop captures of the seeded concept demo (also used on https://matthummel.com/projects/acreline/):

1. Homepage — search, Acreline house mark, and the path from listing to showing. (`docs/marketplace/screenshots/01-homepage.png`)
2. Listings — sample inventory with type, price, acreage, and area filters. (`02-listings.png`)
3. Listing single — farmhouse / land example with agent card and book-a-showing. (`03-listing.png`)
4. Agents — license, specialties, and contact fields (not a live MLS roster). (`04-agents.png`)
5. Contact — office phone and address from Customize → Identity. (`05-contact.png`)
6. Areas — sample markets you replace with your own counties. (`06-areas.png`)
7. Book a showing — writes a Booking post (concept pipeline, not a calendar). (`07-book.png`)

Theme thumbnail: screenshot.png (1200×900). Extra captures: docs/marketplace/screenshots/ (copied into Documentation/screenshots/ in the seller pack), plus public/images/brand/ for the house mark and lockup.

== Branding ==

Original house mark and horizontal lockup (not NAR / HUD / MLS artwork):

* public/images/brand/acreline-mark.svg
* public/images/brand/acreline-lockup.svg

Upload your office logo under Customize → Site Identity. Colors: Forest sample ink #141210, paper #f5f4f1, accent #1f6b4a. Footer “Equal Housing Opportunity (concept)” is sample copy — use official artwork on a licensed office. See docs/marketplace/branding.html (Documentation/branding.html in the seller pack).

== Changelog ==

= 1.4.5 =
* Primary menu, hamburger, and mobile drawer follow the active color scheme. Link, hover, and current colors stay readable on the header.

= 1.4.4 =
* Homepage hero motion toggles (Ken Burns and mobile search tilt) now live in Appearance → Acreline Settings → General and stay saved when you leave the page.
* Customize → Header shows the same saved values after you save either screen.
* Top bar (desktop and mobile nav) uses the active color scheme. Text and icons auto-pick a readable pair when accent-on-paper would fail. Custom colors still override.

= 1.4.3 =
* Homepage hero photo slowly pans and zooms. Headlines, overlay, and listing search stay still.
* Optional: on phones, the listing search panel can gently follow device tilt. Off by default. Does nothing without sensors or permission.
* Customize → Header: “Animate homepage hero image” (on) and “Tilt listing search on mobile” (off).

= 1.4.2 =
* Homepage hero now shows a full-bleed rural farmhouse photo (the theme’s existing default listing still) with the ink veil so white type stays readable. Buyers can replace it from the Home Hero block’s image picker.
* Seed and Tools → Migrate to Blocks → Force rebuild write that image URL onto the home page so older installs pick it up.

= 1.4.1 =
* Marketing pages now seed the full Gutenberg stacks: agent list on Agents, stats/reviews/FAQ on Listings, how-it-works + checklist on Guide, and a richer Blog page.
* New blocks: Trust Strip, Buyer Checklist, Showing Prep Checklist, Area Compare Table, Topic Cards, Post Grid. Agent List is registered in the editor inserter.
* Re-seed (`wp ks seed` / Tools → Seed) and Tools → Migrate to Blocks → Force rebuild overwrite page stacks so older thin content is replaced.
* Blog template renders the Blog page’s block content (`the_content`) instead of a hardcoded Blade subset.

= 1.4.0 =
* Feature: Top bar (Appearance → Customize → Top Bar) — desktop-only slim bar above the header. Four color styles (Dark / Accent / Light / Custom), custom colors, announcement badge + message + link, CTA pill, show/hide for phone/email/address/hours, social icon toggles, dismissible mode.
* Feature: Mobile nav fallback — all active top-bar items (announcement, contacts, social icons, CTA) surface at the foot of the mobile nav so no information is hidden on small screens.
* Accessibility: focus trap and aria-live announcements on listing modal, saved drawer, and compare modal.
* Accessibility: screen-reader announcer for save and compare actions; dynamic aria-label on each button.
* Responsive: listing detail grid, compare table, compare bar, mortgage calc grid, and saved drawer all stack or scroll gracefully below 380–600 px.
* Content: seed listings enriched with open house dates, virtual tour, utilities, HOA, school district, flood zone, outbuildings, and land acreage breakdowns.
* Content: seed agents enriched with performance stats, social links, bio video, certifications, and awards.
* Block: new acreline/agent-list block renders the agent grid on the Agents page.
* Docs: README.md, readme.txt, CHANGELOG.md, themeforest-listing.md, and buyer-guide.html updated for 1.4.0.

= 1.3.3 =
* Admin: Theme Settings completely redesigned — tabbed UI (Listings, Agents, Bookings, Market, Labels, General), iOS-style toggle switches, sticky save bar, and unsaved-changes guard.
* Admin: New Theme Support page (Appearance → Acreline Support) with quick start, feature reference cards, shortcode cheatsheet, FAQ accordion, and changelog.
* Feature: Listing comparison modal — compare up to 3 listings side-by-side from the grid page.
* Feature: Persistent saved listings drawer — heart icon on every card saves to localStorage; floating button opens the slide-out panel.
* Feature: Sticky CTA bar on single listing pages — price + "Book a showing" appears after the hero scrolls out of view.
* Feature: Share button with Web Share API and URL clipboard-copy fallback.
* Feature: Print-friendly property flyer layout.
* Feature: Recently viewed listings (up to 5 in localStorage) shown as chips on single listing pages.
* Feature: Nearby listings section — server-rendered same-township listings, no API required.
* Feature: Market snapshot shortcode [acreline_market_snapshot] with dedicated Market tab in Theme Settings.
* Feature: Built-in mortgage calculator on listing detail pages — no plugin or API.
* Feature: Media picker for all image URL inputs in listing and agent metaboxes.
* Feature: Rich listing fields — property details, utilities, HOA, land & farm, flood zone, school district, green/eco features, smart home chips.
* Feature: Advanced agent profiles — performance stats grid, social links, certifications, intro video, calendar booking URL, mobile number.
* Content: Updated seeded blog post covering all new differentiating features.
* Docs: README.md and readme.txt updated with new feature list.

= 1.3.2 =
* Accessibility: mobile nav close button raised to 44×44 px touch target (WCAG 2.2 Target Size).
* Accessibility: concept-badge and light-outline-button focus rings now visible on dark backgrounds.
* Accessibility: footer column headings downgraded from h2 to h3 for correct document outline.
* Accessibility: main element gets tabindex="-1" so keyboard skip links land correctly.
* Accessibility: header phone link carries a visible accessible name when the number is hidden at tablet widths.
* Contrast: form-note and booking-times legend raised from ink-faint to ink-soft for WCAG AA.
* Contrast: footer-bottom text opacity raised from .45 to .58; calc-result label from .60 to .78; testi-loc and testi-avg promoted to ink-soft.
* Contrast: hero search panel gets explicit color-scheme:light to prevent OS dark-mode override.
* i18n: hardcoded English strings in single-listing, single-agent, content-single, and footer wrapped in __().
* i18n: listing image alt text populated from listing title.
* Docs: SUPPORT.md block-editor statement corrected (Gutenberg is on for pages and posts).
* Docs: DEVELOPMENT.md CSS architecture table updated with responsive-forms.css and form-contrast.css.

= 1.3.1 =
* Fix a critical error on single blog posts (adjacent/related post data passed as arrays, not invokable view variables).

= 1.3.0 =
* Appearance → Acreline Setup wizard: identity, colors, optional demo seed.

= 1.2.3 =
* Concept demo Theme URI and docs point to https://acreline.matthummel.com/.
* readme.txt aligned with live demo, brand SVG paths, and marketplace screenshot set used on matthummel.com/projects/acreline/.

= 1.2.2 =
* GitHub repo, updater, and support links use `matthummel-pa/wp-acreline`.

= 1.2.1 =
* Marketing pages: taller photo heroes, sample-market cards, and a scannable land-buying guide. Retired Keystone / Adams County strings fall back to Acreline copy.

= 1.2.0 =
* Install folder, text domain, and theme zip are `acreline`. Child theme `acreline-child`. Companion plugin is Acreline Core.
* Sample office default is Acreline. Concept emails use `@acreline-concept.test`.

= 1.1.0 =
* Eight named color styles, paper/ink pickers, header size, and a demo style switcher.
* Sample page and listing copy is generic market language for store previews.

= 1.0.2 =
* Author credit is Matt Hummel (matthummel.com) for theme shops and WordPress.org.

= 1.0.1 =
* Theme Name is now Acreline. Sample office in the demo stays Acreline until you change Identity.

= 1.0.0 =
* Marketplace layer: Customizer identity/colors/social, WP menus, breadcrumbs, setup checklist, companion plugin, child theme, readme.txt
* Text domain is `acreline` (install folder)
* Footer copyright is the site name; author credit is optional and nofollow

== Developer notes ==

Git is Sage 11 source (Blade, Vite 8, Acorn). Store and host zips already include compiled assets — do not run Composer or npm on the client host.

Local clone and future update checklist: https://github.com/matthummel-pa/wp-acreline/blob/main/README.md

Do not rename the install folder, `ks_*` meta, or the `keystone/v1` REST namespace. Version in style.css must match this Stable tag.

== Documentation ==

Source hub: `docs/marketplace/` (seller pack copies it to Documentation/):

* index.html — contents and screenshots (start here on ThemeForest)
* buyer-guide.html — install, homepage, Customizer, fields, menus, demo, child theme, updates, translation, SEO, file map, FAQ
* branding.html — logos, Forest palette, fair housing note
* customizer.html, listings.html, templates.html, child-theme.html, translation.html, faq.html
* requirements.html — host needs + ThemeForest / TemplateMonster form fields and long description
* support.html — help channels
* sources.html — fonts, Sage/Acorn, original SVG marks, what is not bundled
* credits.html — third-party credits
* changelog.html — user-facing history
* screenshots/ — item images (01–07) used on the matthummel.com concept page
* assets/ — house mark, lockup, docs CSS/JS

GitHub: README.md, SUPPORT.md, docs/marketplace/. Product landing: https://matthummel.com/projects/acreline/

== Resources ==

* Inter, Google Fonts, SIL Open Font License, https://fonts.google.com/specimen/Inter
* Plus Jakarta Sans, Google Fonts, SIL OFL
* DM Sans, Google Fonts, SIL OFL
* Manrope, Google Fonts, SIL OFL
* Montserrat, Google Fonts, SIL OFL
* Poppins, Google Fonts, SIL OFL
* Source Sans 3, Google Fonts, SIL OFL
* Nunito Sans, Google Fonts, SIL OFL
* Outfit, Google Fonts, SIL OFL
* Lato, Google Fonts, SIL OFL
* Sage / Acorn, Roots, MIT, https://roots.io/sage/
* Acreline house mark and lockup SVG, Matt Hummel, GPLv2 or later
* Theme placeholders and SVG marks, Matt Hummel, GPLv2 or later
* Unsplash photographs used as hotlinked concept images only — not bundled in the theme zip
