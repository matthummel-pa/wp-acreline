<?php

/**
 * Acreline custom post types: listings, bookings, agents.
 *
 * Store-correct home is the Acreline Core plugin. This file is the
 * concept-site fallback so the live demo keeps working without the plugin.
 */

namespace App;

use App\Support\Catalog;
use App\Support\Compliance;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

if (defined('KEYSTONE_CORE_VERSION') || (defined('KS_DISABLE_THEME_CPTS') && KS_DISABLE_THEME_CPTS)) {
    return;
}

add_action('init', function () {
    register_post_type(Catalog::LISTING, [
        'labels' => [
            'name' => __('Listings', 'acreline'),
            'singular_name' => __('Listing', 'acreline'),
            'add_new_item' => __('Add Listing', 'acreline'),
            'edit_item' => __('Edit Listing', 'acreline'),
            'search_items' => __('Search Listings', 'acreline'),
            'not_found' => __('No listings found.', 'acreline'),
        ],
        'public' => true,
        'has_archive' => false,
        'rewrite' => ['slug' => 'listing'],
        // Pretty URLs stay /listing/{slug}/. Do not register `listing` as a
        // public query var — /book/?listing=33 would 404 (looks up slug "33").
        'query_var' => false,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-admin-home',
        'menu_position' => 20,
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'],
        'capability_type' => 'post',
    ]);

    register_post_type(Catalog::BOOKING, [
        'labels' => [
            'name' => __('Bookings', 'acreline'),
            'singular_name' => __('Booking', 'acreline'),
            'add_new_item' => __('Add Booking', 'acreline'),
            'edit_item' => __('Edit Booking', 'acreline'),
            'search_items' => __('Search Bookings', 'acreline'),
            'not_found' => __('No bookings found.', 'acreline'),
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
        'has_archive' => false,
        'rewrite' => false,
        'menu_icon' => 'dashicons-calendar-alt',
        'menu_position' => 21,
        'supports' => ['title'],
        'capability_type' => 'post',
    ]);

    register_post_type(Catalog::AGENT, [
        'labels' => [
            'name' => __('Agents', 'acreline'),
            'singular_name' => __('Agent', 'acreline'),
            'add_new_item' => __('Add Agent', 'acreline'),
            'edit_item' => __('Edit Agent', 'acreline'),
            'search_items' => __('Search Agents', 'acreline'),
            'not_found' => __('No agents found.', 'acreline'),
        ],
        'public' => true,
        'has_archive' => false,
        'rewrite' => ['slug' => 'agent'],
        'query_var' => false,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-businessperson',
        'menu_position' => 22,
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'],
        'capability_type' => 'post',
    ]);
});

add_action('after_switch_theme', function () {
    flush_rewrite_rules();
});

/**
 * Booking links pass a numeric listing post ID as ?listing=33.
 * If `listing` is still a query var (old rewrite rules), WordPress treats
 * that as a CPT slug and 404s /book/. Drop numeric values only.
 */
add_filter('request', function (array $vars): array {
    foreach (['listing', 'agent'] as $var) {
        if (isset($vars[$var]) && ctype_digit((string) $vars[$var])) {
            unset($vars[$var]);
        }
    }

    return $vars;
});

add_action('rest_api_init', function () {
    register_rest_route('keystone/v1', '/bookings', [
        'methods' => 'POST',
        'permission_callback' => '__return_true',
        'callback' => __NAMESPACE__.'\\create_booking_request',
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
 * Create a showing booking from the public Book a showing form.
 */
function create_booking_request(WP_REST_Request $request): WP_REST_Response|WP_Error
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $key = 'ks_book_'.md5((string) $ip);
    $hits = (int) get_transient($key);
    if ($hits >= 8) {
        return new WP_Error('ks_rate_limited', __('Too many showing requests. Try again later.', 'acreline'), ['status' => 429]);
    }
    set_transient($key, $hits + 1, HOUR_IN_SECONDS);

    $listingId = (int) $request->get_param('listing_id');
    $listing = Catalog::listing($listingId);
    if (! $listing) {
        return new WP_Error('ks_bad_listing', __('Choose a sample listing to tour.', 'acreline'), ['status' => 400]);
    }

    $date = sanitize_text_field((string) $request->get_param('date'));
    $time = sanitize_text_field((string) $request->get_param('time'));
    $type = sanitize_text_field((string) $request->get_param('type'));
    $name = sanitize_text_field((string) $request->get_param('name'));
    $email = sanitize_email((string) $request->get_param('email'));
    $phone = sanitize_text_field((string) $request->get_param('phone'));
    $notes = sanitize_textarea_field((string) $request->get_param('notes'));

    if ($date === '' || strtotime($date.' 23:59:59') < time()) {
        return new WP_Error('ks_bad_date', __('Pick a future date for the showing.', 'acreline'), ['status' => 400]);
    }
    if ($time === '' || $name === '' || $phone === '' || ! is_email($email)) {
        return new WP_Error('ks_bad_fields', __('Name, phone, email, date and time are required.', 'acreline'), ['status' => 400]);
    }

    $consentOn = Compliance::flag('ks_consent_enable');
    $consented = $request->get_param('consent') === true
        || $request->get_param('consent') === 1
        || $request->get_param('consent') === '1'
        || $request->get_param('lead_consent') === '1';
    if ($consentOn && ! $consented) {
        return new WP_Error('ks_consent_required', __('Please confirm the contact consent checkbox.', 'acreline'), ['status' => 400]);
    }

    if (! array_key_exists($type, Catalog::SHOWING_TYPES)) {
        $type = 'in-person';
    }

    $title = sprintf('%s — %s — %s %s', $name, $listing['title'], $date, $time);
    $bookingId = wp_insert_post([
        'post_type' => Catalog::BOOKING,
        'post_status' => 'publish',
        'post_title' => $title,
    ], true);

    if (is_wp_error($bookingId)) {
        return $bookingId;
    }

    $agentId = (int) $listing['listing_agent'];
    Catalog::updateMeta($bookingId, 'listing_id', $listingId);
    Catalog::updateMeta($bookingId, 'listing_title', $listing['title']);
    Catalog::updateMeta($bookingId, 'showing_date', $date);
    Catalog::updateMeta($bookingId, 'showing_time', $time);
    Catalog::updateMeta($bookingId, 'showing_type', $type);
    Catalog::updateMeta($bookingId, 'client_name', $name);
    Catalog::updateMeta($bookingId, 'client_email', $email);
    Catalog::updateMeta($bookingId, 'client_phone', $phone);
    Catalog::updateMeta($bookingId, 'notes', $notes);
    Catalog::updateMeta($bookingId, 'status', 'requested');
    Catalog::updateMeta($bookingId, 'agent_id', $agentId);
    if ($consented) {
        Catalog::updateMeta($bookingId, 'consent', '1');
        Catalog::updateMeta($bookingId, 'consent_at', gmdate('c'));
    }

    return new WP_REST_Response([
        'id' => $bookingId,
        'status' => 'requested',
        'message' => sprintf(
            /* translators: 1: client name, 2: listing title, 3: date, 4: time */
            __('Showing requested for %1$s at %2$s on %3$s · %4$s. The request is now in Bookings (Requested).', 'acreline'),
            $name,
            $listing['title'],
            $date,
            $time
        ),
    ], 201);
}
