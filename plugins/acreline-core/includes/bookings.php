<?php

use App\Support\Compliance;

defined('ABSPATH') || exit;

add_action('rest_api_init', static function (): void {
    register_rest_route('keystone/v1', '/bookings', [
        'methods' => 'POST',
        'permission_callback' => '__return_true',
        'callback' => 'keystone_core_create_booking_request',
        'args' => [
            'listing_id' => ['required' => true],
            'date' => ['required' => true],
            'time' => ['required' => true],
            'name' => ['required' => true],
            'email' => ['required' => true],
            'phone' => ['required' => true],
        ],
    ]);
});

/**
 * @return WP_REST_Response|WP_Error
 */
function keystone_core_create_booking_request(WP_REST_Request $request)
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $key = 'ks_book_'.md5((string) $ip);
    $hits = (int) get_transient($key);
    if ($hits >= 8) {
        return new WP_Error('ks_rate_limited', __('Too many showing requests. Try again later.', 'acreline-core'), ['status' => 429]);
    }
    set_transient($key, $hits + 1, HOUR_IN_SECONDS);

    $listingId = (int) $request->get_param('listing_id');
    $listing = get_post($listingId);
    if (! $listing instanceof WP_Post || $listing->post_type !== 'listing' || $listing->post_status !== 'publish') {
        return new WP_Error('ks_bad_listing', __('Choose a listing to tour.', 'acreline-core'), ['status' => 400]);
    }

    $date = sanitize_text_field((string) $request->get_param('date'));
    $time = sanitize_text_field((string) $request->get_param('time'));
    $type = sanitize_text_field((string) $request->get_param('type'));
    $name = sanitize_text_field((string) $request->get_param('name'));
    $email = sanitize_email((string) $request->get_param('email'));
    $phone = sanitize_text_field((string) $request->get_param('phone'));
    $notes = sanitize_textarea_field((string) $request->get_param('notes'));

    if ($date === '' || strtotime($date.' 23:59:59') < time()) {
        return new WP_Error('ks_bad_date', __('Pick a future date for the showing.', 'acreline-core'), ['status' => 400]);
    }
    if ($time === '' || $name === '' || $phone === '' || ! is_email($email)) {
        return new WP_Error('ks_bad_fields', __('Name, phone, email, date and time are required.', 'acreline-core'), ['status' => 400]);
    }

    $consentOn = function_exists('get_theme_mod') && (bool) get_theme_mod('ks_consent_enable', true);
    if (class_exists('\\App\\Support\\Compliance')) {
        $consentOn = Compliance::flag('ks_consent_enable');
    }
    $consented = $request->get_param('consent') === true
        || $request->get_param('consent') === 1
        || $request->get_param('consent') === '1'
        || $request->get_param('lead_consent') === '1';
    if ($consentOn && ! $consented) {
        return new WP_Error('ks_consent_required', __('Please confirm the contact consent checkbox.', 'acreline-core'), ['status' => 400]);
    }

    $allowed = ['in-person', 'preview', 'virtual'];
    if (! in_array($type, $allowed, true)) {
        $type = 'in-person';
    }

    $title = sprintf('%s — %s — %s %s', $name, get_the_title($listing), $date, $time);
    $bookingId = wp_insert_post([
        'post_type' => 'booking',
        'post_status' => 'publish',
        'post_title' => $title,
    ], true);

    if (is_wp_error($bookingId)) {
        return $bookingId;
    }

    $agentId = (int) get_post_meta($listingId, 'ks_listing_agent', true);
    update_post_meta($bookingId, 'ks_listing_id', $listingId);
    update_post_meta($bookingId, 'ks_listing_title', get_the_title($listing));
    update_post_meta($bookingId, 'ks_showing_date', $date);
    update_post_meta($bookingId, 'ks_showing_time', $time);
    update_post_meta($bookingId, 'ks_showing_type', $type);
    update_post_meta($bookingId, 'ks_client_name', $name);
    update_post_meta($bookingId, 'ks_client_email', $email);
    update_post_meta($bookingId, 'ks_client_phone', $phone);
    update_post_meta($bookingId, 'ks_notes', $notes);
    update_post_meta($bookingId, 'ks_status', 'requested');
    update_post_meta($bookingId, 'ks_agent_id', $agentId);
    if ($consented) {
        update_post_meta($bookingId, 'ks_consent', '1');
        update_post_meta($bookingId, 'ks_consent_at', gmdate('c'));
    }

    return new WP_REST_Response([
        'id' => $bookingId,
        'status' => 'requested',
        'message' => sprintf(
            /* translators: 1: client name, 2: listing title, 3: date, 4: time */
            __('Showing requested for %1$s at %2$s on %3$s · %4$s. The request is now in Bookings (Requested).', 'acreline-core'),
            $name,
            get_the_title($listing),
            $date,
            $time
        ),
    ], 201);
}
