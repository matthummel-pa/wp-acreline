<?php

/**
 * Acreline Theme Settings — Appearance → Acreline Settings.
 *
 * Provides a central admin page for customizing display options, labels,
 * and feature toggles for listings, agents, and bookings — all stored as
 * a single serialised option (`acreline_settings`) so there are no orphaned
 * option rows on uninstall.
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
        'label_township' => 'Township',
        'label_listing' => 'Listing',
        'label_agent' => 'Agent',
        'label_beds' => 'Beds',
        'label_baths' => 'Baths',
        'label_sqft' => 'Sq Ft',
        'label_acres' => 'Acres',
        'currency_symbol' => '$',
        // ── Listing cards & grid ─────────────────────────────────────────────
        'listing_grid_cols' => '3',
        'listing_show_price' => '1',
        'listing_show_beds' => '1',
        'listing_show_baths' => '1',
        'listing_show_sqft' => '1',
        'listing_show_acres' => '1',
        'listing_show_status_badge' => '1',
        'listing_show_type_badge' => '1',
        'listing_show_mls' => '1',
        'listing_show_days_on_market' => '1',
        'listing_show_price_per_sqft' => '0',
        'listing_show_open_house' => '1',
        'listing_show_virtual_tour' => '1',
        'listing_show_video_tour' => '1',
        'listing_show_floor_plan' => '1',
        'listing_show_property_details' => '1',
        'listing_show_utilities' => '1',
        'listing_show_hoa' => '1',
        'listing_show_land_section' => '1',
        'listing_show_school_district' => '1',
        'listing_show_flood_zone' => '1',
        'listing_show_green_features' => '1',
        'listing_show_smart_home' => '1',
        // ── Agent features ───────────────────────────────────────────────────
        'agent_show_stats' => '1',
        'agent_show_social' => '1',
        'agent_show_certifications' => '1',
        'agent_show_awards' => '1',
        'agent_show_bio_video' => '1',
        'agent_show_team' => '1',
        'agent_show_calendly' => '1',
        // ── Booking form ─────────────────────────────────────────────────────
        'booking_show_buyer_type' => '1',
        'booking_show_attendees' => '1',
        'booking_show_comm_preference' => '1',
        'booking_show_source' => '0',
        // ── General ──────────────────────────────────────────────────────────
        'show_mortgage_calc' => '1',
        'mortgage_rate_default' => '7.0',
        'show_concept_banner' => '1',
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

// ─── Save handler ─────────────────────────────────────────────────────────────

add_action('admin_post_ks_save_settings', function () {
    if (! current_user_can('manage_options')) {
        wp_die(esc_html__('Permission denied.', 'acreline'));
    }
    check_admin_referer('ks_settings_save');

    $raw = isset($_POST['ks']) && is_array($_POST['ks']) ? $_POST['ks'] : [];
    $defaults = ks_default_settings();
    $clean = [];

    foreach ($defaults as $key => $default) {
        $posted = $raw[$key] ?? null;
        if (is_null($posted)) {
            // Unchecked checkboxes are not posted — store '0'.
            $clean[$key] = '0';

            continue;
        }
        if (str_contains($key, 'url') || str_starts_with($key, 'social_')) {
            $clean[$key] = esc_url_raw((string) $posted);
        } elseif (str_starts_with($key, 'label_') || str_starts_with($key, 'currency_')) {
            $clean[$key] = sanitize_text_field((string) $posted);
        } elseif (str_starts_with($key, 'mortgage_rate')) {
            $clean[$key] = (string) round((float) $posted, 2);
        } elseif (str_starts_with($key, 'listing_grid')) {
            $clean[$key] = in_array((string) $posted, ['2', '3', '4'], true) ? (string) $posted : '3';
        } else {
            $clean[$key] = sanitize_text_field((string) $posted);
        }
    }

    update_option(KS_SETTINGS_OPTION, $clean);

    wp_safe_redirect(add_query_arg(['page' => 'acreline-settings', 'ks_saved' => '1'], admin_url('themes.php')));
    exit;
});

// ─── Render settings page ─────────────────────────────────────────────────────

function render_settings_page(): void
{
    if (! current_user_can('manage_options')) {
        return;
    }
    $saved = isset($_GET['ks_saved']);
    ?>
    <div class="wrap">
      <h1><?php esc_html_e('Acreline Settings', 'acreline'); ?></h1>
      <?php if ($saved) { ?>
        <div class="notice notice-success is-dismissible"><p><?php esc_html_e('Settings saved.', 'acreline'); ?></p></div>
      <?php } ?>

      <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <?php wp_nonce_field('ks_settings_save'); ?>
        <input type="hidden" name="action" value="ks_save_settings">

        <?php ks_settings_section(__('Labels', 'acreline'), __('Rename field labels sitewide. Changes are reflected on listing cards and detail pages.', 'acreline')); ?>
        <table class="form-table" role="presentation">
          <?php
            ks_text_field('label_township', __('Township / area label', 'acreline'), __('e.g. Township, County, Area, Neighborhood', 'acreline'));
            ks_text_field('label_listing', __('Listing label', 'acreline'), '');
            ks_text_field('label_agent', __('Agent label', 'acreline'), '');
            ks_text_field('label_beds', __('Beds label', 'acreline'), '');
            ks_text_field('label_baths', __('Baths label', 'acreline'), '');
            ks_text_field('label_sqft', __('Sq Ft label', 'acreline'), '');
            ks_text_field('label_acres', __('Acres label', 'acreline'), '');
            ks_text_field('currency_symbol', __('Currency symbol', 'acreline'), __('e.g. $ or €', 'acreline'));
          ?>
        </table>

        <?php ks_settings_section(__('Listing display', 'acreline'), __('Choose what information appears on listing cards and detail pages.', 'acreline')); ?>
        <table class="form-table" role="presentation">
          <?php
            ks_select_field('listing_grid_cols', __('Default grid columns', 'acreline'), ['2' => '2 columns', '3' => '3 columns', '4' => '4 columns']);
            ks_toggle_field('listing_show_price', __('Show list price', 'acreline'));
            ks_toggle_field('listing_show_beds', __('Show beds', 'acreline'));
            ks_toggle_field('listing_show_baths', __('Show baths', 'acreline'));
            ks_toggle_field('listing_show_sqft', __('Show square feet', 'acreline'));
            ks_toggle_field('listing_show_acres', __('Show acreage', 'acreline'));
            ks_toggle_field('listing_show_status_badge', __('Show status badge (Active, Pending, Sold)', 'acreline'));
            ks_toggle_field('listing_show_type_badge', __('Show property type badge', 'acreline'));
            ks_toggle_field('listing_show_mls', __('Show MLS number', 'acreline'));
            ks_toggle_field('listing_show_days_on_market', __('Show days on market', 'acreline'));
            ks_toggle_field('listing_show_price_per_sqft', __('Show price per square foot', 'acreline'));
            ks_toggle_field('listing_show_open_house', __('Show open house banner', 'acreline'));
            ks_toggle_field('listing_show_virtual_tour', __('Show virtual tour button', 'acreline'));
            ks_toggle_field('listing_show_video_tour', __('Show video / drone tour link', 'acreline'));
            ks_toggle_field('listing_show_floor_plan', __('Show floor plan image', 'acreline'));
            ks_toggle_field('listing_show_property_details', __('Show property details panel (condition, garage, basement…)', 'acreline'));
            ks_toggle_field('listing_show_utilities', __('Show utilities panel (water, sewer, heating, cooling)', 'acreline'));
            ks_toggle_field('listing_show_hoa', __('Show HOA / dues info', 'acreline'));
            ks_toggle_field('listing_show_land_section', __('Show land &amp; farm details section', 'acreline'));
            ks_toggle_field('listing_show_school_district', __('Show school district', 'acreline'));
            ks_toggle_field('listing_show_flood_zone', __('Show flood zone', 'acreline'));
            ks_toggle_field('listing_show_green_features', __('Show green / eco features', 'acreline'));
            ks_toggle_field('listing_show_smart_home', __('Show smart home features', 'acreline'));
          ?>
        </table>

        <?php ks_settings_section(__('Agent profile', 'acreline'), __('Control which sections appear on agent profile pages.', 'acreline')); ?>
        <table class="form-table" role="presentation">
          <?php
            ks_toggle_field('agent_show_stats', __('Show performance stats panel (homes sold, volume, DOM)', 'acreline'));
            ks_toggle_field('agent_show_social', __('Show social media links', 'acreline'));
            ks_toggle_field('agent_show_certifications', __('Show certifications &amp; designations', 'acreline'));
            ks_toggle_field('agent_show_awards', __('Show awards / recognition', 'acreline'));
            ks_toggle_field('agent_show_bio_video', __('Show intro video link', 'acreline'));
            ks_toggle_field('agent_show_team', __('Show team name', 'acreline'));
            ks_toggle_field('agent_show_calendly', __('Show booking / calendar button', 'acreline'));
          ?>
        </table>

        <?php ks_settings_section(__('Booking form', 'acreline'), __('Choose which optional fields appear on the public showing request form.', 'acreline')); ?>
        <table class="form-table" role="presentation">
          <?php
            ks_toggle_field('booking_show_buyer_type', __('Ask buyer financing status', 'acreline'));
            ks_toggle_field('booking_show_attendees', __('Ask number of attendees', 'acreline'));
            ks_toggle_field('booking_show_comm_preference', __('Ask preferred contact method', 'acreline'));
            ks_toggle_field('booking_show_source', __('Ask how they heard about the listing', 'acreline'));
          ?>
        </table>

        <?php ks_settings_section(__('General', 'acreline'), ''); ?>
        <table class="form-table" role="presentation">
          <?php
            ks_toggle_field('show_mortgage_calc', __('Show mortgage calculator on listing detail pages', 'acreline'));
            ks_text_field('mortgage_rate_default', __('Default mortgage rate (%)', 'acreline'), __('Used as the pre-filled rate in the calculator', 'acreline'));
            ks_toggle_field('show_concept_banner', __('Show concept demo disclaimer banner', 'acreline'));
          ?>
        </table>

        <?php submit_button(__('Save settings', 'acreline')); ?>
      </form>
    </div>
    <?php
}

// ─── Render helpers ───────────────────────────────────────────────────────────

function ks_settings_section(string $title, string $desc): void
{
    echo '<h2 style="margin-top:2rem;padding-top:1rem;border-top:1px solid #ddd">'.esc_html($title).'</h2>';
    if ($desc !== '') {
        echo '<p class="description">'.esc_html($desc).'</p>';
    }
}

function ks_toggle_field(string $key, string $label): void
{
    $on = (string) ks_setting($key) !== '0';
    echo '<tr>';
    echo '<th scope="row">'.esc_html($label).'</th>';
    echo '<td><label><input type="checkbox" name="ks['.esc_attr($key).']" value="1"'.checked($on, true, false).'> '.esc_html__('Enabled', 'acreline').'</label></td>';
    echo '</tr>';
}

function ks_text_field(string $key, string $label, string $desc): void
{
    $value = (string) ks_setting($key);
    echo '<tr>';
    echo '<th scope="row"><label for="ks_'.esc_attr($key).'">'.esc_html($label).'</label></th>';
    echo '<td>';
    echo '<input class="regular-text" type="text" id="ks_'.esc_attr($key).'" name="ks['.esc_attr($key).']" value="'.esc_attr($value).'">';
    if ($desc !== '') {
        echo '<p class="description">'.esc_html($desc).'</p>';
    }
    echo '</td>';
    echo '</tr>';
}

/**
 * @param  array<string, string>  $options
 */
function ks_select_field(string $key, string $label, array $options): void
{
    $current = (string) ks_setting($key);
    echo '<tr>';
    echo '<th scope="row"><label for="ks_'.esc_attr($key).'">'.esc_html($label).'</label></th>';
    echo '<td><select id="ks_'.esc_attr($key).'" name="ks['.esc_attr($key).']">';
    foreach ($options as $val => $text) {
        echo '<option value="'.esc_attr($val).'" '.selected($current, $val, false).'>'.esc_html($text).'</option>';
    }
    echo '</select></td>';
    echo '</tr>';
}
