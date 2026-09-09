<?php

/**
 * Acreline Theme Settings — Appearance → Acreline Settings.
 *
 * Polished tabbed admin page for customising display options, labels, and
 * feature toggles. All values are stored as a single serialised option
 * (`acreline_settings`) — no orphaned rows on uninstall.
 */

namespace App;

use App\Support\Catalog;

// ─── Option key & defaults ────────────────────────────────────────────────────

define('KS_SETTINGS_OPTION', 'acreline_settings');

/**
 * @return array<string, mixed>
 */
function ks_default_settings(): array
{
    return [
        // ── Labels ──────────────────────────────────────────────────────────
        'label_township'   => 'Township',
        'label_listing'    => 'Listing',
        'label_agent'      => 'Agent',
        'label_beds'       => 'Beds',
        'label_baths'      => 'Baths',
        'label_sqft'       => 'Sq Ft',
        'label_acres'      => 'Acres',
        'currency_symbol'  => '$',
        // ── Listing cards & grid ─────────────────────────────────────────────
        'listing_grid_cols'           => '3',
        'listing_show_price'          => '1',
        'listing_show_beds'           => '1',
        'listing_show_baths'          => '1',
        'listing_show_sqft'           => '1',
        'listing_show_acres'          => '1',
        'listing_show_status_badge'   => '1',
        'listing_show_type_badge'     => '1',
        'listing_show_mls'            => '1',
        'listing_show_days_on_market' => '1',
        'listing_show_price_per_sqft' => '0',
        'listing_show_open_house'     => '1',
        'listing_show_virtual_tour'   => '1',
        'listing_show_video_tour'     => '1',
        'listing_show_floor_plan'     => '1',
        'listing_show_property_details' => '1',
        'listing_show_utilities'      => '1',
        'listing_show_hoa'            => '1',
        'listing_show_land_section'   => '1',
        'listing_show_school_district'=> '1',
        'listing_show_flood_zone'     => '1',
        'listing_show_green_features' => '1',
        'listing_show_smart_home'     => '1',
        // ── Agent features ───────────────────────────────────────────────────
        'agent_show_stats'         => '1',
        'agent_show_social'        => '1',
        'agent_show_certifications'=> '1',
        'agent_show_awards'        => '1',
        'agent_show_bio_video'     => '1',
        'agent_show_team'          => '1',
        'agent_show_calendly'      => '1',
        // ── Booking form ─────────────────────────────────────────────────────
        'booking_show_buyer_type'     => '1',
        'booking_show_attendees'      => '1',
        'booking_show_comm_preference'=> '1',
        'booking_show_source'         => '0',
        // ── General ──────────────────────────────────────────────────────────
        'show_mortgage_calc'    => '1',
        'mortgage_rate_default' => '7.0',
        'show_concept_banner'   => '1',
        // ── Market snapshot ──────────────────────────────────────────────────
        'market_show_snapshot'     => '1',
        'market_median_price'      => '',
        'market_avg_dom'           => '',
        'market_inventory_months'  => '',
        'market_yoy_change'        => '',
        'market_as_of'             => '',
    ];
}

/**
 * Retrieve a setting value, falling back to the default.
 *
 * @param  mixed  $fallback  Extra fallback if not in defaults either.
 * @return mixed
 */
function ks_setting(string $key, mixed $fallback = ''): mixed
{
    $all = get_option(KS_SETTINGS_OPTION, []);
    if (! is_array($all)) {
        $all = [];
    }
    $defaults = ks_default_settings();

    return $all[$key] ?? $defaults[$key] ?? $fallback;
}

// ─── Menu registration ────────────────────────────────────────────────────────

add_action('admin_menu', function () {
    add_theme_page(
        __('Acreline Settings', 'acreline'),
        __('Acreline Settings', 'acreline'),
        'manage_options',
        'acreline-settings',
        __NAMESPACE__.'\\render_settings_page'
    );
});

// ─── Inline CSS + JS for settings page ───────────────────────────────────────

add_action('admin_enqueue_scripts', function (string $hook) {
    if ($hook !== 'appearance_page_acreline-settings') {
        return;
    }
    wp_add_inline_style('wp-admin', ks_settings_css());
    wp_add_inline_script('jquery', ks_settings_js());
});

// ─── Save handler ─────────────────────────────────────────────────────────────

add_action('admin_post_ks_save_settings', function () {
    if (! current_user_can('manage_options')) {
        wp_die(esc_html__('Permission denied.', 'acreline'));
    }
    check_admin_referer('ks_settings_save');

    $raw      = isset($_POST['ks']) && is_array($_POST['ks']) ? $_POST['ks'] : [];
    $defaults = ks_default_settings();
    $clean    = [];

    foreach ($defaults as $key => $default) {
        $posted = $raw[$key] ?? null;
        if (is_null($posted)) {
            $clean[$key] = '0';
            continue;
        }
        if (str_contains($key, 'url') || str_starts_with($key, 'social_')) {
            $clean[$key] = esc_url_raw((string) $posted);
        } elseif (str_starts_with($key, 'label_') || str_starts_with($key, 'currency_') || str_starts_with($key, 'market_')) {
            $clean[$key] = sanitize_text_field((string) $posted);
        } elseif (str_starts_with($key, 'mortgage_rate')) {
            $clean[$key] = (string) round((float) $posted, 2);
        } elseif ($key === 'listing_grid_cols') {
            $clean[$key] = in_array((string) $posted, ['2', '3', '4'], true) ? (string) $posted : '3';
        } else {
            $clean[$key] = sanitize_text_field((string) $posted);
        }
    }

    update_option(KS_SETTINGS_OPTION, $clean);

    $tab = isset($_POST['ks_active_tab']) ? sanitize_key((string) $_POST['ks_active_tab']) : 'listings';
    wp_safe_redirect(add_query_arg([
        'page'     => 'acreline-settings',
        'ks_saved' => '1',
        'tab'      => $tab,
    ], admin_url('themes.php')));
    exit;
});

// ─── Render ───────────────────────────────────────────────────────────────────

function render_settings_page(): void
{
    if (! current_user_can('manage_options')) {
        return;
    }

    $saved      = isset($_GET['ks_saved']);
    $activeTab  = sanitize_key((string) ($_GET['tab'] ?? 'listings'));
    $validTabs  = ['labels', 'listings', 'agents', 'bookings', 'market', 'general'];
    if (! in_array($activeTab, $validTabs, true)) {
        $activeTab = 'listings';
    }

    $tabs = [
        'listings' => ['icon' => '🏡', 'label' => __('Listings',  'acreline')],
        'agents'   => ['icon' => '👤', 'label' => __('Agents',    'acreline')],
        'bookings' => ['icon' => '📅', 'label' => __('Bookings',  'acreline')],
        'market'   => ['icon' => '📊', 'label' => __('Market',    'acreline')],
        'labels'   => ['icon' => '✏️', 'label' => __('Labels',    'acreline')],
        'general'  => ['icon' => '⚙️', 'label' => __('General',   'acreline')],
    ];

    $supportUrl = admin_url('themes.php?page=acreline-support');
    $themeVer   = wp_get_theme()->get('Version');
    ?>
    <div class="ks-page">

      <!-- Header -->
      <div class="ks-header">
        <div class="ks-header-brand">
          <span class="ks-logo-mark" aria-hidden="true">A</span>
          <div>
            <h1 class="ks-header-title"><?php esc_html_e('Acreline Settings', 'acreline'); ?></h1>
            <p class="ks-header-sub"><?php echo esc_html(sprintf(__('Theme version %s', 'acreline'), $themeVer)); ?></p>
          </div>
        </div>
        <a href="<?php echo esc_url($supportUrl); ?>" class="ks-header-support-link">
          <?php esc_html_e('Getting started &amp; support →', 'acreline'); ?>
        </a>
      </div>

      <?php if ($saved) { ?>
      <div class="ks-saved-toast" id="ksSavedToast" role="status">
        <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
        <?php esc_html_e('Settings saved', 'acreline'); ?>
      </div>
      <?php } ?>

      <!-- Tab nav -->
      <nav class="ks-tabs" aria-label="<?php esc_attr_e('Settings sections', 'acreline'); ?>">
        <?php foreach ($tabs as $slug => $tab) { ?>
          <a href="<?php echo esc_url(add_query_arg(['page' => 'acreline-settings', 'tab' => $slug], admin_url('themes.php'))); ?>"
             class="ks-tab<?php echo $activeTab === $slug ? ' is-active' : ''; ?>"
             aria-current="<?php echo $activeTab === $slug ? 'page' : 'false'; ?>">
            <span class="ks-tab-icon" aria-hidden="true"><?php echo $tab['icon']; ?></span>
            <?php echo esc_html($tab['label']); ?>
          </a>
        <?php } ?>
      </nav>

      <!-- Settings form -->
      <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="ks-form" id="ksSettingsForm">
        <?php wp_nonce_field('ks_settings_save'); ?>
        <input type="hidden" name="action" value="ks_save_settings">
        <input type="hidden" name="ks_active_tab" value="<?php echo esc_attr($activeTab); ?>">

        <!-- ── LISTINGS ── -->
        <?php if ($activeTab === 'listings') { ?>
        <div class="ks-panels">

          <div class="ks-card">
            <div class="ks-card-head">
              <span class="ks-card-icon" aria-hidden="true">📋</span>
              <div>
                <h2 class="ks-card-title"><?php esc_html_e('Card & grid', 'acreline'); ?></h2>
                <p class="ks-card-desc"><?php esc_html_e('Core fields shown on listing cards and the grid page.', 'acreline'); ?></p>
              </div>
            </div>
            <div class="ks-toggles">
              <?php
                ks_sel('listing_grid_cols', __('Grid columns', 'acreline'), ['2' => '2', '3' => '3', '4' => '4'], __('Default columns on the listings page.', 'acreline'));
              ?>
              <div class="ks-toggles-grid">
                <?php
                  ks_tog('listing_show_price',        __('List price', 'acreline'));
                  ks_tog('listing_show_beds',          __('Beds', 'acreline'));
                  ks_tog('listing_show_baths',         __('Baths', 'acreline'));
                  ks_tog('listing_show_sqft',          __('Sq ft', 'acreline'));
                  ks_tog('listing_show_acres',         __('Acres', 'acreline'));
                  ks_tog('listing_show_status_badge',  __('Status badge', 'acreline'));
                  ks_tog('listing_show_type_badge',    __('Type badge', 'acreline'));
                  ks_tog('listing_show_mls',           __('MLS number', 'acreline'));
                  ks_tog('listing_show_days_on_market',__('Days on market', 'acreline'));
                  ks_tog('listing_show_price_per_sqft',__('Price / sq ft', 'acreline'));
                ?>
              </div>
            </div>
          </div>

          <div class="ks-card">
            <div class="ks-card-head">
              <span class="ks-card-icon" aria-hidden="true">🏠</span>
              <div>
                <h2 class="ks-card-title"><?php esc_html_e('Detail page sections', 'acreline'); ?></h2>
                <p class="ks-card-desc"><?php esc_html_e('Toggle each section on the single listing page.', 'acreline'); ?></p>
              </div>
            </div>
            <div class="ks-toggles-grid">
              <?php
                ks_tog('listing_show_open_house',      __('Open house banner', 'acreline'));
                ks_tog('listing_show_virtual_tour',    __('Virtual tour button', 'acreline'));
                ks_tog('listing_show_video_tour',      __('Video / drone tour', 'acreline'));
                ks_tog('listing_show_floor_plan',      __('Floor plan image', 'acreline'));
                ks_tog('listing_show_property_details',__('Property details panel', 'acreline'));
                ks_tog('listing_show_utilities',       __('Utilities panel', 'acreline'));
                ks_tog('listing_show_hoa',             __('HOA / dues info', 'acreline'));
                ks_tog('listing_show_land_section',    __('Land & farm details', 'acreline'));
                ks_tog('listing_show_school_district', __('School district', 'acreline'));
                ks_tog('listing_show_flood_zone',      __('Flood zone', 'acreline'));
                ks_tog('listing_show_green_features',  __('Green / eco features', 'acreline'));
                ks_tog('listing_show_smart_home',      __('Smart home features', 'acreline'));
              ?>
            </div>
          </div>

        </div>
        <?php } ?>

        <!-- ── AGENTS ── -->
        <?php if ($activeTab === 'agents') { ?>
        <div class="ks-panels">
          <div class="ks-card">
            <div class="ks-card-head">
              <span class="ks-card-icon" aria-hidden="true">👤</span>
              <div>
                <h2 class="ks-card-title"><?php esc_html_e('Agent profile sections', 'acreline'); ?></h2>
                <p class="ks-card-desc"><?php esc_html_e('Control which sections appear on individual agent pages.', 'acreline'); ?></p>
              </div>
            </div>
            <div class="ks-toggles-grid">
              <?php
                ks_tog('agent_show_stats',          __('Performance stats (homes sold, volume, DOM)', 'acreline'));
                ks_tog('agent_show_social',         __('Social media links', 'acreline'));
                ks_tog('agent_show_certifications', __('Certifications & designations', 'acreline'));
                ks_tog('agent_show_awards',         __('Awards & recognition', 'acreline'));
                ks_tog('agent_show_bio_video',      __('Intro video link', 'acreline'));
                ks_tog('agent_show_team',           __('Team name', 'acreline'));
                ks_tog('agent_show_calendly',       __('Booking / calendar button', 'acreline'));
              ?>
            </div>
          </div>
        </div>
        <?php } ?>

        <!-- ── BOOKINGS ── -->
        <?php if ($activeTab === 'bookings') { ?>
        <div class="ks-panels">
          <div class="ks-card">
            <div class="ks-card-head">
              <span class="ks-card-icon" aria-hidden="true">📅</span>
              <div>
                <h2 class="ks-card-title"><?php esc_html_e('Showing request form', 'acreline'); ?></h2>
                <p class="ks-card-desc"><?php esc_html_e('Choose which optional fields appear on the public booking form.', 'acreline'); ?></p>
              </div>
            </div>
            <div class="ks-toggles-grid">
              <?php
                ks_tog('booking_show_buyer_type',      __('Ask financing / buyer status', 'acreline'));
                ks_tog('booking_show_attendees',       __('Ask number of attendees', 'acreline'));
                ks_tog('booking_show_comm_preference', __('Ask preferred contact method', 'acreline'));
                ks_tog('booking_show_source',          __('Ask how they heard about the listing', 'acreline'));
              ?>
            </div>
          </div>
        </div>
        <?php } ?>

        <!-- ── MARKET ── -->
        <?php if ($activeTab === 'market') { ?>
        <div class="ks-panels">
          <div class="ks-card">
            <div class="ks-card-head">
              <span class="ks-card-icon" aria-hidden="true">📊</span>
              <div>
                <h2 class="ks-card-title"><?php esc_html_e('Market snapshot stats', 'acreline'); ?></h2>
                <p class="ks-card-desc">
                  <?php esc_html_e('Enter your current market data. Display anywhere with the shortcode:', 'acreline'); ?>
                  <code class="ks-code">[acreline_market_snapshot]</code>
                </p>
              </div>
            </div>
            <div class="ks-fields">
              <?php ks_tog('market_show_snapshot', __('Enable market snapshot widget', 'acreline')); ?>
              <div class="ks-field-row">
                <div class="ks-field">
                  <?php ks_txt('market_median_price', __('Median sale price', 'acreline'), '$485,000'); ?>
                </div>
                <div class="ks-field">
                  <?php ks_txt('market_avg_dom', __('Avg. days on market', 'acreline'), '28'); ?>
                </div>
              </div>
              <div class="ks-field-row">
                <div class="ks-field">
                  <?php ks_txt('market_inventory_months', __('Months of inventory', 'acreline'), '1.8'); ?>
                </div>
                <div class="ks-field">
                  <?php ks_txt('market_yoy_change', __('Year-over-year price change', 'acreline'), '+4.2%'); ?>
                </div>
              </div>
              <?php ks_txt('market_as_of', __('Stats as of', 'acreline'), __('Q3 2026 · Sample county', 'acreline')); ?>
            </div>

            <?php
            /* Live preview */
            $mp  = ks_setting('market_median_price');
            $dom = ks_setting('market_avg_dom');
            $inv = ks_setting('market_inventory_months');
            $yoy = ks_setting('market_yoy_change');
            $ao  = ks_setting('market_as_of');
            if ($mp || $dom || $inv || $yoy) { ?>
            <div class="ks-market-preview">
              <p class="ks-preview-label"><?php esc_html_e('Preview', 'acreline'); ?></p>
              <div class="ks-preview-band">
                <?php if ($mp)  echo '<div class="ks-prev-stat"><strong>'.esc_html($mp).'</strong><span>'.esc_html__('Median price', 'acreline').'</span></div>'; ?>
                <?php if ($dom) echo '<div class="ks-prev-stat"><strong>'.esc_html($dom).'<small>d</small></strong><span>'.esc_html__('Avg. DOM', 'acreline').'</span></div>'; ?>
                <?php if ($inv) echo '<div class="ks-prev-stat"><strong>'.esc_html($inv).'<small>mo</small></strong><span>'.esc_html__('Inventory', 'acreline').'</span></div>'; ?>
                <?php if ($yoy) echo '<div class="ks-prev-stat"><strong>'.esc_html($yoy).'</strong><span>'.esc_html__('YoY', 'acreline').'</span></div>'; ?>
                <?php if ($ao)  echo '<p class="ks-prev-asof">'.esc_html__('As of', 'acreline').' '.esc_html($ao).'</p>'; ?>
              </div>
            </div>
            <?php } ?>

          </div>
        </div>
        <?php } ?>

        <!-- ── LABELS ── -->
        <?php if ($activeTab === 'labels') { ?>
        <div class="ks-panels">
          <div class="ks-card">
            <div class="ks-card-head">
              <span class="ks-card-icon" aria-hidden="true">✏️</span>
              <div>
                <h2 class="ks-card-title"><?php esc_html_e('Rename labels', 'acreline'); ?></h2>
                <p class="ks-card-desc"><?php esc_html_e('Changes apply sitewide — on listing cards, detail pages, and admin columns.', 'acreline'); ?></p>
              </div>
            </div>
            <div class="ks-fields">
              <div class="ks-field-row">
                <div class="ks-field">
                  <?php ks_txt('label_township', __('Township / area', 'acreline'), __('Township', 'acreline')); ?>
                </div>
                <div class="ks-field">
                  <?php ks_txt('label_listing', __('Listing', 'acreline'), __('Listing', 'acreline')); ?>
                </div>
              </div>
              <div class="ks-field-row">
                <div class="ks-field">
                  <?php ks_txt('label_agent', __('Agent', 'acreline'), __('Agent', 'acreline')); ?>
                </div>
                <div class="ks-field">
                  <?php ks_txt('currency_symbol', __('Currency symbol', 'acreline'), '$'); ?>
                </div>
              </div>
              <div class="ks-field-row">
                <div class="ks-field">
                  <?php ks_txt('label_beds', __('Beds label', 'acreline'), __('Beds', 'acreline')); ?>
                </div>
                <div class="ks-field">
                  <?php ks_txt('label_baths', __('Baths label', 'acreline'), __('Baths', 'acreline')); ?>
                </div>
              </div>
              <div class="ks-field-row">
                <div class="ks-field">
                  <?php ks_txt('label_sqft', __('Sq Ft label', 'acreline'), __('Sq Ft', 'acreline')); ?>
                </div>
                <div class="ks-field">
                  <?php ks_txt('label_acres', __('Acres label', 'acreline'), __('Acres', 'acreline')); ?>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php } ?>

        <!-- ── GENERAL ── -->
        <?php if ($activeTab === 'general') { ?>
        <div class="ks-panels">
          <div class="ks-card">
            <div class="ks-card-head">
              <span class="ks-card-icon" aria-hidden="true">🧮</span>
              <div>
                <h2 class="ks-card-title"><?php esc_html_e('Mortgage calculator', 'acreline'); ?></h2>
                <p class="ks-card-desc"><?php esc_html_e('Built-in calculator on listing detail pages and the listing popup modal.', 'acreline'); ?></p>
              </div>
            </div>
            <div class="ks-fields">
              <?php ks_tog('show_mortgage_calc', __('Show mortgage calculator', 'acreline')); ?>
              <div class="ks-field" style="max-width:200px">
                <?php ks_txt('mortgage_rate_default', __('Default rate (%)', 'acreline'), '7.0'); ?>
              </div>
            </div>
          </div>

          <div class="ks-card">
            <div class="ks-card-head">
              <span class="ks-card-icon" aria-hidden="true">🏷️</span>
              <div>
                <h2 class="ks-card-title"><?php esc_html_e('Concept demo', 'acreline'); ?></h2>
                <p class="ks-card-desc"><?php esc_html_e('The sitewide "concept demo · fiction only" disclaimer banner. Remove once you replace sample data with real listings.', 'acreline'); ?></p>
              </div>
            </div>
            <div class="ks-fields">
              <?php ks_tog('show_concept_banner', __('Show disclaimer banner', 'acreline')); ?>
            </div>
          </div>
        </div>
        <?php } ?>

        <!-- Sticky save bar -->
        <div class="ks-save-bar">
          <span class="ks-save-status" id="ksSaveStatus"></span>
          <button type="submit" class="ks-save-btn">
            <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M17 3H5a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V5l-2-2zm-5 14a3 3 0 110-6 3 3 0 010 6zm3-10H5V5h10v2z"/></svg>
            <?php esc_html_e('Save settings', 'acreline'); ?>
          </button>
        </div>

      </form>
    </div>
    <?php
}

// ─── Inline CSS ───────────────────────────────────────────────────────────────

function ks_settings_css(): string
{
    return '
/* ── Acreline Settings page ─────────────────────────────────────────────── */
#wpwrap { background: #f0f0f1; }

.ks-page {
  max-width: 900px;
  margin: 20px auto 100px;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}

/* Header */
.ks-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #155539;
  border-radius: 12px 12px 0 0;
  padding: 20px 28px;
  gap: 16px;
  flex-wrap: wrap;
}
.ks-header-brand { display: flex; align-items: center; gap: 14px; }
.ks-logo-mark {
  width: 40px; height: 40px;
  background: #fff;
  color: #155539;
  font-weight: 900;
  font-size: 1.2rem;
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.ks-header-title {
  color: #fff; font-size: 1.2rem; font-weight: 700;
  margin: 0; padding: 0; line-height: 1.2;
}
.ks-header-sub { color: rgba(255,255,255,.65); font-size: .8rem; margin: 2px 0 0; }
.ks-header-support-link {
  color: rgba(255,255,255,.8); font-size: .82rem;
  text-decoration: none; white-space: nowrap;
  border: 1px solid rgba(255,255,255,.3);
  padding: 6px 14px; border-radius: 20px;
  transition: background .15s, color .15s;
}
.ks-header-support-link:hover { background: rgba(255,255,255,.15); color: #fff; }

/* Saved toast */
.ks-saved-toast {
  display: flex; align-items: center; gap: 8px;
  background: #dcfce7; color: #166534;
  border: 1px solid #bbf7d0; border-radius: 8px;
  padding: 12px 18px; margin: 12px 0 0;
  font-size: .875rem; font-weight: 600;
  animation: ksToastIn .3s ease;
}
.ks-saved-toast svg { width: 18px; height: 18px; flex-shrink: 0; }
@keyframes ksToastIn { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }

/* Tabs */
.ks-tabs {
  display: flex;
  background: #fff;
  border-bottom: 1px solid #e0e0e0;
  overflow-x: auto;
  gap: 0;
  scrollbar-width: none;
}
.ks-tabs::-webkit-scrollbar { display: none; }
.ks-tab {
  display: flex; align-items: center; gap: 6px;
  padding: 14px 20px;
  font-size: .875rem; font-weight: 600;
  color: #666; text-decoration: none;
  border-bottom: 3px solid transparent;
  white-space: nowrap; transition: color .15s, border-color .15s;
  margin-bottom: -1px;
}
.ks-tab:hover { color: #155539; }
.ks-tab.is-active { color: #155539; border-bottom-color: #155539; }
.ks-tab-icon { font-size: 1rem; line-height: 1; }

/* Form wrapper */
.ks-form { display: flex; flex-direction: column; gap: 0; }

/* Panels */
.ks-panels {
  display: flex; flex-direction: column; gap: 16px;
  padding: 20px 0;
}

/* Card */
.ks-card {
  background: #fff;
  border: 1px solid #e5e5e5;
  border-radius: 10px;
  overflow: hidden;
}
.ks-card-head {
  display: flex; align-items: flex-start; gap: 14px;
  padding: 20px 24px 16px;
  border-bottom: 1px solid #f0f0f0;
}
.ks-card-icon {
  font-size: 1.4rem; line-height: 1;
  width: 36px; height: 36px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  background: #f0f7f4; border-radius: 8px;
}
.ks-card-title { font-size: .95rem; font-weight: 700; color: #1a1a1a; margin: 0 0 3px; }
.ks-card-desc { font-size: .82rem; color: #666; margin: 0; line-height: 1.4; }
.ks-code { background: #f0f0f1; padding: 1px 6px; border-radius: 4px; font-size: .8rem; font-family: monospace; }

/* Toggle grid */
.ks-toggles { padding: 16px 24px; }
.ks-toggles-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 8px;
  padding: 16px 24px;
}

/* Toggle switch */
.ks-toggle-wrap {
  display: flex; align-items: center; gap: 10px;
  padding: 8px 10px; border-radius: 8px;
  cursor: pointer; transition: background .12s;
}
.ks-toggle-wrap:hover { background: #f8f8f8; }
.ks-toggle-input { position: absolute; opacity: 0; width: 0; height: 0; }
.ks-toggle-switch {
  position: relative; flex-shrink: 0;
  width: 40px; height: 22px;
  background: #ccc; border-radius: 11px;
  transition: background .2s;
}
.ks-toggle-switch::after {
  content: ""; position: absolute;
  width: 16px; height: 16px; border-radius: 50%;
  background: #fff; top: 3px; left: 3px;
  transition: transform .2s, box-shadow .2s;
  box-shadow: 0 1px 3px rgba(0,0,0,.2);
}
.ks-toggle-input:checked + .ks-toggle-switch { background: #155539; }
.ks-toggle-input:checked + .ks-toggle-switch::after { transform: translateX(18px); }
.ks-toggle-input:focus-visible + .ks-toggle-switch { outline: 3px solid #155539; outline-offset: 2px; }
.ks-toggle-text { font-size: .85rem; font-weight: 500; color: #333; line-height: 1.3; }

/* Select field */
.ks-select-row {
  display: flex; align-items: center; gap: 16px;
  padding: 12px 24px; border-bottom: 1px solid #f5f5f5;
  flex-wrap: wrap;
}
.ks-select-row label { font-size: .875rem; font-weight: 600; color: #333; min-width: 140px; }
.ks-select-row select {
  border: 1px solid #ddd; border-radius: 6px;
  padding: 6px 10px; font-size: .875rem; color: #333;
  background: #fff; cursor: pointer;
}
.ks-select-row .ks-field-hint { font-size: .78rem; color: #888; margin: 0; flex-basis: 100%; padding-left: 156px; }

/* Text fields */
.ks-fields { padding: 16px 24px; display: flex; flex-direction: column; gap: 12px; }
.ks-field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
@media (max-width: 640px) { .ks-field-row { grid-template-columns: 1fr; } }
.ks-field label { display: block; font-size: .82rem; font-weight: 600; color: #555; margin-bottom: 5px; }
.ks-field input[type="text"],
.ks-field input[type="number"] {
  width: 100%; border: 1px solid #ddd; border-radius: 7px;
  padding: 8px 12px; font-size: .875rem; color: #333;
  background: #fff; box-sizing: border-box;
  transition: border-color .15s, box-shadow .15s;
}
.ks-field input:focus {
  border-color: #155539; outline: none;
  box-shadow: 0 0 0 3px rgba(21,85,57,.12);
}
.ks-field-hint { font-size: .76rem; color: #888; margin: 4px 0 0; }

/* Market preview */
.ks-market-preview { padding: 16px 24px 20px; border-top: 1px solid #f0f0f0; }
.ks-preview-label { font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #888; margin: 0 0 10px; }
.ks-preview-band {
  background: #155539; border-radius: 10px;
  display: flex; flex-wrap: wrap; gap: 0;
  padding: 16px 20px;
}
.ks-prev-stat { flex: 1; min-width: 100px; padding: 6px 12px; display: flex; flex-direction: column; gap: 4px; }
.ks-prev-stat strong { font-size: 1.3rem; font-weight: 800; color: #fff; }
.ks-prev-stat strong small { font-size: .75rem; font-weight: 500; opacity: .8; }
.ks-prev-stat span { font-size: .72rem; font-weight: 700; color: rgba(255,255,255,.6); text-transform: uppercase; letter-spacing: .05em; }
.ks-prev-asof { width: 100%; font-size: .72rem; color: rgba(255,255,255,.45); margin: 8px 0 0; padding: 8px 12px 0; border-top: 1px solid rgba(255,255,255,.12); }

/* Sticky save bar */
.ks-save-bar {
  position: fixed; bottom: 0; left: 160px; right: 0;
  background: #fff; border-top: 1px solid #e5e5e5;
  box-shadow: 0 -2px 12px rgba(0,0,0,.06);
  display: flex; align-items: center; justify-content: flex-end;
  gap: 14px; padding: 12px 32px;
  z-index: 100;
}
@media (max-width: 782px) { .ks-save-bar { left: 0; padding: 12px 16px; } }
.ks-save-status { font-size: .82rem; color: #166534; font-weight: 600; }
.ks-save-btn {
  display: inline-flex; align-items: center; gap: 7px;
  background: #155539; color: #fff;
  border: none; border-radius: 8px;
  padding: 10px 22px; font-size: .9rem; font-weight: 700;
  cursor: pointer; transition: background .15s, transform .1s;
}
.ks-save-btn svg { width: 16px; height: 16px; }
.ks-save-btn:hover { background: #1a6a47; }
.ks-save-btn:active { transform: scale(.98); }
.ks-save-btn:focus-visible { outline: 3px solid #155539; outline-offset: 3px; }
';
}

// ─── Inline JS ────────────────────────────────────────────────────────────────

function ks_settings_js(): string
{
    return '
jQuery(function($){
  /* Auto-dismiss saved toast */
  var toast = document.getElementById("ksSavedToast");
  if(toast){ setTimeout(function(){ toast.style.opacity="0"; toast.style.transition="opacity .4s"; setTimeout(function(){ toast.remove(); },400); }, 3000); }

  /* Track unsaved changes */
  var dirty = false;
  $("#ksSettingsForm").on("change input","input,select",function(){ dirty=true; $("#ksSaveStatus").text("Unsaved changes"); });
  $("#ksSettingsForm").on("submit",function(){ dirty=false; });
  $(window).on("beforeunload",function(e){ if(dirty){ e.preventDefault(); return "You have unsaved changes."; } });
});
';
}

// ─── Render helpers (internal) ────────────────────────────────────────────────

/** Render a visual toggle switch row. */
function ks_tog(string $key, string $label): void
{
    $on = (string) ks_setting($key) !== '0';
    $id = 'ks_'.esc_attr($key);
    echo '<label class="ks-toggle-wrap" for="'.esc_attr($id).'">';
    echo '<input class="ks-toggle-input" type="checkbox" id="'.esc_attr($id).'" name="ks['.esc_attr($key).']" value="1"'.checked($on, true, false).'>';
    echo '<span class="ks-toggle-switch" aria-hidden="true"></span>';
    echo '<span class="ks-toggle-text">'.esc_html($label).'</span>';
    echo '</label>';
}

/** Render a text input field. */
function ks_txt(string $key, string $label, string $placeholder = ''): void
{
    $value = (string) ks_setting($key);
    $id    = 'ks_'.esc_attr($key);
    echo '<div class="ks-field">';
    echo '<label for="'.esc_attr($id).'">'.esc_html($label).'</label>';
    echo '<input type="text" id="'.esc_attr($id).'" name="ks['.esc_attr($key).']" value="'.esc_attr($value).'" placeholder="'.esc_attr($placeholder).'">';
    echo '</div>';
}

/** Render a select field row. */
/**
 * @param  array<string, string>  $options
 */
function ks_sel(string $key, string $label, array $options, string $hint = ''): void
{
    $current = (string) ks_setting($key);
    $id      = 'ks_'.esc_attr($key);
    echo '<div class="ks-select-row">';
    echo '<label for="'.esc_attr($id).'">'.esc_html($label).'</label>';
    echo '<select id="'.esc_attr($id).'" name="ks['.esc_attr($key).']">';
    foreach ($options as $val => $text) {
        echo '<option value="'.esc_attr($val).'" '.selected($current, $val, false).'>'.esc_html($text).'</option>';
    }
    echo '</select>';
    if ($hint !== '') {
        echo '<p class="ks-field-hint">'.esc_html($hint).'</p>';
    }
    echo '</div>';
}

/* ── Legacy helpers kept for backward compat (used nowhere externally, but
   defensive in case other plugins call them) ─────────────────────────────── */
function ks_settings_section(string $title, string $desc): void {}
function ks_toggle_field(string $key, string $label): void { ks_tog($key, $label); }
function ks_text_field(string $key, string $label, string $desc): void { ks_txt($key, $label, $desc); }
/** @param array<string, string> $options */
function ks_select_field(string $key, string $label, array $options): void { ks_sel($key, $label, $options); }

// ─── Market snapshot shortcode ────────────────────────────────────────────────

add_shortcode('acreline_market_snapshot', function (): string {
    if ((string) ks_setting('market_show_snapshot') === '0') {
        return '';
    }

    $medianPrice = sanitize_text_field((string) ks_setting('market_median_price'));
    $avgDom      = sanitize_text_field((string) ks_setting('market_avg_dom'));
    $inventory   = sanitize_text_field((string) ks_setting('market_inventory_months'));
    $yoy         = sanitize_text_field((string) ks_setting('market_yoy_change'));
    $asOf        = sanitize_text_field((string) ks_setting('market_as_of'));

    if (! $medianPrice && ! $avgDom && ! $inventory && ! $yoy) {
        return '';
    }

    $stats = [];
    if ($medianPrice) {
        $stats[] = ['value' => esc_html($medianPrice),                                                      'label' => esc_html__('Median sale price',    'acreline')];
    }
    if ($avgDom) {
        $stats[] = ['value' => esc_html($avgDom).' <span>'.esc_html__('days', 'acreline').'</span>',       'label' => esc_html__('Avg. days on market',  'acreline')];
    }
    if ($inventory) {
        $stats[] = ['value' => esc_html($inventory).' <span>'.esc_html__('mo', 'acreline').'</span>',      'label' => esc_html__('Months of inventory',  'acreline')];
    }
    if ($yoy) {
        $stats[] = ['value' => esc_html($yoy),                                                              'label' => esc_html__('Year-over-year',        'acreline')];
    }

    $html = '<div class="market-snapshot market-snapshot--'.count($stats).'">';
    foreach ($stats as $stat) {
        $html .= '<div class="market-stat"><strong>'.$stat['value'].'</strong><span>'.$stat['label'].'</span></div>';
    }
    if ($asOf) {
        $html .= '<p class="market-as-of">'.esc_html__('As of', 'acreline').' '.esc_html($asOf).'</p>';
    }
    $html .= '</div>';

    return $html;
});
