<?php

/**
 * Marketplace chrome: menus, Theme Check helpers.
 * Setup wizard lives in app/setup-wizard.php.
 */

namespace App;

use App\Support\Identity;

add_filter('nav_menu_css_class', function (array $classes, $item): array {
    if (in_array('current-menu-item', $classes, true) || in_array('current-menu-ancestor', $classes, true)) {
        $classes[] = 'is-active';
    }

    return $classes;
}, 10, 2);

add_filter('nav_menu_link_attributes', function (array $atts, $item): array {
    if (! empty($item->current) || ! empty($item->current_item_ancestor)) {
        $atts['aria-current'] = 'page';
        $atts['class'] = trim(($atts['class'] ?? '').' is-active');
    }

    return $atts;
}, 10, 2);

add_action('wp_head', function (): void {
    echo '<style id="keystone-identity">'.Identity::cssVariables().'</style>'."\n";
}, 19);
