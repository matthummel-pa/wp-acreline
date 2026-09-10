<?php

namespace App\Support;

class PageCopy
{
    /**
     * @return array<string, array<string, array{label: string, type: string, default: string}>>
     */
    public static function schemas(): array
    {
        $hero = fn (string $eyebrow, string $title, string $text) => [
            'hero_brand' => ['label' => 'Hero brand', 'type' => 'text', 'default' => ''],
            'hero_eyebrow' => ['label' => 'Hero eyebrow', 'type' => 'text', 'default' => $eyebrow],
            'hero_title' => ['label' => 'Hero title (use <em> for italics)', 'type' => 'text', 'default' => $title],
            'hero_text' => ['label' => 'Hero intro', 'type' => 'textarea', 'default' => $text],
            'hero_image' => ['label' => 'Hero image', 'type' => 'image', 'default' => ''],
        ];

        $cta = [
            'cta_title' => ['label' => 'Bottom CTA title', 'type' => 'text', 'default' => ''],
            'cta_text' => ['label' => 'Bottom CTA text', 'type' => 'textarea', 'default' => ''],
            'cta_primary' => ['label' => 'Primary CTA label', 'type' => 'text', 'default' => 'Book a showing'],
            'cta_secondary' => ['label' => 'Secondary CTA label', 'type' => 'text', 'default' => 'Contact the office'],
        ];

        return [
            'home' => $hero(
                'Homes, neighborhoods, and local agents',
                'Homes worth <em>walking through.</em>',
                'Sample houses, condos, and commercial listings across three demo neighborhoods. Filter by type and area, then book a showing.'
            ) + [
                'intent_eyebrow' => ['label' => 'Start-here eyebrow', 'type' => 'text', 'default' => 'Start here'],
                'intent_title' => ['label' => 'Start-here title', 'type' => 'text', 'default' => 'Pick the path'],
                'intent_text' => ['label' => 'Start-here intro', 'type' => 'textarea', 'default' => 'Then we match a listing or a tour. Neighborhood first — the rest follows.'],
                'intent_buy' => ['label' => 'Buy card text (legacy)', 'type' => 'text', 'default' => 'Area first — North Ridge, Mill Creek, Oak Hollow. Schools, commute, and inventory mix change before the listing photo does.'],
                'intent_buy_kicker' => ['label' => 'Buy card kicker', 'type' => 'text', 'default' => 'Buy'],
                'intent_buy_title' => ['label' => 'Buy card title', 'type' => 'text', 'default' => 'Scan homes and neighborhoods'],
                'intent_buy_lead' => ['label' => 'Buy card text', 'type' => 'textarea', 'default' => 'Area first — North Ridge, Mill Creek, Oak Hollow. Schools, commute, and inventory mix change before the listing photo does.'],
                'intent_buy_cta' => ['label' => 'Buy card CTA', 'type' => 'text', 'default' => 'Browse listings'],
                'intent_sell' => ['label' => 'Sell card text (legacy)', 'type' => 'text', 'default' => 'Run a sample value range for a fictional address. Not an appraisal — a next step if you are comparing options.'],
                'intent_sell_kicker' => ['label' => 'Sell card kicker', 'type' => 'text', 'default' => 'Sell'],
                'intent_sell_title' => ['label' => 'Sell card title', 'type' => 'text', 'default' => 'Price it before you list'],
                'intent_sell_lead' => ['label' => 'Sell card text', 'type' => 'textarea', 'default' => 'Run a sample value range for a fictional address. Not an appraisal — a next step if you are comparing options.'],
                'intent_sell_cta' => ['label' => 'Sell card CTA', 'type' => 'text', 'default' => 'Estimate value'],
                'intent_tour' => ['label' => 'Tour card text (legacy)', 'type' => 'text', 'default' => 'Pick a sample listing, a date, and a slot. Rural showings mean a lane, a well, and boots — mention perc or pets in the notes.'],
                'intent_tour_kicker' => ['label' => 'Tour card kicker', 'type' => 'text', 'default' => 'Tour'],
                'intent_tour_title' => ['label' => 'Tour card title', 'type' => 'text', 'default' => 'Walk it on the ground'],
                'intent_tour_lead' => ['label' => 'Tour card text', 'type' => 'textarea', 'default' => 'Pick a sample listing, a date, and a slot. Rural showings mean a lane, a well, and boots — mention perc or pets in the notes.'],
                'intent_tour_cta' => ['label' => 'Tour card CTA', 'type' => 'text', 'default' => 'Book a showing'],
                'intent_notes_label' => ['label' => 'Help notes label', 'type' => 'text', 'default' => 'Good to know'],
                'intent_note_1_title' => ['label' => 'Help note 1 title', 'type' => 'text', 'default' => 'Neighborhood first'],
                'intent_note_1_text' => ['label' => 'Help note 1 text', 'type' => 'textarea', 'default' => 'Schools, commute, and HOA rules change from one sample area to the next.'],
                'intent_note_2_title' => ['label' => 'Help note 2 title', 'type' => 'text', 'default' => 'Read the listing, then walk'],
                'intent_note_2_text' => ['label' => 'Help note 2 text', 'type' => 'textarea', 'default' => 'Photos skip systems, parking, and the block. Book a showing before you write.'],
                'intent_note_3_title' => ['label' => 'Help note 3 title', 'type' => 'text', 'default' => 'One office, three desks'],
                'intent_note_3_text' => ['label' => 'Help note 3 text', 'type' => 'textarea', 'default' => 'Buyers, sellers, and commercial inquiries route to the matching specialist.'],
                'search_eyebrow' => ['label' => 'Search eyebrow', 'type' => 'text', 'default' => 'Find'],
                'search_title' => ['label' => 'Search title', 'type' => 'text', 'default' => 'Search sample inventory'],
                'search_text' => ['label' => 'Search intro', 'type' => 'textarea', 'default' => 'Short filters. Fast scan. Every result is fictional demo data.'],
                'hero_cta_primary' => ['label' => 'Hero search button', 'type' => 'text', 'default' => 'Show matches'],
                'hero_cta_secondary' => ['label' => 'Hero browse link', 'type' => 'text', 'default' => 'Browse all listings'],
                'spotlight_eyebrow' => ['label' => 'Spotlight eyebrow', 'type' => 'text', 'default' => 'Spotlight'],
                'spotlight_title' => ['label' => 'Spotlight title', 'type' => 'text', 'default' => 'Three sample homes to scan'],
                'spotlight_text' => ['label' => 'Spotlight intro', 'type' => 'textarea', 'default' => 'Price · beds · acres — then book a fictional walk-through.'],
                'book_eyebrow' => ['label' => 'Booking eyebrow', 'type' => 'text', 'default' => 'Appointments'],
                'book_title' => ['label' => 'Booking title', 'type' => 'text', 'default' => 'Book a house showing'],
                'book_text' => ['label' => 'Booking intro', 'type' => 'textarea', 'default' => 'Demo scheduler for touring sample homes. Requests are saved to Bookings as Requested.'],
            ],
            'listings' => array_merge($hero('Sample inventory', 'Sample homes &amp; commercial <em>for demo tours</em>', 'Eight fictional properties for layout and filter testing. Switch grid or map — nothing here is a live MLS feed.'), [
                'intro_title' => ['label' => 'Local note title', 'type' => 'text', 'default' => 'Buying in a new neighborhood'],
                'intro_text' => ['label' => 'Local note', 'type' => 'textarea', 'default' => 'Every sample listing sits in an area — schools, commute, and HOA rules change from one neighborhood to the next. Filter first, then book a walk-through. Replace this inventory with your own market.'],
            ], $cta, [
                'cta_title' => ['label' => 'Bottom CTA title', 'type' => 'text', 'default' => 'See a property you like?'],
                'cta_text' => ['label' => 'Bottom CTA text', 'type' => 'textarea', 'default' => 'Book a walkthrough with a sample agent, or run the numbers first with the land-loan and pre-qualification tools.'],
                'cta_secondary' => ['label' => 'Secondary CTA label', 'type' => 'text', 'default' => 'Financing tools'],
            ]),
            'areas' => array_merge($hero('Sample neighborhoods', 'Areas we <em>demo</em>', 'Fictional North Ridge, Mill Creek, and Oak Hollow profiles — written so you can swap in your own counties.'), [
                'intro_title' => ['label' => 'Intro title', 'type' => 'text', 'default' => 'Houses, condos &amp; commercial in a sample market'],
                'intro_text' => ['label' => 'Intro', 'type' => 'textarea', 'default' => 'The office sits at 100 Concept Way in Sample Borough. The six neighborhood cards below show how a brokerage talks about its territory. Replace the names with yours.'],
                'grid_eyebrow' => ['label' => 'Grid eyebrow', 'type' => 'text', 'default' => 'Neighborhood by neighborhood'],
                'grid_title' => ['label' => 'Grid title', 'type' => 'text', 'default' => 'Where the sample office works'],
                'grid_text' => ['label' => 'Grid intro', 'type' => 'textarea', 'default' => 'A quick read on six sample neighborhoods — housing mix, commute, and what a buyer should watch for.'],
                'area_1_meta' => ['label' => 'Area 1 meta', 'type' => 'text', 'default' => 'West ridge · established streets'],
                'area_1_title' => ['label' => 'Area 1 title', 'type' => 'text', 'default' => 'North Ridge'],
                'area_1_body' => ['label' => 'Area 1 body', 'type' => 'textarea', 'default' => 'Tree-lined blocks, family homes, and a short drive to the sample county seat. Buyers come for schools and porch-front streets.'],
                'area_2_meta' => ['label' => 'Area 2 meta', 'type' => 'text', 'default' => 'Creek corridor · parks and schools'],
                'area_2_title' => ['label' => 'Area 2 title', 'type' => 'text', 'default' => 'Mill Creek'],
                'area_2_body' => ['label' => 'Area 2 body', 'type' => 'textarea', 'default' => 'Mid-range houses, townhomes, and quiet cul-de-sacs. A practical commute and parks along the creek.'],
                'area_3_meta' => ['label' => 'Area 3 meta', 'type' => 'text', 'default' => 'Walkable core · shops and older homes'],
                'area_3_title' => ['label' => 'Area 3 title', 'type' => 'text', 'default' => 'Oak Hollow'],
                'area_3_body' => ['label' => 'Area 3 body', 'type' => 'textarea', 'default' => 'A compact main street, older houses, and condos above the shops. Best for buyers who want to walk to coffee.'],
                'area_4_meta' => ['label' => 'Area 4 meta', 'type' => 'text', 'default' => 'New construction · HOA streets'],
                'area_4_title' => ['label' => 'Area 4 title', 'type' => 'text', 'default' => 'Riverbend'],
                'area_4_body' => ['label' => 'Area 4 body', 'type' => 'textarea', 'default' => 'Builder homes, HOA amenities, and wide streets. Good for buyers who want low-maintenance and a predictable finish.'],
                'area_5_meta' => ['label' => 'Area 5 meta', 'type' => 'text', 'default' => 'Mixed-use · condos and storefronts'],
                'area_5_title' => ['label' => 'Area 5 title', 'type' => 'text', 'default' => 'Midtown'],
                'area_5_body' => ['label' => 'Area 5 body', 'type' => 'textarea', 'default' => 'Condos, lofts, and a few commercial suites. A sample urban-edge pocket inside the same county.'],
                'area_6_meta' => ['label' => 'Area 6 meta', 'type' => 'text', 'default' => 'First-home streets · townhomes'],
                'area_6_title' => ['label' => 'Area 6 title', 'type' => 'text', 'default' => 'Southgate'],
                'area_6_body' => ['label' => 'Area 6 body', 'type' => 'textarea', 'default' => 'Townhomes and modest single-family streets. Often the first stop for buyers stretching into the sample market.'],
            ], $cta, [
                'cta_title' => ['label' => 'Bottom CTA title', 'type' => 'text', 'default' => 'Walk an area with us'],
                'cta_text' => ['label' => 'Bottom CTA text', 'type' => 'textarea', 'default' => 'Tell us which kind of ground you want to understand, and we\'ll match you with the sample agent who talks that language.'],
            ]),
            'guide' => array_merge($hero('Buyer tools', 'A clearer path to <em>buying a home</em>', 'Short guides and demo calculators. Use with the showing scheduler for a full agent workflow.'), [
                'intro_title' => ['label' => 'Intro title', 'type' => 'text', 'default' => 'What to sort before you write an offer'],
                'intro_text' => ['label' => 'Intro', 'type' => 'textarea', 'default' => 'A finished house still needs a clear inspection path, HOA rules, and a payment you can live with. Use the sample tools below to plan — then book a showing.'],
                'check_eyebrow' => ['label' => 'Scan-cards eyebrow', 'type' => 'text', 'default' => 'Before the calculators'],
                'check_title' => ['label' => 'Scan-cards title', 'type' => 'text', 'default' => 'Four things that change what a house is worth'],
                'check_text' => ['label' => 'Scan-cards intro', 'type' => 'textarea', 'default' => 'Read these first, then run the demo numbers. A pretty listing photo without a clear inspection path or HOA picture is a different product than a move-in-ready house.'],
                'tools_eyebrow' => ['label' => 'Tools eyebrow', 'type' => 'text', 'default' => 'Run Your Numbers'],
                'tools_title' => ['label' => 'Tools title', 'type' => 'text', 'default' => 'Payment &amp; pre-qualification tools'],
                'tools_text' => ['label' => 'Tools intro', 'type' => 'textarea', 'default' => 'Friendly estimates to help you plan — not loan offers. A licensed lender will verify everything with full documentation.'],
            ], $cta, [
                'cta_title' => ['label' => 'Bottom CTA title', 'type' => 'text', 'default' => 'Ready to walk a sample home?'],
                'cta_text' => ['label' => 'Bottom CTA text', 'type' => 'textarea', 'default' => 'Book a demo showing or browse the sample inventory.'],
                'cta_secondary' => ['label' => 'Secondary CTA label', 'type' => 'text', 'default' => 'Browse listings'],
            ]),
            'agents' => array_merge($hero('Sample team', 'Agents who know the <em>sample market</em>', 'Team profiles are Agent posts. Contact numbers are fictional 555 lines.'), [
                'intro_title' => ['label' => 'Intro title', 'type' => 'text', 'default' => 'A small, local team by design'],
                'intro_text' => ['label' => 'Intro', 'type' => 'textarea', 'default' => 'This sample office is built around a simple idea: buyers and sellers deserve an agent who knows the neighborhood. Houses, condos, and commercial suites all come with questions a generic listing site skips.'],
                'how_eyebrow' => ['label' => 'How-we-work eyebrow', 'type' => 'text', 'default' => 'How we work'],
                'how_title' => ['label' => 'How-we-work title', 'type' => 'text', 'default' => 'What working with this office looks like'],
                'how_text' => ['label' => 'How-we-work intro', 'type' => 'textarea', 'default' => 'No pressure, no jargon, and a straight answer about the house, the block, and the next step.'],
            ], $cta, [
                'cta_title' => ['label' => 'Bottom CTA title', 'type' => 'text', 'default' => 'Talk to a sample agent'],
                'cta_text' => ['label' => 'Bottom CTA text', 'type' => 'textarea', 'default' => 'Reach the office at (555) 010-0455, or book a no-pressure showing and we\'ll match you with the sample agent who knows that kind of ground.'],
            ]),
            'contact' => $hero('Concept office', 'Get in touch <em>(demo only)</em>', 'Fictional address and phone. Prefer booking a sample showing for the full appointment UX.') + [
                'office_title' => ['label' => 'Office heading', 'type' => 'text', 'default' => ''],
                'office_address' => ['label' => 'Address', 'type' => 'textarea', 'default' => "100 Concept Way\nSample Borough, PA 00000"],
                'office_phone' => ['label' => 'Phone', 'type' => 'text', 'default' => '(555) 010-0455'],
                'office_email' => ['label' => 'Email', 'type' => 'text', 'default' => 'hello@acreline-concept.test'],
                'office_hours' => ['label' => 'Hours', 'type' => 'textarea', 'default' => "Mon–Fri: 8:30am – 5:30pm\nSaturday: 9:00am – 1:00pm\nSunday: By appointment"],
                'form_title' => ['label' => 'Form title', 'type' => 'text', 'default' => 'Send us a message'],
                'form_text' => ['label' => 'Form intro', 'type' => 'textarea', 'default' => 'Tell us what you\'re looking for — or what you\'re thinking of selling — and we\'ll be in touch.'],
                'when_eyebrow' => ['label' => 'Reach-us eyebrow', 'type' => 'text', 'default' => 'How to reach us'],
                'when_title' => ['label' => 'Reach-us title', 'type' => 'text', 'default' => 'Call, message, or book a walk'],
                'when_text' => ['label' => 'Reach-us intro', 'type' => 'textarea', 'default' => 'Pick the path that matches the job. All three stay on this concept site — nothing is emailed or texted.'],
            ],
            'book' => $hero('Appointments', 'Book a house showing', 'Pick a listing, date and time. The request is saved as a Booking in Requested status — nothing is emailed.') + [
                'book_note' => ['label' => 'Form note', 'type' => 'text', 'default' => 'Demo only — no emails, texts or calendar invites are sent. Staff can advance the booking in WP Admin → Bookings.'],
            ],
            'blog' => array_merge($hero('Guide', 'Realtor notes you can publish', 'Sample posts for showings, buyer checklists, and neighborhood search — ready to adapt for local SEO.'), $cta, [
                'cta_title' => ['label' => 'Bottom CTA title', 'type' => 'text', 'default' => 'Ready to tour a sample home?'],
                'cta_text' => ['label' => 'Bottom CTA text', 'type' => 'textarea', 'default' => 'Use the showing scheduler — property, date and time in one flow.'],
                'cta_secondary' => ['label' => 'Secondary CTA label', 'type' => 'text', 'default' => 'Browse samples'],
            ]),
            'simple' => $hero('', '', '') + [
                'body' => ['label' => 'Page body', 'type' => 'textarea', 'default' => ''],
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function templateMap(): array
    {
        return [
            'front-page.blade.php' => 'home',
            'page-listings.blade.php' => 'listings',
            'page-areas.blade.php' => 'areas',
            'page-guide.blade.php' => 'guide',
            'page-agents.blade.php' => 'agents',
            'page-contact.blade.php' => 'contact',
            'page-book.blade.php' => 'book',
            'home.blade.php' => 'blog',
            'page.blade.php' => 'simple',
        ];
    }

    public static function schemaKeyForPost(int $postId): string
    {
        $slug = get_post_field('post_name', $postId);
        $bySlug = [
            'home' => 'home',
            'listings' => 'listings',
            'areas' => 'areas',
            'guide' => 'guide',
            'agents' => 'agents',
            'contact' => 'contact',
            'book' => 'book',
            'blog' => 'blog',
        ];
        if (isset($bySlug[$slug])) {
            return $bySlug[$slug];
        }

        $template = (string) get_page_template_slug($postId);
        $base = $template !== '' ? basename($template) : '';

        return self::templateMap()[$base] ?? 'simple';
    }

    public static function schemaKeyForContext(?int $postId = null): string
    {
        $virtual = DemoPages::currentSlug() ?? DemoPages::requestSlug();
        if ($virtual === 'blog') {
            return 'blog';
        }
        if ($virtual && isset(self::schemas()[$virtual])) {
            return $virtual;
        }

        $id = $postId ?? (int) (get_queried_object_id() ?: get_the_ID());
        if (function_exists('is_front_page') && is_front_page()) {
            return 'home';
        }
        if ($id > 0) {
            return self::schemaKeyForPost($id);
        }
        if (function_exists('is_home') && is_home()) {
            return 'blog';
        }

        return 'simple';
    }

    /**
     * @return array<string, array{label: string, type: string, default: string}>
     */
    public static function schemaForPost(int $postId): array
    {
        $schemas = self::schemas();
        $key = self::schemaKeyForPost($postId);

        return $schemas[$key] ?? $schemas['simple'];
    }

    public static function field(string $key, string $default = '', ?int $postId = null): string
    {
        $id = $postId ?: (int) (get_queried_object_id() ?: get_the_ID());
        $stored = $id ? get_post_meta($id, Catalog::metaKey($key), true) : '';
        $value = ($stored === '' || $stored === false) ? $default : $stored;
        $value = self::dropLegacyCopy((string) $value, $default);

        return wp_kses((string) $value, [
            'em' => [],
            'strong' => [],
            'br' => [],
            'a' => ['href' => [], 'class' => []],
        ]);
    }

    /**
     * @return array<string, string>
     */
    public static function all(?int $postId = null): array
    {
        $key = self::schemaKeyForContext($postId);
        $id = $postId ?: (int) (get_queried_object_id() ?: get_the_ID());
        if ($key === 'home') {
            $front = function_exists('get_option') ? (int) get_option('page_on_front') : 0;
            if ($front > 0) {
                $id = $front;
            }
        }
        if (DemoPages::currentSlug()) {
            $id = 0;
        }
        $schemas = self::schemas();
        $schema = $schemas[$key] ?? $schemas['simple'];
        $out = [];
        foreach ($schema as $field => $def) {
            $out[$field] = self::field($field, $def['default'] ?? '', $id ?: null);
        }

        return $out;
    }

    /**
     * @return list<array{meta: string, title: string, body: string}>
     */
    public static function areaCards(?int $postId = null): array
    {
        $id = $postId ?: (int) get_the_ID();
        $copy = self::all($id);
        $cards = [];
        for ($i = 1; $i <= 6; $i++) {
            $title = $copy["area_{$i}_title"] ?? '';
            if ($title === '') {
                continue;
            }
            $cards[] = [
                'meta' => $copy["area_{$i}_meta"] ?? '',
                'title' => $title,
                'body' => $copy["area_{$i}_body"] ?? '',
            ];
        }

        return $cards;
    }

    /**
     * Live demo still stores Keystone / Adams County strings from early seeds.
     * Prefer the current sample-market default so marketing pages stay Acreline.
     */
    private static function dropLegacyCopy(string $value, string $default): string
    {
        if ($value === '' || $value === $default) {
            return $value === '' ? $default : $value;
        }

        $hay = strtolower(wp_strip_all_tags($value));
        $needles = [
            'keystone',
            'keystone-concept.test',
            'gettysburg',
            'adams county',
            'menallen',
            'hamiltonban',
            'biglerville',
            'michaux',
            'idaville',
            'bendersville',
            'franklin township',
            'butler township',
            'tyrone township',
            'liberty township',
            'northwest of gettysburg',
        ];
        foreach ($needles as $needle) {
            if (str_contains($hay, $needle)) {
                return $default;
            }
        }

        return $value;
    }
}
