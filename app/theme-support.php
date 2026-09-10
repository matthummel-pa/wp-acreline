<?php

/**
 * Acreline Theme Support — Appearance → Acreline Support.
 *
 * Getting-started guide, feature reference, shortcode cheatsheet, and
 * quick links — all in one polished admin screen.
 */

namespace App;

// ─── Menu registration ────────────────────────────────────────────────────────

add_action('admin_menu', function () {
    add_theme_page(
        __('Acreline Support', 'acreline'),
        __('Acreline Support', 'acreline'),
        'manage_options',
        'acreline-support',
        __NAMESPACE__.'\\render_support_page'
    );
});

// ─── CSS for support page ─────────────────────────────────────────────────────

add_action('admin_enqueue_scripts', function (string $hook) {
    if ($hook !== 'appearance_page_acreline-support') {
        return;
    }
    wp_add_inline_style('wp-admin', ks_support_css());
});

// ─── Render ───────────────────────────────────────────────────────────────────

function render_support_page(): void
{
    if (! current_user_can('manage_options')) {
        return;
    }

    $themeVer = wp_get_theme()->get('Version');
    $settingsUrl = admin_url('themes.php?page=acreline-settings');
    $siteUrl = home_url('/');
    ?>
    <div class="kss-page">

      <!-- Header -->
      <div class="kss-header">
        <div class="kss-header-brand">
          <span class="kss-logo-mark" aria-hidden="true">A</span>
          <div>
            <h1 class="kss-title"><?php esc_html_e('Acreline — Theme Support', 'acreline'); ?></h1>
            <p class="kss-subtitle">
              <?php echo esc_html(sprintf(__('Version %s · Sage 11 / Acorn / Tailwind CSS v4', 'acreline'), $themeVer)); ?>
            </p>
          </div>
        </div>
        <div class="kss-header-links">
          <a href="<?php echo esc_url($settingsUrl); ?>" class="kss-btn kss-btn-outline">⚙️ <?php esc_html_e('Theme Settings', 'acreline'); ?></a>
          <a href="<?php echo esc_url($siteUrl); ?>" target="_blank" rel="noopener" class="kss-btn kss-btn-outline">🌐 <?php esc_html_e('View Site', 'acreline'); ?></a>
        </div>
      </div>

      <!-- Quick start steps -->
      <section class="kss-section">
        <h2 class="kss-section-title"><?php esc_html_e('Quick start', 'acreline'); ?></h2>
        <div class="kss-steps">
          <?php
          $steps = [
              ['num' => '1', 'title' => __('Run the setup wizard', 'acreline'),
                  'desc' => __('Go to Appearance → Acreline Setup and follow the wizard to set your brand name, phone, brokerage legal name, color palette, and seed demo content.', 'acreline'),
                  'link' => admin_url('themes.php?page=acreline-setup'), 'link_text' => __('Open setup wizard', 'acreline')],
              ['num' => '2', 'title' => __('Add your listings', 'acreline'),
                  'desc' => __('Every listing is a Listing CPT with full metaboxes: photos, MLS info, property details, utilities, land data, open house, virtual tour, floor plan, and smart home features.', 'acreline'),
                  'link' => admin_url('edit.php?post_type=listing'), 'link_text' => __('View listings', 'acreline')],
              ['num' => '3', 'title' => __('Create agent profiles', 'acreline'),
                  'desc' => __('Each agent is an Agent CPT with a photo, bio, stats (homes sold, volume, DOM), certifications, social links, calendar URL, and team assignment.', 'acreline'),
                  'link' => admin_url('edit.php?post_type=agent'), 'link_text' => __('View agents', 'acreline')],
              ['num' => '4', 'title' => __('Customise Identity &amp; Compliance', 'acreline'),
                  'desc' => __('Phone, email, address, and hours live under Customize → Identity. Brokerage legal name, Fair Housing, IDX slots, privacy URL, and form consent live under Customize → Compliance (same fields in Acreline Settings). Not legal advice.', 'acreline'),
                  'link' => admin_url('customize.php?autofocus[section]=ks_compliance'), 'link_text' => __('Open Compliance', 'acreline')],
              ['num' => '5', 'title' => __('Adjust feature toggles', 'acreline'),
                  'desc' => __('Use Theme Settings (Appearance → Acreline Settings) to turn individual sections on or off — mortgage calculator, open house banners, agent stats, booking fields, and more.', 'acreline'),
                  'link' => $settingsUrl, 'link_text' => __('Open settings', 'acreline')],
          ];
    foreach ($steps as $step) { ?>
          <div class="kss-step">
            <div class="kss-step-num" aria-hidden="true"><?php echo esc_html($step['num']); ?></div>
            <div class="kss-step-body">
              <h3><?php echo esc_html($step['title']); ?></h3>
              <p><?php echo esc_html($step['desc']); ?></p>
              <a href="<?php echo esc_url($step['link']); ?>" class="kss-step-link"><?php echo esc_html($step['link_text']); ?> →</a>
            </div>
          </div>
          <?php } ?>
        </div>
      </section>

      <!-- What's new / features -->
      <section class="kss-section">
        <h2 class="kss-section-title"><?php esc_html_e("What's new in 1.4.x", 'acreline'); ?></h2>
        <div class="kss-feature-grid">
          <?php
    $features = [
        ['icon' => '⚖️',  'title' => __('Listing comparison', 'acreline'),
            'desc' => __('Buyers can compare up to 3 listings side-by-side in a full-screen modal. Compare button appears on every card.', 'acreline')],
        ['icon' => '❤️',  'title' => __('Saved listings drawer', 'acreline'),
            'desc' => __('Heart icon saves any listing to localStorage. A floating button opens the slide-out drawer without leaving the page.', 'acreline')],
        ['icon' => '📌',  'title' => __('Sticky CTA bar', 'acreline'),
            'desc' => __('After the hero scrolls out of view, a slim bar with the price and "Book a showing" CTA sticks to the top of single listing pages.', 'acreline')],
        ['icon' => '🔗',  'title' => __('Share & print flyer', 'acreline'),
            'desc' => __('Share button uses the Web Share API (mobile) with a clipboard-copy fallback. Print generates a clean one-page PDF-ready layout.', 'acreline')],
        ['icon' => '🕐',  'title' => __('Recently viewed', 'acreline'),
            'desc' => __('The last 5 listings a visitor viewed are tracked in localStorage and shown as chips at the bottom of each listing page.', 'acreline')],
        ['icon' => '📍',  'title' => __('Nearby listings', 'acreline'),
            'desc' => __('Server-rendered section shows up to 3 other active listings in the same township — no third-party API required.', 'acreline')],
        ['icon' => '📊',  'title' => __('Market snapshot shortcode', 'acreline'),
            'desc' => __('Enter median price, avg DOM, inventory months, and YoY change in Theme Settings, then embed anywhere with [acreline_market_snapshot].', 'acreline')],
        ['icon' => '🧮',  'title' => __('Mortgage calculator', 'acreline'),
            'desc' => __('Built-in calculator on every listing detail page. Rate, term, and down-payment are editable. No plugin or API required.', 'acreline')],
        ['icon' => '🏡',  'title' => __('Rich listing fields', 'acreline'),
            'desc' => __('Property details, utilities, HOA, flood zone, school district, green features, and smart home chips.', 'acreline')],
        ['icon' => '👤',  'title' => __('Advanced agent profiles', 'acreline'),
            'desc' => __('Performance stats grid, credentials, social links, intro video, calendar booking URL, mobile number, and team name.', 'acreline')],
        ['icon' => '🖼️',  'title' => __('Media picker', 'acreline'),
            'desc' => __('All image URL fields in listing and agent metaboxes use the native WordPress media library picker instead of a plain text input.', 'acreline')],
        ['icon' => '♿',  'title' => __('Accessibility improvements', 'acreline'),
            'desc' => __('WCAG 2.2 AA contrast across all components, 44px touch targets, skip-link focus, semantic heading hierarchy, and aria-live regions.', 'acreline')],
        ['icon' => '🎬',  'title' => __('Homepage hero animation', 'acreline'),
            'desc' => __('The homepage hero photo slowly pans and zooms. Turn it off under Customize → Header → Animate homepage hero image. Reduced-motion visitors see a still cover.', 'acreline')],
        ['icon' => '📱',  'title' => __('Mobile listing-search tilt', 'acreline'),
            'desc' => __('Optional: the homepage search panel gently follows device tilt on phones. Off by default. Does nothing without sensors or if permission is denied.', 'acreline')],
        ['icon' => '⚖️',  'title' => __('Compliance tools', 'acreline'),
            'desc' => __('Brokerage legal name, optional licenses, Fair Housing statement, MLS/IDX disclaimer slots, privacy URL, and a consent checkbox on forms. Not legal advice — check your state commission.', 'acreline')],
    ];
    foreach ($features as $f) { ?>
          <div class="kss-feature-card">
            <span class="kss-feature-icon" aria-hidden="true"><?php echo $f['icon']; ?></span>
            <h3><?php echo esc_html($f['title']); ?></h3>
            <p><?php echo esc_html($f['desc']); ?></p>
          </div>
          <?php } ?>
        </div>
      </section>

      <!-- Shortcode reference -->
      <section class="kss-section">
        <h2 class="kss-section-title"><?php esc_html_e('Shortcode reference', 'acreline'); ?></h2>
        <div class="kss-table-wrap">
          <table class="kss-table">
            <thead>
              <tr>
                <th><?php esc_html_e('Shortcode', 'acreline'); ?></th>
                <th><?php esc_html_e('Where to use', 'acreline'); ?></th>
                <th><?php esc_html_e('Description', 'acreline'); ?></th>
              </tr>
            </thead>
            <tbody>
              <?php
        $shortcodes = [
            ['[acreline_market_snapshot]', __('Any page or post', 'acreline'),
                __('Renders the market stats strip (median price, DOM, inventory, YoY). Values are set in Theme Settings → Market.', 'acreline')],
        ];
    foreach ($shortcodes as $sc) { ?>
              <tr>
                <td><code class="kss-code"><?php echo esc_html($sc[0]); ?></code></td>
                <td><?php echo esc_html($sc[1]); ?></td>
                <td><?php echo esc_html($sc[2]); ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </section>

      <!-- Theme Settings quick reference -->
      <section class="kss-section">
        <h2 class="kss-section-title"><?php esc_html_e('Theme Settings tabs', 'acreline'); ?></h2>
        <div class="kss-settings-ref">
          <?php
          $settingsTabs = [
              ['🏡', __('Listings', 'acreline'),  __('Grid columns, card fields, and every detail page section toggle — open house, virtual tour, floor plan, utilities, HOA, land, eco, and smart home.', 'acreline')],
              ['👤', __('Agents', 'acreline'),     __('Turn stats, social links, certifications, awards, intro video, team name, and calendar button on or off.', 'acreline')],
              ['📅', __('Bookings', 'acreline'),   __('Optional fields on the showing request form: financing status, attendees, preferred contact method, and lead source.', 'acreline')],
              ['📊', __('Market', 'acreline'),     __('Median price, avg DOM, inventory months, and YoY change — powering the [acreline_market_snapshot] shortcode.', 'acreline')],
              ['✏️', __('Labels', 'acreline'),     __('Rename Township, Beds, Baths, Sq Ft, Acres, currency symbol, and the Listing and Agent labels sitewide.', 'acreline')],
              ['⚙️', __('General', 'acreline'),    __('Homepage hero motion (Ken Burns and mobile search tilt), mortgage calculator default rate, and the fiction-only concept disclaimer banner.', 'acreline')],
          ];
    foreach ($settingsTabs as $t) { ?>
          <div class="kss-ref-row">
            <span class="kss-ref-icon" aria-hidden="true"><?php echo $t[0]; ?></span>
            <strong><?php echo esc_html($t[1]); ?></strong>
            <span><?php echo esc_html($t[2]); ?></span>
          </div>
          <?php } ?>
        </div>
        <p style="margin-top:14px">
          <a href="<?php echo esc_url($settingsUrl); ?>" class="kss-btn kss-btn-primary">
            <?php esc_html_e('Open Theme Settings', 'acreline'); ?>
          </a>
        </p>
      </section>

      <!-- FAQ -->
      <section class="kss-section">
        <h2 class="kss-section-title"><?php esc_html_e('Frequently asked questions', 'acreline'); ?></h2>
        <div class="kss-faqs">
          <?php
    $faqs = [
        [__('Can I use this with real MLS listings?', 'acreline'),
            __('Yes. Every listing is a WordPress CPT — enter your actual property data in the metaboxes. The fictional sample data is only pre-seeded for demo purposes. Remove it before going live.', 'acreline')],
        [__('Why do I see "Vite manifest not found"?', 'acreline'),
            __('Build assets have not been compiled. Run "npm run build" in the theme directory, or install the theme via the pre-built zip (from Appearance → Update Theme or the GitHub release), which ships with compiled assets.', 'acreline')],
        [__('How do I clear the Blade template cache?', 'acreline'),
            __('Run: wp acorn view:clear --path="~/wp" --allow-root (or wp acorn optimize:clear). Required after editing any .blade.php file on a live server.', 'acreline')],
        [__('Can I rename "Township" to "County" or "Neighborhood"?', 'acreline'),
            __('Yes — open Theme Settings → Labels and change the Township / area label. The new label appears on listing cards and detail pages immediately.', 'acreline')],
        [__('Does the mortgage calculator require an API?', 'acreline'),
            __('No. It runs entirely in the browser using standard amortization math. No third-party service, no API key, no tracking.', 'acreline')],
        [__('How do I update the theme on a live host?', 'acreline'),
            __('The GitHub CI workflow publishes a ready-to-install zip on the "theme-latest" release. Enter your GitHub token in Customizer → GitHub Token, then click Update Theme.', 'acreline')],
        [__('How do I change the site colors?', 'acreline'),
            __('Appearance → Acreline Settings → General, or Customize → Colors. Named styles (Forest, Harvest, and the rest) set accent, paper, and ink together. You can still tweak the three colors after you pick a style.', 'acreline')],
        [__('How do I hide the front-end color chip?', 'acreline'),
            __('Appearance → Acreline Settings → General, or Customize → Colors → uncheck “Show front-end color style switcher.” On by default for the concept demo. Both screens keep the same saved value.', 'acreline')],
        [__('How do I turn off the homepage hero animation?', 'acreline'),
            __('Appearance → Acreline Settings → General, or Customize → Header → uncheck “Animate homepage hero image.” The photo stays; it no longer pans. Visitors who prefer reduced motion already see a still cover. Both screens keep the same saved value.', 'acreline')],
        [__('How do I turn on listing-search tilt on phones?', 'acreline'),
            __('Appearance → Acreline Settings → General, or Customize → Header → check “Tilt listing search on mobile.” Off by default. It does nothing on desktop, without sensors, if permission is denied, or if the visitor prefers reduced motion.', 'acreline')],
        [__('Where are bookings stored?', 'acreline'),
            __('Showing requests become Booking CPT posts in WordPress Admin → Bookings. There is no external CRM connection by default — all data stays in your database.', 'acreline')],
        [__('Can I submit this theme to WordPress.org?', 'acreline'),
            __('A lite version path is planned. The current build ships Sage 11 + Acorn, which will not pass wp.org first review without stripping Acorn. See docs/marketplace/SELLING.md for the current release strategy.', 'acreline')],
    ];
    foreach ($faqs as [$q, $a]) { ?>
          <details class="kss-faq">
            <summary><?php echo esc_html($q); ?></summary>
            <p><?php echo esc_html($a); ?></p>
          </details>
          <?php } ?>
        </div>
      </section>

      <!-- Changelog -->
      <section class="kss-section">
        <h2 class="kss-section-title"><?php esc_html_e('Recent changelog', 'acreline'); ?></h2>
        <div class="kss-changelog">
          <?php
    $changelog = [
        ['1.5.4', [
            __('Contrast pass: muted text, top-bar chips, buttons, footer, and Equal Housing stay readable on every color style', 'acreline'),
            __('Custom pale accents pick a darker text color when the swatch would fail WCAG AA', 'acreline'),
        ]],
        ['1.5.3', [
            __('The top bar defaults to Accent so it tracks the active color style', 'acreline'),
            __('Sites that already saved Dark, Light, or Custom keep that choice', 'acreline'),
            __('Identity color tokens load after the compiled stylesheet so the bar follows the scheme', 'acreline'),
        ]],
        ['1.5.2', [
            __('Newsletter / new-listings digest: weekly-note band with listing teasers and a form card', 'acreline'),
            __('Inspector options for layout, band color, form style, copy, teasers, benefits, and fine print', 'acreline'),
            __('Color styles still theme the band; concept note stays quiet and nothing is emailed', 'acreline'),
        ]],
        ['1.5.1', [
            __('Partner logo strip: fictional sample marks, media-library picker, add or remove partners', 'acreline'),
            __('Newsletter / market-note band redesigned with layout variants (demo — nothing is emailed)', 'acreline'),
            __('Agents page: Team Intro block under the hero with desks, stats, and office copy', 'acreline'),
            __('Block inspector: band, heading, and spacing controls apply on every marketing section', 'acreline'),
            __('After you install the zip, run Tools → Migrate to Blocks → Force rebuild so Home and Agents pick up the new stacks', 'acreline'),
        ]],
        ['1.5.0', [
            __('New Region Coverage block: split map + neighborhood cards, or bento/grid', 'acreline'),
            __('New blocks: Pricing plans, logo strip, and newsletter signup (demo form — nothing is emailed)', 'acreline'),
            __('Area Grid photos and card styles; How We Work steps are editable', 'acreline'),
            __('Marketing pages and FAQs speak to real estate agents: homes, listings, and showings', 'acreline'),
            __('After you install the zip, run Tools → Migrate to Blocks → Force rebuild so existing pages pick up the new stacks', 'acreline'),
        ]],
        ['1.4.7', [
            __('The marketing CTA band follows the active color scheme', 'acreline'),
            __('The desktop top bar follows the active color scheme; custom top-bar colors still override', 'acreline'),
            __('Appearance → Acreline Settings → General: set the color style and hide the front-end color chip', 'acreline'),
        ]],
        ['1.4.6', [
            __('The mobile-menu contact, social, and new-listing panel follows the active color scheme', 'acreline'),
            __('Text and icons on that panel stay readable when the accent is pale', 'acreline'),
        ]],
        ['1.4.5', [
            __('Primary menu, hamburger, and mobile drawer follow the active color scheme', 'acreline'),
            __('Link, hover, and current menu colors stay readable on the header background', 'acreline'),
        ]],
        ['1.4.4', [
            __('Homepage hero motion toggles on Appearance → Acreline Settings → General; values stay saved when you leave the page', 'acreline'),
            __('Customize → Header stays in sync with Acreline Settings for Ken Burns and mobile search tilt', 'acreline'),
            __('Top bar (desktop and mobile nav) uses the active color scheme; text and icons auto-pick a readable pair when accent-on-paper would fail', 'acreline'),
        ]],
        ['1.4.3', [
            __('Homepage hero photo slowly pans and zooms; headlines, overlay, and listing search stay still', 'acreline'),
            __('Optional phone tilt on the homepage listing search — off by default; does nothing without sensors or permission', 'acreline'),
            __('Customize → Header: “Animate homepage hero image” (on) and “Tilt listing search on mobile” (off)', 'acreline'),
        ]],
        ['1.4.2', [
            __('Homepage hero shows a full-bleed rural farmhouse photo with a readable ink veil — change it from the Home Hero block image picker', 'acreline'),
            __('Seed and force-rebuild write the hero image URL onto the home page', 'acreline'),
        ]],
        ['1.4.1', [
            __('Full Gutenberg stacks on every marketing page — agent list, FAQ, stats, reviews, checklists, compare table, and a richer Blog page', 'acreline'),
            __('New blocks: Trust Strip, Buyer Checklist, Showing Prep, Area Compare Table, Topic Cards, Post Grid', 'acreline'),
            __('Agent List now appears in the block inserter', 'acreline'),
            __('Re-seed and Tools → Migrate to Blocks → Force rebuild overwrite thin legacy page content', 'acreline'),
        ]],
        ['1.4.0', [
            __('Top bar (Customize → Top Bar) — desktop-only, four color presets, announcement badge + message + link, CTA pill, contact item toggles, social icon toggles, optional dismiss', 'acreline'),
            __('Mobile nav fallback — all active top-bar items surface in the slide-out nav on mobile', 'acreline'),
            __('Agent List Gutenberg block — photo, stats, designations, and social links from the Agent CPT', 'acreline'),
            __('Accessibility — focus trap on listing modal, saved drawer, and compare modal; aria-live announcements for save/compare actions', 'acreline'),
            __('Responsive — listing detail grid, compare table, compare bar, mortgage calc grid all stack or scroll on narrow phones', 'acreline'),
            __('Seed listings enriched with open house dates, virtual tour, utilities, HOA, school district, outbuildings, and land acres', 'acreline'),
            __('Seed agents enriched with performance stats, social links, bio video, certifications, and awards', 'acreline'),
            __('README, readme.txt, and marketplace docs updated for v1.4.0', 'acreline'),
        ]],
        ['1.3.3', [
            __('Redesigned Theme Settings page — tabbed UI, iOS-style toggles, sticky save bar, and unsaved-changes guard', 'acreline'),
            __('New Theme Support admin page (this page) with quick start, feature reference, shortcode cheatsheet, FAQ, and changelog', 'acreline'),
            __('Listing comparison modal — compare up to 3 listings side-by-side', 'acreline'),
            __('Persistent saved listings drawer using localStorage — heart icon on every card', 'acreline'),
            __('Sticky CTA bar on single listing pages — price + "Book a showing" after hero scrolls out', 'acreline'),
            __('Share button with Web Share API and URL-copy fallback', 'acreline'),
            __('Print-friendly property flyer layout', 'acreline'),
            __('Recently viewed listings (up to 5, localStorage) on single listing pages', 'acreline'),
            __('Nearby listings section — server-rendered, same township, no API', 'acreline'),
            __('Market snapshot shortcode [acreline_market_snapshot]', 'acreline'),
            __('Built-in mortgage calculator on listing detail pages', 'acreline'),
            __('Media picker for all image URL inputs in listing and agent metaboxes', 'acreline'),
            __('Rich listing fields: property details, utilities, HOA, land & farm, green/smart chips', 'acreline'),
            __('Advanced agent profiles: stats grid, social links, certifications, calendar URL', 'acreline'),
        ]],
        ['1.3.2', [
            __('WCAG 2.2 AA contrast fixes across form notes, footer, testimonials, and hero search', 'acreline'),
            __('44px touch targets for mobile nav and concept badge', 'acreline'),
            __('Skip-link focus (tabindex="-1" on <main>)', 'acreline'),
            __('Semantic heading hierarchy correction in footer (h2 → h3)', 'acreline'),
            __('i18n: hardcoded strings in single listing, agent, blog, and footer templates wrapped in __()', 'acreline'),
        ]],
        ['1.3.0', [
            __('Setup wizard under Appearance → Acreline Setup', 'acreline'),
            __('Saves brand, phone, email, hours, color style, and optional demo seed', 'acreline'),
        ]],
    ];
    foreach ($changelog as [$ver, $items]) { ?>
          <div class="kss-cl-block">
            <div class="kss-cl-ver"><?php echo esc_html($ver); ?></div>
            <ul class="kss-cl-list">
              <?php foreach ($items as $item) { ?>
              <li><?php echo esc_html($item); ?></li>
              <?php } ?>
            </ul>
          </div>
          <?php } ?>
        </div>
      </section>

      <!-- About card -->
      <section class="kss-section kss-about-card">
        <div class="kss-about-inner">
          <div class="kss-about-brand">
            <span class="kss-logo-mark kss-logo-mark--lg" aria-hidden="true">A</span>
            <div>
              <h2 class="kss-about-title">Acreline</h2>
              <p class="kss-about-tagline"><?php esc_html_e('Homes · neighborhoods · local agents', 'acreline'); ?></p>
              <p class="kss-about-meta">
                <?php echo esc_html(sprintf(__('Version %s · WordPress theme by Matt Hummel', 'acreline'), $themeVer)); ?>
              </p>
            </div>
          </div>
          <div class="kss-about-links">
            <a href="https://matthummel.com/projects/acreline/" target="_blank" rel="noopener noreferrer" class="kss-about-link">
              <span class="kss-about-link-icon" aria-hidden="true">🏡</span>
              <span>
                <strong><?php esc_html_e('Product page', 'acreline'); ?></strong>
                <small>matthummel.com/projects/acreline/</small>
              </span>
            </a>
            <a href="https://matthummel.com/support/acreline/" target="_blank" rel="noopener noreferrer" class="kss-about-link">
              <span class="kss-about-link-icon" aria-hidden="true">🙋</span>
              <span>
                <strong><?php esc_html_e('Support', 'acreline'); ?></strong>
                <small>matthummel.com/support/acreline/</small>
              </span>
            </a>
            <a href="https://acreline.matthummel.com/" target="_blank" rel="noopener noreferrer" class="kss-about-link">
              <span class="kss-about-link-icon" aria-hidden="true">🌐</span>
              <span>
                <strong><?php esc_html_e('Live demo', 'acreline'); ?></strong>
                <small>acreline.matthummel.com</small>
              </span>
            </a>
            <a href="https://github.com/matthummel-pa/wp-acreline/issues" target="_blank" rel="noopener noreferrer" class="kss-about-link">
              <span class="kss-about-link-icon" aria-hidden="true">🐛</span>
              <span>
                <strong><?php esc_html_e('Bug reports', 'acreline'); ?></strong>
                <small>github.com/matthummel-pa/wp-acreline/issues</small>
              </span>
            </a>
          </div>
          <p class="kss-about-license">
            <?php esc_html_e('Released under GPLv2 or later. Sage / Acorn remain MIT. Sample listing, agent, and booking data is fiction only — not a live MLS or licensed brokerage.', 'acreline'); ?>
          </p>
        </div>
      </section>

    </div>
    <?php
}

// ─── CSS ──────────────────────────────────────────────────────────────────────

function ks_support_css(): string
{
    return '
/* ── Acreline Support page ──────────────────────────────────────────────── */
#wpwrap { background: #f0f0f1; }

.kss-page {
  max-width: 960px;
  margin: 20px auto 60px;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  color: #1a1a1a;
}

/* Header */
.kss-header {
  display: flex; align-items: center; justify-content: space-between;
  background: #155539; border-radius: 12px 12px 0 0;
  padding: 22px 28px; gap: 16px; flex-wrap: wrap;
}
.kss-header-brand { display: flex; align-items: center; gap: 16px; }
.kss-logo-mark {
  width: 44px; height: 44px; background: #fff; color: #155539;
  font-weight: 900; font-size: 1.4rem; border-radius: 12px;
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.kss-title { color: #fff; font-size: 1.25rem; font-weight: 800; margin: 0 0 3px; padding: 0; }
.kss-subtitle { color: rgba(255,255,255,.6); font-size: .8rem; margin: 0; }
.kss-header-links { display: flex; gap: 10px; flex-wrap: wrap; }
.kss-btn {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 8px 16px; border-radius: 8px; font-size: .82rem; font-weight: 700;
  text-decoration: none; transition: background .15s, color .15s;
  white-space: nowrap;
}
.kss-btn-outline {
  background: transparent; color: rgba(255,255,255,.85);
  border: 1.5px solid rgba(255,255,255,.35);
}
.kss-btn-outline:hover { background: rgba(255,255,255,.12); color: #fff; border-color: rgba(255,255,255,.6); }
.kss-btn-primary { background: #155539; color: #fff; border: none; }
.kss-btn-primary:hover { background: #1a6a47; color: #fff; }

/* Section */
.kss-section {
  background: #fff; border: 1px solid #e5e5e5;
  margin-top: 16px; border-radius: 10px; padding: 28px;
}
.kss-section-title {
  font-size: 1rem; font-weight: 800; color: #155539;
  margin: 0 0 20px; padding-bottom: 12px;
  border-bottom: 2px solid #e8f3ed;
}

/* Steps */
.kss-steps { display: flex; flex-direction: column; gap: 0; }
.kss-step {
  display: flex; gap: 16px; padding: 16px 0;
  border-bottom: 1px solid #f4f4f4;
}
.kss-step:last-child { border-bottom: none; padding-bottom: 0; }
.kss-step-num {
  width: 32px; height: 32px; flex-shrink: 0;
  background: #155539; color: #fff;
  font-size: .9rem; font-weight: 800; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  margin-top: 2px;
}
.kss-step-body h3 { font-size: .9rem; font-weight: 700; color: #1a1a1a; margin: 0 0 4px; }
.kss-step-body p { font-size: .85rem; color: #555; margin: 0 0 6px; line-height: 1.5; }
.kss-step-link { font-size: .82rem; font-weight: 700; color: #155539; text-decoration: none; }
.kss-step-link:hover { text-decoration: underline; }

/* Feature grid */
.kss-feature-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 14px;
}
.kss-feature-card {
  border: 1px solid #e5e5e5; border-radius: 8px;
  padding: 16px; background: #fafafa;
  transition: border-color .15s, box-shadow .15s;
}
.kss-feature-card:hover { border-color: #b8dac8; box-shadow: 0 2px 8px rgba(21,85,57,.08); }
.kss-feature-icon { font-size: 1.4rem; display: block; margin-bottom: 8px; }
.kss-feature-card h3 { font-size: .88rem; font-weight: 700; color: #1a1a1a; margin: 0 0 6px; }
.kss-feature-card p { font-size: .8rem; color: #555; margin: 0; line-height: 1.5; }

/* Table */
.kss-table-wrap { overflow-x: auto; }
.kss-table { width: 100%; border-collapse: collapse; font-size: .875rem; }
.kss-table th {
  background: #f4f8f6; color: #155539; font-weight: 700;
  padding: 10px 14px; text-align: left; border-bottom: 2px solid #d4ebdd;
}
.kss-table td { padding: 10px 14px; border-bottom: 1px solid #f0f0f0; color: #333; vertical-align: top; }
.kss-table tr:last-child td { border-bottom: none; }
.kss-code { background: #f0f0f1; padding: 2px 6px; border-radius: 4px; font-family: monospace; font-size: .8rem; }

/* Settings reference */
.kss-settings-ref { display: flex; flex-direction: column; gap: 0; }
.kss-ref-row {
  display: flex; align-items: baseline; gap: 12px;
  padding: 10px 0; border-bottom: 1px solid #f4f4f4; font-size: .875rem;
}
.kss-ref-row:last-child { border-bottom: none; }
.kss-ref-icon { font-size: 1rem; flex-shrink: 0; width: 22px; text-align: center; }
.kss-ref-row strong { font-weight: 700; color: #155539; min-width: 80px; flex-shrink: 0; }
.kss-ref-row span { color: #555; line-height: 1.5; }

/* FAQ */
.kss-faqs { display: flex; flex-direction: column; gap: 2px; }
.kss-faq {
  border: 1px solid #e8e8e8; border-radius: 8px;
  overflow: hidden;
}
.kss-faq + .kss-faq { margin-top: 6px; }
.kss-faq summary {
  padding: 13px 16px; font-size: .875rem; font-weight: 600; color: #1a1a1a;
  cursor: pointer; list-style: none; display: flex;
  align-items: center; gap: 10px;
  background: #fafafa;
}
.kss-faq summary::-webkit-details-marker { display: none; }
.kss-faq summary::before {
  content: "+"; font-size: 1.1rem; font-weight: 700; color: #155539;
  width: 20px; flex-shrink: 0; text-align: center;
}
.kss-faq[open] summary::before { content: "−"; }
.kss-faq[open] summary { background: #f0f7f4; color: #155539; }
.kss-faq p { padding: 12px 16px 14px 46px; font-size: .85rem; color: #555; margin: 0; line-height: 1.6; }

/* Changelog */
.kss-changelog { display: flex; flex-direction: column; gap: 20px; }
.kss-cl-block { display: flex; gap: 20px; align-items: flex-start; }
.kss-cl-ver {
  background: #155539; color: #fff; font-size: .78rem; font-weight: 800;
  border-radius: 20px; padding: 4px 12px; white-space: nowrap; flex-shrink: 0;
  margin-top: 2px;
}
.kss-cl-list { margin: 0; padding: 0 0 0 18px; }
.kss-cl-list li { font-size: .85rem; color: #444; margin-bottom: 4px; line-height: 1.5; }

/* About card */
.kss-about-card { background: linear-gradient(135deg, #f0f7f4 0%, #e8f4ed 100%); border: 1px solid #c8e0d4; }
.kss-about-inner { display: flex; flex-direction: column; gap: 22px; }
.kss-about-brand { display: flex; align-items: flex-start; gap: 18px; }
.kss-logo-mark--lg { width: 52px; height: 52px; font-size: 1.6rem; border-radius: 14px; flex-shrink: 0; }
.kss-about-title { font-size: 1.25rem; font-weight: 800; color: #141210; margin: 0 0 2px; }
.kss-about-tagline { font-size: .9rem; color: #155539; font-weight: 600; margin: 0 0 4px; letter-spacing: .02em; }
.kss-about-meta { font-size: .82rem; color: #666; margin: 0; }
.kss-about-links {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 10px;
}
.kss-about-link {
  display: flex; align-items: center; gap: 12px;
  background: #fff; border: 1px solid #d6e8de; border-radius: 10px;
  padding: 12px 14px; text-decoration: none; color: inherit;
  transition: border-color .15s, box-shadow .15s;
}
.kss-about-link:hover { border-color: #155539; box-shadow: 0 2px 8px rgba(21,85,57,.10); }
.kss-about-link-icon { font-size: 1.3rem; flex-shrink: 0; }
.kss-about-link strong { display: block; font-size: .85rem; color: #1a1a1a; font-weight: 700; }
.kss-about-link small { display: block; font-size: .78rem; color: #777; margin-top: 1px; }
.kss-about-license { font-size: .8rem; color: #777; margin: 0; line-height: 1.6; }
';
}
