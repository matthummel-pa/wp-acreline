<?php

namespace App\Support;

/**
 * Migrates legacy ks_* post meta into Gutenberg block content.
 *
 * Usage (WP-CLI or admin action):
 *   BlockMigration::migrateAll();
 *   BlockMigration::migrate($postId);
 */
class BlockMigration
{
    /** Option key that records which posts have been migrated. */
    private const MIGRATED_KEY = 'ks_block_migration_v1';

    /**
     * Migrate all pages that have not been migrated yet.
     *
     * @return array{migrated: int, skipped: int, errors: list<string>}
     */
    public static function migrateAll(): array
    {
        $done = (array) get_option(self::MIGRATED_KEY, []);
        $results = ['migrated' => 0, 'skipped' => 0, 'errors' => []];

        $pages = get_posts([
            'post_type' => 'page',
            'post_status' => 'any',
            'posts_per_page' => -1,
            'fields' => 'ids',
        ]);

        foreach ($pages as $postId) {
            if (in_array((int) $postId, $done, true)) {
                $results['skipped']++;

                continue;
            }

            $migrated = self::migrate((int) $postId);
            if ($migrated) {
                $done[] = (int) $postId;
                $results['migrated']++;
            } else {
                $results['skipped']++;
            }
        }

        update_option(self::MIGRATED_KEY, $done, false);

        return $results;
    }

    /**
     * Migrate a single page: convert ks_* meta into block HTML in post_content.
     */
    public static function migrate(int $postId): bool
    {
        $post = get_post($postId);
        if (! $post || $post->post_type !== 'page') {
            return false;
        }

        // Skip if post_content already contains blocks.
        if (str_contains($post->post_content, '<!-- wp:')) {
            return false;
        }

        $schemaKey = PageCopy::schemaKeyForPost($postId);
        $schemas = PageCopy::schemas();
        $schema = $schemas[$schemaKey] ?? $schemas['simple'];

        // Read stored meta for this page.
        $meta = [];
        foreach (array_keys($schema) as $field) {
            $stored = get_post_meta($postId, Catalog::metaKey($field), true);
            $meta[$field] = is_string($stored) ? $stored : '';
        }

        $blocks = self::buildBlocksForTemplate($schemaKey, $meta, $postId);

        if (empty($blocks)) {
            return false;
        }

        $content = implode("\n", $blocks);

        wp_update_post([
            'ID' => $postId,
            'post_content' => $content,
        ]);

        return true;
    }

    /**
     * Mark a post as already migrated (e.g. after manual seeding with block content).
     */
    public static function markMigrated(int $postId): void
    {
        $done = (array) get_option(self::MIGRATED_KEY, []);
        $done[] = $postId;
        update_option(self::MIGRATED_KEY, array_unique($done), false);
    }

    /**
     * Reset the migration record so all pages will be processed again.
     */
    public static function resetMigrationRecord(): void
    {
        delete_option(self::MIGRATED_KEY);
    }

    /**
     * Build the serialized block HTML for a given page template + meta.
     *
     * @param  array<string, string>  $meta
     * @return list<string>
     */
    private static function buildBlocksForTemplate(string $schemaKey, array $meta, int $postId): array
    {
        $m = $meta;

        return match ($schemaKey) {
            'home' => self::homeBlocks($m, $postId),
            'listings' => self::listingsBlocks($m, $postId),
            'areas' => self::areasBlocks($m, $postId),
            'guide' => self::guideBlocks($m, $postId),
            'agents' => self::agentsBlocks($m, $postId),
            'contact' => self::contactBlocks($m, $postId),
            'book' => self::bookBlocks($m, $postId),
            'blog' => self::blogBlocks($m, $postId),
            default => self::simpleBlocks($m, $postId),
        };
    }

    // ---------------------------------------------------------------------------
    // Per-template block sequences
    // ---------------------------------------------------------------------------

    /** @param array<string, string> $m */
    private static function homeBlocks(array $m, int $postId): array
    {
        $heroAttrs = [
            'eyebrow' => $m['hero_eyebrow'] ?? 'Farms, land, and historic homes',
            'title' => $m['hero_title'] ?? 'Homes worth <em>walking through.</em>',
            'text' => $m['hero_text'] ?? '',
            'imageUrl' => self::resolveHeroImage($postId),
            'primaryLabel' => $m['hero_cta_primary'] ?? 'Show matches',
            'secondaryLabel' => $m['hero_cta_secondary'] ?? 'Browse all listings',
        ];

        $intentAttrs = [
            'eyebrow' => $m['intent_eyebrow'] ?? 'Start here',
            'title' => $m['intent_title'] ?? 'Pick the path',
            'text' => $m['intent_text'] ?? '',
            'buyKicker' => $m['intent_buy_kicker'] ?? 'Buy',
            'buyTitle' => $m['intent_buy_title'] ?? '',
            'buyLead' => $m['intent_buy_lead'] ?? $m['intent_buy'] ?? '',
            'buyCta' => $m['intent_buy_cta'] ?? 'Browse listings',
            'sellKicker' => $m['intent_sell_kicker'] ?? 'Sell',
            'sellTitle' => $m['intent_sell_title'] ?? '',
            'sellLead' => $m['intent_sell_lead'] ?? $m['intent_sell'] ?? '',
            'sellCta' => $m['intent_sell_cta'] ?? 'Estimate value',
            'tourKicker' => $m['intent_tour_kicker'] ?? 'Tour',
            'tourTitle' => $m['intent_tour_title'] ?? '',
            'tourLead' => $m['intent_tour_lead'] ?? $m['intent_tour'] ?? '',
            'tourCta' => $m['intent_tour_cta'] ?? 'Book a showing',
            'notesLabel' => $m['intent_notes_label'] ?? 'Good to know',
            'note1Title' => $m['intent_note_1_title'] ?? 'Township first',
            'note1Text' => $m['intent_note_1_text'] ?? '',
            'note2Title' => $m['intent_note_2_title'] ?? 'Well and perc',
            'note2Text' => $m['intent_note_2_text'] ?? '',
            'note3Title' => $m['intent_note_3_title'] ?? 'Boots for showings',
            'note3Text' => $m['intent_note_3_text'] ?? '',
        ];

        $spotlightAttrs = [
            'eyebrow' => $m['spotlight_eyebrow'] ?? 'Spotlight',
            'title' => $m['spotlight_title'] ?? 'Three sample homes to scan',
            'text' => $m['spotlight_text'] ?? '',
        ];

        $bookAttrs = [
            'eyebrow' => $m['book_eyebrow'] ?? 'Appointments',
            'title' => $m['book_title'] ?? 'Book a house showing',
            'text' => $m['book_text'] ?? '',
        ];

        return [
            self::block('acreline/home-hero', $heroAttrs),
            self::block('acreline/intent-cards', $intentAttrs),
            self::block('acreline/spotlight', $spotlightAttrs),
            self::block('acreline/how-it-works', []),
            self::block('acreline/booking-section', $bookAttrs),
            self::block('acreline/market-stats', []),
            self::block('acreline/agent-tools', []),
            self::block('acreline/seo-content', []),
            self::block('acreline/faq-list', []),
            self::block('acreline/reviews', []),
            self::block('acreline/cta-band', []),
        ];
    }

    /** @param array<string, string> $m */
    private static function listingsBlocks(array $m, int $postId): array
    {
        $heroAttrs = [
            'eyebrow' => $m['hero_eyebrow'] ?? 'Sample inventory',
            'title' => $m['hero_title'] ?? 'Sample homes &amp; land <em>for demo tours</em>',
            'text' => $m['hero_text'] ?? '',
            'imageUrl' => self::resolveHeroImage($postId),
            'primaryLabel' => 'Book a showing',
            'secondaryLabel' => 'Buyer tools',
            'secondaryUrl' => home_url('/guide'),
        ];

        $ctaAttrs = [
            'title' => $m['cta_title'] ?? 'See a property you like?',
            'text' => $m['cta_text'] ?? '',
            'primaryLabel' => $m['cta_primary'] ?? 'Book a showing',
            'secondaryLabel' => $m['cta_secondary'] ?? 'Financing tools',
        ];

        return [
            self::block('acreline/page-hero', $heroAttrs),
            self::block('acreline/listing-grid', [
                'introTitle' => $m['intro_title'] ?? 'Buying rural property',
                'introText' => $m['intro_text'] ?? '',
            ]),
            self::block('acreline/cta-band', $ctaAttrs),
        ];
    }

    /** @param array<string, string> $m */
    private static function areasBlocks(array $m, int $postId): array
    {
        $heroAttrs = [
            'eyebrow' => $m['hero_eyebrow'] ?? 'Sample markets',
            'title' => $m['hero_title'] ?? 'Areas we <em>demo</em>',
            'text' => $m['hero_text'] ?? '',
            'imageUrl' => self::resolveHeroImage($postId),
            'primaryLabel' => 'Browse listings',
            'primaryUrl' => home_url('/listings'),
            'secondaryLabel' => 'Book a showing',
        ];

        $introAttrs = [
            'eyebrow' => 'The sample market',
            'title' => $m['intro_title'] ?? 'Land, farms &amp; homesteads in a sample market',
            'text' => $m['intro_text'] ?? '',
        ];

        $gridAttrs = [
            'gridEyebrow' => $m['grid_eyebrow'] ?? 'Area by area',
            'gridTitle' => $m['grid_title'] ?? 'Where the sample office works',
            'gridText' => $m['grid_text'] ?? '',
        ];

        for ($i = 1; $i <= 6; $i++) {
            $gridAttrs["area{$i}Meta"] = $m["area_{$i}_meta"] ?? '';
            $gridAttrs["area{$i}Title"] = $m["area_{$i}_title"] ?? '';
            $gridAttrs["area{$i}Body"] = $m["area_{$i}_body"] ?? '';
        }

        $ctaAttrs = [
            'title' => $m['cta_title'] ?? 'Walk an area with us',
            'text' => $m['cta_text'] ?? '',
            'primaryLabel' => $m['cta_primary'] ?? 'Book a showing',
        ];

        return [
            self::block('acreline/page-hero', $heroAttrs),
            self::block('acreline/intro-section', $introAttrs),
            self::block('acreline/area-grid', $gridAttrs),
            self::block('acreline/cta-band', $ctaAttrs),
        ];
    }

    /** @param array<string, string> $m */
    private static function guideBlocks(array $m, int $postId): array
    {
        $heroAttrs = [
            'eyebrow' => $m['hero_eyebrow'] ?? 'Buyer tools',
            'title' => $m['hero_title'] ?? 'A clearer path to <em>buying land or a home</em>',
            'text' => $m['hero_text'] ?? '',
            'imageUrl' => self::resolveHeroImage($postId),
            'primaryLabel' => 'Book a showing',
            'secondaryLabel' => 'Browse listings',
        ];

        $toolsAttrs = [
            'introTitle' => $m['intro_title'] ?? "What's different about buying land",
            'introText' => $m['intro_text'] ?? '',
            'eyebrow' => $m['tools_eyebrow'] ?? 'Run Your Numbers',
            'title' => $m['tools_title'] ?? 'Land-loan &amp; pre-qualification tools',
            'text' => $m['tools_text'] ?? '',
        ];

        $ctaAttrs = [
            'title' => $m['cta_title'] ?? 'Ready to walk a sample parcel?',
            'text' => $m['cta_text'] ?? '',
            'primaryLabel' => $m['cta_primary'] ?? 'Book a showing',
            'secondaryLabel' => $m['cta_secondary'] ?? 'Browse listings',
        ];

        return [
            self::block('acreline/page-hero', $heroAttrs),
            self::block('acreline/tools-section', $toolsAttrs),
            self::block('acreline/faq-list', ['title' => 'Common questions', 'headClass' => 'left']),
            self::block('acreline/cta-band', $ctaAttrs),
        ];
    }

    /** @param array<string, string> $m */
    private static function agentsBlocks(array $m, int $postId): array
    {
        $heroAttrs = [
            'eyebrow' => $m['hero_eyebrow'] ?? 'Sample team',
            'title' => $m['hero_title'] ?? 'Agents who know the <em>demo ground</em>',
            'text' => $m['hero_text'] ?? '',
            'imageUrl' => self::resolveHeroImage($postId),
        ];

        $introAttrs = [
            'title' => $m['intro_title'] ?? 'A small, local team by design',
            'text' => $m['intro_text'] ?? '',
        ];

        $howAttrs = [
            'eyebrow' => $m['how_eyebrow'] ?? 'How We Work',
            'title' => $m['how_title'] ?? 'What working with this office looks like',
            'text' => $m['how_text'] ?? '',
        ];

        $ctaAttrs = [
            'title' => $m['cta_title'] ?? 'Talk to a sample agent',
            'text' => $m['cta_text'] ?? '',
            'primaryLabel' => $m['cta_primary'] ?? 'Book a showing',
        ];

        return [
            self::block('acreline/page-hero', $heroAttrs),
            self::block('acreline/intro-section', $introAttrs),
            self::block('acreline/how-we-work', $howAttrs),
            self::block('acreline/cta-band', $ctaAttrs),
        ];
    }

    /** @param array<string, string> $m */
    private static function contactBlocks(array $m, int $postId): array
    {
        $heroAttrs = [
            'eyebrow' => $m['hero_eyebrow'] ?? 'Concept office',
            'title' => $m['hero_title'] ?? 'Get in touch <em>(demo only)</em>',
            'text' => $m['hero_text'] ?? '',
            'imageUrl' => self::resolveHeroImage($postId),
            'primaryLabel' => 'Book a showing',
            'secondaryLabel' => 'Call the office',
        ];

        $formAttrs = [
            'formTitle' => $m['form_title'] ?? 'Send us a message',
            'formText' => $m['form_text'] ?? "Tell us what you're looking for and we'll be in touch.",
        ];

        return [
            self::block('acreline/page-hero', $heroAttrs),
            self::block('acreline/contact-form', $formAttrs),
            self::block('acreline/cta-band', ['title' => 'Prefer a walk-through?']),
        ];
    }

    /** @param array<string, string> $m */
    private static function bookBlocks(array $m, int $postId): array
    {
        $heroAttrs = [
            'eyebrow' => $m['hero_eyebrow'] ?? 'Appointments',
            'title' => $m['hero_title'] ?? 'Book a house showing',
            'text' => $m['hero_text'] ?? '',
            'imageUrl' => self::resolveHeroImage($postId),
        ];

        return [
            self::block('acreline/page-hero', $heroAttrs),
            self::block('acreline/book-note', [
                'note' => $m['book_note'] ?? 'Demo only — no emails, texts or calendar invites are sent.',
            ]),
            self::block('acreline/faq-list', []),
        ];
    }

    /** @param array<string, string> $m */
    private static function blogBlocks(array $m, int $postId): array
    {
        $heroAttrs = [
            'eyebrow' => $m['hero_eyebrow'] ?? 'Guide',
            'title' => $m['hero_title'] ?? 'Realtor notes you can publish',
            'text' => $m['hero_text'] ?? '',
            'imageUrl' => self::resolveHeroImage($postId),
        ];

        return [
            self::block('acreline/page-hero', $heroAttrs),
            self::block('acreline/cta-band', [
                'title' => $m['cta_title'] ?? 'Ready to tour a sample home?',
                'text' => $m['cta_text'] ?? '',
                'primaryLabel' => $m['cta_primary'] ?? 'Book a showing',
                'secondaryLabel' => $m['cta_secondary'] ?? 'Browse samples',
            ]),
        ];
    }

    /** @param array<string, string> $m */
    private static function simpleBlocks(array $m, int $postId): array
    {
        $heroAttrs = [
            'eyebrow' => $m['hero_eyebrow'] ?? '',
            'title' => $m['hero_title'] ?? '',
            'text' => $m['hero_text'] ?? '',
            'imageUrl' => self::resolveHeroImage($postId),
        ];

        return [
            self::block('acreline/page-hero', $heroAttrs),
        ];
    }

    // ---------------------------------------------------------------------------
    // Serialization helpers
    // ---------------------------------------------------------------------------

    /**
     * Serialize a block as WordPress block comment markup.
     *
     * @param  array<string, mixed>  $attrs
     */
    private static function block(string $name, array $attrs): string
    {
        $filtered = array_filter($attrs, fn ($v) => $v !== '' && $v !== null);

        if (empty($filtered)) {
            return "<!-- wp:{$name} /-->";
        }

        $json = wp_json_encode($filtered, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return "<!-- wp:{$name} {$json} /-->";
    }

    /**
     * Resolve the hero image URL from post thumbnail or ks_hero_image meta.
     * The meta may store either an attachment ID (numeric string) or a full URL.
     */
    private static function resolveHeroImage(int $postId): string
    {
        $stored = get_post_meta($postId, Catalog::metaKey('hero_image'), true);
        if (is_string($stored) && $stored !== '') {
            if (ctype_digit($stored)) {
                $url = wp_get_attachment_image_url((int) $stored, 'full');

                return is_string($url) ? $url : '';
            }

            return $stored;
        }

        $thumbId = get_post_thumbnail_id($postId);
        if ($thumbId) {
            $url = wp_get_attachment_image_url((int) $thumbId, 'full');

            return is_string($url) ? $url : '';
        }

        return '';
    }
}
