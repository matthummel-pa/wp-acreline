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
     * Force-rebuild block content for all known marketing pages, overwriting
     * any existing block content. Safe to re-run; used after copy updates.
     *
     * @return array{updated: int, errors: list<string>}
     */
    public static function forceRebuildAll(): array
    {
        $slugs = ['home', 'listings', 'areas', 'guide', 'agents', 'contact', 'book', 'blog'];
        $results = ['updated' => 0, 'errors' => []];

        foreach ($slugs as $slug) {
            $post = get_page_by_path($slug);
            if (! $post) {
                $results['errors'][] = "Page not found: {$slug}";

                continue;
            }

            $schemaKey = PageCopy::schemaKeyForPost($post->ID);
            $schemas = PageCopy::schemas();
            $schema = $schemas[$schemaKey] ?? $schemas['simple'];

            $meta = [];
            foreach (array_keys($schema) as $field) {
                $stored = get_post_meta($post->ID, Catalog::metaKey($field), true);
                $meta[$field] = is_string($stored) ? $stored : '';
            }

            $blocks = self::buildBlocksForTemplate($schemaKey, $meta, $post->ID);
            if (empty($blocks)) {
                $results['errors'][] = "No blocks built for: {$slug}";

                continue;
            }

            wp_update_post([
                'ID' => $post->ID,
                'post_content' => implode("\n", $blocks),
            ]);

            $results['updated']++;
        }

        return $results;
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
            'eyebrow' => $m['hero_eyebrow'] ?? 'Homes, neighborhoods, and local agents',
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
            'note1Title' => $m['intent_note_1_title'] ?? 'Neighborhood first',
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
            self::block('acreline/region-coverage', [
                'eyebrow' => 'Area service',
                'title' => 'How we cover the region',
                'text' => 'Neighborhood-first coverage across North Ridge, Mill Creek, and Oak Hollow — one sample office, local specialists, and a showing on the calendar.',
                'layout' => 'split',
                'bandStyle' => 'alt',
                'maxAreas' => 3,
            ]),
            self::block('acreline/how-it-works', []),
            self::block('acreline/booking-section', $bookAttrs),
            self::block('acreline/market-stats', []),
            self::block('acreline/agent-tools', []),
            self::block('acreline/seo-content', []),
            self::block('acreline/pricing-plans', []),
            self::block('acreline/faq-list', []),
            self::block('acreline/reviews', []),
            self::block('acreline/logo-strip', []),
            self::block('acreline/newsletter', [
                'layout' => 'digest',
                'bandStyle' => 'accent',
                'formStyle' => 'card',
                'eyebrow' => 'New listings',
                'title' => 'Get the weekly sample market note',
                'text' => 'A short digest of new addresses in North Ridge, Mill Creek, and Oak Hollow.',
                'cadence' => 'Weekly · sample county',
                'formEyebrow' => 'Weekly digest',
                'formTitle' => 'Save a concept email',
                'buttonLabel' => 'Join the list',
                'note' => 'Concept capture — confirmation stays on this page. Nothing is emailed.',
                'highlightsLabel' => 'Covered this week',
                'highlight1' => 'North Ridge',
                'highlight2' => 'Mill Creek',
                'highlight3' => 'Oak Hollow',
                'teasersLabel' => "This week's addresses",
                'showTeasers' => true,
                'showBenefits' => true,
            ]),
            self::block('acreline/cta-band', []),
        ];
    }

    /** @param array<string, string> $m */
    private static function listingsBlocks(array $m, int $postId): array
    {
        $heroAttrs = [
            'eyebrow' => $m['hero_eyebrow'] ?? 'Sample inventory',
            'title' => $m['hero_title'] ?? 'Homes &amp; commercial — <em>browse the sample inventory</em>',
            'text' => $m['hero_text'] ?? 'Filter by area, price, and property type. Every listing below is a concept record — use the tools, save favorites, and compare side by side before you reach out.',
            'imageUrl' => self::resolveHeroImage($postId),
            'primaryLabel' => 'Book a showing',
            'secondaryLabel' => 'Buyer guide',
            'secondaryUrl' => home_url('/guide'),
        ];

        $ctaAttrs = [
            'title' => $m['cta_title'] ?? 'Found a property worth a closer look?',
            'text' => $m['cta_text'] ?? 'Book a sample showing or reach out directly — a specialist will confirm availability, answer questions about the home, and walk you through the next steps.',
            'primaryLabel' => $m['cta_primary'] ?? 'Book a showing',
            'secondaryLabel' => $m['cta_secondary'] ?? 'Run the numbers',
        ];

        return [
            self::block('acreline/page-hero', $heroAttrs),
            self::block('acreline/listing-grid', [
                'introTitle' => $m['intro_title'] ?? 'Buying in a new neighborhood takes more than a quick scroll',
                'introText' => $m['intro_text'] ?? 'Every listing here has a story beyond the card: HOA rules, commute, school options, and how the block actually lives. Use the filter below to narrow by area and price, then save the ones worth a walk-through.',
            ]),
            self::block('acreline/market-stats', []),
            self::block('acreline/reviews', [
                'eyebrow' => 'Buyer feedback',
                'title' => 'What buyers say about the search process',
                'text' => 'Sample quotes for demonstration — not reviews from a licensed brokerage.',
            ]),
            self::block('acreline/faq-list', [
                'title' => 'Listings FAQ',
                'headClass' => 'left',
            ]),
            self::block('acreline/newsletter', []),
            self::block('acreline/cta-band', $ctaAttrs),
        ];
    }

    /** @param array<string, string> $m */
    private static function areasBlocks(array $m, int $postId): array
    {
        $heroAttrs = [
            'eyebrow' => $m['hero_eyebrow'] ?? 'Six sample neighborhoods',
            'title' => $m['hero_title'] ?? 'Know the area before <em>you make an offer</em>',
            'text' => $m['hero_text'] ?? 'Six distinct sample neighborhoods — from established streets to a walkable core and new-construction HOA. Each one has a different price range, housing mix, and commute.',
            'imageUrl' => self::resolveHeroImage($postId),
            'primaryLabel' => 'Browse listings',
            'primaryUrl' => home_url('/listings'),
            'secondaryLabel' => 'Book a showing',
        ];

        $introAttrs = [
            'eyebrow' => $m['intro_eyebrow'] ?? 'Why neighborhood knowledge matters',
            'title' => $m['intro_title'] ?? 'One county is not one market',
            'text' => $m['intro_text'] ?? 'The difference between a porch-front street in North Ridge and a condo above the shops in Oak Hollow is not just price — it is commute, schools, HOA rules, and who your neighbors will be. This office covers six sample neighborhoods so you can compare before you book a showing.',
        ];

        $gridAttrs = [
            'gridEyebrow' => $m['grid_eyebrow'] ?? 'Neighborhood by neighborhood',
            'gridTitle' => $m['grid_title'] ?? 'Where the sample office works',
            'gridText' => $m['grid_text'] ?? 'A quick read on six sample neighborhoods — housing mix, commute, and what a buyer should watch for.',
            'cardStyle' => 'featured',
            'bandStyle' => 'alt',
        ];

        for ($i = 1; $i <= 6; $i++) {
            $gridAttrs["area{$i}Meta"] = $m["area_{$i}_meta"] ?? '';
            $gridAttrs["area{$i}Title"] = $m["area_{$i}_title"] ?? '';
            $gridAttrs["area{$i}Body"] = $m["area_{$i}_body"] ?? '';
        }

        $howAttrs = [
            'eyebrow' => 'Area service',
            'title' => 'How we cover the region',
            'text' => 'Each agent on the sample team works a neighborhood — schools, commute, HOA rules, and which lenders close cleanly in that part of the sample county. When you book a showing, the specialist who covers that area walks it with you.',
            'layout' => 'bento',
            'maxAreas' => 6,
            'bandStyle' => 'paper',
        ];

        $ctaAttrs = [
            'title' => $m['cta_title'] ?? 'Ready to walk an area with a local specialist?',
            'text' => $m['cta_text'] ?? 'Book a sample showing for any area and the right specialist will reach out to confirm — no obligation, no pressure.',
            'primaryLabel' => $m['cta_primary'] ?? 'Book a showing',
            'secondaryLabel' => 'Browse listings',
        ];

        return [
            self::block('acreline/page-hero', $heroAttrs),
            self::block('acreline/intro-section', $introAttrs),
            self::block('acreline/area-grid', $gridAttrs),
            self::block('acreline/compare-table', []),
            self::block('acreline/region-coverage', $howAttrs),
            self::block('acreline/market-stats', []),
            self::block('acreline/reviews', [
                'eyebrow' => 'From the areas',
                'title' => 'What buyers say about working local',
                'text' => 'Sample quotes — not reviews from a licensed brokerage.',
            ]),
            self::block('acreline/newsletter', []),
            self::block('acreline/cta-band', $ctaAttrs),
        ];
    }

    /** @param array<string, string> $m */
    private static function guideBlocks(array $m, int $postId): array
    {
        $heroAttrs = [
            'eyebrow' => $m['hero_eyebrow'] ?? 'Buyer tools & education',
            'title' => $m['hero_title'] ?? 'The buying guide <em>agents wish every buyer read</em>',
            'text' => $m['hero_text'] ?? 'Houses, condos, and commercial spaces come with different questions: HOA rules, parking, permitted use, and a payment you can live with. This guide answers the ones that matter most — before you fall in love with the photos.',
            'imageUrl' => self::resolveHeroImage($postId),
            'primaryLabel' => 'Book a showing',
            'secondaryLabel' => 'Browse listings',
        ];

        $toolsAttrs = [
            'introTitle' => $m['intro_title'] ?? 'What to sort before you write an offer',
            'introText' => $m['intro_text'] ?? 'A finished house still needs a clear inspection path, HOA rules, and a payment you can live with. Condos add dues and rental rules. Commercial adds permitted use. Use the sample tools below to plan — then book a showing with an agent who knows the neighborhood.',
            'eyebrow' => $m['tools_eyebrow'] ?? 'Run your numbers first',
            'title' => $m['tools_title'] ?? 'Payment and pre-qualification tools',
            'text' => $m['tools_text'] ?? 'These are friendly estimates to help you plan before you talk to a lender — not loan commitments. Use the tools below to get a realistic payment range, then call a licensed lender.',
        ];

        $howAttrs = [
            'eyebrow' => 'The buying process',
            'title' => 'From first search to closing day',
            'text' => 'Most purchases follow the same arc — but the details vary by property type. Here is what a typical house, condo, or commercial deal looks like when working with a specialist who knows the neighborhood.',
        ];

        $ctaAttrs = [
            'title' => $m['cta_title'] ?? 'Ready to put this guide to use?',
            'text' => $m['cta_text'] ?? 'Browse the current sample inventory, or book a showing to walk a home with a specialist who can answer the on-the-ground questions.',
            'primaryLabel' => $m['cta_primary'] ?? 'Book a showing',
            'secondaryLabel' => $m['cta_secondary'] ?? 'Browse listings',
        ];

        return [
            self::block('acreline/page-hero', $heroAttrs),
            self::block('acreline/tools-section', $toolsAttrs),
            self::block('acreline/how-it-works', $howAttrs),
            self::block('acreline/checklist', []),
            self::block('acreline/faq-list', [
                'title' => 'Common buyer questions',
                'headClass' => 'left',
            ]),
            self::block('acreline/reviews', [
                'eyebrow' => 'First-time buyers',
                'title' => 'What buyers found most useful',
                'text' => 'Sample quotes — not reviews from a licensed brokerage.',
            ]),
            self::block('acreline/newsletter', []),
            self::block('acreline/cta-band', $ctaAttrs),
        ];
    }

    /** @param array<string, string> $m */
    private static function agentsBlocks(array $m, int $postId): array
    {
        $heroAttrs = [
            'eyebrow' => $m['hero_eyebrow'] ?? 'Meet the sample team',
            'title' => $m['hero_title'] ?? 'Local agents. <em>Real market knowledge.</em>',
            'text' => $m['hero_text'] ?? 'Three demo specialists — buyers, sellers, and a commercial desk. All phone numbers are fictional 555 lines.',
            'imageUrl' => self::resolveHeroImage($postId),
            'primaryLabel' => 'Book a showing',
            'secondaryLabel' => 'Browse listings',
        ];

        $introAttrs = [
            'title' => $m['intro_title'] ?? 'A focused team, not a franchise',
            'text' => $m['intro_text'] ?? 'This sample office is built around a simple idea: buyers and sellers deserve an agent who knows the neighborhood. Houses, condos, and commercial suites all carry different questions — HOA rules, parking, permitted use, and a commute that holds up on a Tuesday. Every agent on this team specialises in the property type you are buying or selling.',
        ];

        $agentListAttrs = [
            'eyebrow' => 'The sample team',
            'title' => 'Specialists, not generalists',
            'text' => 'Each agent focuses on a specific desk — buyers, sellers, or commercial. Pick the specialist whose background matches what you are buying.',
            'headingAlign' => 'left',
        ];

        $reviewsAttrs = [
            'eyebrow' => 'Client stories',
            'title' => 'What buyers say about the process',
            'text' => 'Sample quotes for layout demonstration — not real reviews from a licensed brokerage.',
        ];

        $howAttrs = [
            'eyebrow' => $m['how_eyebrow'] ?? 'How we work',
            'title' => $m['how_title'] ?? 'What the process actually looks like',
            'text' => $m['how_text'] ?? 'No pressure, no jargon, and a straight answer about the house, the block, and the next step. We walk the property with you, explain the issues we find, and let you decide.',
        ];

        $ctaAttrs = [
            'title' => $m['cta_title'] ?? 'Ready to talk to a specialist?',
            'text' => $m['cta_text'] ?? 'Pick an agent above, or book a general showing and we will match you with the right specialist.',
            'primaryLabel' => $m['cta_primary'] ?? 'Book a showing',
            'secondaryLabel' => 'Browse listings',
        ];

        return [
            self::block('acreline/page-hero', $heroAttrs),
            self::block('acreline/intro-section', $introAttrs),
            self::block('acreline/agent-list', $agentListAttrs),
            self::block('acreline/pricing-plans', []),
            self::block('acreline/reviews', $reviewsAttrs),
            self::block('acreline/how-we-work', $howAttrs),
            self::block('acreline/logo-strip', []),
            self::block('acreline/cta-band', $ctaAttrs),
        ];
    }

    /** @param array<string, string> $m */
    private static function contactBlocks(array $m, int $postId): array
    {
        $heroAttrs = [
            'eyebrow' => $m['hero_eyebrow'] ?? 'Concept office',
            'title' => $m['hero_title'] ?? 'Talk to a specialist — <em>not a call centre</em>',
            'text' => $m['hero_text'] ?? 'Every message goes to a real person who knows the properties and the ground. Expect a reply the same business day.',
            'imageUrl' => self::resolveHeroImage($postId),
            'primaryLabel' => 'Book a showing',
            'secondaryLabel' => 'Call the office',
        ];

        $formAttrs = [
            'formTitle' => $m['form_title'] ?? 'Send a message',
            'formText' => $m['form_text'] ?? "Tell us what you're looking for — or what you're thinking of selling — and the right specialist will be in touch. Buyers: mention the neighborhood and approximate price range. Sellers: mention the property type and beds.",
        ];

        $introAttrs = [
            'eyebrow' => 'Other ways to reach us',
            'title' => 'Phone, email, or walk in',
            'text' => 'The sample office is staffed Monday through Saturday. For after-hours questions about a specific listing, book a showing request and an agent will follow up first thing the next business day.',
        ];

        $howAttrs = [
            'eyebrow' => 'What to expect',
            'title' => 'What happens after you send a message',
            'text' => 'No automated drip campaigns. A real agent reviews every inquiry, looks up the property you mentioned, and responds with something useful — not a canned pitch.',
        ];

        $ctaAttrs = [
            'title' => $m['cta_title'] ?? 'Prefer to walk a property first?',
            'text' => $m['cta_text'] ?? 'Use the booking form to request a showing — you pick the listing, date, and time, and the assigned agent will confirm.',
            'primaryLabel' => $m['cta_primary'] ?? 'Book a showing',
            'secondaryLabel' => 'Browse listings',
        ];

        return [
            self::block('acreline/page-hero', $heroAttrs),
            self::block('acreline/contact-form', $formAttrs),
            self::block('acreline/office-info', ['showMap' => true]),
            self::block('acreline/trust-strip', []),
            self::block('acreline/intro-section', $introAttrs),
            self::block('acreline/how-we-work', $howAttrs),
            self::block('acreline/agent-list', [
                'eyebrow' => 'Direct contacts',
                'title' => 'Reach the right specialist',
                'text' => 'Skip the contact form — call or email the agent who covers the area you are buying in.',
                'headingAlign' => 'left',
            ]),
            self::block('acreline/cta-band', $ctaAttrs),
        ];
    }

    /** @param array<string, string> $m */
    private static function bookBlocks(array $m, int $postId): array
    {
        $heroAttrs = [
            'eyebrow' => $m['hero_eyebrow'] ?? 'Schedule a showing',
            'title' => $m['hero_title'] ?? 'Book a showing — <em>we walk it with you</em>',
            'text' => $m['hero_text'] ?? 'Pick a listing, choose a date, and submit. Your assigned specialist will confirm within a few hours and come prepared with a property briefing — access route, known issues, and the questions most buyers ask about that type of ground.',
            'imageUrl' => self::resolveHeroImage($postId),
        ];

        $introAttrs = [
            'eyebrow' => 'What to expect',
            'title' => 'A showing, not a sales pitch',
            'text' => 'A house tour is not a 20-minute scroll. Plan 45–60 minutes so your agent can walk the rooms, check the block, and answer questions on site — not in a follow-up email. Come with a list of questions you want answered before you think about an offer.',
        ];

        $ctaAttrs = [
            'title' => $m['cta_title'] ?? 'Questions before booking?',
            'text' => $m['cta_text'] ?? 'Call the office directly or use the contact form — a specialist will reply the same business day.',
            'primaryLabel' => $m['cta_primary'] ?? 'Contact the office',
            'primaryUrl' => home_url('/contact'),
            'secondaryLabel' => 'Browse listings',
        ];

        return [
            self::block('acreline/page-hero', $heroAttrs),
            self::block('acreline/book-note', [
                'note' => $m['book_note'] ?? 'Demo only — no emails, texts or calendar invites are sent. Staff can advance the booking status in WP Admin → Bookings.',
                'noteStyle' => 'banner',
                'showSidePhoto' => true,
            ]),
            self::block('acreline/intro-section', $introAttrs),
            self::block('acreline/prep-checklist', []),
            self::block('acreline/faq-list', [
                'title' => 'Showing FAQ',
                'headClass' => 'left',
            ]),
            self::block('acreline/cta-band', $ctaAttrs),
        ];
    }

    /** @param array<string, string> $m */
    private static function blogBlocks(array $m, int $postId): array
    {
        $heroAttrs = [
            'eyebrow' => $m['hero_eyebrow'] ?? 'Buyer resources',
            'title' => $m['hero_title'] ?? 'Field notes from the sample county',
            'text' => $m['hero_text'] ?? 'Practical articles on house tours, first-time checklists, and neighborhood search — what to know before you book a showing.',
            'imageUrl' => self::resolveHeroImage($postId),
        ];

        return [
            self::block('acreline/page-hero', $heroAttrs),
            self::block('acreline/topic-cards', [
                'eyebrow' => 'What these notes cover',
                'title' => 'Short reads you can adapt for your market',
                'text' => 'Showings, first-time checklists, and neighborhood search — the three posts buyers actually ask for. Use them as local SEO starters, then link back to listings and the booking form.',
            ]),
            self::block('acreline/post-grid', []),
            self::block('acreline/reviews', [
                'eyebrow' => 'From the notes',
                'title' => 'What readers take into a showing',
                'text' => 'Sample quotes for layout — not reviews from a licensed brokerage.',
            ]),
            self::block('acreline/newsletter', [
                'layout' => 'compact',
                'bandStyle' => 'accent',
            ]),
            self::block('acreline/cta-band', [
                'title' => $m['cta_title'] ?? 'Ready to put these notes to use?',
                'text' => $m['cta_text'] ?? 'Browse the current sample inventory or book a showing to walk a home with a specialist.',
                'primaryLabel' => $m['cta_primary'] ?? 'Browse listings',
                'primaryUrl' => home_url('/listings'),
                'secondaryLabel' => $m['cta_secondary'] ?? 'Book a showing',
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
            if (is_string($url) && $url !== '') {
                return $url;
            }
        }

        $slug = (string) get_post_field('post_name', $postId);
        $bundled = HeroImage::bundledUrl($slug !== '' ? $slug : 'home');
        if ($bundled !== '') {
            return $bundled;
        }

        return HeroImage::url();
    }
}
