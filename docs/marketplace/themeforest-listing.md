# ThemeForest / own-site listing copy (Acreline)

Paste-ready fields for Envato, Creative Market, TemplateMonster, Gumroad, or a product page on matthummel.com. Do **not** put this file in the buyer zip. Version, demo URL, and feature bullets must stay in sync with `style.css` Version and `readme.txt` Stable tag.

Do not claim WordPress.org approval, Gutenberg-optimized, Elementor, a live IDX/MLS, or "#1 realtor theme."

---

## Title (≤ 70 characters)

```
Acreline — WordPress Theme for Real Estate Agents
```

Alternate:

```
Acreline — Homes, Listings & Showings WordPress Theme
```

---

## Excerpt / short description

```
Acreline is a WordPress theme for real estate agents — featured homes, agent bios, and showing requests. Customizer identity, eight color styles, 32 custom Gutenberg blocks, no page builder, no IDX required. Concept demo by Matt Hummel.
```

---

## Demo URL

```
https://acreline.matthummel.com/
```

## Support URL

```
https://github.com/matthummel-pa/wp-acreline/blob/main/SUPPORT.md
```

## Product / purchase URL

```
https://matthummel.com/product/acreline/
```

Shop index (do not invent a second product slug):

```
https://matthummel.com/shop/
```

## Author

Matt Hummel · https://matthummel.com/

---

## Tags

```
real estate, realtor, listings, agents, brokerage, neighborhoods, sage, showing requests
```

WordPress.org-style tags (theme header): `blog, custom-colors, custom-logo, custom-menu, featured-images, footer-widgets, theme-options, threaded-comments, translation-ready, sticky-post`

---

## Item attributes

| Field | Value |
| --- | --- |
| **Version** | 1.5.1 |
| **Software version** | WordPress 6.6, 6.7, 6.8, 7.0 (tested up to 7.0.1) |
| **Compatible browsers** | Chrome, Firefox, Safari, Edge (current two versions) |
| **Compatible with** | No page builder required. Optional: Yoast, Rank Math, SEOPress, AIOSEO |
| **Files included** | PHP, CSS, JS (compiled Vite 8 assets + production Composer vendor) |
| **Documentation** | Well documented (HTML buyer hub + inline feature reference) |
| **Layout** | Responsive |
| **Columns** | 4+ |
| **Gutenberg optimized** | **No** — classic metaboxes for listings/agents/bookings by design |
| **High resolution** | Yes |
| **Widget ready** | Yes (footer widget area) |
| **License** | GPLv2 or later (theme). Sage / Acorn remain MIT (GPL-compatible) |

---

## Long description (HTML; Envato, TemplateMonster, or own site)

```html
<h2>A working agent site — not a luxury listing portal</h2>
<p>Acreline is a WordPress theme for <strong>solo agents and small teams</strong>. Buyers search featured homes, open a listing, meet an agent, and request a showing — all from one zip. Identity, colors, and the header bar live in the Customizer. No page builder, no ACF, and no IDX subscription required on day one.</p>
<p>Portal-scale kits such as Houzez, RealHomes, and WPResidence solve a different job: large inventories and MLS-first sites. Acreline is the middle path — Gutenberg marketing pages plus a listing shell an agent can launch without Elementor, ACF, or an IDX bill. Add a feed later if you need one.</p>

<p><strong>Fiction only.</strong> The live preview uses 555 phone numbers and <code>@acreline-concept.test</code> emails. It is not a live MLS, licensed brokerage, or booking desk. Replace every field from the WordPress admin.</p>

<h3>32 custom Gutenberg blocks</h3>
<p>All marketing pages are built with actual WordPress blocks (not Classic Editor shortcodes or HTML blocks). Every block renders server-side, so the editor canvas matches the live page exactly. Inspector Controls expose typography, image picker, hero overlay, CTA styles, FAQ format, column count, band colors, and per-block show/hide toggles — no coding needed.</p>
<ul>
  <li>Home Hero with live listing search (type, price, area), cinematic photo pan (on by default), and optional phone tilt on the search panel (off by default)</li>
  <li>Region Coverage — split map + neighborhood cards, or bento/grid; editable methods and CTAs</li>
  <li>Pricing plans, logo strip, and newsletter / new-listings digest (digest teasers, split, centered, or compact — demo form, nothing is emailed)</li>
  <li>Featured Listings Spotlight — dynamic, pulls from WP</li>
  <li>Listing Grid with filter toolbar and map view</li>
  <li>Agent List block — photo, stats, designations, social links</li>
  <li>Area Grid with photos, card styles, and a section CTA</li>
  <li>Contact Form + Office Info from Customizer</li>
  <li>CTA Band, Market Stats, Booking Form, FAQ, Reviews, and more</li>
</ul>
<p><strong>Block Generator</strong> (Tools → Block Generator): create new custom blocks by filling in a form — no PHP or JS required.</p>

<h3>Listing features that go further</h3>
<ul>
  <li><strong>Mortgage calculator</strong> built in — no plugin or API, editable rate/term/down payment</li>
  <li><strong>Listing comparison modal</strong> — compare up to 3 properties side-by-side</li>
  <li><strong>Saved listings drawer</strong> — heart icon saves to localStorage, floating button opens panel</li>
  <li><strong>Sticky CTA bar</strong> — price + "Book a showing" after hero scrolls out</li>
  <li><strong>Recently viewed</strong> and <strong>nearby listings</strong> sections — no API required</li>
  <li><strong>Market snapshot shortcode</strong> <code>[acreline_market_snapshot]</code> with dedicated Theme Settings tab</li>
  <li><strong>Share</strong> (Web Share API + clipboard) and <strong>print</strong> (property flyer CSS)</li>
  <li><strong>Rich listing fields</strong>: open house date/time, virtual/video tour, floor plan URL, condition, garage, basement, utilities (water/sewer/heating/cooling), HOA, school district, flood zone, green features, smart-home chips, outbuildings, tillable/pasture acres</li>
</ul>

<h3>Homepage hero motion (1.4.3 / 1.4.4)</h3>
<p>The homepage hero photo slowly pans and zooms. Headlines, the overlay, and the listing search stay still. Turn the animation off under Customize → Header or Appearance → Acreline Settings → General (on by default). An optional phone-only tilt on the search panel is off by default (“Tilt listing search on mobile”) — it does nothing without sensors or if the visitor declines access. Both screens store the same saved values.</p>

<h3>Top bar (new in 1.4.0)</h3>
<p>A configurable slim bar above the header — desktop only (hidden below 900 px). All enabled content surfaces automatically at the foot of the mobile nav drawer.</p>
<ul>
  <li>Four color presets: Dark, Accent (brand color), Light, Custom (pick any bg + text color)</li>
  <li>Announcement badge + message with optional link (centre slot)</li>
  <li>CTA pill button (right side)</li>
  <li>Contact items: phone, email, address, hours (values from Identity section)</li>
  <li>Social icons: Facebook, Instagram, YouTube, LinkedIn, X</li>
  <li>Optional dismiss button — state stored in sessionStorage</li>
</ul>

<h3>Customizer identity</h3>
<ul>
  <li>Brand name, tagline, phone, email, office address, hours, header CTA label + URL</li>
  <li>Removable footer author credit (WordPress.org requirement)</li>
  <li>Demo banner toggle (off on a real site)</li>
  <li>Eight color styles: Forest (default), Clay, Navy, Burgundy, Harvest, Lake, Orchard, Charcoal</li>
  <li>Accent, paper, and ink color pickers per style</li>
  <li>Header: sticky / static, standard / compact, homepage Ken Burns photo animation (on), optional mobile listing-search tilt (off)</li>
  <li>Typography: five font families, base size, heading weight</li>
  <li>Social links (Facebook, Instagram, YouTube, LinkedIn, X)</li>
  <li>GitHub token for one-click theme updates</li>
</ul>

<h3>Admin pages</h3>
<ul>
  <li><strong>Acreline Setup wizard</strong> (Appearance → Acreline Setup) — Welcome, Identity, Colors, Demo, Done. No upsells.</li>
  <li><strong>Acreline Settings</strong> (Appearance → Acreline Settings) — Tabbed UI with iOS-style toggles for listing, agent, booking, market, label, and general options.</li>
  <li><strong>Acreline Support</strong> (Appearance → Acreline Support) — Quick start, feature cards, shortcode cheatsheet, FAQ accordion, changelog.</li>
</ul>

<h3>Advanced agent profiles</h3>
<ul>
  <li>Performance stats grid: homes sold, total volume, avg days on market, list-to-sale ratio, review count</li>
  <li>Social links (Facebook, Instagram, LinkedIn, YouTube, personal site)</li>
  <li>Certifications/designations (ALC, GRI, ABR, etc.)</li>
  <li>Intro video URL and calendar/booking URL</li>
  <li>Team name, awards, MLS ID, NRDS ID</li>
</ul>

<h3>What is in the download</h3>
<ol>
  <li><code>acreline.zip</code> — upload in Appearance → Themes. Keep the folder named <code>acreline</code>.</li>
  <li><code>acreline-child.zip</code> — optional child theme for CSS customization.</li>
  <li><code>acreline-core.zip</code> — optional companion plugin; keeps listings, agents, and bookings if you switch themes.</li>
  <li><code>Documentation/</code> — HTML buyer hub (open <code>index.html</code> first).</li>
  <li><code>Demos/</code> — seed notes (Tools → Seed Acreline demo).</li>
  <li><code>Licensing/</code> — GPLv2 notice and third-party credits.</li>
</ol>
<p>Do not upload the outer pack zip into WordPress. Extract it first, then upload the inner <code>acreline.zip</code>.</p>

<h3>Requirements</h3>
<ul>
  <li>WordPress 6.6+</li>
  <li>PHP 8.3+</li>
  <li>No Composer or npm on the host — the zip ships compiled <code>vendor/</code> and <code>public/build/</code></li>
  <li>No ACF, Elementor, or IDX plugin needed</li>
</ul>

<h3>Credits</h3>
<p>Fonts from Google Fonts (SIL OFL). Sage / Acorn MIT. Theme code GPLv2 or later. Unsplash demo photos are hotlinks — not bundled in the zip.</p>
```

---

## Key features (bullet form for the store form)

- 32 custom server-side Gutenberg blocks, no page builder
- Block Generator: create new blocks without PHP or JS
- Customizer identity (brand, phone, email, hours, removable footer credit)
- Eight named color styles + accent / paper / ink pickers
- **Top bar** (desktop-only, mobile nav fallback) with announcement, contacts, social icons, CTA
- Homepage Ken Burns hero photo animation (Customize → Header, on by default)
- Optional mobile tilt on the homepage listing search (Customize → Header, off by default; no-op without sensors or permission)
- Listings, agents, and showing-request bookings
- Listing comparison modal (up to 3 side-by-side)
- Saved listings drawer (localStorage, floating button)
- Sticky CTA bar on single listing pages
- Built-in mortgage calculator
- Market snapshot shortcode
- Share (Web Share API + clipboard) + print flyer
- Rich listing fields (open house, virtual tour, utilities, lot data, green features, smart home)
- Advanced agent profiles (stats, social, video, designations, certifications)
- Acreline Setup wizard (no upsells)
- Tabbed Theme Settings page with iOS-style toggles
- Acreline Support admin page
- Native title / description / OG tags; yields to Rank Math, Yoast, SEOPress, AIOSEO
- Child theme + Acreline Core companion plugin
- Translation-ready (`acreline`)
- GPLv2+ license; Sage / Acorn MIT
- HTML documentation hub

---

## Screenshots for item images (01–07)

Path in repo: `docs/marketplace/screenshots/` (copied to `Documentation/screenshots/` in the seller pack).

| # | Filename | Caption |
| --- | --- | --- |
| 1 | `01-homepage.png` | Homepage — hero search, Intent cards, Featured listings spotlight |
| 2 | `02-listings.png` | Listings — filter bar (type, price, area), grid view |
| 3 | `03-listing.png` | Listing detail — photo, stats, mortgage calculator, agent card, sticky CTA |
| 4 | `04-agents.png` | Agents page — stats grid, designations, social links |
| 5 | `05-contact.png` | Contact — office info from Customizer + message form |
| 6 | `06-areas.png` | Areas — numbered sample market cards |
| 7 | `07-book.png` | Book a Showing — request form, agent selector |

Theme thumbnail: `screenshot.png` (1200×900).

---

## WordPress.org note

This theme ships compiled Vite assets and an Acorn/Sage vendor tree. The WP.org directory prefers simpler classic or block themes. Treat a WP.org upload as a **later lite listing** — not a first-pass approval guarantee. ThemeForest and your own store take the full pack first.
