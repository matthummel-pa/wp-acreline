<?php

namespace App\Support;

/**
 * One-time demo content for a fresh WordPress install of this theme.
 */
class DemoContent
{
    public const OPTION = 'ks_demo_content_v1';

    public const LOCK = 'ks_demo_content_lock';

    public static function isComplete(): bool
    {
        return self::findId('page', 'home') > 0
            && self::findId('page', 'listings') > 0
            && self::findId(Catalog::LISTING, 'wheatland-farmhouse') > 0;
    }

    public static function maybeSeed(): void
    {
        if (self::isComplete()) {
            if (! get_option(self::OPTION)) {
                update_option(self::OPTION, '1');
            }
            if (! get_option('ks_demo_heroes_v1')) {
                self::attachHeroImages();
                update_option('ks_demo_heroes_v1', '1');
            }

            return;
        }
        if (function_exists('wp_installing') && wp_installing()) {
            return;
        }

        $lock = get_option(self::LOCK);
        if ($lock && (time() - (int) $lock) < 90) {
            return;
        }
        update_option(self::LOCK, (string) time(), false);

        try {
            self::seed();
            update_option(self::OPTION, '1');
            update_option('ks_demo_heroes_v1', '1');
        } finally {
            delete_option(self::LOCK);
        }
    }

    public static function seed(): void
    {
        self::identity();
        self::permalinks();
        self::pages();
        self::posts();
        self::cpts();
        self::menus();
        self::attachHeroImages();
        self::buildPageBlocks();
        self::cleanupDefaults();
        flush_rewrite_rules(false);
    }

    public static function identity(): void
    {
        $name = (string) get_option('blogname');
        if ($name === '' || in_array($name, ['My WordPress', 'WordPress', 'Another WordPress Site'], true)) {
            update_option('blogname', 'Acreline');
        }

        $tagline = (string) get_option('blogdescription');
        if ($tagline === '' || $tagline === 'Just another WordPress site') {
            update_option('blogdescription', 'Farms · land · historic homes');
        }
    }

    public static function menus(): void
    {
        $primary = self::ensureMenu('Primary', [
            ['title' => 'Home', 'url' => home_url('/')],
            ['title' => 'Listings', 'url' => home_url('/listings/')],
            ['title' => 'Areas', 'url' => home_url('/areas/')],
            ['title' => 'Guide', 'url' => home_url('/guide/')],
            ['title' => 'Blog', 'url' => home_url('/blog/')],
            ['title' => 'Agents', 'url' => home_url('/agents/')],
            ['title' => 'Contact', 'url' => home_url('/contact/')],
        ]);
        $footer = self::ensureMenu('Footer', [
            ['title' => 'Listings', 'url' => home_url('/listings/')],
            ['title' => 'Areas', 'url' => home_url('/areas/')],
            ['title' => 'Book a showing', 'url' => home_url('/book/')],
            ['title' => 'Buyer guide', 'url' => home_url('/guide/')],
            ['title' => 'Agents', 'url' => home_url('/agents/')],
            ['title' => 'Contact', 'url' => home_url('/contact/')],
        ]);

        $locations = get_theme_mod('nav_menu_locations');
        if (! is_array($locations)) {
            $locations = [];
        }
        if ($primary > 0) {
            $locations['primary_navigation'] = $primary;
        }
        if ($footer > 0) {
            $locations['footer_navigation'] = $footer;
        }
        set_theme_mod('nav_menu_locations', $locations);
    }

    /**
     * @param  list<array{title: string, url: string}>  $items
     */
    private static function ensureMenu(string $name, array $items): int
    {
        $existing = wp_get_nav_menu_object($name);
        if ($existing instanceof \WP_Term) {
            return (int) $existing->term_id;
        }

        $id = (int) wp_create_nav_menu($name);
        if ($id <= 0) {
            return 0;
        }

        foreach ($items as $item) {
            wp_update_nav_menu_item($id, 0, [
                'menu-item-title' => $item['title'],
                'menu-item-url' => $item['url'],
                'menu-item-status' => 'publish',
                'menu-item-type' => 'custom',
            ]);
        }

        return $id;
    }

    public static function permalinks(): void
    {
        if ((string) get_option('permalink_structure') === '') {
            update_option('permalink_structure', '/%postname%/');
        }
    }

    public static function pages(): void
    {
        $pages = [
            ['Home', 'home', 'front-page.blade.php'],
            ['Listings', 'listings', 'page-listings.blade.php'],
            ['Areas', 'areas', 'page-areas.blade.php'],
            ['Guide', 'guide', 'page-guide.blade.php'],
            ['Agents', 'agents', 'page-agents.blade.php'],
            ['Contact', 'contact', 'page-contact.blade.php'],
            ['Book a showing', 'book', 'page-book.blade.php'],
            ['Blog', 'blog', 'home.blade.php'],
        ];

        $ids = [];
        foreach ($pages as [$title, $slug, $template]) {
            $id = self::upsertPost('page', $slug, $title);
            update_post_meta($id, '_wp_page_template', $template);
            self::seedPageCopy($id);
            $ids[$slug] = $id;
        }

        update_option('show_on_front', 'page');
        update_option('page_on_front', (string) $ids['home']);
        update_option('page_for_posts', (string) $ids['blog']);
    }

    public static function posts(): void
    {
        $seed = get_template_directory().'/resources/seed';
        $posts = [
            [
                'slug' => 'book-a-home-showing',
                'title' => 'How to book a home showing',
                'excerpt' => 'A step-by-step demo of the appointment flow buyers expect on modern listing sites.',
                'file' => $seed.'/book-a-home-showing.html',
                'category' => ['name' => 'Showings', 'slug' => 'showings'],
            ],
            [
                'slug' => 'first-time-buyer-checklist',
                'title' => 'First-time buyer checklist',
                'excerpt' => 'Scannable list covering pre-approval, tour questions, inspections and offer timing.',
                'file' => $seed.'/first-time-buyer-checklist.html',
                'category' => ['name' => 'Buyers', 'slug' => 'buyers'],
            ],
            [
                'slug' => 'land-vs-home-search',
                'title' => 'Land vs home search',
                'excerpt' => 'What to filter first when shoppers are comparing acreage parcels with turnkey houses.',
                'file' => $seed.'/land-vs-home-search.html',
                'category' => ['name' => 'Search', 'slug' => 'search'],
            ],
        ];

        foreach ($posts as $post) {
            $id = self::upsertPost('post', $post['slug'], $post['title'], self::extractPostBody($post['file']), $post['excerpt']);
            $term = self::ensureCategory($post['category']['name'], $post['category']['slug']);
            if ($term > 0) {
                wp_set_post_terms($id, [$term], 'category');
            }
        }
    }

    public static function cpts(): void
    {
        $themeDir = get_template_directory();
        $agents = json_decode((string) file_get_contents($themeDir.'/resources/seed/agents.json'), true) ?: [];
        $agentIds = [];
        foreach ($agents as $agent) {
            if (! is_array($agent)) {
                continue;
            }
            $meta = $agent;
            unset($meta['slug'], $meta['name']);
            $agentIds[(string) $agent['slug']] = self::upsertCpt(
                Catalog::AGENT,
                (string) $agent['slug'],
                (string) $agent['name'],
                $meta,
                (string) ($agent['bio'] ?? '')
            );
        }

        $listings = json_decode((string) file_get_contents($themeDir.'/resources/seed/listings.json'), true) ?: [];
        foreach ($listings as $item) {
            if (! is_array($item)) {
                continue;
            }
            $meta = $item;
            unset($meta['slug'], $meta['title'], $meta['agent']);
            $agentSlug = (string) ($item['agent'] ?? '');
            if ($agentSlug !== '' && isset($agentIds[$agentSlug])) {
                $meta['listing_agent'] = $agentIds[$agentSlug];
            }
            self::upsertCpt(
                Catalog::LISTING,
                (string) $item['slug'],
                (string) $item['title'],
                $meta,
                (string) ($item['description'] ?? '')
            );
        }

        if (self::findId(Catalog::BOOKING, 'sample-showing-requested') === 0 && $listings !== []) {
            $first = $listings[0];
            $listingId = self::findId(Catalog::LISTING, (string) $first['slug']);
            self::upsertCpt(Catalog::BOOKING, 'sample-showing-requested', 'Alex Buyer — sample showing', [
                'listing_id' => $listingId,
                'listing_title' => (string) ($first['title'] ?? 'Sample listing'),
                'showing_date' => gmdate('Y-m-d', time() + DAY_IN_SECONDS * 3),
                'showing_time' => '10:30 AM',
                'showing_type' => 'in-person',
                'client_name' => 'Alex Buyer',
                'client_email' => 'alex@acreline-concept.test',
                'client_phone' => '(555) 010-0199',
                'notes' => 'Seeded sample request so the Bookings pipeline is visible.',
                'status' => 'requested',
                'agent_id' => $agentIds['renee-musselman'] ?? 0,
            ]);
        }
    }

    public static function attachHeroImages(): void
    {
        $pages = [
            'listings' => 'listings',
            'areas' => 'areas',
            'guide' => 'guide',
            'agents' => 'agents',
            'contact' => 'contact',
            'book' => 'book',
            'blog' => 'blog',
        ];
        foreach ($pages as $slug => $key) {
            $id = self::findId('page', $slug);
            if ($id > 0) {
                self::ensureBundledHero($id, $key, true);
            }
        }

        $posts = [
            'book-a-home-showing' => 'post-showing',
            'first-time-buyer-checklist' => 'post-checklist',
            'land-vs-home-search' => 'post-land',
        ];
        foreach ($posts as $slug => $key) {
            $id = self::findId('post', $slug);
            if ($id > 0) {
                self::ensureBundledHero($id, $key, false);
            }
        }
    }

    private static function ensureBundledHero(int $postId, string $key, bool $setCopy): void
    {
        $thumbId = (int) get_post_thumbnail_id($postId);
        if ($thumbId > 0) {
            if ($setCopy && (string) Catalog::getMeta($postId, 'hero_image', '') === '') {
                Catalog::updateMeta($postId, 'hero_image', (string) $thumbId);
            }

            return;
        }

        $attachmentId = self::importBundledHero($key, $postId);
        if ($attachmentId <= 0) {
            return;
        }
        set_post_thumbnail($postId, $attachmentId);
        if ($setCopy) {
            Catalog::updateMeta($postId, 'hero_image', (string) $attachmentId);
        }
    }

    private static function importBundledHero(string $key, int $parentId = 0): int
    {
        $title = 'Hero · '.$key;
        $existing = get_posts([
            'post_type' => 'attachment',
            'title' => $title,
            'posts_per_page' => 1,
            'post_status' => 'inherit',
            'fields' => 'ids',
        ]);
        if ($existing) {
            return (int) $existing[0];
        }

        $path = '';
        foreach (['public/images/heroes/'.$key.'.jpg', 'resources/images/heroes/'.$key.'.jpg'] as $relative) {
            $candidate = get_theme_file_path($relative);
            if (is_readable($candidate)) {
                $path = $candidate;
                break;
            }
        }
        if ($path === '') {
            return 0;
        }

        if (! function_exists('media_handle_sideload')) {
            require_once ABSPATH.'wp-admin/includes/file.php';
            require_once ABSPATH.'wp-admin/includes/media.php';
            require_once ABSPATH.'wp-admin/includes/image.php';
        }

        $tmp = wp_tempnam($key.'.jpg');
        if (! $tmp || ! copy($path, $tmp)) {
            return 0;
        }

        $file = [
            'name' => $key.'.jpg',
            'tmp_name' => $tmp,
            'type' => 'image/jpeg',
            'error' => 0,
            'size' => (int) filesize($tmp),
        ];
        $id = media_handle_sideload($file, $parentId, $title);
        if (is_wp_error($id)) {
            @unlink($tmp);

            return 0;
        }

        wp_update_post([
            'ID' => (int) $id,
            'post_title' => $title,
        ]);
        update_post_meta((int) $id, '_wp_attachment_image_alt', HeroImage::bundledAlt($key));

        return (int) $id;
    }

    public static function cleanupDefaults(): void
    {
        foreach (['hello-world' => 'post', 'sample-page' => 'page'] as $slug => $type) {
            $id = self::findId($type, $slug);
            if ($id > 0) {
                wp_delete_post($id, true);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private static function upsertCpt(string $type, string $slug, string $title, array $meta, string $content = ''): int
    {
        $id = self::upsertPost($type, $slug, $title, $content);
        $featured = null;
        if (array_key_exists('featured', $meta)) {
            $featured = Catalog::isFeaturedFlag($meta['featured']);
            unset($meta['featured']);
        }
        foreach ($meta as $key => $value) {
            Catalog::updateMeta($id, (string) $key, $value);
        }
        if ($featured !== null) {
            Catalog::setFeaturedFlag($id, $featured);
        }

        return $id;
    }

    private static function upsertPost(string $type, string $slug, string $title, string $content = '', string $excerpt = ''): int
    {
        $id = self::findId($type, $slug);
        $payload = [
            'post_type' => $type,
            'post_status' => 'publish',
            'post_title' => $title,
            'post_name' => $slug,
            'post_excerpt' => $excerpt,
        ];

        if ($id > 0) {
            // Preserve existing block content so re-seeding doesn't wipe Gutenberg markup.
            $existing = get_post($id);
            $existingContent = $existing instanceof \WP_Post ? $existing->post_content : '';
            $preserveBlocks = $content === '' && str_contains($existingContent, '<!-- wp:');
            $payload['post_content'] = $preserveBlocks ? $existingContent : $content;
            $payload['ID'] = $id;
            wp_update_post($payload);
        } else {
            $payload['post_content'] = $content;
            $id = (int) wp_insert_post($payload, true);
            if ($id <= 0) {
                return 0;
            }
        }

        return $id;
    }

    private static function findId(string $type, string $slug): int
    {
        $found = get_posts([
            'post_type' => $type,
            'name' => $slug,
            'post_status' => 'any',
            'numberposts' => 1,
            'fields' => 'ids',
        ]);

        return $found ? (int) $found[0] : 0;
    }

    private static function ensureCategory(string $name, string $slug): int
    {
        $term = get_term_by('slug', $slug, 'category');
        if ($term && ! is_wp_error($term)) {
            return (int) $term->term_id;
        }
        $created = wp_insert_term($name, 'category', ['slug' => $slug]);
        if (is_wp_error($created)) {
            return 0;
        }

        return (int) $created['term_id'];
    }

    private static function extractPostBody(string $file): string
    {
        if (! is_readable($file)) {
            return '';
        }
        $html = (string) file_get_contents($file);
        if (preg_match('#<div class="post-body">(.*?)</div>#s', $html, $match)) {
            return trim($match[1]);
        }

        return $html;
    }

    private static function seedPageCopy(int $id): void
    {
        if ($id <= 0) {
            return;
        }

        foreach (PageCopy::schemaForPost($id) as $key => $def) {
            update_post_meta($id, Catalog::metaKey($key), $def['default'] ?? '');
        }
    }

    /**
     * Populate post_content with Gutenberg block markup for all seeded pages.
     * Runs after attachHeroImages() so the hero image URL resolves correctly.
     */
    public static function buildPageBlocks(): void
    {
        $slugs = ['home', 'listings', 'areas', 'guide', 'agents', 'contact', 'book', 'blog'];
        foreach ($slugs as $slug) {
            $id = self::findId('page', $slug);
            if ($id <= 0) {
                continue;
            }
            $post = get_post($id);
            if (! $post instanceof \WP_Post) {
                continue;
            }
            // Skip pages that already have block content (idempotent).
            if (str_contains($post->post_content, '<!-- wp:')) {
                continue;
            }
            BlockMigration::migrate($id);
            BlockMigration::markMigrated($id);
        }
    }
}
