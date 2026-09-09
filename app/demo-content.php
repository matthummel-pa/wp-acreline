<?php

/**
 * Load demo pages, listings, agents, and posts on a fresh install.
 */

namespace App;

use App\Support\BlockMigration;
use App\Support\DemoContent;
use App\Support\DemoPages;

add_action('init', function () {
    if (defined('WP_CLI') && WP_CLI) {
        return;
    }
    DemoContent::maybeSeed();
}, 40);

add_action('after_switch_theme', function () {
    DemoContent::maybeSeed();
    flush_rewrite_rules();
});

add_action('template_redirect', [DemoPages::class, 'maybeServe'], 1);
add_filter('template_include', [DemoPages::class, 'templateFile'], 50);

add_action('admin_notices', function () {
    if (! current_user_can('manage_options') || DemoContent::isComplete()) {
        return;
    }
    $url = admin_url('tools.php?page=ks-seed-demo');
    echo '<div class="notice notice-warning is-dismissible"><p>';
    echo esc_html__('Acreline demo pages and listings are missing, so nav links 404 and the homepage has no inventory.', 'acreline');
    echo ' <a href="'.esc_url($url).'">'.esc_html__('Load demo content', 'acreline').'</a>';
    echo '</p></div>';
});

add_action('admin_menu', function () {
    add_management_page(
        __('Seed Acreline demo', 'acreline'),
        __('Seed Acreline demo', 'acreline'),
        'manage_options',
        'ks-seed-demo',
        __NAMESPACE__.'\\render_demo_seed_page'
    );
});

function render_demo_seed_page(): void
{
    if (! current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have permission to seed demo content.', 'acreline'));
    }

    $notice = null;
    if ('POST' === ($_SERVER['REQUEST_METHOD'] ?? '') && isset($_POST['ks_seed_nonce'])) {
        check_admin_referer('ks_seed_demo', 'ks_seed_nonce');
        DemoContent::seed();
        update_option(DemoContent::OPTION, '1');
        $notice = __('Demo pages, listings, agents, and blog posts were loaded. Theme files were not changed.', 'acreline');
    }

    echo '<div class="wrap">';
    echo '<h1>'.esc_html__('Seed Acreline demo', 'acreline').'</h1>';
    if ($notice) {
        printf('<div class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html($notice));
    }
    echo '<p style="max-width:70ch">'.esc_html__('Creates the marketing pages, eight sample listings, three agents, blog posts, and sets Home / Blog as the front page. Safe to run again — existing slugs are updated, not duplicated. Re-running also rebuilds each marketing page’s Gutenberg block stack so Agents, Listings, Guide, and Blog pick up the full sequences.', 'acreline').'</p>';
    echo '<form method="post">';
    wp_nonce_field('ks_seed_demo', 'ks_seed_nonce');
    printf('<p><button type="submit" class="button button-primary">%s</button></p>', esc_html__('Load demo content', 'acreline'));
    echo '</form>';
    echo '</div>';
}

if (defined('WP_CLI') && WP_CLI) {
    \WP_CLI::add_command('ks seed', function (): void {
        DemoContent::seed();
        update_option(DemoContent::OPTION, '1');
        \WP_CLI::success('Acreline demo content loaded.');
    });

    \WP_CLI::add_command('ks rebuild-blocks', function (): void {
        $result = BlockMigration::forceRebuildAll();
        foreach ($result['errors'] as $error) {
            \WP_CLI::warning($error);
        }
        \WP_CLI::success(sprintf('Rebuilt block stacks on %d marketing page(s).', $result['updated']));
    });
}
