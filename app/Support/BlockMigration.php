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
            'title' => $m['hero_title'] ?? 'Farms, land &amp; homes — <em>browse the sample inventory</em>',
            'text' => $m['hero_text'] ?? 'Filter by area, price, and property type. Every listing below is a concept record — use the tools, save favorites, and compare side by side before you reach out.',
            'imageUrl' => self::resolveHeroImage($postId),
            'primaryLabel' => 'Book a showing',
            'secondaryLabel' => 'Buyer guide',
            'secondaryUrl' => home_url('/guide'),
        ];

        $ctaAttrs = [
            'title' => $m['cta_title'] ?? 'Found a property worth a closer look?',
            'text' => $m['cta_text'] ?? 'Book a sample showing or reach out directly — a specialist will confirm availability, answer questions about the parcel, and walk you through the next steps.',
            'primaryLabel' => $m['cta_primary'] ?? 'Book a showing',
            'secondaryLabel' => $m['cta_secondary'] ?? 'Run the numbers',
        ];

        return [
            self::block('acreline/page-hero', $heroAttrs),
            self::block('acreline/listing-grid', [
                'introTitle' => $m['intro_title'] ?? 'Buying rural property takes more than a quick scroll',
                'introText' => $m['intro_text'] ?? 'Every parcel here has a story beyond the MLS data: soil type, water source, road access, easements, and any agricultural enrollments that affect taxes or use. Use the filter below to narrow by area and price, then save the ones worth a walk-through. Our agents know these parcels personally and can answer the questions that a listing sheet leaves out.',
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
            self::block('acreline/cta-band', $ctaAttrs),
        ];
    }

    /** @param array<string, string> $m */
    private static function areasBlocks(array $m, int $postId): array
    {
        $heroAttrs = [
            'eyebrow' => $m['hero_eyebrow'] ?? 'Six sample markets',
            'title' => $m['hero_title'] ?? 'Know the ground before <em>you make an offer</em>',
            'text' => $m['hero_text'] ?? 'Six distinct sample areas — from fruit-belt orchards to timber ridges and creek-bottom farmland. Each one has a different price range, land type, and set of buyer considerations.',
            'imageUrl' => self::resolveHeroImage($postId),
            'primaryLabel' => 'Browse listings',
            'primaryUrl' => home_url('/listings'),
            'secondaryLabel' => 'Book a showing',
        ];

        $introAttrs = [
            'eyebrow' => $m['intro_eyebrow'] ?? 'Why area knowledge matters',
            'title' => $m['intro_title'] ?? 'Rural land is not one market — it is six',
            'text' => $m['intro_text'] ?? 'The difference between a flat cash-crop farm in Grain Country and a wooded ridge lot in Hill Country is not just price per acre — it is water source, road maintenance, zoning overlays, agricultural enrollments, and who your neighbors will be. This office covers a range of sample area types so you can compare what each ground type is actually like before you drive out for a showing.',
        ];

        $gridAttrs = [
            'gridEyebrow' => $m['grid_eyebrow'] ?? 'Area by area',
            'gridTitle' => $m['grid_title'] ?? 'Where the sample office works',
            'gridText' => $m['grid_text'] ?? 'A quick read on six rural area types — what the ground is like, what typically lists, and what a buyer should watch for.',
        ];

        for ($i = 1; $i <= 6; $i++) {
            $gridAttrs["area{$i}Meta"] = $m["area_{$i}_meta"] ?? '';
            $gridAttrs["area{$i}Title"] = $m["area_{$i}_title"] ?? '';
            $gridAttrs["area{$i}Body"] = $m["area_{$i}_body"] ?? '';
        }

        $howAttrs = [
            'eyebrow' => 'Area service',
            'title' => 'How we cover the region',
            'text' => 'Each agent on the sample team is based in — or grew up in — the area they cover. That means real knowledge of road conditions, water table depth, which soils perc and which do not, and which local lenders understand agricultural land loans. When you work with this office on a rural purchase, your agent has likely walked ground within a mile of the parcel you are considering.',
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
            self::block('acreline/how-we-work', $howAttrs),
            self::block('acreline/market-stats', []),
            self::block('acreline/reviews', [
                'eyebrow' => 'From the areas',
                'title' => 'What buyers say about working local',
                'text' => 'Sample quotes — not reviews from a licensed brokerage.',
            ]),
            self::block('acreline/cta-band', $ctaAttrs),
        ];
    }

    /** @param array<string, string> $m */
    private static function guideBlocks(array $m, int $postId): array
    {
        $heroAttrs = [
            'eyebrow' => $m['hero_eyebrow'] ?? 'Buyer tools & education',
            'title' => $m['hero_title'] ?? 'The land-buying guide <em>agents wish every buyer read</em>',
            'text' => $m['hero_text'] ?? 'Rural properties come with questions you would never ask about a condo: Is the well drinkable? Does the septic perc? Who maintains the lane? This guide answers the ones that matter most — before you fall in love with a view.',
            'imageUrl' => self::resolveHeroImage($postId),
            'primaryLabel' => 'Book a showing',
            'secondaryLabel' => 'Browse listings',
        ];

        $toolsAttrs = [
            'introTitle' => $m['intro_title'] ?? 'What changes when you buy land instead of a house',
            'introText' => $m['intro_text'] ?? 'With a finished home, utilities are already in place: municipal water, city sewer, paved road, and a clear address for delivery trucks. When you buy raw land or a rural farm, you often prove those things yourself — and the answers change what the land is worth and what it will cost to build or operate. Perc test results, well yield logs, and road-access easements are not optional paperwork. They are the foundation of your offer price.',
            'eyebrow' => $m['tools_eyebrow'] ?? 'Run your numbers first',
            'title' => $m['tools_title'] ?? 'Land-loan &amp; pre-qualification tools',
            'text' => $m['tools_text'] ?? 'These are friendly estimates to help you plan before you talk to a lender — not loan commitments. Land loans are different from home mortgages: expect higher down payments (typically 20–35%), shorter terms, and lenders who specialise in agricultural collateral. Use the tools below to get a realistic payment range, then call a farm-credit lender.',
        ];

        $howAttrs = [
            'eyebrow' => 'The buying process',
            'title' => 'From first search to closing day',
            'text' => 'Most rural purchases follow the same arc — but the details vary a lot by property type. Here is what a typical farm or land purchase looks like when working with a specialist who knows the ground.',
        ];

        $ctaAttrs = [
            'title' => $m['cta_title'] ?? 'Ready to put this guide to use?',
            'text' => $m['cta_text'] ?? 'Browse the current sample inventory, or book a showing to walk a parcel with a specialist who can answer the on-the-ground questions.',
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
            self::block('acreline/cta-band', $ctaAttrs),
        ];
    }

    /** @param array<string, string> $m */
    private static function agentsBlocks(array $m, int $postId): array
    {
        $heroAttrs = [
            'eyebrow' => $m['hero_eyebrow'] ?? 'Meet the sample team',
            'title' => $m['hero_title'] ?? 'Local agents. <em>Real land knowledge.</em>',
            'text' => $m['hero_text'] ?? 'Three demo specialists — farms, historic homes, and raw land. All phone numbers are fictional 555 lines.',
            'imageUrl' => self::resolveHeroImage($postId),
            'primaryLabel' => 'Book a showing',
            'secondaryLabel' => 'Browse listings',
        ];

        $introAttrs = [
            'title' => $m['intro_title'] ?? 'A focused team, not a franchise',
            'text' => $m['intro_text'] ?? 'This sample office is built around a simple idea: rural property deserves an agent who understands it. Farms, orchards, raw land, and century homesteads all carry questions a typical residential agent rarely faces — use-value tax enrollment, agricultural conservation easements, perc evaluations, soil testing, and stone-foundation inspections. Every agent on this team specialises in the property type you are buying or selling.',
        ];

        $agentListAttrs = [
            'eyebrow' => 'The sample team',
            'title' => 'Specialists, not generalists',
            'text' => 'Each agent focuses on a specific type of rural property. Pick the specialist whose background matches what you are buying.',
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
            'text' => $m['how_text'] ?? 'No pressure, no jargon, and a straight answer about the ground under your feet. We walk the property with you, explain the issues we find, and let you decide.',
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
            self::block('acreline/reviews', $reviewsAttrs),
            self::block('acreline/how-we-work', $howAttrs),
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
            'formText' => $m['form_text'] ?? "Tell us what you're looking for — or what you're thinking of selling — and the right specialist will be in touch. Buyers: mention the area and approximate price range. Sellers: mention the property type and acreage.",
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
            'text' => 'Rural property tours take more time than a 20-minute condo walk-through. A farm or land showing typically runs 60–90 minutes. Your agent will walk boundaries, point out drainage, check the well or septic records if available, and answer questions on site — not in a follow-up email. Come with boots if the ground is wet and a list of questions you want answered before you think about an offer.',
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
            'text' => $m['hero_text'] ?? 'Practical articles on land buying, rural home searches, and what to know before you book a showing. Written to be useful — not to rank for keywords.',
            'imageUrl' => self::resolveHeroImage($postId),
        ];

        return [
            self::block('acreline/page-hero', $heroAttrs),
            self::block('acreline/topic-cards', [
                'eyebrow' => 'What these notes cover',
                'title' => 'Short reads you can adapt for your market',
                'text' => 'Showings, first-time checklists, and land vs home search — the three posts buyers actually ask for. Use them as local SEO starters, then link back to listings and the booking form.',
            ]),
            self::block('acreline/post-grid', []),
            self::block('acreline/reviews', [
                'eyebrow' => 'From the notes',
                'title' => 'What readers take into a showing',
                'text' => 'Sample quotes for layout — not reviews from a licensed brokerage.',
            ]),
            self::block('acreline/cta-band', [
                'title' => $m['cta_title'] ?? 'Ready to put these notes to use?',
                'text' => $m['cta_text'] ?? 'Browse the current sample inventory or book a showing to walk a parcel with a specialist.',
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
