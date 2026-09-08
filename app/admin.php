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
    ?>
    <table class="form-table" role="presentation">
      <tr>
        <th><label for="ks_type"><?php esc_html_e('Type', 'acreline'); ?></label></th>
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
      <?php render_meta_inputs($post->ID, [
          'address', 'city', 'state', 'zip', 'township', 'price', 'beds', 'baths', 'sqft', 'acres',
          'year_built', 'mls_number', 'lat', 'lng', 'photo_grad', 'image', 'virtual_tour', 'property_tax', 'hoa',
          'description',
      ], Catalog::listingFields()); ?>
      <tr>
        <th><label for="ks_featured"><?php esc_html_e('Featured', 'acreline'); ?></label></th>
        <td>
          <label>
            <input type="checkbox" name="ks_featured" id="ks_featured" value="1" <?php checked(Catalog::isFeaturedFlag(Catalog::getMeta($post->ID, 'featured', '')), true); ?>>
            <?php esc_html_e('Show in the homepage spotlight', 'acreline'); ?>
          </label>
        </td>
      </tr>
    </table>
    <?php
}

function agent_metabox(\WP_Post $post): void
{
    wp_nonce_field('ks_agent_meta', 'ks_agent_nonce');
    ?>
    <p><?php esc_html_e('Standard fields used on realtor team pages — license, MLS/NRDS, contact, specialties, and social.', 'acreline'); ?></p>
    <table class="form-table" role="presentation">
      <?php render_meta_inputs($post->ID, array_keys(Catalog::agentFields()), Catalog::agentFields()); ?>
    </table>
    <?php
}

function booking_metabox(\WP_Post $post): void
{
    wp_nonce_field('ks_booking_meta', 'ks_booking_nonce');
    $status = (string) Catalog::getMeta($post->ID, 'status', 'requested');
    $listingId = (int) Catalog::getMeta($post->ID, 'listing_id', 0);
    $agentId = (int) Catalog::getMeta($post->ID, 'agent_id', 0);
    $type = (string) Catalog::getMeta($post->ID, 'showing_type', 'in-person');
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
      <?php render_meta_inputs($post->ID, [
          'showing_date', 'showing_time', 'client_name', 'client_email', 'client_phone', 'notes', 'listing_title',
      ], Catalog::bookingFields()); ?>
    </table>
    <?php
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
    foreach ($fields as $field) {
        $value = Catalog::getMeta($postId, $field, '');
        $label = $labels[$field] ?? $field;
        $id = 'ks_'.$field;
        $isLong = in_array($field, ['notes', 'specialties', 'service_areas', 'photo_grad', 'virtual_tour', 'description', 'bio', 'body'], true);
        echo '<tr><th><label for="'.esc_attr($id).'">'.esc_html($label).'</label></th><td>';
        if ($field === 'image') {
            render_media_field($id, (string) $value, (int) get_post_thumbnail_id($postId), __('Select photo', 'acreline'));
            echo '</td></tr>';

            continue;
        }
        if ($isLong) {
            echo '<textarea class="large-text" rows="3" name="'.esc_attr($id).'" id="'.esc_attr($id).'">'.esc_textarea((string) $value).'</textarea>';
        } else {
            $type = str_contains($field, 'email') ? 'email' : (str_contains($field, 'date') ? 'date' : 'text');
            echo '<input class="regular-text" type="'.esc_attr($type).'" name="'.esc_attr($id).'" id="'.esc_attr($id).'" value="'.esc_attr((string) $value).'">';
        }
        echo '</td></tr>';
    }
}

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

    foreach (array_keys(Catalog::listingFields()) as $field) {
        if ($field === 'featured') {
            Catalog::setFeaturedFlag($postId, ! empty($_POST['ks_featured']));

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
        $value = in_array($field, ['photo_grad', 'virtual_tour', 'description'], true)
            ? sanitize_textarea_field((string) $raw)
            : sanitize_text_field((string) $raw);
        Catalog::updateMeta($postId, $field, $value);
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
        $value = in_array($field, ['specialties', 'service_areas', 'bio'], true)
            ? sanitize_textarea_field((string) $raw)
            : sanitize_text_field((string) $raw);
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

    foreach (array_keys(Catalog::bookingFields()) as $field) {
        if (! isset($_POST['ks_'.$field])) {
            continue;
        }
        $raw = wp_unslash($_POST['ks_'.$field]);
        $value = $field === 'notes'
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
    $columns['ks_status'] = __('Status', 'acreline');

    return $columns;
});

add_action('manage_'.Catalog::BOOKING.'_posts_custom_column', function (string $column, int $postId) {
    $status = (string) Catalog::getMeta($postId, 'status', 'requested');
    match ($column) {
        'ks_when' => print esc_html(trim(Catalog::getMeta($postId, 'showing_date', '').' '.Catalog::getMeta($postId, 'showing_time', ''))),
        'ks_listing' => print esc_html((string) Catalog::getMeta($postId, 'listing_title', '')),
        'ks_client' => print esc_html(trim(Catalog::getMeta($postId, 'client_name', '').' · '.Catalog::getMeta($postId, 'client_phone', ''))),
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
