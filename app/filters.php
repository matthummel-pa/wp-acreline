<?php

/**
 * Theme filters.
 */

namespace App;

use App\Support\Identity;

/**
 * Add "… Continued" to the excerpt.
 *
 * @return string
 */
add_filter('excerpt_more', function () {
    return sprintf(' &hellip; <a href="%s">%s</a>', get_permalink(), __('Continued', 'acreline'));
});

/**
 * Gate homepage hero motion CSS on body classes from Customizer theme_mods.
 *
 * @param  list<string>  $classes
 * @return list<string>
 */
add_filter('body_class', function (array $classes): array {
    if (Identity::heroKenBurns()) {
        $classes[] = 'ken-burns-enabled';
    }
    if (Identity::heroSearchTilt()) {
        $classes[] = 'hero-search-tilt-enabled';
    }

    return $classes;
});
