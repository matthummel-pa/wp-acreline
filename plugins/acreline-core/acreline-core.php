<?php

/**
 * Plugin Name:       Acreline Core
 * Plugin URI:        https://github.com/matthummel-pa/wp-acreline
 * Description:       Listings, agents, and showing bookings for the Acreline theme. Keeps inventory in the database when you switch themes.
 * Version:           1.0.0
 * Requires at least: 6.6
 * Requires PHP:      8.1
 * Author:            Matt Hummel
 * Author URI:        https://matthummel.com/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       acreline-core
 */
defined('ABSPATH') || exit;

define('ACRELINE_CORE_VERSION', '1.0.0');
define('ACRELINE_CORE_FILE', __FILE__);
define('KEYSTONE_CORE_VERSION', ACRELINE_CORE_VERSION);
define('KEYSTONE_CORE_FILE', ACRELINE_CORE_FILE);

require_once __DIR__.'/includes/post-types.php';
require_once __DIR__.'/includes/bookings.php';

register_activation_hook(__FILE__, static function (): void {
    keystone_core_register_post_types();
    flush_rewrite_rules();
});

register_deactivation_hook(__FILE__, static function (): void {
    flush_rewrite_rules();
});
