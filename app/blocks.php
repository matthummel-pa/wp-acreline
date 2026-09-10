<?php

/**
 * Acreline Gutenberg blocks — registration, editor assets, block patterns.
 *
 * All blocks are server-side rendered (save returns null).
 * JS editor UI lives in resources/js/blocks/index.js (loaded via editor.js).
 */

namespace App;

use App\Support\BlockMigration;
use App\Support\Catalog;
use App\Support\Faqs;
use App\Support\HeroImage;
use App\Support\Identity;

// ---------------------------------------------------------------------------
// Block category
// ---------------------------------------------------------------------------
add_filter('block_categories_all', function (array $categories): array {
    array_unshift($categories, [
        'slug' => 'acreline',
        'title' => __('Acreline', 'acreline'),
        'icon' => null,
    ]);

    return $categories;
});

// ---------------------------------------------------------------------------
// Register all blocks after init
// ---------------------------------------------------------------------------
add_action('init', function (): void {
    ks_register_blocks();
});

/**
 * Title/text attribute pairs for checklist-style blocks.
 *
 * @return array<string, array{type: string, default: string}>
 */
function ks_item_pair_attrs(int $count, string $prefix): array
{
    $attrs = [];
    for ($i = 1; $i <= $count; $i++) {
        $attrs["{$prefix}{$i}Title"] = ['type' => 'string', 'default' => ''];
        $attrs["{$prefix}{$i}Text"] = ['type' => 'string', 'default' => ''];
    }

    return $attrs;
}

function ks_register_blocks(): void
{
    // Shared attribute groups — merged into blocks that use them.
    $typo = [
        'headingAlign' => ['type' => 'string', 'default' => 'left'],
        'headingSize' => ['type' => 'string', 'default' => 'default'],
        'headingWeight' => ['type' => 'string', 'default' => 'default'],
        'bodySize' => ['type' => 'string', 'default' => 'default'],
    ];

    $heroBase = [
        'imageId' => ['type' => 'integer', 'default' => 0],
        'heroHeight' => ['type' => 'string', 'default' => 'default'],
        'overlayOpacity' => ['type' => 'integer', 'default' => 60],
        'overlayPreset' => ['type' => 'string', 'default' => 'default'],
        'imagePosition' => ['type' => 'string', 'default' => 'center'],
        'textAlign' => ['type' => 'string', 'default' => 'left'],
        'primaryBtnStyle' => ['type' => 'string', 'default' => 'primary'],
    ];

    $blocks = [
        'acreline/home-hero' => [
            'render_callback' => __NAMESPACE__.'\\ks_render_home_hero',
            'attributes' => array_merge($typo, $heroBase, [
                'eyebrow' => ['type' => 'string', 'default' => 'Homes, neighborhoods, and local agents'],
                'title' => ['type' => 'string', 'default' => 'Homes worth <em>walking through.</em>'],
                'text' => ['type' => 'string', 'default' => 'Sample houses, condos, and commercial listings across three demo neighborhoods. Filter by type and area, then book a showing.'],
                'imageUrl' => ['type' => 'string', 'default' => HeroImage::DEFAULT],
                'primaryLabel' => ['type' => 'string', 'default' => 'Show matches'],
                'secondaryLabel' => ['type' => 'string', 'default' => 'Browse all listings'],
            ]),
        ],
        'acreline/page-hero' => [
            'render_callback' => __NAMESPACE__.'\\ks_render_page_hero',
            'attributes' => array_merge($typo, $heroBase, [
                'brand' => ['type' => 'string', 'default' => ''],
                'eyebrow' => ['type' => 'string', 'default' => ''],
                'title' => ['type' => 'string', 'default' => ''],
                'text' => ['type' => 'string', 'default' => ''],
                'imageUrl' => ['type' => 'string', 'default' => ''],
                'primaryLabel' => ['type' => 'string', 'default' => 'Book a showing'],
                'primaryUrl' => ['type' => 'string', 'default' => ''],
                'secondaryLabel' => ['type' => 'string', 'default' => ''],
                'secondaryUrl' => ['type' => 'string', 'default' => ''],
                'secondaryBtnStyle' => ['type' => 'string', 'default' => 'outline-light'],
            ]),
        ],
        'acreline/intent-cards' => [
            'render_callback' => __NAMESPACE__.'\\ks_render_intent_cards',
            'attributes' => array_merge($typo, [
                'eyebrow' => ['type' => 'string', 'default' => 'Start here'],
                'title' => ['type' => 'string', 'default' => 'Pick the path'],
                'text' => ['type' => 'string', 'default' => 'Then we match a listing or a tour. Neighborhood first — the rest follows.'],
                'buyKicker' => ['type' => 'string', 'default' => 'Buy'],
                'buyTitle' => ['type' => 'string', 'default' => 'Scan homes and neighborhoods'],
                'buyLead' => ['type' => 'string', 'default' => 'Area first — North Ridge, Mill Creek, Oak Hollow. Schools, commute, and inventory mix change before the listing photo does.'],
                'buyCta' => ['type' => 'string', 'default' => 'Browse listings'],
                'sellKicker' => ['type' => 'string', 'default' => 'Sell'],
                'sellTitle' => ['type' => 'string', 'default' => 'Price it before you list'],
                'sellLead' => ['type' => 'string', 'default' => 'Run a sample value range for a fictional address. Not an appraisal — a next step if you are comparing options.'],
                'sellCta' => ['type' => 'string', 'default' => 'Estimate value'],
                'tourKicker' => ['type' => 'string', 'default' => 'Tour'],
                'tourTitle' => ['type' => 'string', 'default' => 'Walk it with an agent'],
                'tourLead' => ['type' => 'string', 'default' => 'Pick a sample listing, a date, and a slot. Mention pets, timing, or a second property in the notes.'],
                'tourCta' => ['type' => 'string', 'default' => 'Book a showing'],
                'notesLabel' => ['type' => 'string', 'default' => 'Good to know'],
                'note1Title' => ['type' => 'string', 'default' => 'Neighborhood first'],
                'note1Text' => ['type' => 'string', 'default' => 'Schools, commute, and HOA rules change from one sample area to the next.'],
                'note2Title' => ['type' => 'string', 'default' => 'Read the listing, then walk'],
                'note2Text' => ['type' => 'string', 'default' => 'Photos skip systems, parking, and the block. Book a showing before you write.'],
                'note3Title' => ['type' => 'string', 'default' => 'One office, three desks'],
                'note3Text' => ['type' => 'string', 'default' => 'Buyers, sellers, and commercial inquiries route to the matching specialist.'],
                'cardStyle' => ['type' => 'string', 'default' => 'photo'],
                'intentCols' => ['type' => 'string', 'default' => '3'],
            ]),
        ],
        'acreline/spotlight' => [
            'render_callback' => __NAMESPACE__.'\\ks_render_spotlight',
            'attributes' => array_merge($typo, [
                'eyebrow' => ['type' => 'string', 'default' => 'Spotlight'],
                'title' => ['type' => 'string', 'default' => 'Three sample homes to scan'],
                'text' => ['type' => 'string', 'default' => 'Price · beds · acres — then book a fictional walk-through.'],
                'itemCount' => ['type' => 'integer', 'default' => 3],
                'gridCols' => ['type' => 'string', 'default' => 'auto'],
            ]),
        ],
        'acreline/booking-section' => [
            'render_callback' => __NAMESPACE__.'\\ks_render_booking_section',
            'attributes' => array_merge($typo, [
                'eyebrow' => ['type' => 'string', 'default' => 'Appointments'],
                'title' => ['type' => 'string', 'default' => 'Book a house showing'],
                'text' => ['type' => 'string', 'default' => 'Demo scheduler for touring sample homes. Requests are saved to Bookings as Requested.'],
                'showSidePhoto' => ['type' => 'boolean', 'default' => true],
            ]),
        ],
        'acreline/market-stats' => [
            'render_callback' => __NAMESPACE__.'\\ks_render_market_stats',
            'attributes' => array_merge($typo, [
                'eyebrow' => ['type' => 'string', 'default' => 'Sample market'],
                'title' => ['type' => 'string', 'default' => 'Pulse at a glance'],
                'text' => ['type' => 'string', 'default' => 'A sample snapshot of how this inventory would read in a listing conversation — not a live CMA or MLS feed.'],
                'stat1Val' => ['type' => 'string', 'default' => '$398k'],
                'stat1Lbl' => ['type' => 'string', 'default' => 'Median sale price'],
                'stat1Sub' => ['type' => 'string', 'default' => '↑ 2.1% vs last quarter'],
                'stat2Val' => ['type' => 'string', 'default' => '32'],
                'stat2Lbl' => ['type' => 'string', 'default' => 'Days on market'],
                'stat2Sub' => ['type' => 'string', 'default' => '↓ 5 days vs last quarter'],
                'stat3Val' => ['type' => 'string', 'default' => '1.6'],
                'stat3Lbl' => ['type' => 'string', 'default' => 'Months of inventory'],
                'stat3Sub' => ['type' => 'string', 'default' => 'Limited active supply'],
                'stat4Val' => ['type' => 'string', 'default' => '95%'],
                'stat4Lbl' => ['type' => 'string', 'default' => 'List-to-sale ratio'],
                'stat4Sub' => ['type' => 'string', 'default' => 'Offers near asking'],
                'statsLayout' => ['type' => 'string', 'default' => '4-col'],
            ]),
        ],
        'acreline/how-it-works' => [
            'render_callback' => __NAMESPACE__.'\\ks_render_how_it_works',
            'attributes' => array_merge($typo, [
                'eyebrow' => ['type' => 'string', 'default' => 'How a tour starts'],
                'title' => ['type' => 'string', 'default' => 'From search to showing'],
                'text' => ['type' => 'string', 'default' => 'Neighborhood first, then a walk-through — not office theater. This demo stops at on-page confirmation.'],
                'stepLayout' => ['type' => 'string', 'default' => 'grid'],
            ]),
        ],
        'acreline/agent-tools' => [
            'render_callback' => __NAMESPACE__.'\\ks_render_agent_tools',
            'attributes' => array_merge($typo, [
                'eyebrow' => ['type' => 'string', 'default' => 'Agent tools'],
                'title' => ['type' => 'string', 'default' => 'Value range and listing alerts'],
                'text' => ['type' => 'string', 'default' => 'Two tools buyers and sellers use first: a sample value range, then an alert for new matches.'],
                'showValueTool' => ['type' => 'boolean', 'default' => true],
                'showAlertTool' => ['type' => 'boolean', 'default' => true],
            ]),
        ],
        'acreline/seo-content' => [
            'render_callback' => __NAMESPACE__.'\\ks_render_seo_content',
            'attributes' => [],
        ],
        'acreline/reviews' => [
            'render_callback' => __NAMESPACE__.'\\ks_render_reviews',
            'attributes' => array_merge($typo, [
                'eyebrow' => ['type' => 'string', 'default' => 'Samples'],
                'title' => ['type' => 'string', 'default' => 'What clients might say'],
                'text' => ['type' => 'string', 'default' => 'Placeholder quotes for layout — not real reviews. Sample names and photos only.'],
                'reviewLayout' => ['type' => 'string', 'default' => 'grid'],
                'reviewCols' => ['type' => 'string', 'default' => '3'],
                'showRating' => ['type' => 'boolean', 'default' => true],
                'showPhoto' => ['type' => 'boolean', 'default' => true],
            ]),
        ],
        'acreline/faq-list' => [
            'render_callback' => __NAMESPACE__.'\\ks_render_faq_list',
            'attributes' => array_merge($typo, [
                'title' => ['type' => 'string', 'default' => 'Questions buyers ask first'],
                'text' => ['type' => 'string', 'default' => 'Practical answers for house and neighborhood shoppers.'],
                'headClass' => ['type' => 'string', 'default' => 'left'],
                'faqStyle' => ['type' => 'string', 'default' => 'dl'],
                'listIcon' => ['type' => 'string', 'default' => 'none'],
                'showNumbers' => ['type' => 'boolean', 'default' => false],
                // Custom FAQ items stored directly in the block.
                'useCustomFaqs' => ['type' => 'boolean', 'default' => false],
                'faqs' => ['type' => 'array', 'default' => [], 'items' => ['type' => 'object']],
                // Accordion: which items start open.
                'accordionDefaultOpen' => ['type' => 'string', 'default' => 'first'],
            ]),
        ],
        'acreline/cta-band' => [
            'render_callback' => __NAMESPACE__.'\\ks_render_cta_band',
            'attributes' => [
                'title' => ['type' => 'string', 'default' => 'Tour a sample home next.'],
                'text' => ['type' => 'string', 'default' => 'Pick an address, choose a slot, and see how a modern realtor booking flow feels.'],
                'primaryLabel' => ['type' => 'string', 'default' => 'Book a showing'],
                'primaryUrl' => ['type' => 'string', 'default' => ''],
                'secondaryLabel' => ['type' => 'string', 'default' => 'Browse samples'],
                'secondaryUrl' => ['type' => 'string', 'default' => ''],
                'bandStyle' => ['type' => 'string', 'default' => 'light'],
                'contentAlign' => ['type' => 'string', 'default' => 'left'],
                'headingSize' => ['type' => 'string', 'default' => 'default'],
            ],
        ],
        'acreline/intro-section' => [
            'render_callback' => __NAMESPACE__.'\\ks_render_intro_section',
            'attributes' => array_merge($typo, [
                'eyebrow' => ['type' => 'string', 'default' => ''],
                'title' => ['type' => 'string', 'default' => ''],
                'text' => ['type' => 'string', 'default' => ''],
            ]),
        ],
        'acreline/listing-grid' => [
            'render_callback' => __NAMESPACE__.'\\ks_render_listing_grid',
            'attributes' => [
                'introTitle' => ['type' => 'string', 'default' => 'Buying in a new neighborhood'],
                'introText' => ['type' => 'string', 'default' => 'Every sample listing sits in an area — schools, commute, HOA rules, and inventory mix change from one neighborhood to the next. Filter first, then book a walk-through.'],
                'defaultView' => ['type' => 'string', 'default' => 'grid'],
                'gridCols' => ['type' => 'string', 'default' => '3'],
            ],
        ],
        'acreline/agent-list' => [
            'render_callback' => __NAMESPACE__.'\\ks_render_agent_list',
            'attributes' => array_merge($typo, [
                'eyebrow' => ['type' => 'string', 'default' => 'The sample team'],
                'title' => ['type' => 'string', 'default' => 'Agents who know this market'],
                'text' => ['type' => 'string', 'default' => 'Three demo profiles — buyers, sellers, and a commercial desk. Replace names and specialties with your own roster.'],
            ]),
        ],
        'acreline/area-grid' => [
            'render_callback' => __NAMESPACE__.'\\ks_render_area_grid',
            'attributes' => array_merge($typo, [
                'gridEyebrow' => ['type' => 'string', 'default' => 'Neighborhood by neighborhood'],
                'gridTitle' => ['type' => 'string', 'default' => 'Where the sample office works'],
                'gridText' => ['type' => 'string', 'default' => 'A quick read on six sample neighborhoods — housing mix, commute, and what a buyer should watch for.'],
                'area1Meta' => ['type' => 'string', 'default' => 'West ridge · established streets'],
                'area1Title' => ['type' => 'string', 'default' => 'North Ridge'],
                'area1Body' => ['type' => 'string', 'default' => 'Tree-lined blocks, family homes, and a short drive to the sample county seat. Buyers come for schools and porch-front streets.'],
                'area2Meta' => ['type' => 'string', 'default' => 'Creek corridor · parks and schools'],
                'area2Title' => ['type' => 'string', 'default' => 'Mill Creek'],
                'area2Body' => ['type' => 'string', 'default' => 'Mid-range houses, townhomes, and quiet cul-de-sacs. A practical commute and parks along the creek.'],
                'area3Meta' => ['type' => 'string', 'default' => 'Walkable core · shops and older homes'],
                'area3Title' => ['type' => 'string', 'default' => 'Oak Hollow'],
                'area3Body' => ['type' => 'string', 'default' => 'A compact main street, older houses, and condos above the shops. Best for buyers who want to walk to coffee.'],
                'area4Meta' => ['type' => 'string', 'default' => 'New construction · HOA streets'],
                'area4Title' => ['type' => 'string', 'default' => 'Riverbend'],
                'area4Body' => ['type' => 'string', 'default' => 'Builder homes, HOA amenities, and wide streets. Good for buyers who want low-maintenance and a predictable finish.'],
                'area5Meta' => ['type' => 'string', 'default' => 'Mixed-use · condos and storefronts'],
                'area5Title' => ['type' => 'string', 'default' => 'Midtown'],
                'area5Body' => ['type' => 'string', 'default' => 'Condos, lofts, and a few commercial suites. A sample urban-edge pocket inside the same county.'],
                'area6Meta' => ['type' => 'string', 'default' => 'First-home streets · townhomes'],
                'area6Title' => ['type' => 'string', 'default' => 'Southgate'],
                'area6Body' => ['type' => 'string', 'default' => 'Townhomes and modest single-family streets. Often the first stop for buyers stretching into the sample market.'],
                'area1ImageUrl' => ['type' => 'string', 'default' => ''],
                'area1ImageId' => ['type' => 'integer', 'default' => 0],
                'area1Cta' => ['type' => 'string', 'default' => ''],
                'area1Url' => ['type' => 'string', 'default' => ''],
                'area2ImageUrl' => ['type' => 'string', 'default' => ''],
                'area2ImageId' => ['type' => 'integer', 'default' => 0],
                'area2Cta' => ['type' => 'string', 'default' => ''],
                'area2Url' => ['type' => 'string', 'default' => ''],
                'area3ImageUrl' => ['type' => 'string', 'default' => ''],
                'area3ImageId' => ['type' => 'integer', 'default' => 0],
                'area3Cta' => ['type' => 'string', 'default' => ''],
                'area3Url' => ['type' => 'string', 'default' => ''],
                'area4ImageUrl' => ['type' => 'string', 'default' => ''],
                'area4ImageId' => ['type' => 'integer', 'default' => 0],
                'area4Cta' => ['type' => 'string', 'default' => ''],
                'area4Url' => ['type' => 'string', 'default' => ''],
                'area5ImageUrl' => ['type' => 'string', 'default' => ''],
                'area5ImageId' => ['type' => 'integer', 'default' => 0],
                'area5Cta' => ['type' => 'string', 'default' => ''],
                'area5Url' => ['type' => 'string', 'default' => ''],
                'area6ImageUrl' => ['type' => 'string', 'default' => ''],
                'area6ImageId' => ['type' => 'integer', 'default' => 0],
                'area6Cta' => ['type' => 'string', 'default' => ''],
                'area6Url' => ['type' => 'string', 'default' => ''],
                'gridCols' => ['type' => 'string', 'default' => '3'],
                'showIndex' => ['type' => 'boolean', 'default' => true],
                'cardStyle' => ['type' => 'string', 'default' => 'classic'],
                'bandStyle' => ['type' => 'string', 'default' => 'alt'],
                'headingLevel' => ['type' => 'string', 'default' => 'h2'],
                'sectionPad' => ['type' => 'string', 'default' => 'default'],
                'primaryLabel' => ['type' => 'string', 'default' => ''],
                'primaryUrl' => ['type' => 'string', 'default' => ''],
            ]),
        ],
        'acreline/tools-section' => [
            'render_callback' => __NAMESPACE__.'\\ks_render_tools_section',
            'attributes' => array_merge($typo, [
                // Intro paragraph (above the tools cards)
                'showIntro' => ['type' => 'boolean', 'default' => true],
                'introTitle' => ['type' => 'string', 'default' => 'What to sort before you write an offer'],
                'introText' => ['type' => 'string', 'default' => 'A finished house still needs a clear inspection path, HOA rules, and a payment you can live with. Use the sample tools below to plan — then book a showing with an agent who knows the neighborhood.'],
                // Tools header
                'eyebrow' => ['type' => 'string', 'default' => 'Run your numbers'],
                'title' => ['type' => 'string', 'default' => 'Payment and pre-qualification tools'],
                'text' => ['type' => 'string', 'default' => 'Friendly estimates to help you plan — not loan offers. A licensed lender will verify everything with full documentation.'],
                // Which tools to show
                'showLoanTool' => ['type' => 'boolean', 'default' => true],
                'showPrequalTool' => ['type' => 'boolean', 'default' => true],
                // Loan estimator labels
                'loanTitle' => ['type' => 'string', 'default' => 'Land loan estimator'],
                'loanLede' => ['type' => 'string', 'default' => 'Sample monthly payment — not a loan offer.'],
                'loanBtn' => ['type' => 'string', 'default' => 'Estimate payment'],
                // Pre-qual labels
                'prequalTitle' => ['type' => 'string', 'default' => 'Pre-qualification check'],
                'prequalLede' => ['type' => 'string', 'default' => 'Rough income check for land loans. Not a lender quote.'],
                'prequalBtn' => ['type' => 'string', 'default' => 'Check eligibility'],
                // Design
                'sectionStyle' => ['type' => 'string', 'default' => 'alt'],
                'panelStyle' => ['type' => 'string', 'default' => 'card'],
                'toolsLayout' => ['type' => 'string', 'default' => 'side'],
            ]),
        ],
        'acreline/how-we-work' => [
            'render_callback' => __NAMESPACE__.'\\ks_render_how_we_work',
            'attributes' => array_merge($typo, [
                'eyebrow' => ['type' => 'string', 'default' => 'How we work'],
                'title' => ['type' => 'string', 'default' => 'What working with this office looks like'],
                'text' => ['type' => 'string', 'default' => 'No pressure, no jargon, and a straight answer about the house, the block, and the next step.'],
                'stepsStyle' => ['type' => 'string', 'default' => 'numbered'],
                'stepsLayout' => ['type' => 'string', 'default' => 'row'],
                'bandStyle' => ['type' => 'string', 'default' => 'paper'],
                'headingLevel' => ['type' => 'string', 'default' => 'h2'],
                'sectionPad' => ['type' => 'string', 'default' => 'default'],
                'step1Title' => ['type' => 'string', 'default' => 'Listen first'],
                'step1Text' => ['type' => 'string', 'default' => 'We start with the neighborhood you want and the life you plan to build. No form to fill — just a conversation.'],
                'step2Title' => ['type' => 'string', 'default' => 'Match the inventory'],
                'step2Text' => ['type' => 'string', 'default' => 'We pull sample listings that match your area, budget, and type. Houses, condos, and commercial spaces are different products — we treat them that way.'],
                'step3Title' => ['type' => 'string', 'default' => 'Walk it with you'],
                'step3Text' => ['type' => 'string', 'default' => 'A real showing, not a drive-by. We walk the rooms, check the block, and explain what we see.'],
                'step4Title' => ['type' => 'string', 'default' => ''],
                'step4Text' => ['type' => 'string', 'default' => ''],
            ]),
        ],
        'acreline/office-info' => [
            'render_callback' => __NAMESPACE__.'\\ks_render_office_info',
            'attributes' => [
                'officeTitle' => ['type' => 'string', 'default' => ''],
                'showMap' => ['type' => 'boolean', 'default' => true],
                'infoLayout' => ['type' => 'string', 'default' => 'vertical'],
            ],
        ],
        'acreline/contact-form' => [
            'render_callback' => __NAMESPACE__.'\\ks_render_contact_form',
            'attributes' => [
                'formTitle' => ['type' => 'string', 'default' => 'Send us a message'],
                'formText' => ['type' => 'string', 'default' => "Tell us what you're looking for — or what you're thinking of selling — and we'll be in touch."],
            ],
        ],
        'acreline/book-note' => [
            'render_callback' => __NAMESPACE__.'\\ks_render_book_note',
            'attributes' => [
                'note' => ['type' => 'string', 'default' => 'Demo only — no emails, texts or calendar invites are sent. Staff can advance the booking in WP Admin → Bookings.'],
                'noteStyle' => ['type' => 'string', 'default' => 'plain'],
                'showSidePhoto' => ['type' => 'boolean', 'default' => false],
            ],
        ],
        'acreline/trust-strip' => [
            'render_callback' => __NAMESPACE__.'\\ks_render_trust_strip',
            'attributes' => array_merge($typo, ks_item_pair_attrs(4, 'item')),
        ],
        'acreline/checklist' => [
            'render_callback' => __NAMESPACE__.'\\ks_render_checklist',
            'attributes' => array_merge($typo, ks_item_pair_attrs(8, 'item'), [
                'eyebrow' => ['type' => 'string', 'default' => 'Before you make an offer'],
                'title' => ['type' => 'string', 'default' => 'The buyer checklist'],
                'text' => ['type' => 'string', 'default' => 'Eight questions to answer before you fall in love with the photos and the price tag.'],
                'primaryLabel' => ['type' => 'string', 'default' => 'Book a showing'],
                'primaryUrl' => ['type' => 'string', 'default' => ''],
                'secondaryLabel' => ['type' => 'string', 'default' => 'Browse listings'],
                'secondaryUrl' => ['type' => 'string', 'default' => ''],
            ]),
        ],
        'acreline/prep-checklist' => [
            'render_callback' => __NAMESPACE__.'\\ks_render_prep_checklist',
            'attributes' => array_merge(
                $typo,
                ks_item_pair_attrs(5, 'left'),
                ks_item_pair_attrs(4, 'right'),
                [
                    'leftHeading' => ['type' => 'string', 'default' => 'Come prepared'],
                    'leftLead' => ['type' => 'string', 'default' => 'A showing is not a photo scroll. Here is what makes yours worth the trip.'],
                    'rightHeading' => ['type' => 'string', 'default' => 'What your agent brings'],
                    'rightLead' => ['type' => 'string', 'default' => 'Preparation goes both ways. Your assigned specialist arrives ready.'],
                ]
            ),
        ],
        'acreline/compare-table' => [
            'render_callback' => __NAMESPACE__.'\\ks_render_compare_table',
            'attributes' => array_merge($typo, [
                'eyebrow' => ['type' => 'string', 'default' => 'Side-by-side'],
                'title' => ['type' => 'string', 'default' => 'Neighborhood comparison at a glance'],
                'text' => ['type' => 'string', 'default' => 'Typical ranges for sample concept listings — actual prices vary by size, condition, and seasonal market.'],
                'disclaimer' => ['type' => 'string', 'default' => 'All figures are sample ranges for concept demonstration only. Not real MLS data or licensed appraisal values.'],
                'col1' => ['type' => 'string', 'default' => 'Area'],
                'col2' => ['type' => 'string', 'default' => 'Housing mix'],
                'col3' => ['type' => 'string', 'default' => 'Typical price range'],
                'col4' => ['type' => 'string', 'default' => 'Commute / lifestyle'],
                'col5' => ['type' => 'string', 'default' => 'Best for'],
                'row1Col1' => ['type' => 'string', 'default' => 'North Ridge'],
                'row1Col2' => ['type' => 'string', 'default' => 'Family homes, tree streets'],
                'row1Col3' => ['type' => 'string', 'default' => '$320K–$640K'],
                'row1Col4' => ['type' => 'string', 'default' => '15 min to sample seat'],
                'row1Col5' => ['type' => 'string', 'default' => 'Schools, established blocks'],
                'row2Col1' => ['type' => 'string', 'default' => 'Mill Creek'],
                'row2Col2' => ['type' => 'string', 'default' => 'Houses and townhomes'],
                'row2Col3' => ['type' => 'string', 'default' => '$240K–$490K'],
                'row2Col4' => ['type' => 'string', 'default' => 'Parks, mid commute'],
                'row2Col5' => ['type' => 'string', 'default' => 'Value buyers, families'],
                'row3Col1' => ['type' => 'string', 'default' => 'Oak Hollow'],
                'row3Col2' => ['type' => 'string', 'default' => 'Older homes, condos, shops'],
                'row3Col3' => ['type' => 'string', 'default' => '$275K–$580K'],
                'row3Col4' => ['type' => 'string', 'default' => 'Walkable core'],
                'row3Col5' => ['type' => 'string', 'default' => 'Main-street buyers'],
                'row4Col1' => ['type' => 'string', 'default' => 'Riverbend'],
                'row4Col2' => ['type' => 'string', 'default' => 'New construction, HOA'],
                'row4Col3' => ['type' => 'string', 'default' => '$360K–$720K'],
                'row4Col4' => ['type' => 'string', 'default' => 'Amenities, longer drive'],
                'row4Col5' => ['type' => 'string', 'default' => 'Low-maintenance buyers'],
                'row5Col1' => ['type' => 'string', 'default' => 'Midtown'],
                'row5Col2' => ['type' => 'string', 'default' => 'Condos, lofts, storefronts'],
                'row5Col3' => ['type' => 'string', 'default' => '$210K–$540K'],
                'row5Col4' => ['type' => 'string', 'default' => 'Urban-edge, transit'],
                'row5Col5' => ['type' => 'string', 'default' => 'Condos, commercial'],
                'row6Col1' => ['type' => 'string', 'default' => 'Southgate'],
                'row6Col2' => ['type' => 'string', 'default' => 'Townhomes, starter houses'],
                'row6Col3' => ['type' => 'string', 'default' => '$185K–$395K'],
                'row6Col4' => ['type' => 'string', 'default' => 'Easier entry price'],
                'row6Col5' => ['type' => 'string', 'default' => 'First-time buyers'],
            ]),
        ],
        'acreline/topic-cards' => [
            'render_callback' => __NAMESPACE__.'\\ks_render_topic_cards',
            'attributes' => array_merge($typo, [
                'eyebrow' => ['type' => 'string', 'default' => 'What these notes cover'],
                'title' => ['type' => 'string', 'default' => 'Short reads you can adapt for your market'],
                'text' => ['type' => 'string', 'default' => 'Showings, first-time checklists, and neighborhood search — the three posts buyers actually ask for. Use them as local SEO starters, then link back to listings and the booking form.'],
                'card1Kicker' => ['type' => 'string', 'default' => 'Showings'],
                'card1Title' => ['type' => 'string', 'default' => 'How a tour should feel'],
                'card1Text' => ['type' => 'string', 'default' => 'What to book, how long to plan, and why a house tour is not a 20-minute scroll.'],
                'card2Kicker' => ['type' => 'string', 'default' => 'Checklists'],
                'card2Title' => ['type' => 'string', 'default' => 'First-time buyers'],
                'card2Text' => ['type' => 'string', 'default' => 'Payment, inspection, and HOA questions in an order you can scan before you call.'],
                'card3Kicker' => ['type' => 'string', 'default' => 'Search'],
                'card3Title' => ['type' => 'string', 'default' => 'House vs condo vs commercial'],
                'card3Text' => ['type' => 'string', 'default' => 'Different card hierarchy so house shoppers and commercial tenants do not share one muddy filter.'],
            ]),
        ],
        'acreline/post-grid' => [
            'render_callback' => __NAMESPACE__.'\\ks_render_post_grid',
            'attributes' => [
                'emptyText' => ['type' => 'string', 'default' => 'Sample posts load with Tools → Seed Acreline demo.'],
            ],
        ],
        'acreline/region-coverage' => [
            'render_callback' => __NAMESPACE__.'\\ks_render_region_coverage',
            'attributes' => array_merge($typo, ks_region_area_attrs(), [
                'eyebrow' => ['type' => 'string', 'default' => 'Area service'],
                'title' => ['type' => 'string', 'default' => 'How we cover the region'],
                'text' => ['type' => 'string', 'default' => 'Neighborhood-first coverage across North Ridge, Mill Creek, and Oak Hollow — one sample office, local specialists, and a showing on the calendar.'],
                'layout' => ['type' => 'string', 'default' => 'split'],
                'bandStyle' => ['type' => 'string', 'default' => 'alt'],
                'headingLevel' => ['type' => 'string', 'default' => 'h2'],
                'sectionPad' => ['type' => 'string', 'default' => 'default'],
                'iconStyle' => ['type' => 'string', 'default' => 'pin'],
                'areaCols' => ['type' => 'string', 'default' => '3'],
                'showMap' => ['type' => 'boolean', 'default' => true],
                'showMethods' => ['type' => 'boolean', 'default' => true],
                'showIndex' => ['type' => 'boolean', 'default' => true],
                'showSchema' => ['type' => 'boolean', 'default' => true],
                'maxAreas' => ['type' => 'integer', 'default' => 3],
                'primaryLabel' => ['type' => 'string', 'default' => 'Browse neighborhoods'],
                'primaryUrl' => ['type' => 'string', 'default' => ''],
                'secondaryLabel' => ['type' => 'string', 'default' => 'Book a showing'],
                'secondaryUrl' => ['type' => 'string', 'default' => ''],
                'method1Title' => ['type' => 'string', 'default' => 'Listen first'],
                'method1Text' => ['type' => 'string', 'default' => 'We start with the neighborhood you want and the commute you will actually make.'],
                'method2Title' => ['type' => 'string', 'default' => 'Match inventory'],
                'method2Text' => ['type' => 'string', 'default' => 'Houses, condos, and commercial spaces are different products — we filter that way.'],
                'method3Title' => ['type' => 'string', 'default' => 'Walk the property'],
                'method3Text' => ['type' => 'string', 'default' => 'A scheduled showing with the specialist who covers that part of the sample county.'],
            ]),
        ],
        'acreline/pricing-plans' => [
            'render_callback' => __NAMESPACE__.'\\ks_render_pricing_plans',
            'attributes' => array_merge($typo, [
                'eyebrow' => ['type' => 'string', 'default' => 'Ways to work together'],
                'title' => ['type' => 'string', 'default' => 'Sample plans for buyers and sellers'],
                'text' => ['type' => 'string', 'default' => 'Concept packages so a brokerage can show how representation is priced — replace the numbers with yours.'],
                'bandStyle' => ['type' => 'string', 'default' => 'paper'],
                'headingLevel' => ['type' => 'string', 'default' => 'h2'],
                'sectionPad' => ['type' => 'string', 'default' => 'default'],
                'planCols' => ['type' => 'string', 'default' => '3'],
                'plan1Kicker' => ['type' => 'string', 'default' => 'Buyers'],
                'plan1Title' => ['type' => 'string', 'default' => 'Buyer consult'],
                'plan1Price' => ['type' => 'string', 'default' => 'Complimentary'],
                'plan1Period' => ['type' => 'string', 'default' => ''],
                'plan1Text' => ['type' => 'string', 'default' => 'A neighborhood match and a sample showing calendar — no obligation.'],
                'plan1Features' => ['type' => 'string', 'default' => "Neighborhood shortlist\nSaved-search setup\nOne accompanied showing\nSame-day follow-up"],
                'plan1Cta' => ['type' => 'string', 'default' => 'Book a consult'],
                'plan1Url' => ['type' => 'string', 'default' => ''],
                'plan1Featured' => ['type' => 'boolean', 'default' => false],
                'plan2Kicker' => ['type' => 'string', 'default' => 'Sellers'],
                'plan2Title' => ['type' => 'string', 'default' => 'Listing launch'],
                'plan2Price' => ['type' => 'string', 'default' => '2.5%'],
                'plan2Period' => ['type' => 'string', 'default' => 'sample rate'],
                'plan2Text' => ['type' => 'string', 'default' => 'Pricing strategy, photos, and a launch week on the sample calendar.'],
                'plan2Features' => ['type' => 'string', 'default' => "Pricing consult\nPhoto + copy package\nOpen-house plan\nWeekly seller update"],
                'plan2Cta' => ['type' => 'string', 'default' => 'Talk listing prep'],
                'plan2Url' => ['type' => 'string', 'default' => ''],
                'plan2Featured' => ['type' => 'boolean', 'default' => true],
                'plan3Kicker' => ['type' => 'string', 'default' => 'Relocation'],
                'plan3Title' => ['type' => 'string', 'default' => 'Relocation desk'],
                'plan3Price' => ['type' => 'string', 'default' => 'Custom'],
                'plan3Period' => ['type' => 'string', 'default' => ''],
                'plan3Text' => ['type' => 'string', 'default' => 'One point of contact for a move into the sample county — buyers or sellers.'],
                'plan3Features' => ['type' => 'string', 'default' => "Remote video tours\nNeighborhood brief\nLender + inspector intros\nClosing-week checklist"],
                'plan3Cta' => ['type' => 'string', 'default' => 'Start a move plan'],
                'plan3Url' => ['type' => 'string', 'default' => ''],
                'plan3Featured' => ['type' => 'boolean', 'default' => false],
            ]),
        ],
        'acreline/logo-strip' => [
            'render_callback' => __NAMESPACE__.'\\ks_render_logo_strip',
            'attributes' => array_merge($typo, [
                'eyebrow' => ['type' => 'string', 'default' => 'Local partners'],
                'title' => ['type' => 'string', 'default' => 'The sample county desk we call'],
                'text' => ['type' => 'string', 'default' => 'Concept partner names — swap in your lenders, title shops, and inspectors.'],
                'bandStyle' => ['type' => 'string', 'default' => 'paper'],
                'headingLevel' => ['type' => 'string', 'default' => 'h2'],
                'sectionPad' => ['type' => 'string', 'default' => 'compact'],
                'grayscale' => ['type' => 'boolean', 'default' => true],
                'logo1Label' => ['type' => 'string', 'default' => 'Sample Credit Union'],
                'logo1Url' => ['type' => 'string', 'default' => ''],
                'logo1ImageUrl' => ['type' => 'string', 'default' => ''],
                'logo1ImageId' => ['type' => 'integer', 'default' => 0],
                'logo2Label' => ['type' => 'string', 'default' => 'County Title Co.'],
                'logo2Url' => ['type' => 'string', 'default' => ''],
                'logo2ImageUrl' => ['type' => 'string', 'default' => ''],
                'logo2ImageId' => ['type' => 'integer', 'default' => 0],
                'logo3Label' => ['type' => 'string', 'default' => 'North Ridge Inspect'],
                'logo3Url' => ['type' => 'string', 'default' => ''],
                'logo3ImageUrl' => ['type' => 'string', 'default' => ''],
                'logo3ImageId' => ['type' => 'integer', 'default' => 0],
                'logo4Label' => ['type' => 'string', 'default' => 'Mill Creek Lending'],
                'logo4Url' => ['type' => 'string', 'default' => ''],
                'logo4ImageUrl' => ['type' => 'string', 'default' => ''],
                'logo4ImageId' => ['type' => 'integer', 'default' => 0],
                'logo5Label' => ['type' => 'string', 'default' => 'Oak Hollow Photo'],
                'logo5Url' => ['type' => 'string', 'default' => ''],
                'logo5ImageUrl' => ['type' => 'string', 'default' => ''],
                'logo5ImageId' => ['type' => 'integer', 'default' => 0],
                'logo6Label' => ['type' => 'string', 'default' => 'Borough Insurance'],
                'logo6Url' => ['type' => 'string', 'default' => ''],
                'logo6ImageUrl' => ['type' => 'string', 'default' => ''],
                'logo6ImageId' => ['type' => 'integer', 'default' => 0],
            ]),
        ],
        'acreline/newsletter' => [
            'render_callback' => __NAMESPACE__.'\\ks_render_newsletter',
            'attributes' => array_merge($typo, [
                'eyebrow' => ['type' => 'string', 'default' => 'New listings'],
                'title' => ['type' => 'string', 'default' => 'Get the weekly sample market note'],
                'text' => ['type' => 'string', 'default' => 'A short digest of new addresses in North Ridge, Mill Creek, and Oak Hollow. Demo only — nothing is emailed.'],
                'placeholder' => ['type' => 'string', 'default' => 'you@acreline-concept.test'],
                'buttonLabel' => ['type' => 'string', 'default' => 'Join the list'],
                'note' => ['type' => 'string', 'default' => 'Concept capture — confirmation stays on this page.'],
                'bandStyle' => ['type' => 'string', 'default' => 'accent'],
                'headingLevel' => ['type' => 'string', 'default' => 'h2'],
                'sectionPad' => ['type' => 'string', 'default' => 'default'],
            ]),
        ],
        'acreline/custom' => [
            'render_callback' => __NAMESPACE__.'\\ks_render_custom_block',
            'attributes' => [
                'blockId' => ['type' => 'string', 'default' => ''],
                'fields' => ['type' => 'object', 'default' => new \stdClass],
            ],
        ],
    ];

    foreach ($blocks as $name => $args) {
        register_block_type($name, $args);
    }
}

// ---------------------------------------------------------------------------
// PHP render helpers — apply attribute-driven classes to block HTML
// ---------------------------------------------------------------------------

/**
 * Build section-head CSS classes from typography attributes.
 * Adds to a base string like 'section-head left reveal'.
 */
function ks_head_class(array $attrs, string $base = 'section-head left reveal'): string
{
    $cls = $base;

    $align = sanitize_key((string) ($attrs['headingAlign'] ?? 'left'));
    if ($align === 'center') {
        $cls = str_replace([' left', ' center'], '', $cls).' center ks-head--center';
    }

    $size = sanitize_key((string) ($attrs['headingSize'] ?? 'default'));
    if ($size !== 'default') {
        $cls .= ' ks-head--size-'.$size;
    }

    $weight = sanitize_key((string) ($attrs['headingWeight'] ?? 'default'));
    if ($weight !== 'default') {
        $cls .= ' ks-head--weight-'.$weight;
    }

    $bodySize = sanitize_key((string) ($attrs['bodySize'] ?? 'default'));
    if ($bodySize !== 'default') {
        $cls .= ' ks-body--'.$bodySize;
    }

    return trim($cls);
}

/**
 * Section background + vertical pad from bandStyle / sectionPad.
 */
function ks_band_section_class(array $attrs, string $base = 'section'): string
{
    $style = sanitize_key((string) ($attrs['bandStyle'] ?? 'paper'));
    $cls = $base;
    if ($style === 'alt') {
        $cls .= ' section-alt';
    } elseif ($style === 'accent') {
        $cls .= ' ks-section--accent';
    } elseif ($style === 'dark') {
        $cls .= ' ks-section--dark';
    }

    $pad = sanitize_key((string) ($attrs['sectionPad'] ?? 'default'));
    if (in_array($pad, ['compact', 'tall'], true)) {
        $cls .= ' ks-pad--'.$pad;
    }

    return $cls;
}

/**
 * Heading tag from headingLevel (h2 / h3 / h4).
 */
function ks_heading_tag(array $attrs, string $default = 'h2'): string
{
    $tag = strtolower((string) ($attrs['headingLevel'] ?? $default));

    return in_array($tag, ['h2', 'h3', 'h4'], true) ? $tag : $default;
}

/**
 * @return array<string, array{type: string, default: string}>
 */
function ks_region_area_attrs(): array
{
    $defaults = [
        1 => ['West ridge · established streets', 'North Ridge', 'Tree-lined blocks, family homes, and a short drive to the sample county seat. Buyers come for schools and porch-front streets.', '12 active'],
        2 => ['Creek corridor · parks and schools', 'Mill Creek', 'Mid-range houses, townhomes, and quiet cul-de-sacs. A practical commute and parks along the creek.', '9 active'],
        3 => ['Walkable core · shops and older homes', 'Oak Hollow', 'A compact main street, older houses, and condos above the shops. Best for buyers who want to walk to coffee.', '7 active'],
        4 => ['New construction · HOA streets', 'Riverbend', 'Builder homes, HOA amenities, and wide streets. Good for buyers who want low-maintenance and a predictable finish.', '6 active'],
        5 => ['Mixed-use · condos and storefronts', 'Midtown', 'Condos, lofts, and a few commercial suites. A sample urban-edge pocket inside the same county.', '5 active'],
        6 => ['First-home streets · townhomes', 'Southgate', 'Townhomes and modest single-family streets. Often the first stop for buyers stretching into the sample market.', '8 active'],
    ];
    $attrs = [];
    for ($i = 1; $i <= 6; $i++) {
        $attrs["area{$i}Kicker"] = ['type' => 'string', 'default' => $defaults[$i][0]];
        $attrs["area{$i}Title"] = ['type' => 'string', 'default' => $defaults[$i][1]];
        $attrs["area{$i}Body"] = ['type' => 'string', 'default' => $defaults[$i][2]];
        $attrs["area{$i}Stat"] = ['type' => 'string', 'default' => $defaults[$i][3]];
        $attrs["area{$i}Url"] = ['type' => 'string', 'default' => ''];
    }

    return $attrs;
}

/**
 * Build hero section CSS class string from hero-specific attributes.
 */
function ks_hero_class(array $attrs, string $base = 'hero'): string
{
    $cls = $base;

    $height = sanitize_key((string) ($attrs['heroHeight'] ?? 'default'));
    if (in_array($height, ['compact', 'tall'], true)) {
        $cls .= ' ks-hero--'.$height;
    }

    $align = sanitize_key((string) ($attrs['textAlign'] ?? 'left'));
    if ($align === 'center') {
        $cls .= ' ks-hero--center';
    }

    $overlay = sanitize_key((string) ($attrs['overlayPreset'] ?? 'default'));
    if (in_array($overlay, ['light', 'dark'], true)) {
        $cls .= ' ks-overlay--'.$overlay;
    }

    $imgPos = sanitize_key((string) ($attrs['imagePosition'] ?? 'center'));
    if (in_array($imgPos, ['top', 'bottom', 'left', 'right'], true)) {
        $cls .= ' img-pos--'.$imgPos;
    }

    return trim($cls);
}

/**
 * Resolve hero image URL: stored URL, attachment ID, featured image, then theme default.
 */
function ks_hero_image_url(array $attrs, int $postId = 0): string
{
    $url = esc_url_raw((string) ($attrs['imageUrl'] ?? ''));
    if ($url !== '') {
        return $url;
    }

    $id = (int) ($attrs['imageId'] ?? 0);
    if ($id > 0) {
        $fromId = wp_get_attachment_image_url($id, 'full');
        if (is_string($fromId) && $fromId !== '') {
            return $fromId;
        }
    }

    $thumbId = $postId > 0 ? get_post_thumbnail_id($postId) : get_post_thumbnail_id();
    if ($thumbId) {
        $fromThumb = wp_get_attachment_image_url((int) $thumbId, 'full');
        if (is_string($fromThumb) && $fromThumb !== '') {
            return $fromThumb;
        }
    }

    $bundled = $postId > 0 ? HeroImage::bundledUrl((string) get_post_field('post_name', $postId)) : '';

    return $bundled !== '' ? $bundled : HeroImage::url();
}

/**
 * URL + srcset for a hero <img>. Always returns a usable photo (theme default if unset).
 *
 * @return array{url: string, srcset: string, alt: string}
 */
function ks_hero_media(array $attrs, int $postId = 0, string $context = ''): array
{
    return HeroImage::forRequest(ks_hero_image_url($attrs, $postId), $context);
}

/**
 * Inline style string for the hero veil at a custom opacity (0-100).
 */
function ks_veil_style(array $attrs): string
{
    $opacity = max(0, min(100, (int) ($attrs['overlayOpacity'] ?? 60)));
    if ($opacity === 60) {
        return '';
    }

    return 'opacity:'.number_format($opacity / 100, 2);
}

/**
 * Map a block button-style slug to the correct CSS class string.
 *
 * @param  string  $style  Slug from the editor's SelectControl.
 * @param  string  $extra  Any additional classes to append (e.g. 'btn-block').
 */
function ks_btn_class(string $style, string $extra = ''): string
{
    $map = [
        'primary' => 'btn btn-primary',
        'outline-light' => 'btn btn-outline light',
        'outline' => 'btn btn-outline',
        'white' => 'btn btn-white',
        'gold' => 'btn btn-gold',
        'ghost' => 'btn btn-ghost',
    ];
    $cls = $map[$style] ?? 'btn btn-primary';

    return $extra ? $cls.' '.$extra : $cls;
}

// ---------------------------------------------------------------------------
// Editor script — load blocks.js in the block editor
// ---------------------------------------------------------------------------
add_action('enqueue_block_editor_assets', function (): void {
    $manifestPath = get_template_directory().'/public/build/manifest.json';
    if (! file_exists($manifestPath)) {
        return;
    }

    $manifest = json_decode((string) file_get_contents($manifestPath), true);
    if (! is_array($manifest)) {
        return;
    }

    $key = 'resources/js/blocks/index.js';
    if (! isset($manifest[$key]['file'])) {
        return;
    }

    $url = get_template_directory_uri().'/public/build/'.$manifest[$key]['file'];
    $deps = $manifest[$key]['imports'] ?? [];

    // Map Vite chunk refs to WP script handles.
    $wpDeps = ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-hooks'];

    $depsFile = get_template_directory().'/public/build/assets/blocks.deps.json';
    if (file_exists($depsFile)) {
        $fromDeps = json_decode((string) file_get_contents($depsFile), true);
        if (is_array($fromDeps)) {
            $wpDeps = array_unique(array_merge($wpDeps, $fromDeps));
        }
    }

    wp_register_script(
        'acreline-blocks',
        $url,
        $wpDeps,
        null,
        true
    );

    wp_localize_script('acreline-blocks', 'ACRELINE_BLOCKS', [
        'themeUri' => get_template_directory_uri(),
        'customBlocks' => ks_get_custom_block_definitions(),
        'restUrl' => rest_url('acreline/v1/'),
        'nonce' => wp_create_nonce('wp_rest'),
    ]);

    wp_enqueue_script('acreline-blocks');
});

// ---------------------------------------------------------------------------
// Block patterns — starter layouts per page type
// ---------------------------------------------------------------------------
add_action('init', function (): void {
    if (! function_exists('register_block_pattern')) {
        return;
    }

    register_block_pattern('acreline/home-page', [
        'title' => __('Acreline — Home page', 'acreline'),
        'description' => __('Homepage: hero, intent, spotlight, region coverage, booking, stats, plans, newsletter, and CTA.', 'acreline'),
        'categories' => ['acreline'],
        'content' => ks_home_page_pattern(),
    ]);

    register_block_pattern('acreline/listings-page', [
        'title' => __('Acreline — Listings page', 'acreline'),
        'description' => __('Listings page: hero, filter grid, market stats, reviews, FAQ, CTA.', 'acreline'),
        'categories' => ['acreline'],
        'content' => ks_listings_page_pattern(),
    ]);

    register_block_pattern('acreline/areas-page', [
        'title' => __('Acreline — Areas page', 'acreline'),
        'description' => __('Areas page: hero, neighborhood grid, comparison, region coverage, newsletter, CTA.', 'acreline'),
        'categories' => ['acreline'],
        'content' => ks_areas_page_pattern(),
    ]);

    register_block_pattern('acreline/guide-page', [
        'title' => __('Acreline — Guide page', 'acreline'),
        'description' => __('Buyer guide: hero, tools, how-it-works, checklist, FAQ, reviews, CTA.', 'acreline'),
        'categories' => ['acreline'],
        'content' => ks_guide_page_pattern(),
    ]);

    register_block_pattern('acreline/agents-page', [
        'title' => __('Acreline — Agents page', 'acreline'),
        'description' => __('Agents page: hero, roster, pricing plans, reviews, process, partners, CTA.', 'acreline'),
        'categories' => ['acreline'],
        'content' => ks_agents_page_pattern(),
    ]);

    register_block_pattern('acreline/contact-page', [
        'title' => __('Acreline — Contact page', 'acreline'),
        'description' => __('Contact page: hero, form, office info, trust strip, agent list, CTA.', 'acreline'),
        'categories' => ['acreline'],
        'content' => ks_contact_page_pattern(),
    ]);

    register_block_pattern('acreline/book-page', [
        'title' => __('Acreline — Book a showing', 'acreline'),
        'description' => __('Booking page: hero, note + form, prep checklist, FAQ, CTA.', 'acreline'),
        'categories' => ['acreline'],
        'content' => ks_book_page_pattern(),
    ]);

    register_block_pattern('acreline/blog-page', [
        'title' => __('Acreline — Blog page', 'acreline'),
        'description' => __('Blog index: hero, topic cards, post grid, CTA.', 'acreline'),
        'categories' => ['acreline'],
        'content' => ks_blog_page_pattern(),
    ]);
}, 11);

// ---------------------------------------------------------------------------
// Sync block attributes → ks_* meta so SEO/Seo.php stays fresh after saves
// ---------------------------------------------------------------------------
add_action('save_post_page', function (int $postId): void {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (! current_user_can('edit_post', $postId)) {
        return;
    }

    $post = get_post($postId);
    if (! $post instanceof \WP_Post || ! str_contains($post->post_content, '<!-- wp:')) {
        return;
    }

    if (! function_exists('parse_blocks')) {
        return;
    }

    $blocks = parse_blocks($post->post_content);

    foreach ($blocks as $block) {
        $name = $block['blockName'] ?? '';
        $attrs = $block['attrs'] ?? [];

        if (in_array($name, ['acreline/home-hero', 'acreline/page-hero'], true)) {
            $syncMap = [
                'eyebrow' => 'hero_eyebrow',
                'title' => 'hero_title',
                'text' => 'hero_text',
                'imageUrl' => 'hero_image',
            ];
            foreach ($syncMap as $attrKey => $metaKey) {
                if (isset($attrs[$attrKey])) {
                    Catalog::updateMeta($postId, $metaKey, (string) $attrs[$attrKey]);
                }
            }
            break;
        }
    }
}, 10);

// ---------------------------------------------------------------------------
// Migration admin page — Tools → Migrate to Blocks
// ---------------------------------------------------------------------------
add_action('admin_menu', function (): void {
    add_management_page(
        __('Migrate to Blocks', 'acreline'),
        __('Migrate to Blocks', 'acreline'),
        'manage_options',
        'ks-migrate-blocks',
        __NAMESPACE__.'\\ks_migration_page'
    );
});

function ks_migration_page(): void
{
    if (! current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have permission to access this page.', 'acreline'));
    }

    $result = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ks_migrate_nonce'])) {
        if (! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['ks_migrate_nonce'])), 'ks_migrate')) {
            wp_die(esc_html__('Security check failed.', 'acreline'));
        }
        $action = sanitize_key((string) ($_POST['ks_action'] ?? ''));

        if ($action === 'reset') {
            BlockMigration::resetMigrationRecord();
            $result = ['type' => 'info', 'msg' => __('Migration record cleared. Pages will be re-processed on next migration run.', 'acreline')];
        } elseif ($action === 'rebuild') {
            $rebuilt = BlockMigration::forceRebuildAll();
            $result = [
                'type' => 'info',
                'msg' => sprintf(
                    /* translators: 1: updated count, 2: error count */
                    __('Force rebuild complete — %1$d page(s) updated, %2$d error(s).', 'acreline'),
                    $rebuilt['updated'],
                    count($rebuilt['errors'])
                ).(empty($rebuilt['errors']) ? '' : ' '.implode(' ', $rebuilt['errors'])),
            ];
        } else {
            $result = BlockMigration::migrateAll();
        }
    }
    ?>
    <div class="wrap">
      <h1><?php esc_html_e('Migrate Pages to Blocks', 'acreline'); ?></h1>
      <p><?php esc_html_e('This tool converts legacy ks_* post meta on each page into Gutenberg block content. Regular migration skips pages that already contain block markup. Use Force rebuild to overwrite marketing pages with the current BlockMigration stacks (agent list, FAQ, stats, checklists, and the rest).', 'acreline'); ?></p>

      <?php if (is_array($result) && isset($result['migrated'])) { ?>
        <div class="notice notice-success is-dismissible">
          <p><?php echo esc_html(sprintf(
              /* translators: 1: migrated count, 2: skipped count */
              __('Migration complete — %1$d page(s) migrated, %2$d skipped.', 'acreline'),
              $result['migrated'],
              $result['skipped']
          )); ?></p>
        </div>
      <?php } elseif (is_array($result) && isset($result['type'])) { ?>
        <div class="notice notice-info is-dismissible"><p><?php echo esc_html($result['msg'] ?? ''); ?></p></div>
      <?php } ?>

      <form method="post" style="display:inline">
        <?php wp_nonce_field('ks_migrate', 'ks_migrate_nonce'); ?>
        <input type="hidden" name="ks_action" value="migrate">
        <button type="submit" class="button button-primary"><?php esc_html_e('Run migration', 'acreline'); ?></button>
      </form>
      &nbsp;
      <form method="post" style="display:inline">
        <?php wp_nonce_field('ks_migrate', 'ks_migrate_nonce'); ?>
        <input type="hidden" name="ks_action" value="reset">
        <button type="submit" class="button button-secondary"><?php esc_html_e('Reset migration record', 'acreline'); ?></button>
      </form>
      &nbsp;
      <form method="post" style="display:inline" onsubmit="return confirm('<?php echo esc_js(__('This overwrites the current Gutenberg content on Home, Listings, Areas, Guide, Agents, Contact, Book, and Blog. Continue?', 'acreline')); ?>');">
        <?php wp_nonce_field('ks_migrate', 'ks_migrate_nonce'); ?>
        <input type="hidden" name="ks_action" value="rebuild">
        <button type="submit" class="button"><?php esc_html_e('Force rebuild all pages', 'acreline'); ?></button>
      </form>

      <hr>
      <h2><?php esc_html_e('What happens', 'acreline'); ?></h2>
      <ol>
        <li><?php esc_html_e('Each page\'s ks_* meta is read and mapped to block attributes.', 'acreline'); ?></li>
        <li><?php esc_html_e('The page\'s post_content is set to the serialized block markup.', 'acreline'); ?></li>
        <li><?php esc_html_e('Pages with existing block markup are skipped to avoid double-migration.', 'acreline'); ?></li>
        <li><?php esc_html_e('The old ks_* meta remains and is not deleted — it is simply superseded by block attributes.', 'acreline'); ?></li>
        <li><?php esc_html_e('After migration, open each page in the editor to review and fine-tune the blocks.', 'acreline'); ?></li>
      </ol>
    </div>
    <?php
}

// ---------------------------------------------------------------------------
// REST endpoint for custom block definitions
// ---------------------------------------------------------------------------
add_action('rest_api_init', function (): void {
    register_rest_route('acreline/v1', '/custom-blocks', [
        'methods' => 'GET',
        'callback' => fn () => rest_ensure_response(ks_get_custom_block_definitions()),
        'permission_callback' => fn () => current_user_can('edit_posts'),
    ]);

    register_rest_route('acreline/v1', '/custom-blocks', [
        'methods' => 'POST',
        'callback' => function (\WP_REST_Request $request) {
            $defs = ks_get_custom_block_definitions();
            $body = $request->get_json_params();
            $id = sanitize_key((string) ($body['id'] ?? uniqid('block_')));
            if ($id === '') {
                return new \WP_Error('invalid', 'Block id is required.', ['status' => 400]);
            }
            $defs[$id] = [
                'id' => $id,
                'title' => sanitize_text_field((string) ($body['title'] ?? $id)),
                'description' => sanitize_text_field((string) ($body['description'] ?? '')),
                'icon' => sanitize_key((string) ($body['icon'] ?? 'star-filled')),
                'fields' => ks_sanitize_block_fields((array) ($body['fields'] ?? [])),
            ];
            update_option('ks_custom_blocks', $defs, false);

            return rest_ensure_response(['success' => true, 'block' => $defs[$id]]);
        },
        'permission_callback' => fn () => current_user_can('manage_options'),
    ]);

    register_rest_route('acreline/v1', '/custom-blocks/(?P<id>[a-z0-9_]+)', [
        'methods' => 'DELETE',
        'callback' => function (\WP_REST_Request $request) {
            $id = sanitize_key((string) $request['id']);
            $defs = ks_get_custom_block_definitions();
            unset($defs[$id]);
            update_option('ks_custom_blocks', $defs, false);

            return rest_ensure_response(['success' => true]);
        },
        'permission_callback' => fn () => current_user_can('manage_options'),
    ]);
});

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

/** @return array<string, array<string, mixed>> */
function ks_get_custom_block_definitions(): array
{
    $defs = get_option('ks_custom_blocks', []);

    return is_array($defs) ? $defs : [];
}

/**
 * @param  array<mixed>  $fields
 * @return list<array{name: string, label: string, type: string, default: string}>
 */
function ks_sanitize_block_fields(array $fields): array
{
    $out = [];
    foreach ($fields as $field) {
        if (! is_array($field)) {
            continue;
        }
        $type = sanitize_key((string) ($field['type'] ?? 'text'));
        if (! in_array($type, ['text', 'textarea', 'url', 'image', 'select', 'toggle'], true)) {
            $type = 'text';
        }
        $out[] = [
            'name' => sanitize_key((string) ($field['name'] ?? '')),
            'label' => sanitize_text_field((string) ($field['label'] ?? '')),
            'type' => $type,
            'default' => sanitize_text_field((string) ($field['default'] ?? '')),
        ];
    }

    return $out;
}

// ---------------------------------------------------------------------------
// Render callbacks
// ---------------------------------------------------------------------------

/** @param array<string, mixed> $attrs */
function ks_render_home_hero(array $attrs): string
{
    $identity = Identity::toArray();
    $listings = Catalog::listings();
    $townships = count(array_unique(array_filter(array_column($listings, 'township'))));
    $count = count($listings);

    $eyebrow = esc_html($attrs['eyebrow'] ?? 'Homes, neighborhoods, and local agents');
    $title = wp_kses($attrs['title'] ?? 'Homes worth <em>walking through.</em>', ['em' => [], 'strong' => []]);
    $text = wp_kses($attrs['text'] ?? '', ['em' => [], 'strong' => [], 'br' => []]);
    $primary = esc_html($attrs['primaryLabel'] ?? 'Show matches');
    $secondary = esc_html($attrs['secondaryLabel'] ?? 'Browse all listings');
    $heroImg = ks_hero_media($attrs, (int) get_the_ID(), 'home');
    $imgUrl = esc_url($heroImg['url']);
    $imgSrcset = esc_attr($heroImg['srcset']);
    $heroClass = esc_attr(ks_hero_class($attrs, 'hero'));
    $veilStyle = ks_veil_style($attrs);
    $primaryBtnClass = esc_attr(ks_btn_class(sanitize_key((string) ($attrs['primaryBtnStyle'] ?? 'primary'))));
    $bookUrl = esc_url(home_url('/book/'));
    $listUrl = esc_url(home_url('/listings'));
    $ldjson = ks_home_ldjson($identity);

    ob_start();
    ?>
    <?php echo $ldjson; // already escaped?>
    <section class="<?php echo $heroClass; ?>" id="top" aria-labelledby="hero-heading" style="--hero-photo:url('<?php echo $imgUrl; ?>')">
      <figure class="hero-media">
        <img src="<?php echo $imgUrl; ?>" srcset="<?php echo $imgSrcset; ?>" sizes="100vw" width="1600" height="900" alt="" fetchpriority="high" loading="eager" decoding="sync">
      </figure>
      <div class="hero-veil" aria-hidden="true"<?php if ($veilStyle) { ?> style="<?php echo esc_attr($veilStyle); ?>"<?php } ?>></div>
      <div class="hero-inner">
        <p class="hero-eyebrow"><?php echo $eyebrow; ?></p>
        <h1 id="hero-heading"><?php echo $title; ?></h1>
        <?php if ($text) { ?><p class="hero-sub"><?php echo $text; ?></p><?php } ?>
        <ul class="hero-proof">
          <li><strong><?php echo esc_html($count ?: 8); ?></strong> <?php esc_html_e('sample listings', 'acreline'); ?></li>
          <li><strong><?php echo esc_html($townships ?: 3); ?></strong> <?php esc_html_e('townships', 'acreline'); ?></li>
          <li><a href="<?php echo $bookUrl; ?>"><?php esc_html_e('Book a showing', 'acreline'); ?></a></li>
        </ul>
        <form class="hero-search" id="heroSearchForm" role="search" aria-label="<?php esc_attr_e('Search sample listings', 'acreline'); ?>">
          <div class="hero-search-row">
            <div class="field"><label for="hsType"><?php esc_html_e('Type', 'acreline'); ?></label>
              <select id="hsType" name="type">
                <option value="all"><?php esc_html_e('Any', 'acreline'); ?></option>
                <option value="home"><?php esc_html_e('Home', 'acreline'); ?></option>
                <option value="farm"><?php esc_html_e('Farm', 'acreline'); ?></option>
                <option value="land"><?php esc_html_e('Land', 'acreline'); ?></option>
                <option value="historic"><?php esc_html_e('Historic', 'acreline'); ?></option>
              </select></div>
            <div class="field"><label for="hsPrice"><?php esc_html_e('Price', 'acreline'); ?></label>
              <select id="hsPrice" name="price">
                <option value="all"><?php esc_html_e('Any', 'acreline'); ?></option>
                <option value="0-250000"><?php esc_html_e('Under $250k', 'acreline'); ?></option>
                <option value="250000-500000">$250k–$500k</option>
                <option value="500000-750000">$500k–$750k</option>
                <option value="750000-999999999">$750k+</option>
              </select></div>
            <div class="field"><label for="hsAcreage"><?php esc_html_e('Acreage', 'acreline'); ?></label>
              <select id="hsAcreage" name="acreage">
                <option value="all"><?php esc_html_e('Any', 'acreline'); ?></option>
                <option value="0-1">&lt; 1 acre</option>
                <option value="1-10">1–10</option>
                <option value="10-30">10–30</option>
                <option value="30-999">30+</option>
              </select></div>
            <div class="field"><label for="hsTownship"><?php esc_html_e('Area', 'acreline'); ?></label>
              <select id="hsTownship" name="township">
                <option value="all"><?php esc_html_e('Any sample area', 'acreline'); ?></option>
                <option value="Cumberland"><?php esc_html_e('North Ridge', 'acreline'); ?></option>
                <option value="Straban"><?php esc_html_e('Mill Creek', 'acreline'); ?></option>
                <option value="Franklin"><?php esc_html_e('Oak Hollow', 'acreline'); ?></option>
              </select></div>
          </div>
          <div class="hero-search-actions">
            <a class="hero-search-link" href="<?php echo esc_url($listUrl); ?>"><?php echo esc_html($secondary); ?></a>
            <button type="submit" class="<?php echo $primaryBtnClass; ?>"><?php echo esc_html($primary); ?></button>
          </div>
        </form>
      </div>
    </section>
    <?php
    return (string) ob_get_clean();
}

/** @param array<string, mixed> $attrs */
function ks_render_page_hero(array $attrs): string
{
    $identity = Identity::toArray();
    $brand = esc_html($attrs['brand'] ?? '') ?: esc_html($identity['brand'] ?? 'Acreline');
    $eyebrow = esc_html($attrs['eyebrow'] ?? '');
    $title = wp_kses($attrs['title'] ?? '', ['em' => [], 'strong' => []]);
    $text = wp_kses($attrs['text'] ?? '', ['em' => [], 'strong' => [], 'br' => []]);
    $primary = esc_html($attrs['primaryLabel'] ?? 'Book a showing');
    $pUrl = esc_url($attrs['primaryUrl'] ?? '') ?: esc_url(home_url('/book/'));
    $secondary = esc_html($attrs['secondaryLabel'] ?? '');
    $sUrl = esc_url($attrs['secondaryUrl'] ?? '');
    $pageId = (int) get_the_ID();
    $heroImg = ks_hero_media($attrs, $pageId, $pageId > 0 ? (string) get_post_field('post_name', $pageId) : '');
    $thumbUrl = esc_url($heroImg['url']);
    $thumbSrcset = esc_attr($heroImg['srcset']);
    $heroClass = esc_attr(ks_hero_class($attrs, 'page-hero page-hero--photo'));
    $veilStyle = ks_veil_style($attrs);
    $primaryBtnClass = esc_attr(ks_btn_class(sanitize_key((string) ($attrs['primaryBtnStyle'] ?? 'primary'))));
    $secondaryBtnClass = esc_attr(ks_btn_class(sanitize_key((string) ($attrs['secondaryBtnStyle'] ?? 'outline-light'))));

    ob_start();
    ?>
    <section class="<?php echo $heroClass; ?>" aria-labelledby="page-hero-heading" style="--hero-photo:url('<?php echo $thumbUrl; ?>')">
      <figure class="page-hero-media">
        <img src="<?php echo $thumbUrl; ?>" srcset="<?php echo $thumbSrcset; ?>" sizes="100vw" width="1600" height="900" alt="" fetchpriority="high" loading="eager" decoding="sync">
      </figure>
      <div class="page-hero-veil" aria-hidden="true"<?php if ($veilStyle) { ?> style="<?php echo esc_attr($veilStyle); ?>"<?php } ?>></div>
      <div class="page-hero-inner">
        <?php if ($brand) { ?><p class="hero-brand"><?php echo $brand; ?></p><?php } ?>
        <?php if ($eyebrow) { ?><p class="hero-eyebrow"><?php echo $eyebrow; ?></p><?php } ?>
        <h1 id="page-hero-heading"><?php echo $title; ?></h1>
        <?php if ($text) { ?><p><?php echo $text; ?></p><?php } ?>
        <div class="page-hero-cta">
          <a class="<?php echo $primaryBtnClass; ?>" href="<?php echo $pUrl; ?>"><?php echo $primary; ?></a>
          <?php if ($secondary && $sUrl) { ?>
            <a class="<?php echo $secondaryBtnClass; ?>" href="<?php echo $sUrl; ?>"><?php echo $secondary; ?></a>
          <?php } ?>
        </div>
      </div>
    </section>
    <?php
    return (string) ob_get_clean();
}

/** @param array<string, mixed> $attrs */
function ks_render_intent_cards(array $attrs): string
{
    $a = $attrs;
    $listUrl = esc_url(home_url('/listings'));
    $bookUrl = esc_url(home_url('/book/'));
    $themeUri = esc_url(get_template_directory_uri());

    $cardStyle = sanitize_key((string) ($a['cardStyle'] ?? 'photo'));
    $gridClass = ks_head_class($a, 'section-head left intent-head reveal');

    // Grid: 2-col or default 3-col
    $gridCols = sanitize_key((string) ($a['intentCols'] ?? '3'));
    $gridCls = 'intent-grid reveal'.($gridCols === '2' ? ' ks-cols--2' : '');

    // Card style modifier
    $cardCls = $cardStyle === 'flat' ? ' ks-style--flat' : '';

    ob_start();
    ?>
    <section class="intent-band" id="search" aria-labelledby="intent-heading">
      <div class="wrap">
        <header class="<?php echo esc_attr($gridClass); ?>">
          <p class="eyebrow"><?php echo esc_html($a['eyebrow'] ?? 'Start here'); ?></p>
          <h2 id="intent-heading"><?php echo wp_kses($a['title'] ?? 'Pick the path', ['em' => [], 'strong' => []]); ?></h2>
          <p><?php echo wp_kses($a['text'] ?? '', ['em' => [], 'strong' => [], 'br' => []]); ?></p>
        </header>
        <div class="<?php echo esc_attr($gridCls); ?>">
          <a class="intent-card<?php echo esc_attr($cardCls); ?>" href="<?php echo $listUrl; ?>">
            <?php if ($cardStyle !== 'flat') { ?>
            <figure class="intent-photo">
              <img src="<?php echo $themeUri; ?>/public/images/intent-buy.jpg" width="1200" height="900" alt="" decoding="async">
            </figure>
            <?php } ?>
            <div class="intent-copy">
              <span class="intent-kicker"><?php echo esc_html($a['buyKicker'] ?? 'Buy'); ?></span>
              <h3><?php echo wp_kses($a['buyTitle'] ?? 'Scan homes and neighborhoods', ['em' => []]); ?></h3>
              <p><?php echo wp_kses($a['buyLead'] ?? '', ['em' => [], 'strong' => []]); ?></p>
              <span class="intent-go"><?php echo esc_html($a['buyCta'] ?? 'Browse listings'); ?> →</span>
            </div>
          </a>
          <a class="intent-card<?php echo esc_attr($cardCls); ?>" href="#value">
            <?php if ($cardStyle !== 'flat') { ?>
            <figure class="intent-photo">
              <img src="<?php echo $themeUri; ?>/public/images/intent-sell.jpg" width="1200" height="900" alt="" decoding="async">
            </figure>
            <?php } ?>
            <div class="intent-copy">
              <span class="intent-kicker"><?php echo esc_html($a['sellKicker'] ?? 'Sell'); ?></span>
              <h3><?php echo wp_kses($a['sellTitle'] ?? 'Price it before you list', ['em' => []]); ?></h3>
              <p><?php echo wp_kses($a['sellLead'] ?? '', ['em' => [], 'strong' => []]); ?></p>
              <span class="intent-go"><?php echo esc_html($a['sellCta'] ?? 'Estimate value'); ?> →</span>
            </div>
          </a>
          <a class="intent-card is-primary<?php echo esc_attr($cardCls); ?>" href="<?php echo $bookUrl; ?>">
            <?php if ($cardStyle !== 'flat') { ?>
            <figure class="intent-photo intent-photo--ground">
              <img src="<?php echo $themeUri; ?>/public/images/intent-tour.jpg" width="1200" height="900" alt="" decoding="async">
            </figure>
            <?php } ?>
            <div class="intent-copy">
              <span class="intent-kicker"><?php echo esc_html($a['tourKicker'] ?? 'Tour'); ?></span>
              <h3><?php echo wp_kses($a['tourTitle'] ?? 'Walk it on the ground', ['em' => []]); ?></h3>
              <p><?php echo wp_kses($a['tourLead'] ?? '', ['em' => [], 'strong' => []]); ?></p>
              <span class="intent-go"><?php echo esc_html($a['tourCta'] ?? 'Book a showing'); ?> →</span>
            </div>
          </a>
        </div>
        <ul class="intent-notes reveal" aria-label="<?php echo esc_attr($a['notesLabel'] ?? 'Good to know'); ?>">
          <?php
          $notes = [
              [$a['note1Title'] ?? 'Neighborhood first', $a['note1Text'] ?? 'Schools, commute, and HOA rules change from one sample area to the next.'],
              [$a['note2Title'] ?? 'Walk the rooms', $a['note2Text'] ?? 'Photos hide layout. Confirm beds, parking, and outdoor space before you offer.'],
              [$a['note3Title'] ?? 'Bring decision-makers', $a['note3Text'] ?? 'If a partner will help decide, book the showing when they can come too.'],
          ];
    foreach ($notes as [$nt, $nb]) { ?>
            <li>
              <strong><?php echo wp_kses($nt, ['em' => []]); ?></strong>
              <span><?php echo wp_kses($nb, ['em' => [], 'strong' => []]); ?></span>
            </li>
          <?php } ?>
        </ul>
      </div>
    </section>
    <?php
    return (string) ob_get_clean();
}

/** @param array<string, mixed> $attrs */
function ks_render_spotlight(array $attrs): string
{
    $itemCount = max(1, min(8, (int) ($attrs['itemCount'] ?? 3)));
    $featured = Catalog::featuredListings($itemCount);
    $eyebrow = esc_html($attrs['eyebrow'] ?? 'Spotlight');
    $title = wp_kses($attrs['title'] ?? 'Three sample homes to scan', ['em' => [], 'strong' => []]);
    $text = wp_kses($attrs['text'] ?? 'Price · beds · acres — then book a fictional walk-through.', ['em' => []]);
    $bookUrl = esc_url(home_url('/book/'));
    $headClass = esc_attr(ks_head_class($attrs));
    $gridCols = sanitize_key((string) ($attrs['gridCols'] ?? 'auto'));
    $gridClass = $gridCols !== 'auto' ? ' listing-mini-grid--'.$gridCols.'col' : '';

    ob_start();
    ?>
    <section class="section section-alt" aria-labelledby="spotlight-heading">
      <div class="wrap">
        <header class="<?php echo $headClass; ?>">
          <p class="eyebrow"><?php echo $eyebrow; ?></p>
          <h2 id="spotlight-heading"><?php echo $title; ?></h2>
          <p><?php echo $text; ?></p>
        </header>
        <div class="listing-mini-grid<?php echo esc_attr($gridClass); ?> reveal">
          <?php if ($featured) {
              foreach ($featured as $listing) { ?>
                <a class="listing-mini" href="<?php echo esc_url($bookUrl.'?listing_id='.$listing['id']); ?>" data-listing-id="<?php echo esc_attr((string) $listing['id']); ?>">
                  <?php if ($listing['image']) { ?>
                    <img src="<?php echo esc_url((string) $listing['image']); ?>" width="800" height="500" alt="" loading="lazy" decoding="async">
                  <?php } else { ?>
                    <div class="listing-mini-photo" style="background:<?php echo esc_attr((string) $listing['grad']); ?>;height:150px"></div>
                  <?php } ?>
                  <div>
                    <strong><?php echo esc_html(Catalog::formatMoney((int) $listing['price'])); ?></strong>
                    <span>
                      <?php echo esc_html((string) $listing['title']); ?>
                      <?php if ($listing['type'] !== 'land') { ?> · <?php echo esc_html((string) $listing['beds']); ?> bd<?php } ?>
                      · <?php echo esc_html((string) $listing['acres']); ?> <?php esc_html_e('acres', 'acreline'); ?>
                    </span>
                    <span class="chip"><?php esc_html_e('Listing · Book showing', 'acreline'); ?></span>
                  </div>
                </a>
              <?php }
              } else { ?>
            <p class="empty-state"><?php esc_html_e('Add featured listings in WP Admin → Listings.', 'acreline'); ?></p>
          <?php } ?>
        </div>
      </div>
    </section>
    <?php
    return (string) ob_get_clean();
}

/** @param array<string, mixed> $attrs */
function ks_render_booking_section(array $attrs): string
{
    $eyebrow = esc_html($attrs['eyebrow'] ?? 'Appointments');
    $title = wp_kses($attrs['title'] ?? 'Book a house showing', ['em' => [], 'strong' => []]);
    $text = wp_kses($attrs['text'] ?? 'Demo scheduler for touring sample homes. Requests are saved to Bookings as Requested.', ['em' => [], 'strong' => []]);
    $headClass = esc_attr(ks_head_class($attrs));
    $showSidePhoto = (bool) ($attrs['showSidePhoto'] ?? true);
    $bookingForm = ks_booking_form_html();
    $bookingPhoto = $showSidePhoto ? ks_booking_photo_html() : '';

    ob_start();
    ?>
    <section class="section section-alt" id="book-showing" aria-labelledby="book-heading">
      <div class="wrap">
        <header class="<?php echo $headClass; ?>">
          <p class="eyebrow"><?php echo $eyebrow; ?></p>
          <h2 id="book-heading"><?php echo $title; ?></h2>
          <p><?php echo $text; ?></p>
        </header>
        <div class="booking-shell reveal<?php echo $showSidePhoto ? '' : ' ks-no-photo'; ?>">
          <?php echo $bookingForm; ?>
          <?php echo $bookingPhoto; ?>
        </div>
      </div>
    </section>
    <?php
    return (string) ob_get_clean();
}

/** @param array<string, mixed> $attrs */
function ks_render_market_stats(array $attrs): string
{
    $a = $attrs;
    $eyebrow = esc_html($a['eyebrow'] ?? 'Sample market');
    $title = wp_kses($a['title'] ?? 'Pulse at a glance', ['em' => [], 'strong' => []]);
    $text = wp_kses($a['text'] ?? '', ['em' => [], 'strong' => []]);
    $headClass = esc_attr(ks_head_class($attrs));
    $statsLayout = sanitize_key((string) ($a['statsLayout'] ?? '4-col'));
    $gridClass = $statsLayout === '2-col' ? 'market-grid ks-cols--2 reveal' : 'market-grid reveal';

    $stats = [
        [$a['stat1Val'] ?? '$398k', $a['stat1Lbl'] ?? 'Median sale price', $a['stat1Sub'] ?? '↑ 2.1% vs last quarter', 'up'],
        [$a['stat2Val'] ?? '32', $a['stat2Lbl'] ?? 'Days on market', $a['stat2Sub'] ?? '↓ 5 days vs last quarter', 'down'],
        [$a['stat3Val'] ?? '1.6', $a['stat3Lbl'] ?? 'Months of inventory', $a['stat3Sub'] ?? 'Limited active supply', ''],
        [$a['stat4Val'] ?? '95%', $a['stat4Lbl'] ?? 'List-to-sale ratio', $a['stat4Sub'] ?? 'Offers near asking', ''],
    ];

    ob_start();
    ?>
    <section class="section" aria-labelledby="market-heading">
      <div class="wrap">
        <header class="<?php echo $headClass; ?>">
          <p class="eyebrow"><?php echo $eyebrow; ?></p>
          <h2 id="market-heading"><?php echo $title; ?></h2>
          <?php if ($text) { ?><p><?php echo $text; ?></p><?php } ?>
        </header>
        <div class="<?php echo esc_attr($gridClass); ?>">
          <?php foreach ($stats as [$val, $lbl, $sub, $dir]) { ?>
            <article class="market-stat">
              <strong><?php echo esc_html($val); ?></strong>
              <span><?php echo esc_html($lbl); ?></span>
              <em<?php if ($dir) { ?> class="is-<?php echo esc_attr($dir); ?>"<?php } ?>>
                <?php if ($dir === 'up') { ?><span aria-hidden="true">↑</span> <?php } ?>
                <?php if ($dir === 'down') { ?><span aria-hidden="true">↓</span> <?php } ?>
                <?php echo esc_html($sub); ?>
              </em>
            </article>
          <?php } ?>
        </div>
      </div>
    </section>
    <?php
    return (string) ob_get_clean();
}

/** @param array<string, mixed> $attrs */
function ks_render_how_it_works(array $attrs): string
{
    $eyebrow = esc_html($attrs['eyebrow'] ?? 'How a tour starts');
    $title = wp_kses($attrs['title'] ?? 'From search to showing', ['em' => [], 'strong' => []]);
    $text = wp_kses($attrs['text'] ?? 'Neighborhood first, then a walk-through — not office theater. This demo stops at on-page confirmation.', ['em' => []]);
    $themeUri = esc_url(get_template_directory_uri());
    $bookUrl = esc_url(home_url('/book/'));

    $steps = [
        ['tour-step-township.jpg', 'Quiet neighborhood street with houses and mature trees', 'Filter the area', 'Start with the neighborhood so you are not comparing a Midtown condo to a North Ridge house.'],
        ['tour-step-card.jpg', 'Hands reviewing listing papers at a kitchen table', 'Read the card', 'Price, beds, baths, and lot size. Featured homes below already select the listing.'],
        ['tour-step-book.jpg', 'Writing a showing time at a kitchen table', 'Book the hour', 'Pick a date and a time. Evening slots exist because many buyers tour after work.'],
        ['tour-step-walk.jpg', 'An agent walking buyers up to a house', 'Walk the rooms', 'Wear comfortable shoes. Here you get an on-page receipt — no email, no calendar invite.'],
    ];

    ob_start();
    ?>
    <section class="section" aria-labelledby="how-heading">
      <div class="wrap">
        <header class="section-head left reveal">
          <p class="eyebrow"><?php echo $eyebrow; ?></p>
          <h2 id="how-heading"><?php echo $title; ?></h2>
          <p><?php echo $text; ?></p>
        </header>
        <ol class="step-grid reveal">
          <?php foreach ($steps as $i => [$img, $alt, $stepTitle, $stepText]) { ?>
            <li class="step">
              <figure class="step-photo">
                <img src="<?php echo $themeUri; ?>/public/images/<?php echo esc_attr($img); ?>"
                     width="1200" height="800" alt="<?php echo esc_attr($alt); ?>" loading="lazy" decoding="async">
              </figure>
              <div class="step-copy">
                <h3><?php echo esc_html($stepTitle); ?></h3>
                <p><?php echo esc_html($stepText); ?></p>
              </div>
              <?php if ($i === count($steps) - 1) { ?>
                <a class="btn btn-primary" href="<?php echo $bookUrl; ?>"><?php esc_html_e('Book a showing', 'acreline'); ?></a>
              <?php } ?>
            </li>
          <?php } ?>
        </ol>
      </div>
    </section>
    <?php
    return (string) ob_get_clean();
}

/** @param array<string, mixed> $attrs */
function ks_render_agent_tools(array $attrs): string
{
    $eyebrow = esc_html($attrs['eyebrow'] ?? 'Agent tools');
    $title = wp_kses($attrs['title'] ?? 'Value range and listing alerts', ['em' => [], 'strong' => []]);
    $text = wp_kses($attrs['text'] ?? 'Two tools buyers and sellers use first.', ['em' => []]);

    ob_start();
    ?>
    <section class="section section-alt" id="value" aria-labelledby="tools-heading">
      <div class="wrap">
        <header class="section-head reveal">
          <p class="eyebrow"><?php echo $eyebrow; ?></p>
          <h2 id="tools-heading"><?php echo $title; ?></h2>
          <p><?php echo $text; ?></p>
        </header>
        <div class="tools-grid">
          <div class="tool-panel reveal">
            <h3><?php esc_html_e('Demo home value', 'acreline'); ?></h3>
            <p class="lede"><?php esc_html_e('Instant range for a fictional address. Not an appraisal.', 'acreline'); ?></p>
            <form class="form-grid two" id="valueForm">
              <div class="field field-span">
                <label for="vAddress"><?php esc_html_e('Street address', 'acreline'); ?></label>
                <input id="vAddress" type="text" autocomplete="street-address" placeholder="100 Concept Way" required>
              </div>
              <div class="field">
                <label for="vBeds"><?php esc_html_e('Beds', 'acreline'); ?></label>
                <select id="vBeds"><option>2</option><option selected>3</option><option>4</option><option>5</option></select>
              </div>
              <div class="field">
                <label for="vAcres"><?php esc_html_e('Acres', 'acreline'); ?></label>
                <select id="vAcres"><option value="0.5">&lt; 1</option><option value="5" selected>1–10</option><option value="20">10–30</option><option value="40">30+</option></select>
              </div>
              <div class="field field-span">
                <button type="submit" class="btn btn-primary btn-block"><?php esc_html_e('Estimate value', 'acreline'); ?></button>
              </div>
            </form>
            <div class="val-result" id="valueResult" role="status" aria-live="polite"></div>
          </div>
          <div class="alert-panel reveal">
            <h3><?php esc_html_e('Listing alerts', 'acreline'); ?></h3>
            <p><?php esc_html_e('Demo inbox signup for new sample matches.', 'acreline'); ?></p>
            <form class="form-grid two" id="alertForm">
              <div class="field field-span">
                <label for="aEmail"><?php esc_html_e('Email', 'acreline'); ?></label>
                <input id="aEmail" type="email" autocomplete="email" placeholder="you@acreline-concept.test" required>
              </div>
              <div class="field">
                <label for="aType"><?php esc_html_e('Looking for', 'acreline'); ?></label>
                <select id="aType">
                  <option><?php esc_html_e('Homes', 'acreline'); ?></option>
                  <option><?php esc_html_e('Land', 'acreline'); ?></option>
                  <option><?php esc_html_e('Farms', 'acreline'); ?></option>
                  <option><?php esc_html_e('Anything', 'acreline'); ?></option>
                </select>
              </div>
              <div class="field">
                <label for="aMax"><?php esc_html_e('Max price', 'acreline'); ?></label>
                <select id="aMax">
                  <option>$400,000</option>
                  <option selected>$600,000</option>
                  <option>$800,000</option>
                  <option><?php esc_html_e('No max', 'acreline'); ?></option>
                </select>
              </div>
              <div class="field field-span">
                <button type="submit" class="btn btn-primary btn-block"><?php esc_html_e('Create alert', 'acreline'); ?></button>
              </div>
            </form>
            <div class="confirm-msg" id="alertConfirm" role="status" aria-live="polite">
              <span><?php esc_html_e('Demo alert saved — no email is sent.', 'acreline'); ?></span>
            </div>
          </div>
        </div>
      </div>
    </section>
    <?php
    return (string) ob_get_clean();
}

/** @param array<string, mixed> $attrs */
function ks_render_seo_content(array $attrs): string
{
    $guideUrl = esc_url(home_url('/guide'));
    $areasUrl = esc_url(home_url('/areas'));
    $listingsUrl = esc_url(home_url('/listings'));

    ob_start();
    ?>
    <section class="section" aria-labelledby="seo-heading">
      <div class="wrap">
        <div class="seo-block reveal">
          <div>
            <p class="eyebrow"><?php esc_html_e('Market notes', 'acreline'); ?></p>
            <h2 id="seo-heading"><?php esc_html_e('Buying a house, condo, or commercial space', 'acreline'); ?></h2>
            <div class="prose-tight">
              <p><?php esc_html_e('This sample county has three neighborhoods buyers actually compare: North Ridge for established streets, Mill Creek for value and parks, and Oak Hollow for a walkable core.', 'acreline'); ?></p>
              <h3><?php esc_html_e('When you are buying a house', 'acreline'); ?></h3>
              <p><?php esc_html_e('Start with price, bedrooms, and the commute. Confirm schools, HOA rules, and systems before a second visit.', 'acreline'); ?></p>
              <h3><?php esc_html_e('When you are buying a condo or commercial suite', 'acreline'); ?></h3>
              <p><?php esc_html_e('Start with dues, parking, and permitted use. A storefront without a clear lease path is a different product than a turnkey house.', 'acreline'); ?></p>
            </div>
            <p class="help-links">
              <a href="<?php echo $guideUrl; ?>"><?php esc_html_e('Buyer guide →', 'acreline'); ?></a>
              <a href="<?php echo $areasUrl; ?>"><?php esc_html_e('Neighborhood notes →', 'acreline'); ?></a>
              <a href="<?php echo $listingsUrl; ?>"><?php esc_html_e('Sample listings →', 'acreline'); ?></a>
            </p>
          </div>
          <div class="scan-grid cols-2">
            <?php
            $scanCards = [
                ['Houses', 'Review first', ['Price, bedrooms, and baths', 'Neighborhood and commute', 'HOA or systems', 'A showing that fits the week']],
                ['Condos', 'Review first', ['Dues and reserves', 'Parking and storage', 'Pet and rental rules', 'A walk of the building']],
                ['Commercial', 'Walk the use', ['Permitted use and zoning', 'Frontage and parking', 'Lease vs purchase', 'Build-out timeline']],
                ['Next', 'Recommended next steps', ['Filter the sample listings', 'Read the neighborhood notes', 'Run a sample payment', 'Schedule a showing']],
            ];
    foreach ($scanCards as [$num, $heading, $items]) { ?>
              <article class="scan-card">
                <span class="num"><?php echo esc_html($num); ?></span>
                <h3><?php echo esc_html($heading); ?></h3>
                <ul>
                  <?php foreach ($items as $item) { ?><li><?php echo esc_html($item); ?></li><?php } ?>
                </ul>
              </article>
            <?php } ?>
          </div>
        </div>
      </div>
    </section>
    <?php
    return (string) ob_get_clean();
}

/** @param array<string, mixed> $attrs */
function ks_render_reviews(array $attrs): string
{
    $eyebrow = esc_html($attrs['eyebrow'] ?? 'Samples');
    $title = wp_kses($attrs['title'] ?? 'What clients might say', ['em' => [], 'strong' => []]);
    $text = wp_kses($attrs['text'] ?? 'Placeholder quotes for layout — not real reviews.', ['em' => []]);
    $themeUri = esc_url(get_template_directory_uri());
    $starPath = 'M12 2.5l2.86 5.8 6.4.93-4.63 4.51 1.09 6.36L12 16.98 6.28 20.1l1.09-6.36L2.74 9.23l6.4-.93L12 2.5z';

    $reviews = [
        ['Sample Buyer A', 5.0, 'The showing scheduler made it obvious what to do next.', 'images/review-buyer-a.jpg', 'a month ago', 'North Ridge · demo'],
        ['Sample Buyer B', 5.0, 'Payment estimate beside the photo helped us compare homes faster.', 'images/review-buyer-b.jpg', '2 weeks ago', 'Mill Creek · demo'],
        ['Sample Seller C', 4.8, 'We used the value tool before calling — then booked a walk-through.', 'images/review-seller-c.jpg', '3 weeks ago', 'Oak Hollow · demo'],
    ];

    $headClass = esc_attr(ks_head_class($attrs));
    $reviewLayout = sanitize_key((string) ($attrs['reviewLayout'] ?? 'grid'));
    $showRating = (bool) ($attrs['showRating'] ?? true);
    $showPhoto = (bool) ($attrs['showPhoto'] ?? true);
    $cols = sanitize_key((string) ($attrs['reviewCols'] ?? '3'));

    $gridCls = 'testi-grid reveal';
    if ($cols === '1') {
        $gridCls .= ' ks-cols--1';
    } elseif ($cols === '2') {
        $gridCls .= ' ks-cols--2';
    }
    if (! $showRating) {
        $gridCls .= ' no-rating';
    }
    if (! $showPhoto) {
        $gridCls .= ' no-photo';
    }

    ob_start();
    ?>
    <section class="section" aria-labelledby="stories-heading">
      <div class="wrap">
        <header class="<?php echo $headClass; ?>">
          <p class="eyebrow"><?php echo $eyebrow; ?></p>
          <h2 id="stories-heading"><?php echo $title; ?></h2>
          <p><?php echo $text; ?></p>
          <p class="testi-avg"><?php esc_html_e('4.9 sample average — demo scores, not a brokerage claim.', 'acreline'); ?></p>
        </header>
        <div class="<?php echo esc_attr($gridCls); ?>" data-reviews-source="demo">
          <?php foreach ($reviews as [$author, $rating, $reviewText, $photo, $time, $loc]) {
              $score = number_format($rating, 1);
              $photoUrl = esc_url($themeUri.'/public/'.$photo);
              $initials = '';
              foreach (array_slice(preg_split('/\s+/', trim($author)) ?: [], 0, 2) as $part) {
                  $initials .= strtoupper(substr($part, 0, 1));
              }
              ?>
            <article class="review-card testi">
              <div class="review-card__head">
                <img class="review-card__photo" src="<?php echo $photoUrl; ?>" alt="<?php echo esc_attr(__('Portrait of ', 'acreline').$author); ?>" width="52" height="52" loading="lazy" decoding="async">
                <div class="review-card__identity">
                  <p class="review-card__author testi-name"><?php echo esc_html($author); ?></p>
                  <span class="review-card__loc testi-loc"><?php echo esc_html($loc); ?></span>
                  <div class="review-card__meta testi-rating">
                    <span class="visually-hidden"><?php echo esc_html(sprintf(__('Sample rating %s out of 5', 'acreline'), $score)); ?></span>
                    <span class="testi-stars" aria-hidden="true">
                      <?php for ($i = 1; $i <= 5; $i++) {
                          $fill = max(0, min(1, $rating - ($i - 1)));
                          $cls = $fill >= 1 ? 'is-full' : ($fill > 0 ? 'is-partial' : 'is-empty');
                          $style = ($fill > 0 && $fill < 1) ? ' style="--star-fill:'.((int) round($fill * 100)).'%"' : '';
                          ?>
                        <span class="testi-star <?php echo esc_attr($cls); ?>"<?php echo $style; ?>>
                          <svg viewBox="0 0 24 24" focusable="false"><path class="testi-star-empty" d="<?php echo esc_attr($starPath); ?>"/><path class="testi-star-fill" d="<?php echo esc_attr($starPath); ?>"/></svg>
                        </span>
                      <?php } ?>
                    </span>
                    <span class="testi-score" aria-hidden="true"><?php echo esc_html($score); ?></span>
                    <span class="review-card__time"><?php echo esc_html($time); ?></span>
                  </div>
                </div>
              </div>
              <blockquote class="review-card__text"><p>"<?php echo esc_html($reviewText); ?>"</p></blockquote>
            </article>
          <?php } ?>
        </div>
      </div>
    </section>
    <?php
    return (string) ob_get_clean();
}

/** @param array<string, mixed> $attrs */
function ks_render_faq_list(array $attrs): string
{
    $title = wp_kses($attrs['title'] ?? 'Questions buyers ask first', ['em' => [], 'strong' => []]);
    $text = wp_kses($attrs['text'] ?? 'Practical answers for house and neighborhood shoppers.', ['em' => []]);
    $headClass = esc_attr(ks_head_class($attrs));
    $faqs = Faqs::forContext();
    $faqStyle = sanitize_key((string) ($attrs['faqStyle'] ?? 'dl'));
    $listIcon = sanitize_key((string) ($attrs['listIcon'] ?? 'none'));
    $showNumbers = (bool) ($attrs['showNumbers'] ?? false);

    if (empty($faqs)) {
        return '';
    }

    $iconMap = [
        'arrow' => '→',
        'check' => '✓',
        'dash' => '–',
        'none' => '',
    ];
    $icon = $iconMap[$listIcon] ?? '';

    // Build faq-list CSS classes
    $faqListClass = 'faq-list';
    if ($faqStyle === 'flat') {
        $faqListClass .= ' ks-faq--flat';
    } elseif ($faqStyle === 'accordion') {
        $faqListClass .= ' ks-faq--accordion';
    }
    if ($showNumbers) {
        $faqListClass .= ' ks-numbered';
    } elseif (in_array($listIcon, ['arrow', 'check'], true)) {
        $faqListClass .= ' ks-icon--'.$listIcon;
    }

    ob_start();
    ?>
    <section class="section faq-section" aria-labelledby="faq-heading">
      <div class="wrap">
        <header class="<?php echo $headClass; ?>">
          <h2 id="faq-heading"><?php echo $title; ?></h2>
          <p><?php echo $text; ?></p>
        </header>
        <?php if ($faqStyle === 'accordion') { ?>
          <div class="<?php echo esc_attr($faqListClass); ?>">
            <?php foreach ($faqs as $i => $faq) { ?>
              <details class="faq-item" <?php if ($i === 0) { ?>open<?php } ?>>
                <summary class="faq-question"><?php echo esc_html($faq['q'] ?? ''); ?></summary>
                <div class="faq-answer"><p><?php echo esc_html($faq['a'] ?? ''); ?></p></div>
              </details>
            <?php } ?>
          </div>
        <?php } else { ?>
          <dl class="<?php echo esc_attr($faqListClass); ?>">
            <?php foreach ($faqs as $n => $faq) { ?>
              <div class="faq-item">
                <dt><?php echo esc_html($faq['q'] ?? ''); ?></dt>
                <dd><?php echo esc_html($faq['a'] ?? ''); ?></dd>
              </div>
            <?php } ?>
          </dl>
        <?php } ?>
      </div>
    </section>
    <?php
    return (string) ob_get_clean();
}

/** @param array<string, mixed> $attrs */
function ks_render_cta_band(array $attrs): string
{
    $title = wp_kses($attrs['title'] ?? 'Tour a sample home next.', ['em' => [], 'strong' => []]);
    $text = wp_kses($attrs['text'] ?? 'Pick an address, choose a slot, and see how a modern realtor booking flow feels.', ['em' => []]);
    $pLabel = esc_html($attrs['primaryLabel'] ?? 'Book a showing');
    $pUrl = esc_url($attrs['primaryUrl'] ?? '') ?: esc_url(home_url('/book/'));
    $sLabel = esc_html($attrs['secondaryLabel'] ?? 'Browse samples');
    $sUrl = esc_url($attrs['secondaryUrl'] ?? '') ?: esc_url(home_url('/listings'));
    $bandStyle = sanitize_key((string) ($attrs['bandStyle'] ?? 'light'));
    $contentAlign = sanitize_key((string) ($attrs['contentAlign'] ?? 'left'));
    $headingSize = sanitize_key((string) ($attrs['headingSize'] ?? 'default'));

    $bandClass = 'cta-band reveal';
    if (in_array($bandStyle, ['accent', 'dark'], true)) {
        $bandClass .= ' ks-band--'.$bandStyle;
    }
    if ($contentAlign === 'center') {
        $bandClass .= ' ks-align--center';
    }
    if ($headingSize !== 'default') {
        $bandClass .= ' ks-head--size-'.$headingSize;
    }
    if ($headingSize !== 'default') {
        $bandClass .= ' ks-head--size-'.$headingSize;
    }

    // Invert button styles when band is dark or accent.
    $primaryClass = 'btn btn-primary';
    $secondaryClass = 'btn btn-outline light';
    if ($bandStyle === 'dark' || $bandStyle === 'accent') {
        $primaryClass = 'btn btn-outline light';
        $secondaryClass = 'btn btn-ghost';
    }

    ob_start();
    ?>
    <section class="section section-alt" aria-labelledby="cta-heading">
      <div class="wrap">
        <div class="<?php echo esc_attr($bandClass); ?>">
          <h2 id="cta-heading"><?php echo $title; ?></h2>
          <p><?php echo $text; ?></p>
          <div class="cta-actions">
            <a class="<?php echo esc_attr($primaryClass); ?>" href="<?php echo $pUrl; ?>"><?php echo $pLabel; ?></a>
            <a class="<?php echo esc_attr($secondaryClass); ?>" href="<?php echo $sUrl; ?>"><?php echo $sLabel; ?></a>
          </div>
        </div>
      </div>
    </section>
    <?php
    return (string) ob_get_clean();
}

/** @param array<string, mixed> $attrs */
function ks_render_intro_section(array $attrs): string
{
    $eyebrow = esc_html($attrs['eyebrow'] ?? '');
    $title = wp_kses($attrs['title'] ?? '', ['em' => [], 'strong' => []]);
    $text = wp_kses($attrs['text'] ?? '', ['em' => [], 'strong' => [], 'br' => []]);

    if (! $title && ! $text) {
        return '';
    }

    ob_start();
    ?>
    <section class="section">
      <div class="wrap mkt-lead reveal">
        <?php if ($eyebrow) { ?><p class="eyebrow"><?php echo $eyebrow; ?></p><?php } ?>
        <div>
          <?php if ($title) { ?><h2><?php echo $title; ?></h2><?php } ?>
          <?php if ($text) { ?><p class="lede"><?php echo $text; ?></p><?php } ?>
        </div>
      </div>
    </section>
    <?php
    return (string) ob_get_clean();
}

/** @param array<string, mixed> $attrs */
function ks_render_agent_list(array $attrs): string
{
    $agents = Catalog::agents();
    $eyebrow = esc_html($attrs['eyebrow'] ?? 'The sample team');
    $title = wp_kses($attrs['title'] ?? 'Agents who know this ground', ['em' => [], 'strong' => []]);
    $text = wp_kses($attrs['text'] ?? '', ['em' => []]);
    $headClass = esc_attr(ks_head_class($attrs));

    // Aggregate team stats from the agents array
    $totalSold = array_sum(array_column($agents, 'homes_sold'));
    $totalReviews = array_sum(array_column($agents, 'client_reviews_count'));
    $allDom = array_filter(array_column($agents, 'avg_dom'));
    $avgDom = $allDom ? (int) round(array_sum($allDom) / count($allDom)) : 0;

    $starPath = 'M12 2.5l2.86 5.8 6.4.93-4.63 4.51 1.09 6.36L12 16.98 6.28 20.1l1.09-6.36L2.74 9.23l6.4-.93L12 2.5z';

    ob_start();
    ?>
    <section class="section" aria-labelledby="agent-list-heading">
      <div class="wrap">
        <header class="<?php echo $headClass; ?> reveal">
          <p class="eyebrow"><?php echo $eyebrow; ?></p>
          <h2 id="agent-list-heading"><?php echo $title; ?></h2>
          <?php if ($text) { ?><p><?php echo $text; ?></p><?php } ?>
        </header>

        <?php if ($totalSold || $totalReviews || $avgDom) { ?>
          <ul class="agent-team-stats reveal" aria-label="<?php esc_attr_e('Team performance at a glance', 'acreline'); ?>">
            <?php if (count($agents) > 0) { ?>
              <li>
                <strong><?php echo esc_html(count($agents)); ?></strong>
                <span><?php esc_html_e('Specialists', 'acreline'); ?></span>
              </li>
            <?php } ?>
            <?php if ($totalSold) { ?>
              <li>
                <strong><?php echo esc_html($totalSold); ?>+</strong>
                <span><?php esc_html_e('Homes Closed', 'acreline'); ?></span>
              </li>
            <?php } ?>
            <?php if ($avgDom) { ?>
              <li>
                <strong><?php echo esc_html($avgDom); ?></strong>
                <span><?php esc_html_e('Avg Days on Market', 'acreline'); ?></span>
              </li>
            <?php } ?>
            <?php if ($totalReviews) { ?>
              <li>
                <strong><?php echo esc_html($totalReviews); ?>+</strong>
                <span><?php esc_html_e('Client Reviews', 'acreline'); ?></span>
              </li>
            <?php } ?>
          </ul>
        <?php } ?>

        <div class="agent-grid" role="list">
          <?php foreach ($agents as $agent) {
              $specialtyTags = array_filter(array_map('trim', explode(',', $agent['specialties'])));
              $rating = (float) ($agent['rating'] ?? 0);
              $ratingLabel = $rating > 0 ? number_format($rating, 1) : '';
              $phone = $agent['phone'] ?? '';
              $email = $agent['email'] ?? '';
              $totalVol = $agent['total_volume'] ?? '';
              $lsr = $agent['list_to_sale_ratio'] ?? '';
              $badge = $agent['featured_badge'] ?? '';
              ?>
            <article class="agent-card reveal<?php echo $agent['featured'] ? ' is-featured' : ''; ?>" role="listitem">

              <?php if ($agent['featured'] && $badge) { ?>
                <p class="agent-featured-badge"><?php echo esc_html($badge); ?></p>
              <?php } elseif ($agent['featured']) { ?>
                <p class="agent-featured-badge"><?php esc_html_e('Featured', 'acreline'); ?></p>
              <?php } ?>

              <div class="agent-card__photo-row">
                <?php if ($agent['photo']) { ?>
                  <a href="<?php echo esc_url($agent['permalink']); ?>" class="agent-photo-link" tabindex="-1" aria-hidden="true">
                    <img src="<?php echo esc_url($agent['photo']); ?>" width="96" height="96"
                      alt="<?php echo esc_attr($agent['name']); ?>" loading="lazy" class="agent-avatar-photo">
                  </a>
                <?php } else { ?>
                  <div class="agent-avatar" style="background:<?php echo esc_attr($agent['avatar_color']); ?>">
                    <?php echo esc_html($agent['initials']); ?>
                  </div>
                <?php } ?>

                <div class="agent-card__photo-meta">
                  <h4 class="agent-card__name">
                    <a href="<?php echo esc_url($agent['permalink']); ?>"><?php echo esc_html($agent['name']); ?></a>
                  </h4>
                  <p class="agent-title"><?php echo esc_html($agent['job_title']); ?></p>
                  <?php if ($agent['designations']) { ?>
                    <p class="agent-designations"><?php echo esc_html($agent['designations']); ?></p>
                  <?php } ?>
                </div>
              </div>

              <?php if ($ratingLabel) {
                  $starCount = (int) floor($rating);
                  $partial = $rating - $starCount;
                  ?>
                <div class="agent-star-row" aria-label="<?php echo esc_attr(sprintf(__('Sample rating: %s out of 5', 'acreline'), $ratingLabel)); ?>">
                  <span class="agent-stars" aria-hidden="true">
                    <?php for ($i = 1; $i <= 5; $i++) {
                        $fill = max(0, min(1.0, $rating - ($i - 1)));
                        $cls = $fill >= 1 ? 'is-full' : ($fill > 0 ? 'is-partial' : 'is-empty');
                        $style = ($fill > 0 && $fill < 1) ? ' style="--star-fill:'.((int) round($fill * 100)).'%"' : '';
                        ?>
                      <span class="testi-star <?php echo esc_attr($cls); ?>"<?php echo $style; ?>>
                        <svg viewBox="0 0 24 24" focusable="false"><path class="testi-star-empty" d="<?php echo esc_attr($starPath); ?>"/><path class="testi-star-fill" d="<?php echo esc_attr($starPath); ?>"/></svg>
                      </span>
                    <?php } ?>
                  </span>
                  <span class="agent-star-score"><?php echo esc_html($ratingLabel); ?></span>
                  <?php if ($agent['client_reviews_count']) { ?>
                    <span class="agent-star-count">(<?php echo esc_html($agent['client_reviews_count']); ?> <?php esc_html_e('reviews', 'acreline'); ?>)</span>
                  <?php } ?>
                </div>
              <?php } ?>

              <?php if (count($specialtyTags) > 0) { ?>
                <ul class="agent-specialty-tags" aria-label="<?php esc_attr_e('Specialties', 'acreline'); ?>">
                  <?php foreach (array_slice($specialtyTags, 0, 4) as $tag) { ?>
                    <li><?php echo esc_html($tag); ?></li>
                  <?php } ?>
                </ul>
              <?php } ?>

              <?php if ($agent['homes_sold'] || $agent['avg_dom'] || $totalVol || $lsr) { ?>
                <dl class="agent-mini-stats">
                  <?php if ($agent['homes_sold']) { ?>
                    <div><dt><?php esc_html_e('Closed', 'acreline'); ?></dt><dd><?php echo esc_html($agent['homes_sold']); ?></dd></div>
                  <?php } ?>
                  <?php if ($agent['avg_dom']) { ?>
                    <div><dt><?php esc_html_e('Avg DOM', 'acreline'); ?></dt><dd><?php echo esc_html($agent['avg_dom']); ?></dd></div>
                  <?php } ?>
                  <?php if ($lsr) { ?>
                    <div><dt><?php esc_html_e('List/Sale', 'acreline'); ?></dt><dd><?php echo esc_html($lsr); ?>%</dd></div>
                  <?php } ?>
                  <?php if ($totalVol) { ?>
                    <div><dt><?php esc_html_e('Volume', 'acreline'); ?></dt><dd>$<?php echo esc_html(number_format((float) $totalVol / 1000000, 1)); ?>M</dd></div>
                  <?php } ?>
                </dl>
              <?php } ?>

              <?php if ($agent['review_snippet']) { ?>
                <blockquote class="agent-review-snip">
                  <p>"<?php echo esc_html($agent['review_snippet']); ?>"</p>
                  <?php if ($agent['review_author']) { ?>
                    <footer>— <cite><?php echo esc_html($agent['review_author']); ?></cite></footer>
                  <?php } ?>
                </blockquote>
              <?php } ?>

              <?php if ($phone || $email) { ?>
                <div class="agent-contact-row">
                  <?php if ($phone) { ?>
                    <a href="tel:<?php echo esc_attr(preg_replace('/[^\d+]/', '', $phone)); ?>" class="agent-contact-link">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.62 3.38 2 2 0 0 1 3.59 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.56a16 16 0 0 0 6.29 6.29l1.63-1.63a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                      <?php echo esc_html($phone); ?>
                    </a>
                  <?php } ?>
                  <?php if ($email) { ?>
                    <a href="mailto:<?php echo esc_attr($email); ?>" class="agent-contact-link">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                      <?php echo esc_html($email); ?>
                    </a>
                  <?php } ?>
                </div>
              <?php } ?>

              <div class="agent-card__actions">
                <a href="<?php echo esc_url($agent['permalink']); ?>" class="btn btn-primary btn-sm">
                  <?php esc_html_e('View profile', 'acreline'); ?>
                </a>
                <?php if ($agent['calendly']) { ?>
                  <a href="<?php echo esc_url($agent['calendly']); ?>" class="btn btn-outline btn-sm" rel="noopener noreferrer" target="_blank">
                    <?php esc_html_e('Book a call', 'acreline'); ?>
                  </a>
                <?php } ?>
              </div>

            </article>
          <?php } ?>
        </div>
      </div>
    </section>
    <?php
    return (string) ob_get_clean();
}

function ks_render_listing_grid(array $attrs): string
{
    $introTitle = wp_kses($attrs['introTitle'] ?? 'Buying in this sample market', ['em' => [], 'strong' => []]);
    $introText = wp_kses($attrs['introText'] ?? 'Every sample home sits in a neighborhood you can filter.', ['em' => [], 'strong' => []]);

    ob_start();
    ?>
    <section class="section section-alt">
      <div class="wrap">
        <form class="listings-toolbar reveal" id="filterForm" aria-label="<?php esc_attr_e('Filter sample listings', 'acreline'); ?>">
          <div class="filters-grid">
            <div class="field"><label for="fType"><?php esc_html_e('Type', 'acreline'); ?></label>
              <select id="fType" name="type">
                <option value="all"><?php esc_html_e('All types', 'acreline'); ?></option>
                <option value="home"><?php esc_html_e('Home', 'acreline'); ?></option>
                <option value="farm"><?php esc_html_e('Farm', 'acreline'); ?></option>
                <option value="land"><?php esc_html_e('Land', 'acreline'); ?></option>
                <option value="historic"><?php esc_html_e('Historic', 'acreline'); ?></option>
              </select></div>
            <div class="field"><label for="fPrice"><?php esc_html_e('Price', 'acreline'); ?></label>
              <select id="fPrice" name="price">
                <option value="all"><?php esc_html_e('Any price', 'acreline'); ?></option>
                <option value="0-250000"><?php esc_html_e('Under $250k', 'acreline'); ?></option>
                <option value="250000-500000">$250k–$500k</option>
                <option value="500000-750000">$500k–$750k</option>
                <option value="750000-999999999">$750k+</option>
              </select></div>
            <div class="field"><label for="fAcreage"><?php esc_html_e('Acreage', 'acreline'); ?></label>
              <select id="fAcreage" name="acreage">
                <option value="all"><?php esc_html_e('Any', 'acreline'); ?></option>
                <option value="0-1">&lt; 1 acre</option>
                <option value="1-10">1–10</option>
                <option value="10-30">10–30</option>
                <option value="30-999">30+</option>
              </select></div>
            <div class="field"><label for="fTownship"><?php esc_html_e('Area', 'acreline'); ?></label>
              <select id="fTownship" name="township">
                <option value="all"><?php esc_html_e('Any area', 'acreline'); ?></option>
                <option value="Cumberland"><?php esc_html_e('North Ridge', 'acreline'); ?></option>
                <option value="Straban"><?php esc_html_e('Mill Creek', 'acreline'); ?></option>
                <option value="Franklin"><?php esc_html_e('Oak Hollow', 'acreline'); ?></option>
              </select></div>
            <div class="field"><label for="fStatus"><?php esc_html_e('Status', 'acreline'); ?></label>
              <select id="fStatus" name="status">
                <option value="all"><?php esc_html_e('Active + Pending', 'acreline'); ?></option>
                <option value="active"><?php esc_html_e('Active only', 'acreline'); ?></option>
                <option value="pending"><?php esc_html_e('Pending only', 'acreline'); ?></option>
                <option value="sold"><?php esc_html_e('Sold', 'acreline'); ?></option>
              </select></div>
            <div class="field"><label for="fSort"><?php esc_html_e('Sort', 'acreline'); ?></label>
              <select id="fSort" name="sort">
                <option value="price-asc"><?php esc_html_e('Price: low to high', 'acreline'); ?></option>
                <option value="price-desc"><?php esc_html_e('Price: high to low', 'acreline'); ?></option>
                <option value="acres-desc"><?php esc_html_e('Most acres', 'acreline'); ?></option>
                <option value="newest"><?php esc_html_e('Newest first', 'acreline'); ?></option>
              </select></div>
          </div>
          <div class="filters-actions">
            <p id="resultCount" class="result-count" role="status" aria-live="polite"></p>
            <div class="view-toggles">
              <button type="button" class="view-btn is-active" id="gridViewBtn" aria-label="<?php esc_attr_e('Grid view', 'acreline'); ?>" aria-pressed="true">
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
              </button>
              <button type="button" class="view-btn" id="mapViewBtn" aria-label="<?php esc_attr_e('Map view', 'acreline'); ?>" aria-pressed="false">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 7l6-3 6 3 6-3v13l-6 3-6-3-6 3V7z"/><path d="M9 4v13M15 7v13"/></svg>
              </button>
            </div>
          </div>
        </form>
        <div id="listingGrid" class="listing-grid reveal"></div>
        <div id="emptyState" class="listing-empty" hidden>
          <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="24" cy="24" r="20"/><path d="M16 24h16M24 16v16"/></svg>
          <p><?php esc_html_e('No properties match these filters.', 'acreline'); ?></p>
          <button type="button" class="btn btn-secondary" id="emptyResetBtn"><?php esc_html_e('Reset filters', 'acreline'); ?></button>
        </div>
        <div id="mapView" class="listing-map-wrap" hidden>
          <div class="listing-map-stage" aria-label="<?php esc_attr_e('Sample property map', 'acreline'); ?>" role="img">
            <div id="mapPins"></div>
          </div>
        </div>
      </div>
    </section>

    <?php if ($introTitle || $introText) { ?>
    <section class="section">
      <div class="wrap intro-note reveal">
        <?php if ($introTitle) { ?><h2><?php echo $introTitle; ?></h2><?php } ?>
        <?php if ($introText) { ?><p><?php echo $introText; ?></p><?php } ?>
      </div>
    </section>
    <?php } ?>

    <!-- Listing detail modal -->
    <div id="modalOverlay" class="listing-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modalTitle" hidden>
      <div id="listingModal" class="listing-modal">
        <div class="modal-header">
          <button type="button" class="modal-close" id="modalCloseBtn" aria-label="<?php esc_attr_e('Close', 'acreline'); ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 6L6 18M6 6l12 12"/></svg>
          </button>
        </div>
        <div class="modal-gallery" id="modalGallery"></div>
        <div class="modal-body">
          <div class="modal-meta">
            <span class="modal-tag" id="modalTag"></span>
            <span class="status-tag" id="modalStatus"></span>
          </div>
          <h2 class="modal-title" id="modalTitle"></h2>
          <p class="modal-address" id="modalAddress">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <span></span>
          </p>
          <p class="modal-price" id="modalPrice"></p>
          <div class="modal-specs" id="modalSpecs"></div>
          <p class="modal-desc" id="modalDesc"></p>
          <div class="modal-actions">
            <a href="#" class="btn btn-primary" id="modalScheduleBtn"><?php esc_html_e('Book a showing', 'acreline'); ?></a>
            <button type="button" class="btn btn-secondary" id="modalSaveBtn"><?php esc_html_e('Save Listing', 'acreline'); ?></button>
          </div>
          <div class="modal-calc">
            <h3 class="modal-calc-heading"><?php esc_html_e('Estimate payment', 'acreline'); ?></h3>
            <div class="modal-calc-grid">
              <div class="field">
                <label for="calcPrice"><?php esc_html_e('Price', 'acreline'); ?></label>
                <input id="calcPrice" type="number" min="0" step="1000" value="0">
              </div>
              <div class="field">
                <label for="calcDown"><?php esc_html_e('Down (%)', 'acreline'); ?></label>
                <input id="calcDown" type="number" min="0" max="100" value="20">
              </div>
              <div class="field">
                <label for="calcRate"><?php esc_html_e('Rate (%)', 'acreline'); ?></label>
                <input id="calcRate" type="number" min="0" max="30" step="0.1" value="7.0">
              </div>
              <div class="field">
                <label for="calcTerm"><?php esc_html_e('Term (yrs)', 'acreline'); ?></label>
                <select id="calcTerm">
                  <option value="10">10</option>
                  <option value="15">15</option>
                  <option value="20">20</option>
                  <option value="30" selected>30</option>
                </select>
              </div>
            </div>
            <p class="modal-calc-result" id="calcMonthly" role="status" aria-live="polite">—</p>
            <p class="modal-calc-note"><?php esc_html_e('Principal &amp; interest only. Concept demo.', 'acreline'); ?></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Saved listings drawer -->
    <div id="savedDrawer" class="saved-drawer" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Saved listings', 'acreline'); ?>" hidden>
      <div class="saved-drawer-header">
        <h2 class="saved-drawer-title"><?php esc_html_e('Saved listings', 'acreline'); ?> <span id="savedDrawerCount" class="saved-count-badge"></span></h2>
        <button type="button" class="modal-close" id="savedDrawerClose" aria-label="<?php esc_attr_e('Close saved listings', 'acreline'); ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
      </div>
      <div id="savedDrawerList" class="saved-drawer-list"></div>
      <p id="savedDrawerEmpty" class="saved-drawer-empty" hidden><?php esc_html_e('No saved listings yet. Click the heart on any property.', 'acreline'); ?></p>
    </div>

    <!-- Floating saved button (injected by JS) -->
    <button type="button" id="savedFab" class="saved-fab" aria-label="<?php esc_attr_e('View saved listings', 'acreline'); ?>" hidden>
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s-7.5-4.6-10-9.3C.5 8 2.4 4.5 6 4c2.1-.3 4 .8 6 3.1C14 4.8 15.9 3.7 18 4c3.6.5 5.5 4 4 7.7-2.5 4.7-10 9.3-10 9.3z"/></svg>
      <span id="savedFabCount"></span>
    </button>

    <!-- Comparison bar -->
    <div id="compareBar" class="compare-bar" hidden aria-live="polite">
      <span id="compareBarLabel"><?php esc_html_e('0 selected', 'acreline'); ?></span>
      <button type="button" class="btn btn-primary btn-sm" id="compareOpenBtn" disabled><?php esc_html_e('Compare', 'acreline'); ?></button>
      <button type="button" class="btn btn-ghost btn-sm" id="compareClearBtn"><?php esc_html_e('Clear', 'acreline'); ?></button>
    </div>

    <!-- Comparison modal -->
    <div id="compareModal" class="compare-modal-overlay" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Compare listings', 'acreline'); ?>" hidden>
      <div class="compare-modal">
        <div class="compare-modal-header">
          <h2><?php esc_html_e('Compare listings', 'acreline'); ?></h2>
          <button type="button" class="modal-close" id="compareCloseBtn" aria-label="<?php esc_attr_e('Close comparison', 'acreline'); ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 6L6 18M6 6l12 12"/></svg>
          </button>
        </div>
        <div id="compareTable" class="compare-table-wrap"></div>
      </div>
    </div>

    <?php
    return (string) ob_get_clean();
}

/** @param array<string, mixed> $attrs */
function ks_render_area_grid(array $attrs): string
{
    $a = $attrs;
    $gridEyebrow = esc_html($a['gridEyebrow'] ?? 'Neighborhood by neighborhood');
    $hTag = ks_heading_tag($a);
    $gridTitle = wp_kses($a['gridTitle'] ?? 'Where the sample office works', ['em' => [], 'strong' => []]);
    $gridText = wp_kses($a['gridText'] ?? '', ['em' => [], 'strong' => []]);
    $headClass = esc_attr(ks_head_class($attrs));
    $gridCols = sanitize_key((string) ($a['gridCols'] ?? '3'));
    $showIndex = (bool) ($a['showIndex'] ?? true);
    $cardStyle = sanitize_key((string) ($a['cardStyle'] ?? 'classic'));
    $sectionClass = ks_band_section_class($a, 'section');
    $gridClass = 'area-grid';
    if (in_array($gridCols, ['2', '4'], true)) {
        $gridClass .= ' ks-cols--'.$gridCols;
    }
    if (! $showIndex) {
        $gridClass .= ' no-index';
    }
    if ($cardStyle === 'featured') {
        $gridClass .= ' ks-area--featured';
    } elseif ($cardStyle === 'compact') {
        $gridClass .= ' ks-area--compact';
    }
    $pinSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>';

    $areas = [];
    for ($i = 1; $i <= 6; $i++) {
        $t = trim((string) ($a["area{$i}Title"] ?? ''));
        if ($t === '') {
            continue;
        }
        $areas[] = [
            'meta' => (string) ($a["area{$i}Meta"] ?? ''),
            'title' => $t,
            'body' => (string) ($a["area{$i}Body"] ?? ''),
            'image' => (string) ($a["area{$i}ImageUrl"] ?? ''),
            'cta' => (string) ($a["area{$i}Cta"] ?? ''),
            'url' => (string) ($a["area{$i}Url"] ?? ''),
        ];
    }

    $primaryLabel = trim((string) ($a['primaryLabel'] ?? ''));
    $primaryUrl = (string) ($a['primaryUrl'] ?? '');
    if ($primaryUrl === '') {
        $primaryUrl = home_url('/listings');
    }

    $hid = function_exists('wp_unique_id') ? wp_unique_id('ks-areas-') : 'ks-areas-heading';

    ob_start();
    ?>
    <section class="<?php echo esc_attr($sectionClass); ?>" aria-labelledby="<?php echo esc_attr($hid); ?>">
      <div class="wrap">
        <div class="<?php echo $headClass; ?>">
          <?php if ($gridEyebrow !== '') { ?><p class="eyebrow"><?php echo $gridEyebrow; ?></p><?php } ?>
          <<?php echo $hTag; ?> id="<?php echo esc_attr($hid); ?>"><?php echo $gridTitle; ?></<?php echo $hTag; ?>>
          <?php if ($gridText) { ?><p><?php echo $gridText; ?></p><?php } ?>
        </div>
        <div class="<?php echo esc_attr($gridClass); ?>">
          <?php foreach ($areas as $idx => $area) {
              $hasPhoto = $area['image'] !== '';
              $cardClass = 'area-card reveal'.($hasPhoto ? ' has-photo' : '');
              ?>
            <article class="<?php echo esc_attr($cardClass); ?>">
              <?php if ($hasPhoto) { ?>
                <div class="area-card__media">
                  <img src="<?php echo esc_url($area['image']); ?>" alt="" loading="lazy" decoding="async">
                </div>
                <div class="area-card__body">
              <?php } ?>
              <?php if ($showIndex) { ?>
                <p class="area-index" aria-hidden="true"><?php echo esc_html(sprintf('%02d', $idx + 1)); ?></p>
              <?php } ?>
              <p class="area-meta"><?php echo wp_kses($area['meta'], ['em' => [], 'strong' => []]); ?></p>
              <h3><span class="pin"><?php echo $pinSvg; ?></span><?php echo wp_kses($area['title'], ['em' => [], 'strong' => []]); ?></h3>
              <p><?php echo wp_kses($area['body'], ['em' => [], 'strong' => []]); ?></p>
              <?php if ($area['cta'] !== '' && $area['url'] !== '') { ?>
                <a class="area-card__cta" href="<?php echo esc_url($area['url']); ?>"><?php echo esc_html($area['cta']); ?></a>
              <?php } ?>
              <?php if ($hasPhoto) { ?></div><?php } ?>
            </article>
          <?php } ?>
        </div>
        <?php if ($primaryLabel !== '') { ?>
          <p class="ks-region__cta" style="margin-top:28px">
            <a class="btn btn-primary" href="<?php echo esc_url($primaryUrl); ?>"><?php echo esc_html($primaryLabel); ?></a>
          </p>
        <?php } ?>
      </div>
    </section>
    <?php
    return (string) ob_get_clean();
}

/** @param array<string, mixed> $attrs */
function ks_render_tools_section(array $attrs): string
{
    $a = $attrs;

    $showIntro = (bool) ($a['showIntro'] ?? true);
    $sectionStyle = sanitize_key((string) ($a['sectionStyle'] ?? 'alt'));
    $headClass = esc_attr(ks_head_class($a));

    $toolsSectionClass = 'section';
    if ($sectionStyle === 'alt') {
        $toolsSectionClass .= ' section-alt';
    } elseif ($sectionStyle === 'dark') {
        $toolsSectionClass .= ' ks-section--dark';
    }

    ob_start();

    if ($showIntro) {
        ?>
    <section class="section">
      <div class="wrap">
        <div class="guide-intro reveal">
          <h2><?php echo wp_kses($a['introTitle'] ?? "What's different about buying here", ['em' => [], 'strong' => []]); ?></h2>
          <p><?php echo wp_kses($a['introText'] ?? '', ['em' => [], 'strong' => []]); ?></p>
        </div>
      </div>
    </section>
        <?php
    }
    ?>
    <section class="<?php echo esc_attr($toolsSectionClass); ?>" aria-labelledby="guide-tools-heading">
      <div class="wrap">
        <header class="<?php echo $headClass; ?>">
          <p class="eyebrow"><?php echo esc_html($a['eyebrow'] ?? 'Run Your Numbers'); ?></p>
          <h2 id="guide-tools-heading"><?php echo wp_kses($a['title'] ?? 'Land-loan &amp; pre-qualification tools', ['em' => [], 'strong' => []]); ?></h2>
          <p><?php echo wp_kses($a['text'] ?? '', ['em' => [], 'strong' => []]); ?></p>
        </header>
        <?php echo ks_guide_tools_html($a); ?>
      </div>
    </section>
    <?php
    return (string) ob_get_clean();
}

/** @param array<string, mixed> $attrs */
function ks_render_how_we_work(array $attrs): string
{
    $eyebrow = esc_html($attrs['eyebrow'] ?? 'How we work');
    $hTag = ks_heading_tag($attrs);
    $title = wp_kses($attrs['title'] ?? 'What working with this office looks like', ['em' => [], 'strong' => []]);
    $text = wp_kses($attrs['text'] ?? 'No pressure, no jargon, and a straight answer about the house, the block, and the next step.', ['em' => []]);
    $headClass = esc_attr(ks_head_class($attrs));
    $stepsStyle = sanitize_key((string) ($attrs['stepsStyle'] ?? 'numbered'));
    $stepsLayout = sanitize_key((string) ($attrs['stepsLayout'] ?? 'row'));
    $sectionClass = ks_band_section_class($attrs, 'section');
    $sectionClass .= ' ks-how-we-work--'.$stepsStyle.' ks-how-we-work--'.$stepsLayout;
    $hid = function_exists('wp_unique_id') ? wp_unique_id('ks-hww-') : 'how-we-work-heading';

    ob_start();
    ?>
    <section class="<?php echo esc_attr($sectionClass); ?>" aria-labelledby="<?php echo esc_attr($hid); ?>">
      <div class="wrap">
        <header class="<?php echo $headClass; ?>">
          <?php if ($eyebrow !== '') { ?><p class="eyebrow"><?php echo $eyebrow; ?></p><?php } ?>
          <<?php echo $hTag; ?> id="<?php echo esc_attr($hid); ?>"><?php echo $title; ?></<?php echo $hTag; ?>>
          <?php if ($text !== '') { ?><p><?php echo $text; ?></p><?php } ?>
        </header>
        <?php echo ks_how_we_work_steps_html($attrs); ?>
      </div>
    </section>
    <?php
    return (string) ob_get_clean();
}

/** @param array<string, mixed> $attrs */
function ks_render_office_info(array $attrs): string
{
    $identity = Identity::toArray();
    $officeTitle = esc_html($attrs['officeTitle'] ?? '') ?: esc_html($identity['brand'] ?? 'Acreline');
    $address = esc_html(Identity::address());
    $phone = esc_html(Identity::phone());
    $phoneHref = esc_url(Identity::phoneHref());
    $email = esc_html(Identity::email());
    $hours = esc_html(Identity::hours());

    ob_start();
    ?>
    <div class="contact-info reveal">
      <p class="eyebrow"><?php esc_html_e('Our Office', 'acreline'); ?></p>
      <h2><?php echo $officeTitle; ?></h2>
      <dl>
        <div><dt><?php esc_html_e('Address', 'acreline'); ?></dt><dd><?php echo nl2br($address); ?></dd></div>
        <div><dt><?php esc_html_e('Phone', 'acreline'); ?></dt><dd><a href="<?php echo $phoneHref; ?>"><?php echo $phone; ?></a></dd></div>
        <div><dt><?php esc_html_e('Email', 'acreline'); ?></dt><dd><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo $email; ?></a></dd></div>
        <div><dt><?php esc_html_e('Hours', 'acreline'); ?></dt><dd><?php echo nl2br($hours); ?></dd></div>
      </dl>
      <div class="map-embed" role="img" aria-label="<?php esc_attr_e('Illustrative map showing the sample office', 'acreline'); ?>">
        <div class="map-roads"></div>
        <div class="map-road-3"></div>
        <span class="pin-static">
          <svg viewBox="0 0 24 24"><path fill="#1f6b4a" stroke="#fffcf7" stroke-width="1.5" d="M12 2C7.6 2 4 5.6 4 10c0 6 8 12 8 12s8-6 8-12c0-4.4-3.6-8-8-8z"/><circle cx="12" cy="10" r="3" fill="#fffcf7"/></svg>
        </span>
        <div class="map-legend"><span><?php esc_html_e('100 Concept Way · Concept demo', 'acreline'); ?></span></div>
      </div>
    </div>
    <?php
    return (string) ob_get_clean();
}

/** @param array<string, mixed> $attrs */
function ks_render_contact_form(array $attrs): string
{
    $formTitle = wp_kses($attrs['formTitle'] ?? 'Send us a message', ['em' => [], 'strong' => []]);
    $formText = wp_kses($attrs['formText'] ?? "Tell us what you're looking for and we'll be in touch.", ['em' => [], 'strong' => []]);

    ob_start();
    ?>
    <section class="section">
      <div class="wrap">
        <div class="contact-grid">
          <?php echo ks_render_office_info([]); ?>
          <div class="reveal">
            <div class="tool-card">
              <h3>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 4h16v12H5.17L4 17.17z"/></svg>
                <?php echo $formTitle; ?>
              </h3>
              <p class="form-lede"><?php echo $formText; ?></p>
              <form id="contactForm">
                <div class="form-grid two">
                  <div class="field"><label for="cName"><?php esc_html_e('Full name', 'acreline'); ?></label><input type="text" id="cName" name="name" autocomplete="name" required placeholder="Jordan Weikert"></div>
                  <div class="field"><label for="cPhone"><?php esc_html_e('Phone', 'acreline'); ?></label><input type="tel" id="cPhone" name="phone" autocomplete="tel" required placeholder="(555) 010-0142"></div>
                  <div class="field"><label for="cEmail"><?php esc_html_e('Email', 'acreline'); ?></label><input type="email" id="cEmail" name="email" autocomplete="email" required placeholder="you@acreline-concept.test"></div>
                  <div class="field"><label for="cTopic"><?php esc_html_e("I'm interested in", 'acreline'); ?></label>
                    <select id="cTopic" name="topic">
                      <option><?php esc_html_e('Buying a home', 'acreline'); ?></option>
                      <option><?php esc_html_e('Buying a condo or townhome', 'acreline'); ?></option>
                      <option><?php esc_html_e('Selling my property', 'acreline'); ?></option>
                      <option><?php esc_html_e('A free valuation', 'acreline'); ?></option>
                      <option><?php esc_html_e('Something else', 'acreline'); ?></option>
                    </select></div>
                  <div class="field field-span"><label for="cMessage"><?php esc_html_e('Message', 'acreline'); ?></label><textarea id="cMessage" name="message" rows="4" placeholder="<?php esc_attr_e('e.g. Looking for a 3-bed near Oak Hollow', 'acreline'); ?>"></textarea></div>
                  <?php echo ks_render_consent_field('contactConsent'); ?>
                  <div class="field field-span"><button type="submit" class="btn btn-primary btn-block"><?php esc_html_e('Send Message', 'acreline'); ?></button></div>
                </div>
              </form>
              <div class="confirm-msg" id="contactConfirm" role="status" aria-live="polite">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>
                <span><?php esc_html_e('Thanks! This is a concept demo — on your live site, this message would go to the listing office.', 'acreline'); ?></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <?php
    return (string) ob_get_clean();
}

/** @param array<string, mixed> $attrs */
function ks_render_book_note(array $attrs): string
{
    $note = wp_kses($attrs['note'] ?? 'Demo only — no emails, texts or calendar invites are sent.', ['em' => [], 'strong' => []]);
    $noteStyle = sanitize_key((string) ($attrs['noteStyle'] ?? 'plain'));
    $showPhoto = (bool) ($attrs['showSidePhoto'] ?? true);

    $noteCls = match ($noteStyle) {
        'info' => 'ks-note--info',
        'warning' => 'ks-note--warning',
        'banner' => 'ks-note--banner',
        default => '',
    };

    $bookingForm = ks_booking_form_html();
    $bookingPhoto = $showPhoto ? ks_booking_photo_html() : '';

    ob_start();
    ?>
    <section class="section">
      <div class="wrap">
        <?php if ($note) { ?>
          <p class="<?php echo esc_attr(trim('notice '.$noteCls)); ?>"><?php echo $note; ?></p>
        <?php } ?>
        <div class="booking-shell<?php echo $showPhoto ? '' : ' ks-no-photo'; ?>">
          <?php echo $bookingForm; ?>
          <?php echo $bookingPhoto; ?>
        </div>
      </div>
    </section>
    <?php
    return (string) ob_get_clean();
}

/** @param array<string, mixed> $attrs */
function ks_render_custom_block(array $attrs): string
{
    $blockId = sanitize_key((string) ($attrs['blockId'] ?? ''));
    if ($blockId === '') {
        return '';
    }

    $defs = ks_get_custom_block_definitions();
    if (! isset($defs[$blockId])) {
        return '';
    }

    $def = $defs[$blockId];
    $fields = (array) ($attrs['fields'] ?? []);
    $title = esc_html($def['title'] ?? $blockId);

    ob_start();
    ?>
    <section class="section ks-custom-block" data-block-id="<?php echo esc_attr($blockId); ?>">
      <div class="wrap">
        <div class="ks-custom-block-inner reveal">
          <h2 class="ks-custom-block-title"><?php echo $title; ?></h2>
          <?php foreach ((array) ($def['fields'] ?? []) as $field) {
              $name = sanitize_key((string) ($field['name'] ?? ''));
              $label = esc_html($field['label'] ?? $name);
              $value = wp_kses((string) ($fields[$name] ?? $field['default'] ?? ''), ['em' => [], 'strong' => [], 'br' => [], 'a' => ['href' => []]]);
              if (! $name || ! $value) {
                  continue;
              }
              if (($field['type'] ?? 'text') === 'textarea') { ?>
                <div class="ks-field-block"><p class="ks-field-label"><?php echo $label; ?></p><div class="ks-field-body"><?php echo $value; ?></div></div>
              <?php } elseif (($field['type'] ?? 'text') === 'image') { ?>
                <figure class="ks-field-image"><img src="<?php echo esc_url($value); ?>" alt="<?php echo esc_attr($label); ?>" loading="lazy"></figure>
              <?php } else { ?>
                <p class="ks-field-text"><span class="ks-field-label"><?php echo $label; ?>:</span> <?php echo $value; ?></p>
              <?php }
              } ?>
        </div>
      </div>
    </section>
    <?php
    return (string) ob_get_clean();
}

// ---------------------------------------------------------------------------
// Shared item helpers + defaults for checklist-style blocks
// ---------------------------------------------------------------------------

/**
 * @param  array<string, mixed>  $attrs
 * @param  list<array{title: string, text: string}>  $defaults
 * @return list<array{title: string, text: string}>
 */
function ks_collect_items(array $attrs, string $prefix, int $max, array $defaults): array
{
    $items = [];
    for ($i = 1; $i <= $max; $i++) {
        $title = trim((string) ($attrs["{$prefix}{$i}Title"] ?? ''));
        $text = trim((string) ($attrs["{$prefix}{$i}Text"] ?? ''));
        $fallback = $defaults[$i - 1] ?? ['title' => '', 'text' => ''];
        $items[] = [
            'title' => $title !== '' ? $title : (string) $fallback['title'],
            'text' => $text !== '' ? $text : (string) $fallback['text'],
        ];
    }

    return array_values(array_filter(
        $items,
        static fn (array $item): bool => $item['title'] !== '' || $item['text'] !== ''
    ));
}

/** @return list<array{title: string, text: string}> */
function ks_default_trust_items(): array
{
    return [
        ['title' => __('Same-day reply', 'acreline'), 'text' => __('Messages sent before 4 PM on a business day get a same-day response from a real agent.', 'acreline')],
        ['title' => __('Specialist matched', 'acreline'), 'text' => __('Your inquiry is routed to the agent who specialises in your area and property type — not whoever is next in the queue.', 'acreline')],
        ['title' => __('No obligation', 'acreline'), 'text' => __('Reaching out does not start a sales process. Ask questions, compare options, and decide at your own pace.', 'acreline')],
        ['title' => __('Private inbox', 'acreline'), 'text' => __('Your contact details stay with this office. No third-party lead sharing, no spam, no automated drip campaigns.', 'acreline')],
    ];
}

/** @return list<array{title: string, text: string}> */
function ks_default_guide_items(): array
{
    return [
        ['title' => __('Financing in writing', 'acreline'), 'text' => __('Know whether you are pre-approved, paying cash, or still shopping lenders. That shapes which homes are realistic to tour and how fast you can write an offer.', 'acreline')],
        ['title' => __('Inspection window', 'acreline'), 'text' => __('Ask what a typical inspection period looks like in this market and which specialists (home, radon, sewer, roof) buyers usually book. Budget time, not just money.', 'acreline')],
        ['title' => __('HOA, condo docs, and dues', 'acreline'), 'text' => __('If the home is in an association, request the documents early. Dues, reserves, rental caps, and pending assessments change monthly payment and resale.', 'acreline')],
        ['title' => __('Title, survey, and easements', 'acreline'), 'text' => __('Confirm who pays for title insurance and whether a recent survey exists. Shared driveways and utility easements should be on the report, not a surprise after closing.', 'acreline')],
        ['title' => __('Flood zone and insurance', 'acreline'), 'text' => __('Check the FEMA flood map and ask about homeowners insurance quotes before you waive contingencies. Zone A properties often need flood coverage on a financed purchase.', 'acreline')],
        ['title' => __('School and commute — verify, don’t assume', 'acreline'), 'text' => __('District lines and bus routes change. Confirm with the district, not a listing remark. Same for drive times at the hour you actually travel.', 'acreline')],
        ['title' => __('Fair Housing in your search notes', 'acreline'), 'text' => __('Describe the house and the lot — not who “belongs” in a neighborhood. Steer clear of demographic or religious preference in notes you send your agent.', 'acreline')],
        ['title' => __('Offer terms, not just price', 'acreline'), 'text' => __('Closing date, inspection length, and what stays with the house often matter as much as the number. Write those down before you fall in love with a kitchen.', 'acreline')],
    ];
}

/** @return list<array{title: string, text: string}> */
function ks_default_prep_left_items(): array
{
    return [
        ['title' => __('Comfortable shoes', 'acreline'), 'text' => __('You will walk rooms, stairs, and often the yard. Wear something you can stay in for an hour.', 'acreline')],
        ['title' => __('Your priority list', 'acreline'), 'text' => __('Write down the three things that would make or break the purchase. Your agent will address them on site, not in a follow-up email.', 'acreline')],
        ['title' => __('Financing status', 'acreline'), 'text' => __('Know roughly what you are approved for — or what you plan to pay cash. It shapes which homes make sense to walk.', 'acreline')],
        ['title' => __('Your timeline', 'acreline'), 'text' => __('Are you buying in the next 60 days or researching for next year? Your agent will calibrate the conversation accordingly.', 'acreline')],
        ['title' => __('All decision-makers', 'acreline'), 'text' => __('If a partner, parent, or business partner will be part of the purchase, bring them. An extra showing costs everyone time.', 'acreline')],
    ];
}

/** @return list<array{title: string, text: string}> */
function ks_default_prep_right_items(): array
{
    return [
        ['title' => __('Listing briefing', 'acreline'), 'text' => __('Floor plan if available, tax record, HOA notes, and any disclosed issues — ready before you arrive.', 'acreline')],
        ['title' => __('Comps and price context', 'acreline'), 'text' => __('Recent sales of similar homes in the same area, so you understand what the asking price reflects.', 'acreline')],
        ['title' => __('On-site answers', 'acreline'), 'text' => __('Questions about condition, systems, and neighborhood logistics answered on the walk — not in a follow-up email three days later.', 'acreline')],
        ['title' => __('No pressure close', 'acreline'), 'text' => __('The goal of a showing is information — not a signature. Agents do not push offers on the property or "back at the office."', 'acreline')],
    ];
}

/** @param array<string, mixed> $attrs */
function ks_render_trust_strip(array $attrs): string
{
    $items = ks_collect_items($attrs, 'item', 4, ks_default_trust_items());
    $icons = [
        '<svg width="24" height="24" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="10" cy="10" r="8"/><path d="M10 6v4l3 3"/></svg>',
        '<svg width="24" height="24" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 2l2.1 4.3 4.7.7-3.4 3.3.8 4.7L10 12.7l-4.2 2.3.8-4.7L3.2 7l4.7-.7z"/></svg>',
        '<svg width="24" height="24" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19V7l6-5 6 5v12"/><path d="M9 19v-6h2v6"/></svg>',
        '<svg width="24" height="24" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="16" height="11" rx="2"/><path d="M2 7l8 5 8-5"/></svg>',
    ];

    ob_start();
    ?>
    <section class="section section-alt contact-trust-strip" aria-label="<?php esc_attr_e('Contact commitments', 'acreline'); ?>">
      <div class="wrap">
        <ul class="contact-trust-list" role="list">
          <?php foreach ($items as $i => $item) { ?>
            <li>
              <span class="contact-trust-icon" aria-hidden="true"><?php echo $icons[$i] ?? $icons[0]; ?></span>
              <div>
                <strong><?php echo esc_html($item['title']); ?></strong>
                <span><?php echo esc_html($item['text']); ?></span>
              </div>
            </li>
          <?php } ?>
        </ul>
      </div>
    </section>
    <?php
    return (string) ob_get_clean();
}

/** @param array<string, mixed> $attrs */
function ks_render_checklist(array $attrs): string
{
    $eyebrow = esc_html($attrs['eyebrow'] ?? '');
    $title = wp_kses((string) ($attrs['title'] ?? ''), ['em' => [], 'strong' => []]);
    $text = wp_kses((string) ($attrs['text'] ?? ''), ['em' => []]);
    $headClass = esc_attr(ks_head_class($attrs));
    $items = ks_collect_items($attrs, 'item', 8, ks_default_guide_items());
    $primaryLabel = trim((string) ($attrs['primaryLabel'] ?? ''));
    $secondaryLabel = trim((string) ($attrs['secondaryLabel'] ?? ''));
    $primaryUrl = esc_url((string) ($attrs['primaryUrl'] ?? '') !== '' ? (string) $attrs['primaryUrl'] : home_url('/book/'));
    $secondaryUrl = esc_url((string) ($attrs['secondaryUrl'] ?? '') !== '' ? (string) $attrs['secondaryUrl'] : home_url('/listings'));

    ob_start();
    ?>
    <section class="section guide-checklist-section" aria-labelledby="guide-checklist-heading">
      <div class="wrap">
        <header class="<?php echo $headClass; ?>">
          <?php if ($eyebrow !== '') { ?><p class="eyebrow"><?php echo $eyebrow; ?></p><?php } ?>
          <?php if ($title !== '') { ?><h2 id="guide-checklist-heading"><?php echo $title; ?></h2><?php } ?>
          <?php if ($text !== '') { ?><p><?php echo $text; ?></p><?php } ?>
        </header>
        <ol class="guide-checklist" role="list">
          <?php foreach ($items as $i => $item) { ?>
            <li class="guide-checklist__item">
              <span class="guide-checklist__num" aria-hidden="true"><?php echo esc_html(str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT)); ?></span>
              <div class="guide-checklist__body">
                <strong><?php echo esc_html($item['title']); ?></strong>
                <p><?php echo esc_html($item['text']); ?></p>
              </div>
            </li>
          <?php } ?>
        </ol>
        <?php if ($primaryLabel !== '' || $secondaryLabel !== '') { ?>
          <p style="margin-top:32px;text-align:center">
            <?php if ($primaryLabel !== '') { ?>
              <a class="btn btn-primary" href="<?php echo $primaryUrl; ?>"><?php echo esc_html($primaryLabel); ?></a>
            <?php } ?>
            <?php if ($secondaryLabel !== '') { ?>
              <a class="btn btn-outline" href="<?php echo $secondaryUrl; ?>" style="margin-left:12px"><?php echo esc_html($secondaryLabel); ?></a>
            <?php } ?>
          </p>
        <?php } ?>
      </div>
    </section>
    <?php
    return (string) ob_get_clean();
}

/** @param array<string, mixed> $attrs */
function ks_render_prep_checklist(array $attrs): string
{
    $leftHeading = esc_html($attrs['leftHeading'] ?? '');
    $leftLead = esc_html($attrs['leftLead'] ?? '');
    $rightHeading = esc_html($attrs['rightHeading'] ?? '');
    $rightLead = esc_html($attrs['rightLead'] ?? '');
    $leftItems = ks_collect_items($attrs, 'left', 5, ks_default_prep_left_items());
    $rightItems = ks_collect_items($attrs, 'right', 4, ks_default_prep_right_items());
    $checkSvg = '<svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="4 10 8 14 16 6"/></svg>';
    $starSvg = '<svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 2l2.1 4.3 4.7.7-3.4 3.3.8 4.7L10 12.7l-4.2 2.3.8-4.7L3.2 7l4.7-.7z"/></svg>';

    ob_start();
    ?>
    <section class="section book-prep-section" aria-labelledby="book-prep-heading">
      <div class="wrap book-prep-grid">
        <div class="book-prep-col">
          <h2 id="book-prep-heading" class="book-prep-heading"><?php echo $leftHeading; ?></h2>
          <?php if ($leftLead !== '') { ?><p class="book-prep-lead"><?php echo $leftLead; ?></p><?php } ?>
          <ul class="book-checklist" role="list">
            <?php foreach ($leftItems as $item) { ?>
              <li>
                <?php echo $checkSvg; ?>
                <strong><?php echo esc_html($item['title']); ?></strong>
                <span><?php echo esc_html($item['text']); ?></span>
              </li>
            <?php } ?>
          </ul>
        </div>
        <div class="book-prep-col">
          <h2 class="book-prep-heading"><?php echo $rightHeading; ?></h2>
          <?php if ($rightLead !== '') { ?><p class="book-prep-lead"><?php echo $rightLead; ?></p><?php } ?>
          <ul class="book-checklist book-checklist--accent" role="list">
            <?php foreach ($rightItems as $item) { ?>
              <li>
                <?php echo $starSvg; ?>
                <strong><?php echo esc_html($item['title']); ?></strong>
                <span><?php echo esc_html($item['text']); ?></span>
              </li>
            <?php } ?>
          </ul>
        </div>
      </div>
    </section>
    <?php
    return (string) ob_get_clean();
}

/** @param array<string, mixed> $attrs */
function ks_render_compare_table(array $attrs): string
{
    $eyebrow = esc_html($attrs['eyebrow'] ?? '');
    $title = wp_kses((string) ($attrs['title'] ?? ''), ['em' => [], 'strong' => []]);
    $text = wp_kses((string) ($attrs['text'] ?? ''), ['em' => []]);
    $disclaimer = esc_html($attrs['disclaimer'] ?? '');
    $headClass = esc_attr(ks_head_class($attrs));
    $cols = [
        esc_html($attrs['col1'] ?? __('Area', 'acreline')),
        esc_html($attrs['col2'] ?? __('Housing mix', 'acreline')),
        esc_html($attrs['col3'] ?? __('Typical price range', 'acreline')),
        esc_html($attrs['col4'] ?? __('Utilities', 'acreline')),
        esc_html($attrs['col5'] ?? __('Best for', 'acreline')),
    ];

    $rows = [];
    for ($i = 1; $i <= 6; $i++) {
        $name = trim((string) ($attrs["row{$i}Col1"] ?? ''));
        if ($name === '') {
            continue;
        }
        $rows[] = [
            esc_html($name),
            esc_html((string) ($attrs["row{$i}Col2"] ?? '')),
            esc_html((string) ($attrs["row{$i}Col3"] ?? '')),
            esc_html((string) ($attrs["row{$i}Col4"] ?? '')),
            esc_html((string) ($attrs["row{$i}Col5"] ?? '')),
        ];
    }

    ob_start();
    ?>
    <section class="section areas-compare-section" aria-labelledby="areas-compare-heading">
      <div class="wrap">
        <header class="<?php echo $headClass; ?>">
          <?php if ($eyebrow !== '') { ?><p class="eyebrow"><?php echo $eyebrow; ?></p><?php } ?>
          <?php if ($title !== '') { ?><h2 id="areas-compare-heading"><?php echo $title; ?></h2><?php } ?>
          <?php if ($text !== '') { ?><p><?php echo $text; ?></p><?php } ?>
        </header>
        <div class="areas-compare-wrap reveal" role="region" aria-label="<?php esc_attr_e('Area comparison table', 'acreline'); ?>">
          <table class="areas-compare-table" aria-describedby="areas-compare-heading">
            <thead>
              <tr>
                <?php foreach ($cols as $col) { ?><th scope="col"><?php echo $col; ?></th><?php } ?>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($rows as $row) { ?>
                <tr>
                  <th scope="row" class="ac-area-name"><?php echo $row[0]; ?></th>
                  <td><?php echo $row[1]; ?></td>
                  <td><?php echo $row[2]; ?></td>
                  <td><?php echo $row[3]; ?></td>
                  <td><?php echo $row[4]; ?></td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        <?php if ($disclaimer !== '') { ?>
          <p class="areas-compare-disclaimer">
            <svg width="13" height="13" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="10" cy="10" r="8"/><path d="M10 9v5M10 7v.5"/></svg>
            <?php echo $disclaimer; ?>
          </p>
        <?php } ?>
      </div>
    </section>
    <?php
    return (string) ob_get_clean();
}

/** @param array<string, mixed> $attrs */
function ks_render_topic_cards(array $attrs): string
{
    $eyebrow = esc_html($attrs['eyebrow'] ?? '');
    $title = wp_kses((string) ($attrs['title'] ?? ''), ['em' => [], 'strong' => []]);
    $text = wp_kses((string) ($attrs['text'] ?? ''), ['em' => []]);
    $headClass = esc_attr(ks_head_class($attrs, 'mkt-lead reveal'));

    $cards = [];
    for ($i = 1; $i <= 3; $i++) {
        $cardTitle = trim((string) ($attrs["card{$i}Title"] ?? ''));
        $kicker = trim((string) ($attrs["card{$i}Kicker"] ?? ''));
        $cardText = trim((string) ($attrs["card{$i}Text"] ?? ''));
        if ($cardTitle === '' && $kicker === '' && $cardText === '') {
            continue;
        }
        $cards[] = [
            'kicker' => $kicker,
            'title' => $cardTitle,
            'text' => $cardText,
        ];
    }

    ob_start();
    ?>
    <section class="section">
      <div class="wrap">
        <header class="<?php echo $headClass; ?>">
          <?php if ($eyebrow !== '') { ?><p class="eyebrow"><?php echo $eyebrow; ?></p><?php } ?>
          <div>
            <?php if ($title !== '') { ?><h2 id="blog-help-heading"><?php echo $title; ?></h2><?php } ?>
            <?php if ($text !== '') { ?><p class="lede"><?php echo $text; ?></p><?php } ?>
          </div>
        </header>
        <div class="scan-grid cols-3 reveal">
          <?php foreach ($cards as $card) { ?>
            <article class="scan-card">
              <?php if ($card['kicker'] !== '') { ?><span class="num"><?php echo esc_html($card['kicker']); ?></span><?php } ?>
              <?php if ($card['title'] !== '') { ?><h3><?php echo esc_html($card['title']); ?></h3><?php } ?>
              <?php if ($card['text'] !== '') { ?><p><?php echo esc_html($card['text']); ?></p><?php } ?>
            </article>
          <?php } ?>
        </div>
      </div>
    </section>
    <?php
    return (string) ob_get_clean();
}

/** @param array<string, mixed> $attrs */
function ks_render_post_grid(array $attrs): string
{
    $emptyText = (string) ($attrs['emptyText'] ?? '');
    $paged = max(1, (int) get_query_var('paged'), (int) get_query_var('page'));
    $query = new \WP_Query([
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => (int) get_option('posts_per_page', 10),
        'paged' => $paged,
    ]);

    ob_start();
    ?>
    <section class="section section-alt">
      <div class="wrap">
        <?php if (! $query->have_posts()) { ?>
          <p class="empty-state"><?php echo esc_html($emptyText !== '' ? $emptyText : __('Sample posts load with Tools → Seed Acreline demo.', 'acreline')); ?>
            <?php echo wp_kses(
                sprintf(
                    /* translators: 1: guide URL, 2: listings URL */
                    __(' Use <a href="%1$s">the buyer’s guide</a> or <a href="%2$s">browse listings</a>.', 'acreline'),
                    esc_url(home_url('/guide')),
                    esc_url(home_url('/listings'))
                ),
                ['a' => ['href' => []]]
            ); ?></p>
        <?php } else { ?>
          <div class="blog-grid reveal">
            <?php while ($query->have_posts()) {
                $query->the_post();
                $id = (int) get_the_ID();
                $minutes = max(1, (int) ceil(str_word_count(wp_strip_all_tags((string) get_post_field('post_content', $id))) / 200));
                $meta = wp_strip_all_tags(get_the_category_list(' · ')) ?: __('Notes', 'acreline');
                ?>
              <a class="blog-card" href="<?php echo esc_url((string) get_permalink()); ?>">
                <img
                  src="<?php echo esc_url(HeroImage::cardUrl($id)); ?>"
                  width="900"
                  height="560"
                  alt="<?php echo esc_attr(HeroImage::cardAlt($id)); ?>"
                  loading="lazy"
                  decoding="async"
                >
                <div class="blog-card-body">
                  <span class="blog-meta"><?php echo esc_html($meta); ?> · <?php echo esc_html((string) $minutes); ?> min</span>
                  <h2><?php echo esc_html(get_the_title()); ?></h2>
                  <p><?php echo esc_html(get_the_excerpt()); ?></p>
                  <span class="teaser-link"><?php esc_html_e('Read post →', 'acreline'); ?></span>
                </div>
              </a>
            <?php } ?>
          </div>
          <?php
            echo wp_kses_post(paginate_links([
                'total' => (int) $query->max_num_pages,
                'current' => $paged,
                'type' => 'list',
                'prev_text' => __('Previous', 'acreline'),
                'next_text' => __('Next', 'acreline'),
            ]) ?: '');
            ?>
        <?php } ?>
      </div>
    </section>
    <?php
    wp_reset_postdata();

    return (string) ob_get_clean();
}

/** @param array<string, mixed> $attrs */
function ks_render_region_coverage(array $attrs): string
{
    $a = $attrs;
    $eyebrow = esc_html((string) ($a['eyebrow'] ?? 'Area service'));
    $hTag = ks_heading_tag($a);
    $title = wp_kses((string) ($a['title'] ?? 'How we cover the region'), ['em' => [], 'strong' => []]);
    $text = wp_kses((string) ($a['text'] ?? ''), ['em' => [], 'strong' => []]);
    $headClass = esc_attr(ks_head_class($a));
    $layout = sanitize_key((string) ($a['layout'] ?? 'split'));
    if (! in_array($layout, ['split', 'bento', 'grid'], true)) {
        $layout = 'split';
    }
    $iconStyle = sanitize_key((string) ($a['iconStyle'] ?? 'pin'));
    $areaCols = sanitize_key((string) ($a['areaCols'] ?? '3'));
    $showMap = (bool) ($a['showMap'] ?? true);
    $showMethods = (bool) ($a['showMethods'] ?? true);
    $showIndex = (bool) ($a['showIndex'] ?? true);
    $showSchema = (bool) ($a['showSchema'] ?? true);
    $maxAreas = (int) ($a['maxAreas'] ?? 3);
    if ($maxAreas < 1) {
        $maxAreas = 1;
    }
    if ($maxAreas > 6) {
        $maxAreas = 6;
    }

    $sectionClass = ks_band_section_class($a, 'section');
    $sectionClass .= ' ks-region ks-region--'.$layout;
    if ($iconStyle === 'none') {
        $sectionClass .= ' ks-region--icon-none';
    }
    if (! $showIndex) {
        $sectionClass .= ' ks-region--no-index';
    }

    $areas = [];
    for ($i = 1; $i <= $maxAreas; $i++) {
        $t = trim((string) ($a["area{$i}Title"] ?? ''));
        if ($t === '') {
            continue;
        }
        $areas[] = [
            'kicker' => (string) ($a["area{$i}Kicker"] ?? ''),
            'title' => $t,
            'body' => (string) ($a["area{$i}Body"] ?? ''),
            'stat' => (string) ($a["area{$i}Stat"] ?? ''),
            'url' => (string) ($a["area{$i}Url"] ?? ''),
        ];
    }

    $methods = [];
    for ($i = 1; $i <= 3; $i++) {
        $mt = trim((string) ($a["method{$i}Title"] ?? ''));
        if ($mt === '') {
            continue;
        }
        $methods[] = [$mt, (string) ($a["method{$i}Text"] ?? '')];
    }

    $primaryLabel = trim((string) ($a['primaryLabel'] ?? ''));
    $primaryUrl = (string) ($a['primaryUrl'] ?? '');
    if ($primaryUrl === '') {
        $primaryUrl = home_url('/areas');
    }
    $secondaryLabel = trim((string) ($a['secondaryLabel'] ?? ''));
    $secondaryUrl = (string) ($a['secondaryUrl'] ?? '');
    if ($secondaryUrl === '') {
        $secondaryUrl = home_url('/book');
    }

    $pinSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>';
    $hid = function_exists('wp_unique_id') ? wp_unique_id('ks-region-') : 'ks-region-heading';
    $gridClass = 'ks-region__areas';
    if ($layout === 'grid' && $areaCols === '3') {
        $gridClass .= ' ks-cols--3';
    }

    $pinSlots = [
        ['22%', '32%'],
        ['68%', '28%'],
        ['48%', '72%'],
        ['78%', '62%'],
        ['18%', '68%'],
        ['55%', '48%'],
    ];

    ob_start();
    ?>
    <section class="<?php echo esc_attr($sectionClass); ?>" aria-labelledby="<?php echo esc_attr($hid); ?>">
      <div class="wrap">
        <div class="ks-region__intro">
          <header class="<?php echo $headClass; ?>">
            <?php if ($eyebrow !== '') { ?><p class="eyebrow"><?php echo $eyebrow; ?></p><?php } ?>
            <<?php echo $hTag; ?> id="<?php echo esc_attr($hid); ?>"><?php echo $title; ?></<?php echo $hTag; ?>>
            <?php if ($text !== '') { ?><p><?php echo $text; ?></p><?php } ?>
          </header>
          <?php if ($showMethods && $methods !== []) { ?>
            <ol class="ks-region__methods">
              <?php foreach ($methods as $mi => [$mTitle, $mText]) { ?>
                <li class="ks-region__method">
                  <span class="ks-region__method-num" aria-hidden="true"><?php echo esc_html(sprintf('%02d', $mi + 1)); ?></span>
                  <strong><?php echo esc_html($mTitle); ?></strong>
                  <?php if ($mText !== '') { ?><p><?php echo esc_html($mText); ?></p><?php } ?>
                </li>
              <?php } ?>
            </ol>
          <?php } ?>
          <?php if ($primaryLabel !== '' || $secondaryLabel !== '') { ?>
            <p class="ks-region__cta">
              <?php if ($primaryLabel !== '') { ?>
                <a class="btn btn-primary" href="<?php echo esc_url($primaryUrl); ?>"><?php echo esc_html($primaryLabel); ?></a>
              <?php } ?>
              <?php if ($secondaryLabel !== '') { ?>
                <a class="btn btn-ghost" href="<?php echo esc_url($secondaryUrl); ?>"><?php echo esc_html($secondaryLabel); ?></a>
              <?php } ?>
            </p>
          <?php } ?>
        </div>
        <div class="ks-region__stage">
          <?php if ($showMap && $layout === 'split') { ?>
            <div class="ks-region-map" aria-hidden="true">
              <div class="ks-region-map__roads"></div>
              <?php foreach (array_slice($areas, 0, 3) as $pi => $area) {
                  $slot = $pinSlots[$pi];
                  ?>
                <span class="ks-region-map__pin" style="left:<?php echo esc_attr($slot[0]); ?>;top:<?php echo esc_attr($slot[1]); ?>">
                  <i></i>
                  <span><?php echo esc_html($area['title']); ?></span>
                </span>
              <?php } ?>
            </div>
          <?php } ?>
          <div class="<?php echo esc_attr($gridClass); ?>">
            <?php foreach ($areas as $idx => $area) { ?>
              <article class="ks-region-card">
                <?php if ($showIndex) { ?>
                  <p class="ks-region-card__index" aria-hidden="true"><?php echo esc_html(sprintf('%02d', $idx + 1)); ?></p>
                <?php } ?>
                <?php if ($area['kicker'] !== '') { ?>
                  <p class="ks-region-card__meta"><?php echo wp_kses($area['kicker'], ['em' => [], 'strong' => []]); ?></p>
                <?php } ?>
                <h3>
                  <?php if ($iconStyle === 'pin') { ?><span class="ks-region-card__pin"><?php echo $pinSvg; ?></span><?php } ?>
                  <?php echo wp_kses($area['title'], ['em' => [], 'strong' => []]); ?>
                </h3>
                <?php if ($area['body'] !== '') { ?>
                  <p><?php echo wp_kses($area['body'], ['em' => [], 'strong' => []]); ?></p>
                <?php } ?>
                <?php if ($area['stat'] !== '') { ?>
                  <p class="ks-region-card__stat"><?php echo esc_html($area['stat']); ?></p>
                <?php } ?>
                <?php if ($area['url'] !== '') { ?>
                  <a class="ks-region-card__link" href="<?php echo esc_url($area['url']); ?>"><?php esc_html_e('View listings →', 'acreline'); ?></a>
                <?php } ?>
              </article>
            <?php } ?>
          </div>
        </div>
      </div>
    </section>
    <?php
    if ($showSchema && $areas !== []) {
        $items = [];
        foreach ($areas as $i => $area) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => wp_strip_all_tags($area['title']),
                'description' => wp_strip_all_tags($area['body']),
            ];
        }
        echo '<script type="application/ld+json">'.wp_json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => wp_strip_all_tags((string) ($a['title'] ?? 'Service areas')),
            'itemListElement' => $items,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).'</script>';
    }

    return (string) ob_get_clean();
}

/** @param array<string, mixed> $attrs */
function ks_render_pricing_plans(array $attrs): string
{
    $a = $attrs;
    $eyebrow = esc_html((string) ($a['eyebrow'] ?? ''));
    $hTag = ks_heading_tag($a);
    $title = wp_kses((string) ($a['title'] ?? ''), ['em' => [], 'strong' => []]);
    $text = wp_kses((string) ($a['text'] ?? ''), ['em' => [], 'strong' => []]);
    $headClass = esc_attr(ks_head_class($a));
    $sectionClass = ks_band_section_class($a, 'section');
    $cols = sanitize_key((string) ($a['planCols'] ?? '3'));
    $gridClass = 'pricing-grid';
    if ($cols === '2') {
        $gridClass .= ' ks-cols--2';
    }
    $hid = function_exists('wp_unique_id') ? wp_unique_id('ks-plans-') : 'ks-plans-heading';
    $bookUrl = home_url('/book');
    $contactUrl = home_url('/contact');

    $plans = [];
    for ($i = 1; $i <= 3; $i++) {
        $name = trim((string) ($a["plan{$i}Title"] ?? ''));
        if ($name === '') {
            continue;
        }
        $features = preg_split('/\r\n|\r|\n/', (string) ($a["plan{$i}Features"] ?? '')) ?: [];
        $features = array_values(array_filter(array_map('trim', $features)));
        $url = (string) ($a["plan{$i}Url"] ?? '');
        if ($url === '') {
            $url = $i === 2 ? $contactUrl : $bookUrl;
        }
        $plans[] = [
            'kicker' => (string) ($a["plan{$i}Kicker"] ?? ''),
            'title' => $name,
            'price' => (string) ($a["plan{$i}Price"] ?? ''),
            'period' => (string) ($a["plan{$i}Period"] ?? ''),
            'text' => (string) ($a["plan{$i}Text"] ?? ''),
            'features' => $features,
            'cta' => (string) ($a["plan{$i}Cta"] ?? 'Get started'),
            'url' => $url,
            'featured' => (bool) ($a["plan{$i}Featured"] ?? false),
        ];
    }

    ob_start();
    ?>
    <section class="<?php echo esc_attr($sectionClass); ?>" aria-labelledby="<?php echo esc_attr($hid); ?>">
      <div class="wrap">
        <header class="<?php echo $headClass; ?>">
          <?php if ($eyebrow !== '') { ?><p class="eyebrow"><?php echo $eyebrow; ?></p><?php } ?>
          <<?php echo $hTag; ?> id="<?php echo esc_attr($hid); ?>"><?php echo $title; ?></<?php echo $hTag; ?>>
          <?php if ($text !== '') { ?><p><?php echo $text; ?></p><?php } ?>
        </header>
        <div class="<?php echo esc_attr($gridClass); ?>">
          <?php foreach ($plans as $plan) { ?>
            <article class="pricing-card<?php echo $plan['featured'] ? ' is-featured' : ''; ?>">
              <?php if ($plan['kicker'] !== '') { ?><p class="pricing-card__kicker"><?php echo esc_html($plan['kicker']); ?></p><?php } ?>
              <h3><?php echo esc_html($plan['title']); ?></h3>
              <?php if ($plan['price'] !== '') { ?>
                <p class="pricing-card__price">
                  <?php echo esc_html($plan['price']); ?>
                  <?php if ($plan['period'] !== '') { ?><span><?php echo esc_html($plan['period']); ?></span><?php } ?>
                </p>
              <?php } ?>
              <?php if ($plan['text'] !== '') { ?><p class="pricing-card__lede"><?php echo esc_html($plan['text']); ?></p><?php } ?>
              <?php if ($plan['features'] !== []) { ?>
                <ul class="pricing-card__features">
                  <?php foreach ($plan['features'] as $feat) { ?><li><?php echo esc_html($feat); ?></li><?php } ?>
                </ul>
              <?php } ?>
              <?php if ($plan['cta'] !== '') { ?>
                <a class="btn <?php echo $plan['featured'] ? 'btn-primary' : 'btn-ghost'; ?>" href="<?php echo esc_url($plan['url']); ?>"><?php echo esc_html($plan['cta']); ?></a>
              <?php } ?>
            </article>
          <?php } ?>
        </div>
      </div>
    </section>
    <?php
    return (string) ob_get_clean();
}

/** @param array<string, mixed> $attrs */
function ks_render_logo_strip(array $attrs): string
{
    $a = $attrs;
    $eyebrow = esc_html((string) ($a['eyebrow'] ?? ''));
    $hTag = ks_heading_tag($a);
    $title = wp_kses((string) ($a['title'] ?? ''), ['em' => [], 'strong' => []]);
    $text = wp_kses((string) ($a['text'] ?? ''), ['em' => [], 'strong' => []]);
    $headClass = esc_attr(ks_head_class($a));
    $sectionClass = ks_band_section_class($a, 'section');
    $grayscale = (bool) ($a['grayscale'] ?? true);
    $hid = function_exists('wp_unique_id') ? wp_unique_id('ks-logos-') : 'ks-logos-heading';

    $logos = [];
    for ($i = 1; $i <= 6; $i++) {
        $label = trim((string) ($a["logo{$i}Label"] ?? ''));
        $image = trim((string) ($a["logo{$i}ImageUrl"] ?? ''));
        if ($label === '' && $image === '') {
            continue;
        }
        $logos[] = [
            'label' => $label !== '' ? $label : __('Partner', 'acreline'),
            'url' => (string) ($a["logo{$i}Url"] ?? ''),
            'image' => $image,
        ];
    }

    ob_start();
    ?>
    <section class="<?php echo esc_attr($sectionClass); ?>" aria-labelledby="<?php echo esc_attr($hid); ?>">
      <div class="wrap">
        <header class="<?php echo $headClass; ?>">
          <?php if ($eyebrow !== '') { ?><p class="eyebrow"><?php echo $eyebrow; ?></p><?php } ?>
          <<?php echo $hTag; ?> id="<?php echo esc_attr($hid); ?>"><?php echo $title; ?></<?php echo $hTag; ?>>
          <?php if ($text !== '') { ?><p><?php echo $text; ?></p><?php } ?>
        </header>
        <div class="logo-strip__grid ks-cols--6">
          <?php foreach ($logos as $logo) {
              $tag = $logo['url'] !== '' ? 'a' : 'div';
              $href = $logo['url'] !== '' ? ' href="'.esc_url($logo['url']).'"' : '';
              $chipClass = 'logo-chip'.($grayscale && $logo['image'] !== '' ? ' is-muted' : '');
              ?>
            <<?php echo $tag; ?> class="<?php echo esc_attr($chipClass); ?>"<?php echo $href; ?>>
              <?php if ($logo['image'] !== '') { ?>
                <img src="<?php echo esc_url($logo['image']); ?>" alt="<?php echo esc_attr($logo['label']); ?>">
              <?php } else { ?>
                <span class="logo-chip__word"><?php echo esc_html($logo['label']); ?></span>
              <?php } ?>
            </<?php echo $tag; ?>>
          <?php } ?>
        </div>
      </div>
    </section>
    <?php
    return (string) ob_get_clean();
}

/** @param array<string, mixed> $attrs */
function ks_render_newsletter(array $attrs): string
{
    $a = $attrs;
    $eyebrow = esc_html((string) ($a['eyebrow'] ?? ''));
    $hTag = ks_heading_tag($a);
    $title = wp_kses((string) ($a['title'] ?? ''), ['em' => [], 'strong' => []]);
    $text = wp_kses((string) ($a['text'] ?? ''), ['em' => [], 'strong' => []]);
    $placeholder = esc_attr((string) ($a['placeholder'] ?? 'you@acreline-concept.test'));
    $buttonLabel = esc_html((string) ($a['buttonLabel'] ?? 'Join the list'));
    $note = esc_html((string) ($a['note'] ?? ''));
    $band = sanitize_key((string) ($a['bandStyle'] ?? 'accent'));
    if (! in_array($band, ['paper', 'alt', 'accent', 'dark'], true)) {
        $band = 'accent';
    }
    $sectionClass = ks_band_section_class(array_merge($a, ['bandStyle' => 'paper']), 'section');
    $hid = function_exists('wp_unique_id') ? wp_unique_id('ks-news-') : 'ks-news-heading';
    $fid = function_exists('wp_unique_id') ? wp_unique_id('ks-news-email-') : 'ksNewsletterEmail';

    ob_start();
    ?>
    <section class="<?php echo esc_attr($sectionClass); ?>">
      <div class="wrap">
        <div class="ks-newsletter ks-band--<?php echo esc_attr($band); ?>">
          <div class="ks-newsletter__inner">
            <div class="ks-newsletter__copy">
              <?php if ($eyebrow !== '') { ?><p class="eyebrow"><?php echo $eyebrow; ?></p><?php } ?>
              <<?php echo $hTag; ?> id="<?php echo esc_attr($hid); ?>"><?php echo $title; ?></<?php echo $hTag; ?>>
              <?php if ($text !== '') { ?><p><?php echo $text; ?></p><?php } ?>
            </div>
            <form class="ks-newsletter__form" id="ksNewsletterForm" aria-labelledby="<?php echo esc_attr($hid); ?>">
              <div class="field">
                <label for="<?php echo esc_attr($fid); ?>"><?php esc_html_e('Email', 'acreline'); ?></label>
                <input type="email" id="<?php echo esc_attr($fid); ?>" name="email" autocomplete="email" required placeholder="<?php echo $placeholder; ?>">
              </div>
              <button type="submit" class="btn btn-primary btn-block"><?php echo $buttonLabel; ?></button>
              <p class="ks-newsletter__note" id="ksNewsletterConfirm" role="status" aria-live="polite"><?php echo $note; ?></p>
            </form>
          </div>
        </div>
      </div>
    </section>
    <?php
    return (string) ob_get_clean();
}

// ---------------------------------------------------------------------------
// Block pattern content generators
// ---------------------------------------------------------------------------

function ks_home_page_pattern(): string
{
    return '<!-- wp:acreline/home-hero {} /-->
<!-- wp:acreline/intent-cards {} /-->
<!-- wp:acreline/spotlight {} /-->
<!-- wp:acreline/region-coverage {} /-->
<!-- wp:acreline/how-it-works {} /-->
<!-- wp:acreline/booking-section {} /-->
<!-- wp:acreline/market-stats {} /-->
<!-- wp:acreline/agent-tools {} /-->
<!-- wp:acreline/seo-content {} /-->
<!-- wp:acreline/pricing-plans {} /-->
<!-- wp:acreline/faq-list {} /-->
<!-- wp:acreline/reviews {} /-->
<!-- wp:acreline/logo-strip {} /-->
<!-- wp:acreline/newsletter {} /-->
<!-- wp:acreline/cta-band {} /-->';
}

function ks_listings_page_pattern(): string
{
    return '<!-- wp:acreline/page-hero {"eyebrow":"Sample inventory","title":"Homes \u0026 commercial — \u003cem\u003ebrowse the sample inventory\u003c\/em\u003e","primaryLabel":"Book a showing","secondaryLabel":"Buyer guide"} /-->
<!-- wp:acreline/listing-grid {} /-->
<!-- wp:acreline/market-stats {} /-->
<!-- wp:acreline/reviews {"eyebrow":"Buyer feedback","title":"What buyers say about the search process"} /-->
<!-- wp:acreline/faq-list {"title":"Listings FAQ","headClass":"left"} /-->
<!-- wp:acreline/newsletter {} /-->
<!-- wp:acreline/cta-band {"title":"Found a property worth a closer look?","primaryLabel":"Book a showing","secondaryLabel":"Run the numbers"} /-->';
}

function ks_areas_page_pattern(): string
{
    return '<!-- wp:acreline/page-hero {"eyebrow":"Six sample neighborhoods","title":"Know the area before \u003cem\u003eyou make an offer\u003c\/em\u003e","primaryLabel":"Browse listings","secondaryLabel":"Book a showing"} /-->
<!-- wp:acreline/intro-section {"eyebrow":"Why neighborhood knowledge matters","title":"One county is not one market"} /-->
<!-- wp:acreline/area-grid {"cardStyle":"featured"} /-->
<!-- wp:acreline/compare-table {} /-->
<!-- wp:acreline/region-coverage {"layout":"bento","maxAreas":6,"eyebrow":"Area service","title":"How we cover the region"} /-->
<!-- wp:acreline/market-stats {} /-->
<!-- wp:acreline/reviews {"eyebrow":"From the areas","title":"What buyers say about working local"} /-->
<!-- wp:acreline/newsletter {} /-->
<!-- wp:acreline/cta-band {"title":"Ready to walk a neighborhood with a local specialist?","primaryLabel":"Book a showing","secondaryLabel":"Browse listings"} /-->';
}

function ks_contact_page_pattern(): string
{
    return '<!-- wp:acreline/page-hero {"eyebrow":"Concept office","title":"Talk to a specialist — \u003cem\u003enot a call centre\u003c\/em\u003e","primaryLabel":"Book a showing","secondaryLabel":"Call the office"} /-->
<!-- wp:acreline/contact-form {} /-->
<!-- wp:acreline/office-info {"showMap":true} /-->
<!-- wp:acreline/trust-strip {} /-->
<!-- wp:acreline/intro-section {"eyebrow":"Other ways to reach us","title":"Phone, email, or walk in"} /-->
<!-- wp:acreline/how-we-work {"eyebrow":"What to expect","title":"What happens after you send a message"} /-->
<!-- wp:acreline/agent-list {"eyebrow":"Direct contacts","title":"Reach the right specialist"} /-->
<!-- wp:acreline/cta-band {"title":"Prefer to walk a property first?","primaryLabel":"Book a showing","secondaryLabel":"Browse listings"} /-->';
}

function ks_guide_page_pattern(): string
{
    return '<!-- wp:acreline/page-hero {"eyebrow":"Buyer tools \u0026 education","title":"The buying guide \u003cem\u003eagents wish every buyer read\u003c\/em\u003e","primaryLabel":"Book a showing","secondaryLabel":"Browse listings"} /-->
<!-- wp:acreline/tools-section {} /-->
<!-- wp:acreline/how-it-works {"eyebrow":"The buying process","title":"From first search to closing day"} /-->
<!-- wp:acreline/checklist {} /-->
<!-- wp:acreline/faq-list {"title":"Common buyer questions","headClass":"left"} /-->
<!-- wp:acreline/reviews {"eyebrow":"First-time buyers","title":"What buyers found most useful"} /-->
<!-- wp:acreline/newsletter {} /-->
<!-- wp:acreline/cta-band {"title":"Ready to put this guide to use?","primaryLabel":"Book a showing","secondaryLabel":"Browse listings"} /-->';
}

function ks_agents_page_pattern(): string
{
    return '<!-- wp:acreline/page-hero {"eyebrow":"Meet the sample team","title":"Local agents. \u003cem\u003eReal market knowledge.\u003c\/em\u003e","primaryLabel":"Book a showing","secondaryLabel":"Browse listings"} /-->
<!-- wp:acreline/intro-section {"title":"A focused team, not a franchise"} /-->
<!-- wp:acreline/agent-list {"eyebrow":"The sample team","title":"Specialists, not generalists"} /-->
<!-- wp:acreline/pricing-plans {} /-->
<!-- wp:acreline/reviews {"eyebrow":"Client stories","title":"What buyers say about the process"} /-->
<!-- wp:acreline/how-we-work {"eyebrow":"How we work","title":"What the process actually looks like"} /-->
<!-- wp:acreline/logo-strip {} /-->
<!-- wp:acreline/cta-band {"title":"Ready to talk to a specialist?","primaryLabel":"Book a showing","secondaryLabel":"Browse listings"} /-->';
}

function ks_book_page_pattern(): string
{
    return '<!-- wp:acreline/page-hero {"eyebrow":"Schedule a showing","title":"Book a showing — \u003cem\u003ewe walk it with you\u003c\/em\u003e"} /-->
<!-- wp:acreline/book-note {"noteStyle":"banner","showSidePhoto":true} /-->
<!-- wp:acreline/intro-section {"eyebrow":"What to expect","title":"A showing, not a sales pitch"} /-->
<!-- wp:acreline/prep-checklist {} /-->
<!-- wp:acreline/faq-list {"title":"Showing FAQ","headClass":"left"} /-->
<!-- wp:acreline/cta-band {"title":"Questions before booking?","primaryLabel":"Contact the office","secondaryLabel":"Browse listings"} /-->';
}

function ks_blog_page_pattern(): string
{
    return '<!-- wp:acreline/page-hero {"eyebrow":"Buyer resources","title":"Field notes from the sample county"} /-->
<!-- wp:acreline/topic-cards {} /-->
<!-- wp:acreline/post-grid {} /-->
<!-- wp:acreline/newsletter {} /-->
<!-- wp:acreline/cta-band {"title":"Ready to put these notes to use?","primaryLabel":"Browse listings","secondaryLabel":"Book a showing"} /-->';
}

// ---------------------------------------------------------------------------
// Shared HTML helpers (reused by render callbacks)
// ---------------------------------------------------------------------------

function ks_booking_form_html(): string
{
    $listings = Catalog::listings();
    $restUrl = esc_url(rest_url('keystone/v1/bookings'));
    $nonce = esc_attr(wp_create_nonce('wp_rest'));

    ob_start();
    ?>
    <div class="booking-form-wrap">
      <form id="showingForm" class="form-grid" data-rest="<?php echo $restUrl; ?>" data-nonce="<?php echo $nonce; ?>">
        <div class="field">
          <label for="sfListing"><?php esc_html_e('Property', 'acreline'); ?></label>
          <select id="sfListing" name="listing_id" required>
            <option value=""><?php esc_html_e('Choose a sample listing…', 'acreline'); ?></option>
            <?php foreach ($listings as $l) { ?>
              <option value="<?php echo (int) $l['id']; ?>"><?php echo esc_html($l['title'].' — '.Catalog::formatMoney((int) $l['price'])); ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="field">
          <label for="sfName"><?php esc_html_e('Your name', 'acreline'); ?></label>
          <input type="text" id="sfName" name="client_name" autocomplete="name" required placeholder="Alex Buyer">
        </div>
        <div class="field">
          <label for="sfEmail"><?php esc_html_e('Email', 'acreline'); ?></label>
          <input type="email" id="sfEmail" name="client_email" autocomplete="email" required placeholder="you@acreline-concept.test">
        </div>
        <div class="field">
          <label for="sfPhone"><?php esc_html_e('Phone', 'acreline'); ?></label>
          <input type="tel" id="sfPhone" name="client_phone" autocomplete="tel" required placeholder="(555) 010-0199">
        </div>
        <div class="field">
          <label for="sfDate"><?php esc_html_e('Preferred date', 'acreline'); ?></label>
          <input type="date" id="sfDate" name="showing_date" required>
        </div>
        <div class="field">
          <label for="sfType"><?php esc_html_e('Showing type', 'acreline'); ?></label>
          <select id="sfType" name="showing_type">
            <option value="in-person"><?php esc_html_e('In-person', 'acreline'); ?></option>
            <option value="virtual"><?php esc_html_e('Virtual tour', 'acreline'); ?></option>
          </select>
        </div>
        <div class="field">
          <label for="sfNotes"><?php esc_html_e('Notes', 'acreline'); ?></label>
          <textarea id="sfNotes" name="notes" rows="3" placeholder="<?php esc_attr_e('Boots on, perc questions, pets in the car…', 'acreline'); ?>"></textarea>
        </div>
        <div class="field">
          <div id="showingSlots" class="slot-grid" role="group" aria-label="<?php esc_attr_e('Choose a time slot', 'acreline'); ?>"></div>
        </div>
        <div class="field">
          <button type="submit" class="btn btn-primary btn-block" id="showingSubmit"><?php esc_html_e('Request showing', 'acreline'); ?></button>
        </div>
        <div class="confirm-msg" id="showingConfirm" role="status" aria-live="polite"></div>
      </form>
    </div>
    <?php
    return (string) ob_get_clean();
}

function ks_booking_photo_html(): string
{
    $themeUri = esc_url(get_template_directory_uri());
    ob_start();
    ?>
    <div class="booking-photo" aria-hidden="true">
      <img src="<?php echo $themeUri; ?>/public/images/booking-showing.jpg" width="800" height="1000" alt="" loading="lazy" decoding="async">
    </div>
    <?php
    return (string) ob_get_clean();
}

function ks_guide_tools_html(array $a = []): string
{
    $showLoan = (bool) ($a['showLoanTool'] ?? true);
    $showPrequal = (bool) ($a['showPrequalTool'] ?? true);

    if (! $showLoan && ! $showPrequal) {
        return '';
    }

    $loanTitle = esc_html($a['loanTitle'] ?? 'Land loan estimator');
    $loanLede = esc_html($a['loanLede'] ?? 'Sample monthly payment — not a loan offer.');
    $loanBtn = esc_html($a['loanBtn'] ?? 'Estimate payment');
    $prequalTitle = esc_html($a['prequalTitle'] ?? 'Pre-qualification check');
    $prequalLede = esc_html($a['prequalLede'] ?? 'Rough income check for land loans. Not a lender quote.');
    $prequalBtn = esc_html($a['prequalBtn'] ?? 'Check eligibility');

    $panelStyle = sanitize_key((string) ($a['panelStyle'] ?? 'card'));
    $layout = sanitize_key((string) ($a['toolsLayout'] ?? 'side'));

    $gridClass = 'tools-grid';
    if ($layout === 'stack') {
        $gridClass .= ' tools-grid--stack';
    }

    $panelClass = 'tool-panel reveal';
    if ($panelStyle === 'flat') {
        $panelClass .= ' tool-panel--flat';
    } elseif ($panelStyle === 'outline') {
        $panelClass .= ' tool-panel--outline';
    }

    ob_start();
    ?>
    <div class="<?php echo esc_attr($gridClass); ?>">
      <?php if ($showLoan) { ?>
      <div class="<?php echo esc_attr($panelClass); ?>">
        <h3><?php echo $loanTitle; ?></h3>
        <p class="lede"><?php echo $loanLede; ?></p>
        <form id="loanForm" class="form-grid two">
          <div class="field"><label for="lPrice"><?php esc_html_e('Property price', 'acreline'); ?></label><input type="number" id="lPrice" name="price" min="50000" step="5000" value="400000" required></div>
          <div class="field"><label for="lDown"><?php esc_html_e('Down payment %', 'acreline'); ?></label><select id="lDown"><option value="10">10%</option><option value="20" selected>20%</option><option value="30">30%</option></select></div>
          <div class="field"><label for="lRate"><?php esc_html_e('Interest rate %', 'acreline'); ?></label><input type="number" id="lRate" min="1" max="20" step="0.25" value="7.25" required></div>
          <div class="field"><label for="lTerm"><?php esc_html_e('Loan term', 'acreline'); ?></label><select id="lTerm"><option value="15">15 years</option><option value="20">20 years</option><option value="25" selected>25 years</option><option value="30">30 years</option></select></div>
          <div class="field field-span"><button type="submit" class="btn btn-primary btn-block"><?php echo $loanBtn; ?></button></div>
        </form>
        <div id="loanResult" role="status" aria-live="polite"></div>
      </div>
      <?php } ?>
      <?php if ($showPrequal) { ?>
      <div class="<?php echo esc_attr($panelClass); ?>">
        <h3><?php echo $prequalTitle; ?></h3>
        <p class="lede"><?php echo $prequalLede; ?></p>
        <form id="prequalForm" class="form-grid two">
          <div class="field"><label for="pqIncome"><?php esc_html_e('Annual household income', 'acreline'); ?></label><input type="number" id="pqIncome" min="20000" step="1000" value="95000" required></div>
          <div class="field"><label for="pqDebt"><?php esc_html_e('Monthly debt payments', 'acreline'); ?></label><input type="number" id="pqDebt" min="0" step="50" value="500"></div>
          <div class="field"><label for="pqCredit"><?php esc_html_e('Credit score range', 'acreline'); ?></label>
            <select id="pqCredit"><option value="620-649">620–649</option><option value="650-699">650–699</option><option value="700-749" selected>700–749</option><option value="750+">750+</option></select></div>
          <div class="field"><label for="pqDown"><?php esc_html_e('Cash available for down', 'acreline'); ?></label><input type="number" id="pqDown" min="0" step="1000" value="80000"></div>
          <div class="field field-span"><button type="submit" class="btn btn-primary btn-block"><?php echo $prequalBtn; ?></button></div>
        </form>
        <div id="prequalResult" role="status" aria-live="polite"></div>
      </div>
      <?php } ?>
    </div>
    <?php
    return (string) ob_get_clean();
}

function ks_how_we_work_steps_html(array $attrs = []): string
{
    $defaults = [
        ['Listen first', 'We start with the neighborhood you want and the life you plan to build. No form to fill — just a conversation.'],
        ['Match the inventory', 'We pull sample listings that match your area, budget, and type. Houses, condos, and commercial spaces are different products — we treat them that way.'],
        ['Walk it with you', 'A real showing, not a drive-by. We walk the rooms, check the block, and explain what we see.'],
    ];
    $steps = [];
    for ($i = 1; $i <= 4; $i++) {
        $title = trim((string) ($attrs["step{$i}Title"] ?? ($defaults[$i - 1][0] ?? '')));
        $text = trim((string) ($attrs["step{$i}Text"] ?? ($defaults[$i - 1][1] ?? '')));
        if ($title === '') {
            continue;
        }
        $steps[] = [$i, $title, $text];
    }
    if ($steps === []) {
        $steps = [
            [1, $defaults[0][0], $defaults[0][1]],
            [2, $defaults[1][0], $defaults[1][1]],
            [3, $defaults[2][0], $defaults[2][1]],
        ];
    }
    $count = count($steps);
    $colsClass = $count === 2 ? ' ks-how-cols--2' : ($count >= 4 ? ' ks-how-cols--4' : '');

    ob_start();
    ?>
    <div class="how-grid reveal<?php echo esc_attr($colsClass); ?>">
      <?php foreach ($steps as [$num, $stepTitle, $stepText]) { ?>
        <div class="how-step">
          <span class="how-num"><?php echo esc_html((string) $num); ?></span>
          <h3><?php echo esc_html($stepTitle); ?></h3>
          <p><?php echo esc_html($stepText); ?></p>
        </div>
      <?php } ?>
    </div>
    <?php
    return (string) ob_get_clean();
}

function ks_home_ldjson(array $identity): string
{
    $brand = esc_js($identity['brand'] ?? 'Acreline');
    $url = esc_url(home_url('/'));
    $email = esc_js($identity['email'] ?? 'hello@acreline-concept.test');

    return '<script type="application/ld+json">{"@context":"https://schema.org","@type":"RealEstateAgent","name":"'.esc_js($brand).' (Concept Demo)","description":"Fictional concept brokerage for design demonstration. Not a live MLS or licensed office.","url":"'.esc_js($url).'","telephone":"+1-555-010-0455","email":"'.esc_js($email).'","priceRange":"$$","address":{"@type":"PostalAddress","streetAddress":"100 Concept Way","addressLocality":"Sample Borough","addressRegion":"PA","postalCode":"00000","addressCountry":"US"}}</script>';
}
