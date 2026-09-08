<?php

/**
 * Theme setup.
 */

namespace App;

use App\Support\Seo;
use Illuminate\Support\Facades\Vite;

/**
 * Inject styles into the block editor.
 *
 * Loads the full frontend stylesheet (app.css) so blocks render with the
 * same design tokens, typography, and card chrome as the public site.
 * Editor-specific overrides (hero height cap, disabled form opacity, etc.)
 * are applied on top via editor.css.
 *
 * @return array
 */
add_filter('block_editor_settings_all', function ($settings) {
    // Full frontend design system — tokens, sections, cards, hero, etc.
    $appStyle = Vite::asset('resources/css/app.css');
    $settings['styles'][] = ['css' => "@import url('{$appStyle}')"];

    // Editor-only overrides (loaded after app.css so they win).
    $editorStyle = Vite::asset('resources/css/editor.css');
    $settings['styles'][] = ['css' => "@import url('{$editorStyle}')"];

    return $settings;
});

/**
 * Inject scripts into the block editor.
 *
 * @return void
 */
add_action('admin_head', function () {
    if (! get_current_screen()?->is_block_editor()) {
        return;
    }

    if (! Vite::isRunningHot()) {
        $dependencies = json_decode(Vite::content('editor.deps.json'));

        foreach ($dependencies as $dependency) {
            if (! wp_script_is($dependency)) {
                wp_enqueue_script($dependency);
            }
        }
    }
    echo Vite::withEntryPoints([
        'resources/js/editor.js',
    ])->toHtml();
});

/**
 * Use the generated theme.json file.
 *
 * @return string
 */
add_filter('theme_file_path', function ($path, $file) {
    return $file === 'theme.json'
        ? public_path('build/assets/theme.json')
        : $path;
}, 10, 2);

/**
 * Disable on-demand block asset loading.
 *
 * @link https://core.trac.wordpress.org/ticket/61965
 */
add_filter('should_load_separate_core_block_assets', '__return_false');

/**
 * Register the initial theme setup.
 *
 * @return void
 */
add_action('after_switch_theme', function (): void {
    if (get_template() !== 'acreline') {
        return;
    }

    $fresh = get_option('theme_mods_acreline');
    $brand = is_array($fresh) ? trim((string) ($fresh['ks_brand_name'] ?? '')) : '';
    if ($brand !== '') {
        return;
    }

    $legacy = get_option('theme_mods_keystone-homes');
    if (! is_array($legacy) || $legacy === []) {
        return;
    }

    update_option('theme_mods_acreline', array_merge($legacy, is_array($fresh) ? $fresh : []));
});

add_action('after_setup_theme', function () {
    global $content_width;
    if (! isset($content_width)) {
        $content_width = 1200;
    }

    load_theme_textdomain('acreline', get_template_directory().'/languages');

    /**
     * Disable full-site editing support.
     *
     * @link https://wptavern.com/gutenberg-10-5-embeds-pdfs-adds-verse-block-color-options-and-introduces-new-patterns
     */
    remove_theme_support('block-templates');

    /**
     * Register the navigation menus.
     *
     * @link https://developer.wordpress.org/reference/functions/register_nav_menus/
     */
    register_nav_menus([
        'primary_navigation' => __('Primary Navigation', 'acreline'),
        'footer_navigation' => __('Footer Navigation', 'acreline'),
    ]);

    add_theme_support('automatic-feed-links');

    add_theme_support('custom-logo', [
        'height' => 80,
        'width' => 240,
        'flex-height' => true,
        'flex-width' => true,
    ]);

    /**
     * Disable the default block patterns.
     *
     * @link https://developer.wordpress.org/block-editor/developers/themes/theme-support/#disabling-the-default-block-patterns
     */
    remove_theme_support('core-block-patterns');
    remove_theme_support('block-template-parts');
    remove_theme_support('widgets-block-editor');

    /**
     * Enable plugins to manage the document title.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
     */
    add_theme_support('title-tag');

    /**
     * Enable post thumbnail support.
     *
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support('post-thumbnails');

    /**
     * Enable responsive embed support.
     *
     * @link https://developer.wordpress.org/block-editor/how-to-guides/themes/theme-support/#responsive-embedded-content
     */
    add_theme_support('responsive-embeds');

    /**
     * Enable HTML5 markup support.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#html5
     */
    add_theme_support('html5', [
        'caption',
        'comment-form',
        'comment-list',
        'gallery',
        'search-form',
        'script',
        'style',
    ]);

    /**
     * Enable selective refresh for widgets in customizer.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#customize-selective-refresh-widgets
     */
    add_theme_support('customize-selective-refresh-widgets');
}, 20);

/**
 * Register the theme sidebars.
 *
 * @return void
 */
/**
 * Gutenberg is enabled for page and post.
 * CPTs (listing, booking, agent) keep classic metabox editing.
 */
add_filter('use_block_editor_for_post_type', function (bool $enabled, string $postType): bool {
    if (in_array($postType, ['listing', 'booking', 'agent'], true)) {
        return false;
    }

    return $enabled;
}, 100, 2);

add_filter('use_widgets_block_editor', '__return_false');
add_filter('should_load_remote_block_patterns', '__return_false');

add_action('init', function () {
    foreach (['listing', 'booking', 'agent'] as $type) {
        remove_post_type_support($type, 'editor');
        remove_post_type_support($type, 'trackbacks');
    }
    // Ensure page and post retain editor support for Gutenberg.
    add_post_type_support('page', 'editor');
    add_post_type_support('post', 'editor');
}, 100);

// Core block patterns are disabled; only Acreline patterns are registered via app/blocks.php.
add_action('init', function () {
    remove_theme_support('core-block-patterns');
}, 99);

add_action('widgets_init', function () {
    $config = [
        'before_widget' => '<section class="widget %1$s %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ];

    register_sidebar([
        'name' => __('Primary', 'acreline'),
        'id' => 'sidebar-primary',
    ] + $config);

    register_sidebar([
        'name' => __('Footer', 'acreline'),
        'id' => 'sidebar-footer',
    ] + $config);
});

Seo::boot();
