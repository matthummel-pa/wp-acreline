<?php

/**
 * Admin UI for listings, bookings, and agents.
 */

namespace App;

use App\Support\Catalog;
use App\Support\PageCopy;

add_action('add_meta_boxes', function () {
    if (post_type_exists(Catalog::LISTING)) {
        add_meta_box('ks_listing_details', __('Listing details', 'acreline'), __NAMESPACE__.'\\listing_metabox', Catalog::LISTING, 'normal', 'high');
    }
    if (post_type_exists(Catalog::AGENT)) {
        add_meta_box('ks_agent_details', __('Agent details', 'acreline'), __NAMESPACE__.'\\agent_metabox', Catalog::AGENT, 'normal', 'high');
    }
    if (post_type_exists(Catalog::BOOKING)) {
        add_meta_box('ks_booking_details', __('Booking details', 'acreline'), __NAMESPACE__.'\\booking_metabox', Catalog::BOOKING, 'normal', 'high');
    }
    // Page and post copy is now managed via Gutenberg blocks.
    // Listing / booking / agent CPTs still use classic metaboxes below.
});

add_action('save_post_'.Catalog::LISTING, __NAMESPACE__.'\\save_listing_metabox');
add_action('save_post_'.Catalog::AGENT, __NAMESPACE__.'\\save_agent_metabox');
add_action('save_post_'.Catalog::BOOKING, __NAMESPACE__.'\\save_booking_metabox');

add_action('admin_enqueue_scripts', function (string $hook): void {
    if (! in_array($hook, ['post.php', 'post-new.php', 'appearance_page_acreline-settings'], true)) {
        return;
    }
    wp_enqueue_media();
    $path = get_theme_file_path('resources/js/admin-media.js');
    $uri = get_theme_file_uri('resources/js/admin-media.js');
    wp_enqueue_script(
        'keystone-admin-media',
        $uri,
        [],
        is_readable($path) ? (string) filemtime($path) : '1',
        true
    );
});

add_action('admin_head', function (): void {
    $screen = get_current_screen();
    if (! $screen || ! in_array($screen->base, ['post'], true)) {
        return;
    }
    echo '<style>
      .ks-meta-section{margin:16px 0 0;padding:10px 0 4px;border-top:2px solid #e0ddd6}
      .ks-meta-section h3{font-size:13px;text-transform:uppercase;letter-spacing:.06em;color:#555;margin:0 0 8px}
      .ks-media-field{display:flex;gap:12px;align-items:flex-start;flex-wrap:wrap;max-width:42rem}
      .ks-media-preview{width:120px;height:80px;object-fit:cover;border-radius:8px;border:1px solid #d6d0c6;background:#f5f4f1}
      .ks-media-actions{display:flex;flex-direction:column;gap:8px;min-width:16rem}
      .ks-media-url{width:100%}
      .ks-cols-2{display:grid;grid-template-columns:1fr 1fr;gap:0}
      @media(max-width:782px){.ks-cols-2{grid-template-columns:1fr}}
    </style>';
});

// ─── Listing metabox ────────────────────────────────────────────────────────

add_action('admin_enqueue_scripts', function (string $hook): void {
    if (! in_array($hook, ['post.php', 'post-new.php'], true)) {
        return;
    }
    wp_enqueue_media();
    $path = get_theme_file_path('resources/js/admin-media.js');
    $uri = get_theme_file_uri('resources/js/admin-media.js');
    wp_enqueue_script(
        'keystone-admin-media',
        $uri,
        [],
        is_readable($path) ? (string) filemtime($path) : '1',
        true
    );
});

add_action('admin_head', function (): void {
    $screen = get_current_screen();
    if (! $screen || ! in_array($screen->base, ['post'], true)) {
        return;
    }
    echo '<style>
      .ks-media-field{display:flex;gap:12px;align-items:flex-start;flex-wrap:wrap;max-width:42rem}
      .ks-media-preview{width:120px;height:80px;object-fit:cover;border-radius:8px;border:1px solid #d6d0c6;background:#f5f4f1}
      .ks-media-actions{display:flex;flex-direction:column;gap:8px;min-width:16rem}
      .ks-media-url{width:100%}
    </style>';
});

function listing_metabox(\WP_Post $post): void
{
    wp_nonce_field('ks_listing_meta', 'ks_listing_nonce');
    $type = (string) Catalog::getMeta($post->ID, 'type', 'home');
    $status = (string) Catalog::getMeta($post->ID, 'status', 'active');
    $agents = Catalog::agents();
    $agentId = (int) Catalog::getMeta($post->ID, 'listing_agent', 0);
    $openHouseType = (string) Catalog::getMeta($post->ID, 'open_house_type', '');
    $listingSource = (string) Catalog::getMeta($post->ID, 'listing_source', '');
    $condition = (string) Catalog::getMeta($post->ID, 'condition', '');
    $garageType = (string) Catalog::getMeta($post->ID, 'garage_type', '');
    $basement = (string) Catalog::getMeta($post->ID, 'basement', '');
    $view = (string) Catalog::getMeta($post->ID, 'view', '');
    $mineralRights = (string) Catalog::getMeta($post->ID, 'mineral_rights', '');
    $waterRights = (string) Catalog::getMeta($post->ID, 'water_rights', '');
    ?>
    <?php ks_section_heading(__('Core', 'acreline')); ?>
    <table class="form-table" role="presentation">
      <tr>
        <th><label for="ks_type"><?php esc_html_e('Property type', 'acreline'); ?></label></th>
        <td>
          <select name="ks_type" id="ks_type">
            <?php foreach (Catalog::LISTING_TYPES as $value => $label) { ?>
              <option value="<?php echo esc_attr($value); ?>" <?php selected($type, $value); ?>><?php echo esc_html($label); ?></option>
            <?php } ?>
          </select>
        </td>
      </tr>
      <tr>
        <th><label for="ks_status"><?php esc_html_e('Status', 'acreline'); ?></label></th>
        <td>
          <select name="ks_status" id="ks_status">
            <?php foreach (Catalog::LISTING_STATUSES as $value => $label) { ?>
              <option value="<?php echo esc_attr($value); ?>" <?php selected($status, $value); ?>><?php echo esc_html($label); ?></option>
            <?php } ?>
          </select>
        </td>
      </tr>
      <tr>
        <th><label for="ks_listing_agent"><?php esc_html_e('Listing agent', 'acreline'); ?></label></th>
        <td>
          <select name="ks_listing_agent" id="ks_listing_agent">
            <option value="0"><?php esc_html_e('Unassigned', 'acreline'); ?></option>
            <?php foreach ($agents as $agent) { ?>
              <option value="<?php echo (int) $agent['id']; ?>" <?php selected($agentId, $agent['id']); ?>><?php echo esc_html($agent['name']); ?></option>
            <?php } ?>
          </select>
        </td>
      </tr>
      <tr>
        <th><label for="ks_featured"><?php esc_html_e('Featured', 'acreline'); ?></label></th>
        <td>
          <label>
            <input type="checkbox" name="ks_featured" id="ks_featured" value="1" <?php checked(Catalog::isFeaturedFlag(Catalog::getMeta($post->ID, 'featured', '')), true); ?>>
            <?php esc_html_e('Show in the homepage spotlight', 'acreline'); ?>
          </label>
        </td>
      </tr>
      <?php render_meta_inputs($post->ID, ['address', 'city', 'state', 'zip', 'township'], Catalog::listingFields()); ?>
      <?php render_meta_inputs($post->ID, ['price', 'beds', 'baths', 'sqft', 'acres', 'year_built', 'mls_number', 'description'], Catalog::listingFields()); ?>
    </table>

    <?php ks_section_heading(__('Property details', 'acreline')); ?>
    <table class="form-table" role="presentation">
      <tr>
        <th><label for="ks_condition"><?php esc_html_e('Condition', 'acreline'); ?></label></th>
        <td><?php render_select('ks_condition', $condition, Catalog::PROPERTY_CONDITIONS); ?></td>
      </tr>
      <?php render_meta_inputs($post->ID, ['garage'], Catalog::listingFields()); ?>
      <tr>
        <th><label for="ks_garage_type"><?php esc_html_e('Garage type', 'acreline'); ?></label></th>
        <td><?php render_select('ks_garage_type', $garageType, Catalog::GARAGE_TYPES); ?></td>
      </tr>
      <tr>
        <th><label for="ks_basement"><?php esc_html_e('Basement', 'acreline'); ?></label></th>
        <td><?php render_select('ks_basement', $basement, Catalog::BASEMENT_TYPES); ?></td>
      </tr>
      <?php render_meta_inputs($post->ID, ['heating', 'cooling', 'school_district', 'zoning', 'flood_zone', 'lot_features'], Catalog::listingFields()); ?>
      <tr>
        <th><label for="ks_view"><?php esc_html_e('View', 'acreline'); ?></label></th>
        <td><?php render_select('ks_view', $view, Catalog::VIEWS); ?></td>
      </tr>
      <?php render_meta_inputs($post->ID, ['smart_home', 'green_features', 'historic_designation'], Catalog::listingFields()); ?>
    </table>

    <?php ks_section_heading(__('Utilities', 'acreline')); ?>
    <table class="form-table" role="presentation">
      <?php render_meta_inputs($post->ID, ['water', 'sewer'], Catalog::listingFields()); ?>
    </table>

    <?php ks_section_heading(__('HOA &amp; Finances', 'acreline')); ?>
    <table class="form-table" role="presentation">
      <?php render_meta_inputs($post->ID, ['property_tax', 'hoa', 'hoa_monthly', 'hoa_amenities'], Catalog::listingFields()); ?>
    </table>

    <?php ks_section_heading(__('Land &amp; Farm details', 'acreline')); ?>
    <table class="form-table" role="presentation">
      <?php render_meta_inputs($post->ID, ['tillable_acres', 'pasture_acres', 'crop_acres', 'outbuildings'], Catalog::listingFields()); ?>
      <tr>
        <th><label for="ks_mineral_rights"><?php esc_html_e('Mineral rights', 'acreline'); ?></label></th>
        <td><?php render_select('ks_mineral_rights', $mineralRights, Catalog::RIGHTS_OPTIONS); ?></td>
      </tr>
      <tr>
        <th><label for="ks_water_rights"><?php esc_html_e('Water rights', 'acreline'); ?></label></th>
        <td><?php render_select('ks_water_rights', $waterRights, Catalog::RIGHTS_OPTIONS); ?></td>
      </tr>
      <?php render_meta_inputs($post->ID, ['conservation_easement'], Catalog::listingFields()); ?>
    </table>

    <?php ks_section_heading(__('Media', 'acreline')); ?>
    <table class="form-table" role="presentation">
      <tr>
        <th><label for="ks_image"><?php esc_html_e('Listing photo', 'acreline'); ?></label></th>
        <td>
          <?php render_media_field('ks_image', (string) Catalog::getMeta($post->ID, 'image', ''), (int) get_post_thumbnail_id($post->ID), __('Select photo', 'acreline')); ?>
        </td>
      </tr>
      <tr>
        <th><label for="ks_floor_plan"><?php esc_html_e('Floor plan image', 'acreline'); ?></label></th>
        <td>
          <?php
            $fpMeta = (string) Catalog::getMeta($post->ID, 'floor_plan', '');
    $fpId = ctype_digit($fpMeta) ? (int) $fpMeta : 0;
    $fpUrl = $fpId > 0 ? ((string) (wp_get_attachment_image_url($fpId, 'medium') ?: '')) : $fpMeta;
    render_media_field('ks_floor_plan', $fpUrl, $fpId, __('Select floor plan', 'acreline'));
    ?>
        </td>
      </tr>
      <?php render_meta_inputs($post->ID, ['virtual_tour', 'video_tour'], Catalog::listingFields()); ?>
      <?php render_meta_inputs($post->ID, ['photo_grad', 'lat', 'lng'], Catalog::listingFields()); ?>
    </table>

    <?php ks_section_heading(__('Showing &amp; Market', 'acreline')); ?>
    <table class="form-table" role="presentation">
      <tr>
        <th><label for="ks_open_house_type"><?php esc_html_e('Open house type', 'acreline'); ?></label></th>
        <td><?php render_select('ks_open_house_type', $openHouseType, Catalog::OPEN_HOUSE_TYPES); ?></td>
      </tr>
      <?php render_meta_inputs($post->ID, ['open_house_date', 'open_house_time'], Catalog::listingFields()); ?>
      <?php render_meta_inputs($post->ID, ['days_on_market', 'commission', 'listing_office'], Catalog::listingFields()); ?>
      <tr>
        <th><label for="ks_listing_source"><?php esc_html_e('Listing source', 'acreline'); ?></label></th>
        <td><?php render_select('ks_listing_source', $listingSource, Catalog::LISTING_SOURCES); ?></td>
      </tr>
      <?php render_meta_inputs($post->ID, ['agent_notes'], Catalog::listingFields()); ?>
    </table>
    <?php
}

// ─── Agent metabox ──────────────────────────────────────────────────────────

function agent_metabox(\WP_Post $post): void
{
    wp_nonce_field('ks_agent_meta', 'ks_agent_nonce');
    $fields = Catalog::agentFields();
    ?>

    <?php ks_section_heading(__('Identity', 'acreline')); ?>
    <table class="form-table" role="presentation">
      <tr>
        <th><label for="ks_image"><?php esc_html_e('Photo', 'acreline'); ?></label></th>
        <td>
          <?php render_media_field('ks_image', (string) Catalog::getMeta($post->ID, 'image', ''), (int) get_post_thumbnail_id($post->ID), __('Select photo', 'acreline')); ?>
        </td>
      </tr>
      <?php render_meta_inputs($post->ID, ['job_title', 'team_name', 'office', 'office_phone', 'featured_badge'], $fields); ?>
      <tr>
        <th><label for="ks_featured"><?php esc_html_e('Featured', 'acreline'); ?></label></th>
        <td>
          <label>
            <input type="checkbox" name="ks_featured" id="ks_featured" value="1" <?php checked(Catalog::isFeaturedFlag(Catalog::getMeta($post->ID, 'featured', '')), true); ?>>
            <?php esc_html_e('Show as featured agent', 'acreline'); ?>
          </label>
        </td>
      </tr>
    </table>

    <?php ks_section_heading(__('Credentials', 'acreline')); ?>
    <table class="form-table" role="presentation">
      <?php render_meta_inputs($post->ID, ['license_number', 'license_state', 'mls_id', 'nrds_id', 'years_experience', 'designations', 'certifications', 'awards'], $fields); ?>
    </table>

    <?php ks_section_heading(__('Contact &amp; Online', 'acreline')); ?>
    <table class="form-table" role="presentation">
      <?php render_meta_inputs($post->ID, ['phone', 'mobile', 'email', 'website', 'calendly'], $fields); ?>
    </table>

    <?php ks_section_heading(__('Social media', 'acreline')); ?>
    <table class="form-table" role="presentation">
      <?php render_meta_inputs($post->ID, ['facebook', 'instagram', 'linkedin', 'twitter', 'youtube'], $fields); ?>
    </table>

    <?php ks_section_heading(__('Performance stats', 'acreline')); ?>
    <p class="description" style="margin:4px 0 12px 200px"><?php esc_html_e('Manually enter stats to display on the agent profile.', 'acreline'); ?></p>
    <table class="form-table" role="presentation">
      <?php render_meta_inputs($post->ID, ['homes_sold', 'avg_dom', 'list_to_sale_ratio', 'total_volume', 'client_reviews_count'], $fields); ?>
    </table>

    <?php ks_section_heading(__('Content', 'acreline')); ?>
    <table class="form-table" role="presentation">
      <?php render_meta_inputs($post->ID, ['bio', 'bio_video', 'specialties', 'service_areas', 'languages'], $fields); ?>
    </table>

    <?php ks_section_heading(__('Avatar', 'acreline')); ?>
    <table class="form-table" role="presentation">
      <?php render_meta_inputs($post->ID, ['initials', 'avatar_color'], $fields); ?>
    </table>
    <?php
}

// ─── Booking metabox ────────────────────────────────────────────────────────

function booking_metabox(\WP_Post $post): void
{
    wp_nonce_field('ks_booking_meta', 'ks_booking_nonce');
    $status = (string) Catalog::getMeta($post->ID, 'status', 'requested');
    $listingId = (int) Catalog::getMeta($post->ID, 'listing_id', 0);
    $agentId = (int) Catalog::getMeta($post->ID, 'agent_id', 0);
    $type = (string) Catalog::getMeta($post->ID, 'showing_type', 'in-person');
    $priority = (string) Catalog::getMeta($post->ID, 'priority', 'normal');
    $buyerType = (string) Catalog::getMeta($post->ID, 'buyer_type', '');
    $commPref = (string) Catalog::getMeta($post->ID, 'comm_preference', '');
    $source = (string) Catalog::getMeta($post->ID, 'source', '');
    $next = Catalog::nextBookingStatus($status);
    ?>
    <p>
      <?php esc_html_e('Pipeline:', 'acreline'); ?>
      <strong><?php echo esc_html(Catalog::BOOKING_STATUSES[$status] ?? $status); ?></strong>
      <?php if ($next) { ?>
        — <?php esc_html_e('Advance to', 'acreline'); ?>
        <strong><?php echo esc_html(Catalog::BOOKING_STATUSES[$next]); ?></strong>
        <?php esc_html_e('with the row action on the Bookings list, or set status below.', 'acreline'); ?>
      <?php } ?>
    </p>

    <?php ks_section_heading(__('Scheduling', 'acreline')); ?>
    <table class="form-table" role="presentation">
      <tr>
        <th><label for="ks_status"><?php esc_html_e('Status', 'acreline'); ?></label></th>
        <td>
          <select name="ks_status" id="ks_status">
            <?php foreach (Catalog::BOOKING_STATUSES as $value => $label) { ?>
              <option value="<?php echo esc_attr($value); ?>" <?php selected($status, $value); ?>><?php echo esc_html($label); ?></option>
            <?php } ?>
          </select>
        </td>
      </tr>
      <tr>
        <th><label for="ks_priority"><?php esc_html_e('Priority', 'acreline'); ?></label></th>
        <td><?php render_select('ks_priority', $priority, Catalog::BOOKING_PRIORITIES); ?></td>
      </tr>
      <tr>
        <th><label for="ks_listing_id"><?php esc_html_e('Listing', 'acreline'); ?></label></th>
        <td>
          <select name="ks_listing_id" id="ks_listing_id">
            <option value="0"><?php esc_html_e('Select listing…', 'acreline'); ?></option>
            <?php foreach (Catalog::listings() as $listing) { ?>
              <option value="<?php echo (int) $listing['id']; ?>" <?php selected($listingId, $listing['id']); ?>>
                <?php echo esc_html($listing['title'].' — '.Catalog::formatMoney((int) $listing['price'])); ?>
              </option>
            <?php } ?>
          </select>
        </td>
      </tr>
      <tr>
        <th><label for="ks_agent_id"><?php esc_html_e('Assigned agent', 'acreline'); ?></label></th>
        <td>
          <select name="ks_agent_id" id="ks_agent_id">
            <option value="0"><?php esc_html_e('Unassigned', 'acreline'); ?></option>
            <?php foreach (Catalog::agents() as $agent) { ?>
              <option value="<?php echo (int) $agent['id']; ?>" <?php selected($agentId, $agent['id']); ?>><?php echo esc_html($agent['name']); ?></option>
            <?php } ?>
          </select>
        </td>
      </tr>
      <tr>
        <th><label for="ks_showing_type"><?php esc_html_e('Showing type', 'acreline'); ?></label></th>
        <td>
          <select name="ks_showing_type" id="ks_showing_type">
            <?php foreach (Catalog::SHOWING_TYPES as $value => $label) { ?>
              <option value="<?php echo esc_attr($value); ?>" <?php selected($type, $value); ?>><?php echo esc_html($label); ?></option>
            <?php } ?>
          </select>
        </td>
      </tr>
      <?php render_meta_inputs($post->ID, ['showing_date', 'showing_time', 'attendees', 'follow_up_date'], Catalog::bookingFields()); ?>
    </table>

    <?php ks_section_heading(__('Client info', 'acreline')); ?>
    <table class="form-table" role="presentation">
      <?php render_meta_inputs($post->ID, ['client_name', 'client_email', 'client_phone'], Catalog::bookingFields()); ?>
      <tr>
        <th><?php esc_html_e('Form consent', 'acreline'); ?></th>
        <td>
          <?php
            $consented = (string) Catalog::getMeta($post->ID, 'consent', '');
    $consentAt = (string) Catalog::getMeta($post->ID, 'consent_at', '');
    if ($consented === '1') {
        echo esc_html($consentAt !== ''
            ? sprintf(__('Recorded %s (UTC)', 'acreline'), $consentAt)
            : __('Yes', 'acreline'));
    } else {
        echo esc_html__('Not recorded', 'acreline');
    }
    ?>
        </td>
      </tr>
      <tr>
        <th><label for="ks_buyer_type"><?php esc_html_e('Buyer type', 'acreline'); ?></label></th>
        <td><?php render_select('ks_buyer_type', $buyerType, Catalog::BUYER_TYPES); ?></td>
      </tr>
      <tr>
        <th><label for="ks_comm_preference"><?php esc_html_e('Preferred contact', 'acreline'); ?></label></th>
        <td><?php render_select('ks_comm_preference', $commPref, Catalog::COMM_PREFERENCES); ?></td>
      </tr>
      <tr>
        <th><label for="ks_source"><?php esc_html_e('Lead source', 'acreline'); ?></label></th>
        <td><?php render_select('ks_source', $source, Catalog::LEAD_SOURCES); ?></td>
      </tr>
    </table>

    <?php ks_section_heading(__('Notes', 'acreline')); ?>
    <table class="form-table" role="presentation">
      <?php render_meta_inputs($post->ID, ['notes', 'special_notes', 'showing_feedback'], Catalog::bookingFields()); ?>
    </table>
    <?php
}

// ─── Shared helpers ─────────────────────────────────────────────────────────

function ks_section_heading(string $title): void
{
    echo '<div class="ks-meta-section"><h3>'.esc_html($title).'</h3></div>';
}

/**
 * @param  array<string, string>  $options
 */
function render_select(string $id, string $current, array $options): void
{
    echo '<select name="'.esc_attr($id).'" id="'.esc_attr($id).'">';
    foreach ($options as $value => $label) {
        echo '<option value="'.esc_attr($value).'" '.selected($current, $value, false).'>'.esc_html($label).'</option>';
    }
    echo '</select>';
}

function render_media_field(string $name, string $url, int $attachmentId, string $buttonLabel): void
{
    $preview = $url;
    if ($preview === '' && $attachmentId > 0) {
        $fromId = wp_get_attachment_image_url($attachmentId, 'medium');
        $preview = is_string($fromId) ? $fromId : '';
        if ($url === '' && $preview !== '') {
            $full = wp_get_attachment_image_url($attachmentId, 'full');
            $url = is_string($full) ? $full : $preview;
        }
    }
    $has = $preview !== '';
    echo '<div class="ks-media-field">';
    echo '<img class="ks-media-preview" alt=""'.($has ? ' src="'.esc_url($preview).'"' : ' hidden').'>';
    echo '<div class="ks-media-actions">';
    echo '<input type="hidden" class="ks-media-id" name="'.esc_attr($name.'_id').'" value="'.($attachmentId > 0 ? (int) $attachmentId : '').'">';
    echo '<input class="ks-media-url large-text" type="url" name="'.esc_attr($name).'" id="'.esc_attr($name).'" value="'.esc_attr($url).'" placeholder="https://">';
    echo '<p>';
    echo '<button type="button" class="button ks-media-select" data-title="'.esc_attr($buttonLabel).'" data-button="'.esc_attr__('Use image', 'acreline').'">'.esc_html($buttonLabel).'</button> ';
    echo '<button type="button" class="button-link ks-media-remove"'.($has ? '' : ' hidden').'>'.esc_html__('Remove', 'acreline').'</button>';
    echo '</p>';
    echo '<p class="description">'.esc_html__('Upload or pick from the media library. A URL is kept for existing Unsplash / hotlinked photos.', 'acreline').'</p>';
    echo '</div></div>';
}

function save_image_field(int $postId, string $field, bool $syncThumbnail = true): void
{
    if (! isset($_POST['ks_'.$field]) && ! isset($_POST['ks_'.$field.'_id'])) {
        return;
    }
    $url = isset($_POST['ks_'.$field])
        ? esc_url_raw(trim((string) wp_unslash($_POST['ks_'.$field])))
        : '';
    $id = isset($_POST['ks_'.$field.'_id']) ? (int) $_POST['ks_'.$field.'_id'] : 0;
    Catalog::updateMeta($postId, $field, $url);
    if (! $syncThumbnail) {
        return;
    }
    if ($id > 0 && get_post_type($id) === 'attachment') {
        set_post_thumbnail($postId, $id);
    } elseif ($url === '') {
        delete_post_thumbnail($postId);
    }
}

/**
 * @param  list<string>  $fields
 * @param  array<string, string>  $labels
 */
function render_meta_inputs(int $postId, array $fields, array $labels): void
{
    /** @var array<string,true> */
    static $textareaFields = [
        'notes' => true, 'specialties' => true, 'service_areas' => true, 'photo_grad' => true,
        'virtual_tour' => true, 'description' => true, 'bio' => true, 'body' => true,
        'special_notes' => true, 'showing_feedback' => true, 'outbuildings' => true,
        'lot_features' => true, 'smart_home' => true, 'green_features' => true,
        'hoa_amenities' => true, 'agent_notes' => true, 'awards' => true,
        'certifications' => true, 'conservation_easement' => true,
    ];

    foreach ($fields as $field) {
        $value = Catalog::getMeta($postId, $field, '');
        $label = $labels[$field] ?? $field;
        $id = 'ks_'.$field;
        echo '<tr><th><label for="'.esc_attr($id).'">'.esc_html($label).'</label></th><td>';
        if (isset($textareaFields[$field])) {
            echo '<textarea class="large-text" rows="3" name="'.esc_attr($id).'" id="'.esc_attr($id).'">'.esc_textarea((string) $value).'</textarea>';
        } else {
            $type = str_contains($field, 'email') ? 'email'
                : (str_contains($field, 'date') ? 'date'
                : ((str_contains($field, 'url') || str_contains($field, 'website') || str_contains($field, 'facebook') || str_contains($field, 'instagram') || str_contains($field, 'linkedin') || str_contains($field, 'twitter') || str_contains($field, 'youtube') || str_contains($field, 'calendly') || str_contains($field, 'video') || str_contains($field, 'tour')) ? 'url'
                : 'text'));
            echo '<input class="regular-text" type="'.esc_attr($type).'" name="'.esc_attr($id).'" id="'.esc_attr($id).'" value="'.esc_attr((string) $value).'">';
        }
        echo '</td></tr>';
    }
}

// ─── Save handlers ───────────────────────────────────────────────────────────

function save_listing_metabox(int $postId): void
{
    if (! isset($_POST['ks_listing_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['ks_listing_nonce'])), 'ks_listing_meta')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (! current_user_can('edit_post', $postId)) {
        return;
    }

    /** @var array<string,true> */
    $textareas = [
        'photo_grad' => true, 'virtual_tour' => true, 'description' => true,
        'notes' => true, 'outbuildings' => true, 'lot_features' => true,
        'smart_home' => true, 'green_features' => true, 'hoa_amenities' => true,
        'agent_notes' => true, 'conservation_easement' => true, 'special_notes' => true,
    ];
    /** @var array<string,true> */
    $urls = ['virtual_tour' => true, 'video_tour' => true];

    foreach (array_keys(Catalog::listingFields()) as $field) {
        if ($field === 'featured') {
            Catalog::setFeaturedFlag($postId, ! empty($_POST['ks_featured']));

            continue;
        }
        if ($field === 'image') {
            save_image_field($postId, 'image');

            continue;
        }
        if ($field === 'floor_plan') {
            save_floor_plan_field($postId);

            continue;
        }
        if ($field === 'image') {
            save_image_field($postId, 'image');

            continue;
        }
        if (! isset($_POST['ks_'.$field])) {
            continue;
        }
        $raw = wp_unslash($_POST['ks_'.$field]);
        if (isset($urls[$field])) {
            $value = esc_url_raw((string) $raw);
        } elseif (isset($textareas[$field])) {
            $value = sanitize_textarea_field((string) $raw);
        } else {
            $value = sanitize_text_field((string) $raw);
        }
        Catalog::updateMeta($postId, $field, $value);
    }
}

/**
 * Save the floor plan media field (stores URL; media ID is used to resolve attachment).
 */
function save_floor_plan_field(int $postId): void
{
    $url = isset($_POST['ks_floor_plan']) ? esc_url_raw(trim((string) wp_unslash($_POST['ks_floor_plan']))) : '';
    $id = isset($_POST['ks_floor_plan_id']) ? (int) $_POST['ks_floor_plan_id'] : 0;
    if ($id > 0 && get_post_type($id) === 'attachment') {
        $full = wp_get_attachment_image_url($id, 'full');
        Catalog::updateMeta($postId, 'floor_plan', is_string($full) ? $full : $url);
    } else {
        Catalog::updateMeta($postId, 'floor_plan', $url);
    }
}

function save_agent_metabox(int $postId): void
{
    if (! isset($_POST['ks_agent_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['ks_agent_nonce'])), 'ks_agent_meta')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (! current_user_can('edit_post', $postId)) {
        return;
    }

    /** @var array<string,true> */
    $textareas = ['specialties' => true, 'service_areas' => true, 'bio' => true, 'awards' => true, 'certifications' => true];
    /** @var array<string,true> */
    $urls = ['website' => true, 'calendly' => true, 'facebook' => true, 'instagram' => true, 'linkedin' => true, 'twitter' => true, 'youtube' => true, 'bio_video' => true];

    foreach (array_keys(Catalog::agentFields()) as $field) {
        if ($field === 'featured') {
            Catalog::setFeaturedFlag($postId, ! empty($_POST['ks_featured']) && (string) $_POST['ks_featured'] === '1');

            continue;
        }
        if ($field === 'image') {
            save_image_field($postId, 'image');

            continue;
        }
        if (! isset($_POST['ks_'.$field])) {
            continue;
        }
        $raw = wp_unslash($_POST['ks_'.$field]);
        if (isset($urls[$field])) {
            $value = esc_url_raw((string) $raw);
        } elseif (isset($textareas[$field])) {
            $value = sanitize_textarea_field((string) $raw);
        } else {
            $value = sanitize_text_field((string) $raw);
        }
        Catalog::updateMeta($postId, $field, $value);
    }
}

function save_booking_metabox(int $postId): void
{
    if (! isset($_POST['ks_booking_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['ks_booking_nonce'])), 'ks_booking_meta')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (! current_user_can('edit_post', $postId)) {
        return;
    }

    /** @var array<string,true> */
    $textareas = ['notes' => true, 'special_notes' => true, 'showing_feedback' => true];

    foreach (array_keys(Catalog::bookingFields()) as $field) {
        if (! isset($_POST['ks_'.$field])) {
            continue;
        }
        $raw = wp_unslash($_POST['ks_'.$field]);
        $value = isset($textareas[$field])
            ? sanitize_textarea_field((string) $raw)
            : sanitize_text_field((string) $raw);
        Catalog::updateMeta($postId, $field, $value);
    }

    $listingId = (int) Catalog::getMeta($postId, 'listing_id', 0);
    $listing = $listingId ? Catalog::listing($listingId) : null;
    if ($listing) {
        Catalog::updateMeta($postId, 'listing_title', $listing['title']);
    }
}

// ─── Admin list columns ──────────────────────────────────────────────────────

add_filter('manage_'.Catalog::LISTING.'_posts_columns', function (array $columns) {
    $columns['ks_price'] = __('Price', 'acreline');
    $columns['ks_type'] = __('Type', 'acreline');
    $columns['ks_status'] = __('Status', 'acreline');
    $columns['ks_township'] = __('Township', 'acreline');

    return $columns;
});

add_action('manage_'.Catalog::LISTING.'_posts_custom_column', function (string $column, int $postId) {
    match ($column) {
        'ks_price' => print esc_html(Catalog::formatMoney((int) Catalog::getMeta($postId, 'price', 0))),
        'ks_type' => print esc_html(Catalog::LISTING_TYPES[(string) Catalog::getMeta($postId, 'type', 'home')] ?? ''),
        'ks_status' => print esc_html(Catalog::LISTING_STATUSES[(string) Catalog::getMeta($postId, 'status', 'active')] ?? ''),
        'ks_township' => print esc_html((string) Catalog::getMeta($postId, 'township', '')),
        default => null,
    };
}, 10, 2);

add_filter('manage_'.Catalog::AGENT.'_posts_columns', function (array $columns) {
    $columns['ks_title'] = __('Title', 'acreline');
    $columns['ks_phone'] = __('Phone', 'acreline');
    $columns['ks_license'] = __('License', 'acreline');

    return $columns;
});

add_action('manage_'.Catalog::AGENT.'_posts_custom_column', function (string $column, int $postId) {
    match ($column) {
        'ks_title' => print esc_html((string) Catalog::getMeta($postId, 'job_title', '')),
        'ks_phone' => print esc_html((string) Catalog::getMeta($postId, 'phone', '')),
        'ks_license' => print esc_html(trim(Catalog::getMeta($postId, 'license_state', '').' '.Catalog::getMeta($postId, 'license_number', ''))),
        default => null,
    };
}, 10, 2);

add_filter('manage_'.Catalog::BOOKING.'_posts_columns', function (array $columns) {
    unset($columns['date']);
    $columns['ks_when'] = __('When', 'acreline');
    $columns['ks_listing'] = __('Listing', 'acreline');
    $columns['ks_client'] = __('Client', 'acreline');
    $columns['ks_priority'] = __('Priority', 'acreline');
    $columns['ks_status'] = __('Status', 'acreline');

    return $columns;
});

add_action('manage_'.Catalog::BOOKING.'_posts_custom_column', function (string $column, int $postId) {
    $status = (string) Catalog::getMeta($postId, 'status', 'requested');
    $priority = (string) Catalog::getMeta($postId, 'priority', 'normal');
    match ($column) {
        'ks_when' => print esc_html(trim(Catalog::getMeta($postId, 'showing_date', '').' '.Catalog::getMeta($postId, 'showing_time', ''))),
        'ks_listing' => print esc_html((string) Catalog::getMeta($postId, 'listing_title', '')),
        'ks_client' => print esc_html(trim(Catalog::getMeta($postId, 'client_name', '').' · '.Catalog::getMeta($postId, 'client_phone', ''))),
        'ks_priority' => print esc_html(Catalog::BOOKING_PRIORITIES[$priority] ?? $priority),
        'ks_status' => print esc_html(Catalog::BOOKING_STATUSES[$status] ?? $status),
        default => null,
    };
}, 10, 2);

add_filter('post_row_actions', function (array $actions, \WP_Post $post) {
    if ($post->post_type !== Catalog::BOOKING) {
        return $actions;
    }
    $status = (string) Catalog::getMeta($post->ID, 'status', 'requested');
    $next = Catalog::nextBookingStatus($status);
    if (! $next || ! current_user_can('edit_post', $post->ID)) {
        return $actions;
    }
    $url = wp_nonce_url(
        admin_url('admin-post.php?action=keystone_advance_booking&post='.$post->ID),
        'ks_advance_'.$post->ID
    );
    $actions['ks_advance'] = '<a href="'.esc_url($url).'">'.esc_html(sprintf(
        /* translators: next booking status */
        __('Advance to %s', 'acreline'),
        Catalog::BOOKING_STATUSES[$next]
    )).'</a>';

    return $actions;
}, 10, 2);

add_action('admin_post_keystone_advance_booking', function () {
    $postId = (int) ($_GET['post'] ?? 0);
    if (! $postId || ! current_user_can('edit_post', $postId)) {
        wp_die(esc_html__('You cannot advance this booking.', 'acreline'));
    }
    check_admin_referer('ks_advance_'.$postId);
    $post = get_post($postId);
    if (! $post || $post->post_type !== Catalog::BOOKING) {
        wp_die(esc_html__('Booking not found.', 'acreline'));
    }
    $status = (string) Catalog::getMeta($postId, 'status', 'requested');
    $next = Catalog::nextBookingStatus($status);
    if ($next) {
        Catalog::updateMeta($postId, 'status', $next);
    }
    wp_safe_redirect(admin_url('edit.php?post_type='.Catalog::BOOKING.'&ks_advanced=1'));
    exit;
});

add_action('admin_notices', function () {
    $screen = get_current_screen();
    if (! $screen || $screen->post_type !== Catalog::BOOKING) {
        return;
    }
    if (isset($_GET['ks_advanced'])) {
        echo '<div class="notice notice-success is-dismissible"><p>'.esc_html__('Booking advanced to the next status.', 'acreline').'</p></div>';
    }
});

// ─── Page / post metaboxes (unchanged) ───────────────────────────────────────

function page_metabox(\WP_Post $post): void
{
    wp_nonce_field('ks_page_meta', 'ks_page_nonce');
    $key = PageCopy::schemaKeyForPost($post->ID);
    echo '<p>'.esc_html(sprintf(
        /* translators: page field group name */
        __('This page uses the %s template. Edit copy in the fields below — the block editor is disabled.', 'acreline'),
        $key
    )).'</p>';
    echo '<table class="form-table" role="presentation">';
    render_page_inputs($post->ID, PageCopy::schemaForPost($post->ID));
    echo '</table>';
}

/**
 * @param  array<string, array{label: string, type: string, default: string}>  $schema
 */
function render_page_inputs(int $postId, array $schema): void
{
    foreach ($schema as $field => $def) {
        $stored = Catalog::getMeta($postId, $field, '');
        $value = $stored !== '' ? $stored : html_entity_decode(str_replace('&amp;', '&', $def['default'] ?? ''), ENT_QUOTES);
        $id = 'ks_'.$field;
        echo '<tr><th><label for="'.esc_attr($id).'">'.esc_html($def['label']).'</label></th><td>';
        if (($def['type'] ?? 'text') === 'image') {
            $attachmentId = (int) get_post_thumbnail_id($postId);
            if ($attachmentId === 0 && $stored !== '') {
                $attachmentId = ctype_digit((string) $stored)
                    ? (int) $stored
                    : (int) attachment_url_to_postid((string) $stored);
            }
            render_media_field($id, (string) $stored, $attachmentId, __('Select image', 'acreline'));
        } elseif (($def['type'] ?? 'text') === 'textarea') {
            echo '<textarea class="large-text" rows="4" name="'.esc_attr($id).'" id="'.esc_attr($id).'">'.esc_textarea((string) $value).'</textarea>';
        } else {
            echo '<input class="large-text" type="text" name="'.esc_attr($id).'" id="'.esc_attr($id).'" value="'.esc_attr((string) $value).'">';
        }
        echo '</td></tr>';
    }
}

function save_page_metabox(int $postId): void
{
    if (! isset($_POST['ks_page_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['ks_page_nonce'])), 'ks_page_meta')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (! current_user_can('edit_post', $postId)) {
        return;
    }

    foreach (PageCopy::schemaForPost($postId) as $field => $def) {
        if (($def['type'] ?? 'text') === 'image') {
            save_image_field($postId, $field, $field === 'hero_image');

            continue;
        }
        if (! isset($_POST['ks_'.$field])) {
            continue;
        }
        $raw = wp_unslash($_POST['ks_'.$field]);
        $type = $def['type'] ?? 'text';
        $value = $type === 'textarea'
            ? wp_kses_post((string) $raw)
            : ($type === 'url'
                ? esc_url_raw((string) $raw)
                : wp_kses((string) $raw, [
                    'em' => [],
                    'strong' => [],
                    'br' => [],
                    'a' => [
                        'href' => true,
                        'title' => true,
                        'rel' => true,
                        'target' => true,
                    ],
                ]));
        Catalog::updateMeta($postId, $field, $value);
    }
}

function post_metabox(\WP_Post $post): void
{
    wp_nonce_field('ks_post_meta', 'ks_post_nonce');
    $body = (string) Catalog::getMeta($post->ID, 'body', $post->post_content);
    $thumbId = (int) get_post_thumbnail_id($post->ID);
    $thumbUrl = $thumbId ? (string) wp_get_attachment_image_url($thumbId, 'full') : '';
    echo '<p>'.esc_html__('Blog posts use fields only. Paste HTML in the body if you need links or lists.', 'acreline').'</p>';
    echo '<table class="form-table" role="presentation">';
    echo '<tr><th><label for="ks_image">'.esc_html__('Featured image', 'acreline').'</label></th><td>';
    render_media_field('ks_image', $thumbUrl, $thumbId, __('Select image', 'acreline'));
    echo '</td></tr>';
    echo '<tr><th><label for="ks_body">'.esc_html__('Article body', 'acreline').'</label></th>';
    echo '<td><textarea class="large-text" rows="16" name="ks_body" id="ks_body">'.esc_textarea($body).'</textarea></td></tr>';
    echo '</table>';
}

function save_post_metabox(int $postId): void
{
    if (! isset($_POST['ks_post_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['ks_post_nonce'])), 'ks_post_meta')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (! current_user_can('edit_post', $postId)) {
        return;
    }

    save_image_field($postId, 'image');

    $body = isset($_POST['ks_body']) ? wp_kses_post((string) wp_unslash($_POST['ks_body'])) : '';
    Catalog::updateMeta($postId, 'body', $body);

    remove_action('save_post_post', __NAMESPACE__.'\\save_post_metabox');
    wp_update_post([
        'ID' => $postId,
        'post_content' => $body,
    ]);
    add_action('save_post_post', __NAMESPACE__.'\\save_post_metabox');
}
