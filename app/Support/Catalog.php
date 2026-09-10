<?php

namespace App\Support;

use WP_Post;
use WP_Query;

class Catalog
{
    public const LISTING = 'listing';

    public const BOOKING = 'booking';

    public const AGENT = 'agent';

    public const LISTING_TYPES = [
        'home' => 'Home',
        'farm' => 'Working Farm',
        'land' => 'Land / Acreage',
        'historic' => 'Historic Home',
        'commercial' => 'Commercial',
        'multi' => 'Multi-family',
    ];

    public const PROPERTY_CONDITIONS = [
        '' => '— Select —',
        'excellent' => 'Excellent',
        'good' => 'Good',
        'fair' => 'Fair',
        'needs-work' => 'Needs work',
    ];

    public const GARAGE_TYPES = [
        '' => 'None / N/A',
        'attached' => 'Attached',
        'detached' => 'Detached',
        'carport' => 'Carport',
        'attached-detached' => 'Attached + detached',
    ];

    public const BASEMENT_TYPES = [
        '' => 'None / Slab',
        'unfinished' => 'Unfinished',
        'partial' => 'Partially finished',
        'finished' => 'Finished',
        'walkout' => 'Walkout / daylight',
        'crawl' => 'Crawl space',
    ];

    public const RIGHTS_OPTIONS = [
        '' => '— Unknown —',
        'included' => 'Included',
        'excluded' => 'Excluded / conveyed',
        'partial' => 'Partial',
    ];

    public const VIEWS = [
        '' => 'None noted',
        'pastoral' => 'Pastoral / open fields',
        'mountain' => 'Mountain / ridge',
        'wooded' => 'Wooded',
        'water' => 'Pond / creek / water',
        'valley' => 'Valley',
        'city' => 'Town / city lights',
    ];

    public const OPEN_HOUSE_TYPES = [
        '' => 'No open house',
        'public' => 'Public open house',
        'by-appointment' => 'By appointment',
        'broker' => 'Broker caravan',
    ];

    public const LISTING_SOURCES = [
        '' => '— Select —',
        'in-house' => 'In-house exclusive',
        'mls' => 'MLS listed',
        'pocket' => 'Pocket listing',
        'fsbo' => 'For Sale by Owner',
    ];

    public const LISTING_STATUSES = [
        'active' => 'Active',
        'new' => 'New',
        'pending' => 'Pending',
        'sold' => 'Sold',
    ];

    public const BOOKING_STATUSES = [
        'requested' => 'Requested',
        'confirmed' => 'Confirmed',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    public const SHOWING_TYPES = [
        'in-person' => 'In-person tour',
        'preview' => 'Private preview',
        'virtual' => 'Virtual walk-through',
    ];

    /** Filter values stay stable; labels are the sample-market names buyers see. */
    public const TOWNSHIP_LABELS = [
        'Cumberland' => 'North Ridge',
        'Straban' => 'Mill Creek',
        'Franklin' => 'Oak Hollow',
    ];

    public static function townshipLabel(string $township): string
    {
        return self::TOWNSHIP_LABELS[$township] ?? $township;
    }

    /**
     * Listing meta keys stored on the listing CPT (no leading underscore so WP-CLI can set them easily).
     *
     * @return array<string, string>
     */
    public static function listingFields(): array
    {
        return [
            // Core
            'type' => 'Property type',
            'status' => 'Status',
            'address' => 'Street address',
            'city' => 'City',
            'state' => 'State',
            'zip' => 'ZIP',
            'township' => 'Township',
            'price' => 'List price',
            'beds' => 'Beds',
            'baths' => 'Baths',
            'sqft' => 'Square feet',
            'acres' => 'Acres',
            'year_built' => 'Year built',
            'mls_number' => 'MLS number',
            'description' => 'Listing description',
            // Property details
            'condition' => 'Property condition',
            'garage' => 'Garage spaces',
            'garage_type' => 'Garage type',
            'basement' => 'Basement',
            'heating' => 'Heating',
            'cooling' => 'Cooling',
            'zoning' => 'Zoning',
            'school_district' => 'School district',
            'lot_features' => 'Lot features',
            'view' => 'View',
            'flood_zone' => 'Flood zone',
            'smart_home' => 'Smart home features',
            'green_features' => 'Green / eco features',
            'historic_designation' => 'Historic designation',
            // Utilities & financials
            'water' => 'Water source',
            'sewer' => 'Sewer / septic',
            'property_tax' => 'Annual taxes',
            'hoa' => 'HOA / dues notes',
            'hoa_monthly' => 'HOA monthly fee',
            'hoa_amenities' => 'HOA amenities',
            // Land & farm (all types; just hide empty on front-end)
            'tillable_acres' => 'Tillable acres',
            'pasture_acres' => 'Pasture acres',
            'crop_acres' => 'Crop / hay acres',
            'outbuildings' => 'Outbuildings',
            'mineral_rights' => 'Mineral rights',
            'water_rights' => 'Water rights',
            'conservation_easement' => 'Conservation easement',
            // Media
            'image' => 'Listing photo',
            'floor_plan' => 'Floor plan image',
            'virtual_tour' => 'Virtual tour URL',
            'video_tour' => 'Video / drone tour URL',
            // Map
            'lat' => 'Map pin top %',
            'lng' => 'Map pin left %',
            'photo_grad' => 'Card gradient (fallback)',
            // Showing & market
            'open_house_date' => 'Open house date',
            'open_house_time' => 'Open house time',
            'open_house_type' => 'Open house type',
            'days_on_market' => 'Days on market',
            'listing_source' => 'Listing source',
            'listing_office' => 'Listing office / brokerage attribution',
            'commission' => 'Buyer agent commission %',
            // Agent & meta
            'listing_agent' => 'Listing agent (agent post ID)',
            'featured' => 'Featured on homepage (store 1 or omit)',
            'agent_notes' => 'Internal agent notes',
        ];
    }

    /**
     * Standard agent fields used across realtor sites.
     *
     * @return array<string, string>
     */
    public static function agentFields(): array
    {
        return [
            // Identity
            'image' => 'Photo',
            'job_title' => 'Title / designation',
            'team_name' => 'Team name',
            'office' => 'Office name',
            'office_phone' => 'Office phone',
            'featured_badge' => 'Featured badge (e.g. Top Producer)',
            'featured' => 'Featured (store 1 or omit)',
            // Credentials
            'license_number' => 'License number',
            'license_state' => 'License state',
            'mls_id' => 'MLS ID',
            'nrds_id' => 'NRDS ID',
            'years_experience' => 'Years of experience',
            'designations' => 'Designations (ABR, CRS, GRI…)',
            'certifications' => 'Certifications',
            'awards' => 'Awards / recognition',
            // Contact
            'phone' => 'Direct phone',
            'mobile' => 'Mobile',
            'email' => 'Email',
            'website' => 'Personal website',
            'calendly' => 'Booking / calendar URL',
            // Social
            'facebook' => 'Facebook URL',
            'instagram' => 'Instagram URL',
            'linkedin' => 'LinkedIn URL',
            'twitter' => 'X / Twitter URL',
            'youtube' => 'YouTube URL',
            // Performance stats (manual entry)
            'homes_sold' => 'Homes sold (last 12 mo.)',
            'avg_dom' => 'Avg. days on market',
            'list_to_sale_ratio' => 'Avg. list-to-sale %',
            'total_volume' => 'Total closed volume',
            'client_reviews_count' => 'Client reviews count',
            // Content
            'bio' => 'Bio',
            'bio_video' => 'Intro video URL',
            'tag_line' => 'Tag line (short headline)',
            'process_note' => 'How I work (1–2 sentences)',
            'transaction_types' => 'Transaction types (e.g. Buyers · sellers · listings)',
            'specialties' => 'Specialties',
            'service_areas' => 'Service areas',
            'languages' => 'Languages',
            // Avatar
            'initials' => 'Avatar initials',
            'avatar_color' => 'Avatar color',
        ];
    }

    public const BUYER_TYPES = [
        '' => '— Unknown —',
        'pre-approved' => 'Pre-approved buyer',
        'cash' => 'Cash buyer',
        'browsing' => 'Browsing / early stage',
        'renting' => 'Currently renting',
        'investor' => 'Investor',
    ];

    public const BOOKING_PRIORITIES = [
        'normal' => 'Normal',
        'hot' => 'Hot lead',
        'cold' => 'Cold lead',
        'follow-up' => 'Needs follow-up',
    ];

    public const COMM_PREFERENCES = [
        '' => 'Any',
        'phone' => 'Phone call',
        'text' => 'Text / SMS',
        'email' => 'Email',
    ];

    public const LEAD_SOURCES = [
        '' => '— Unknown —',
        'website' => 'Website',
        'referral' => 'Referral',
        'sign' => 'Yard sign',
        'social' => 'Social media',
        'mls' => 'MLS / Zillow / Realtor.com',
        'open-house' => 'Open house',
        'other' => 'Other',
    ];

    /**
     * @return array<string, string>
     */
    public static function bookingFields(): array
    {
        return [
            'listing_id' => 'Listing ID',
            'listing_title' => 'Listing title (snapshot)',
            'showing_date' => 'Showing date',
            'showing_time' => 'Showing time',
            'showing_type' => 'Showing type',
            'client_name' => 'Client name',
            'client_email' => 'Client email',
            'client_phone' => 'Client phone',
            'status' => 'Status',
            'agent_id' => 'Assigned agent ID',
            // Enhanced fields
            'priority' => 'Priority',
            'buyer_type' => 'Buyer type',
            'comm_preference' => 'Preferred contact method',
            'attendees' => 'Number of attendees',
            'source' => 'Lead source',
            'notes' => 'Notes (visible to agent)',
            'consent' => 'Marketing consent (1 or empty)',
            'consent_at' => 'Consent timestamp (UTC)',
            'special_notes' => 'Special access notes',
            'showing_feedback' => 'Post-showing feedback',
            'follow_up_date' => 'Follow-up date',
        ];
    }

    public static function metaKey(string $field): string
    {
        return 'ks_'.$field;
    }

    public static function getMeta(int $postId, string $field, mixed $default = ''): mixed
    {
        $value = get_post_meta($postId, self::metaKey($field), true);

        return $value === '' || $value === false ? $default : $value;
    }

    public static function updateMeta(int $postId, string $field, mixed $value): void
    {
        update_post_meta($postId, self::metaKey($field), $value);
    }

    /**
     * Featured image, then ks_image as attachment ID or URL.
     */
    public static function imageUrl(int $postId, string $size = 'large'): string
    {
        $thumb = get_the_post_thumbnail_url($postId, $size);
        if (is_string($thumb) && $thumb !== '') {
            return $thumb;
        }

        $raw = trim((string) self::getMeta($postId, 'image', ''));
        if ($raw === '') {
            return '';
        }
        if (ctype_digit($raw)) {
            $url = wp_get_attachment_image_url((int) $raw, $size);

            return is_string($url) ? $url : '';
        }

        return $raw;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function listings(): array
    {
        if (! post_type_exists(self::LISTING)) {
            return self::listingsFromSeed();
        }

        $query = new WP_Query([
            'post_type' => self::LISTING,
            'post_status' => 'publish',
            'posts_per_page' => 100,
            'orderby' => ['menu_order' => 'ASC', 'date' => 'ASC'],
            'no_found_rows' => true,
        ]);

        $items = array_map([self::class, 'listingToArray'], $query->posts);
        wp_reset_postdata();

        return $items !== [] ? $items : self::listingsFromSeed();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function featuredListings(int $limit = 3): array
    {
        $featured = array_values(array_filter(self::listings(), fn ($item) => ! empty($item['featured'])));

        if ($featured === []) {
            $featured = array_values(array_filter(self::listings(), fn ($item) => $item['type'] !== 'land'));
        }

        return array_slice($featured, 0, $limit);
    }

    /**
     * @return list<string>
     */
    public static function townships(): array
    {
        $towns = [];
        foreach (self::listings() as $listing) {
            if ($listing['township'] !== '') {
                $towns[$listing['township']] = $listing['township'];
            }
        }
        ksort($towns);

        return array_values($towns);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function agents(): array
    {
        if (! post_type_exists(self::AGENT)) {
            return self::agentsFromSeed();
        }

        $query = new WP_Query([
            'post_type' => self::AGENT,
            'post_status' => 'publish',
            'posts_per_page' => 50,
            'orderby' => ['menu_order' => 'ASC', 'title' => 'ASC'],
            'no_found_rows' => true,
        ]);

        $items = array_map([self::class, 'agentToArray'], $query->posts);
        wp_reset_postdata();

        return $items !== [] ? $items : self::agentsFromSeed();
    }

    public static function agent(int $id): ?array
    {
        $post = get_post($id);
        if ($post instanceof WP_Post && $post->post_type === self::AGENT) {
            return self::agentToArray($post);
        }

        foreach (self::agents() as $item) {
            if ((int) $item['id'] === $id) {
                return $item;
            }
        }

        return null;
    }

    public static function listing(int $id): ?array
    {
        $post = get_post($id);
        if ($post instanceof WP_Post && $post->post_type === self::LISTING) {
            return self::listingToArray($post);
        }

        foreach (self::listings() as $item) {
            if ((int) $item['id'] === $id) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function listingsForAgent(int $agentId): array
    {
        return array_values(array_filter(
            self::listings(),
            fn ($item) => (int) $item['listing_agent'] === $agentId
        ));
    }

    public static function listingToArray(WP_Post $post): array
    {
        $type = (string) self::getMeta($post->ID, 'type', 'home');
        $image = self::imageUrl($post->ID, 'large');
        $floorPlan = self::getMeta($post->ID, 'floor_plan', '');
        $floorPlanUrl = '';
        if (is_string($floorPlan) && $floorPlan !== '') {
            $floorPlanUrl = ctype_digit($floorPlan)
                ? ((string) (wp_get_attachment_image_url((int) $floorPlan, 'large') ?: ''))
                : $floorPlan;
        }
        $sqft = (int) self::getMeta($post->ID, 'sqft', 0);
        $price = (int) self::getMeta($post->ID, 'price', 0);

        return [
            'id' => (int) $post->ID,
            'slug' => $post->post_name,
            'permalink' => get_permalink($post),
            'title' => get_the_title($post),
            'desc' => (string) (self::getMeta($post->ID, 'description', '') ?: wp_strip_all_tags($post->post_content)),
            'type' => $type,
            'typeLabel' => self::LISTING_TYPES[$type] ?? 'Home',
            'status' => (string) self::getMeta($post->ID, 'status', 'active'),
            'address' => (string) self::getMeta($post->ID, 'address', ''),
            'city' => (string) self::getMeta($post->ID, 'city', ''),
            'state' => (string) self::getMeta($post->ID, 'state', 'PA'),
            'zip' => (string) self::getMeta($post->ID, 'zip', ''),
            'township' => (string) self::getMeta($post->ID, 'township', ''),
            'townshipLabel' => self::townshipLabel((string) self::getMeta($post->ID, 'township', '')),
            'price' => $price,
            'beds' => (float) self::getMeta($post->ID, 'beds', 0),
            'baths' => (float) self::getMeta($post->ID, 'baths', 0),
            'sqft' => $sqft,
            'acres' => (float) self::getMeta($post->ID, 'acres', 0),
            'year_built' => (string) self::getMeta($post->ID, 'year_built', ''),
            'mls_number' => (string) self::getMeta($post->ID, 'mls_number', ''),
            'lat' => (float) self::getMeta($post->ID, 'lat', 40),
            'lng' => (float) self::getMeta($post->ID, 'lng', 40),
            'grad' => (string) self::getMeta($post->ID, 'photo_grad', 'linear-gradient(135deg,#155539,#1f6b4a)'),
            'image' => $image,
            'virtual_tour' => (string) self::getMeta($post->ID, 'virtual_tour', ''),
            'video_tour' => (string) self::getMeta($post->ID, 'video_tour', ''),
            'floor_plan' => $floorPlanUrl,
            'property_tax' => (string) self::getMeta($post->ID, 'property_tax', ''),
            'hoa' => (string) self::getMeta($post->ID, 'hoa', ''),
            'hoa_monthly' => (string) self::getMeta($post->ID, 'hoa_monthly', ''),
            'hoa_amenities' => (string) self::getMeta($post->ID, 'hoa_amenities', ''),
            'listing_agent' => (int) self::getMeta($post->ID, 'listing_agent', 0),
            'featured' => self::isFeaturedFlag(self::getMeta($post->ID, 'featured', '')),
            // Property details
            'condition' => (string) self::getMeta($post->ID, 'condition', ''),
            'garage' => (string) self::getMeta($post->ID, 'garage', ''),
            'garage_type' => (string) self::getMeta($post->ID, 'garage_type', ''),
            'basement' => (string) self::getMeta($post->ID, 'basement', ''),
            'heating' => (string) self::getMeta($post->ID, 'heating', ''),
            'cooling' => (string) self::getMeta($post->ID, 'cooling', ''),
            'water' => (string) self::getMeta($post->ID, 'water', ''),
            'sewer' => (string) self::getMeta($post->ID, 'sewer', ''),
            'zoning' => (string) self::getMeta($post->ID, 'zoning', ''),
            'school_district' => (string) self::getMeta($post->ID, 'school_district', ''),
            'lot_features' => (string) self::getMeta($post->ID, 'lot_features', ''),
            'view' => (string) self::getMeta($post->ID, 'view', ''),
            'flood_zone' => (string) self::getMeta($post->ID, 'flood_zone', ''),
            'smart_home' => (string) self::getMeta($post->ID, 'smart_home', ''),
            'green_features' => (string) self::getMeta($post->ID, 'green_features', ''),
            'historic_designation' => (string) self::getMeta($post->ID, 'historic_designation', ''),
            // Land & farm
            'tillable_acres' => (string) self::getMeta($post->ID, 'tillable_acres', ''),
            'pasture_acres' => (string) self::getMeta($post->ID, 'pasture_acres', ''),
            'crop_acres' => (string) self::getMeta($post->ID, 'crop_acres', ''),
            'outbuildings' => (string) self::getMeta($post->ID, 'outbuildings', ''),
            'mineral_rights' => (string) self::getMeta($post->ID, 'mineral_rights', ''),
            'water_rights' => (string) self::getMeta($post->ID, 'water_rights', ''),
            'conservation_easement' => (string) self::getMeta($post->ID, 'conservation_easement', ''),
            // Showing & market
            'open_house_date' => (string) self::getMeta($post->ID, 'open_house_date', ''),
            'open_house_time' => (string) self::getMeta($post->ID, 'open_house_time', ''),
            'open_house_type' => (string) self::getMeta($post->ID, 'open_house_type', ''),
            'days_on_market' => (string) self::getMeta($post->ID, 'days_on_market', ''),
            'listing_source' => (string) self::getMeta($post->ID, 'listing_source', ''),
            'listing_office' => (string) self::getMeta($post->ID, 'listing_office', ''),
            'commission' => (string) self::getMeta($post->ID, 'commission', ''),
            'updated' => get_the_modified_date('Y-m-d', $post),
            // Derived
            'price_per_sqft' => ($sqft > 0 && $price > 0) ? (int) round($price / $sqft) : 0,
        ];
    }

    public static function agentToArray(WP_Post $post): array
    {
        $photo = self::imageUrl($post->ID, 'medium');

        return [
            'id' => (int) $post->ID,
            'slug' => $post->post_name,
            'permalink' => get_permalink($post),
            'name' => get_the_title($post),
            'bio' => (string) (self::getMeta($post->ID, 'bio', '') ?: wp_strip_all_tags($post->post_content)),
            'job_title' => (string) self::getMeta($post->ID, 'job_title', 'Agent'),
            'team_name' => (string) self::getMeta($post->ID, 'team_name', ''),
            'license_number' => (string) self::getMeta($post->ID, 'license_number', ''),
            'license_state' => (string) self::getMeta($post->ID, 'license_state', 'PA'),
            'phone' => (string) self::getMeta($post->ID, 'phone', ''),
            'mobile' => (string) self::getMeta($post->ID, 'mobile', ''),
            'email' => self::conceptEmail((string) self::getMeta($post->ID, 'email', '')),
            'office' => self::conceptOffice((string) self::getMeta($post->ID, 'office', 'Acreline')),
            'office_phone' => (string) self::getMeta($post->ID, 'office_phone', '(555) 010-0455'),
            'years_experience' => (string) self::getMeta($post->ID, 'years_experience', ''),
            'specialties' => (string) self::getMeta($post->ID, 'specialties', ''),
            'service_areas' => (string) self::getMeta($post->ID, 'service_areas', ''),
            'languages' => (string) self::getMeta($post->ID, 'languages', 'English'),
            'designations' => (string) self::getMeta($post->ID, 'designations', ''),
            'certifications' => (string) self::getMeta($post->ID, 'certifications', ''),
            'awards' => (string) self::getMeta($post->ID, 'awards', ''),
            'mls_id' => (string) self::getMeta($post->ID, 'mls_id', ''),
            'nrds_id' => (string) self::getMeta($post->ID, 'nrds_id', ''),
            'website' => (string) self::getMeta($post->ID, 'website', ''),
            'calendly' => (string) self::getMeta($post->ID, 'calendly', ''),
            'facebook' => (string) self::getMeta($post->ID, 'facebook', ''),
            'instagram' => (string) self::getMeta($post->ID, 'instagram', ''),
            'linkedin' => (string) self::getMeta($post->ID, 'linkedin', ''),
            'twitter' => (string) self::getMeta($post->ID, 'twitter', ''),
            'youtube' => (string) self::getMeta($post->ID, 'youtube', ''),
            'bio_video' => (string) self::getMeta($post->ID, 'bio_video', ''),
            'homes_sold' => (string) self::getMeta($post->ID, 'homes_sold', ''),
            'avg_dom' => (string) self::getMeta($post->ID, 'avg_dom', ''),
            'list_to_sale_ratio' => (string) self::getMeta($post->ID, 'list_to_sale_ratio', ''),
            'total_volume' => (string) self::getMeta($post->ID, 'total_volume', ''),
            'client_reviews_count' => (string) self::getMeta($post->ID, 'client_reviews_count', ''),
            'featured_badge' => (string) self::getMeta($post->ID, 'featured_badge', ''),
            'initials' => (string) self::getMeta($post->ID, 'initials', self::initials(get_the_title($post))),
            'avatar_color' => (string) self::getMeta($post->ID, 'avatar_color', 'var(--accent)'),
            'photo' => $photo ?: '',
            'rating' => (string) self::getMeta($post->ID, 'rating', ''),
            'review_snippet' => (string) self::getMeta($post->ID, 'review_snippet', ''),
            'review_author' => (string) self::getMeta($post->ID, 'review_author', ''),
            'review_location' => (string) self::getMeta($post->ID, 'review_location', ''),
            'tag_line' => (string) self::getMeta($post->ID, 'tag_line', ''),
            'process_note' => (string) self::getMeta($post->ID, 'process_note', ''),
            'transaction_types' => (string) self::getMeta($post->ID, 'transaction_types', ''),
            'featured' => self::isFeaturedFlag(self::getMeta($post->ID, 'featured', '')),
        ];
    }

    /**
     * Featured must be store "1" or omit. Never treat string "0" as true — (bool) '0' is true in PHP.
     */
    public static function isFeaturedFlag(mixed $value): bool
    {
        return ! empty($value) && (string) $value !== '0';
    }

    /**
     * Persist featured as "1", or delete the meta when off (do not store "0").
     */
    public static function setFeaturedFlag(int $postId, bool $featured): void
    {
        $key = self::metaKey('featured');
        if ($featured) {
            update_post_meta($postId, $key, '1');

            return;
        }
        delete_post_meta($postId, $key);
    }

    public static function formatMoney(int $amount): string
    {
        return '$'.number_format($amount);
    }

    public static function nextBookingStatus(string $status): ?string
    {
        return match ($status) {
            'requested' => 'confirmed',
            'confirmed' => 'completed',
            default => null,
        };
    }

    public static function initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $letters = array_map(fn ($part) => strtoupper(substr($part, 0, 1)), array_slice($parts, 0, 2));

        return implode('', $letters) ?: 'KH';
    }

    public static function telHref(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        return $digits !== '' ? 'tel:+1'.$digits : '#';
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function listingsFromSeed(): array
    {
        $rows = self::decodeSeed('listings.json');
        $items = [];
        foreach (array_values($rows) as $index => $row) {
            if (! is_array($row)) {
                continue;
            }
            $items[] = self::listingFromSeed($row, $index + 1);
        }

        return $items;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function agentsFromSeed(): array
    {
        $rows = self::decodeSeed('agents.json');
        $items = [];
        foreach (array_values($rows) as $index => $row) {
            if (! is_array($row)) {
                continue;
            }
            $items[] = self::agentFromSeed($row, $index + 1);
        }

        return $items;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function decodeSeed(string $file): array
    {
        $path = get_template_directory().'/resources/seed/'.$file;
        if (! is_readable($path)) {
            return [];
        }
        $data = json_decode((string) file_get_contents($path), true);

        return is_array($data) ? $data : [];
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    private static function listingFromSeed(array $item, int $id): array
    {
        $type = (string) ($item['type'] ?? 'home');
        $slug = (string) ($item['slug'] ?? 'listing-'.$id);
        $sqft = (int) ($item['sqft'] ?? 0);
        $price = (int) ($item['price'] ?? 0);

        return [
            'id' => $id,
            'slug' => $slug,
            'permalink' => home_url('/listings'),
            'title' => (string) ($item['title'] ?? 'Sample listing'),
            'desc' => (string) ($item['description'] ?? ''),
            'type' => $type,
            'typeLabel' => self::LISTING_TYPES[$type] ?? 'Home',
            'status' => (string) ($item['status'] ?? 'active'),
            'address' => (string) ($item['address'] ?? ''),
            'city' => (string) ($item['city'] ?? ''),
            'state' => (string) ($item['state'] ?? 'PA'),
            'zip' => (string) ($item['zip'] ?? ''),
            'township' => (string) ($item['township'] ?? ''),
            'townshipLabel' => self::townshipLabel((string) ($item['township'] ?? '')),
            'price' => $price,
            'beds' => (float) ($item['beds'] ?? 0),
            'baths' => (float) ($item['baths'] ?? 0),
            'sqft' => $sqft,
            'acres' => (float) ($item['acres'] ?? 0),
            'year_built' => (string) ($item['year_built'] ?? ''),
            'mls_number' => (string) ($item['mls_number'] ?? ''),
            'lat' => (float) ($item['lat'] ?? 40),
            'lng' => (float) ($item['lng'] ?? 40),
            'grad' => (string) ($item['photo_grad'] ?? 'linear-gradient(135deg,#155539,#1f6b4a)'),
            'image' => (string) ($item['image'] ?? ''),
            'virtual_tour' => (string) ($item['virtual_tour'] ?? ''),
            'video_tour' => (string) ($item['video_tour'] ?? ''),
            'floor_plan' => (string) ($item['floor_plan'] ?? ''),
            'property_tax' => (string) ($item['property_tax'] ?? ''),
            'hoa' => (string) ($item['hoa'] ?? ''),
            'hoa_monthly' => (string) ($item['hoa_monthly'] ?? ''),
            'hoa_amenities' => (string) ($item['hoa_amenities'] ?? ''),
            'listing_agent' => 0,
            'featured' => self::isFeaturedFlag($item['featured'] ?? ''),
            'condition' => (string) ($item['condition'] ?? ''),
            'garage' => (string) ($item['garage'] ?? ''),
            'garage_type' => (string) ($item['garage_type'] ?? ''),
            'basement' => (string) ($item['basement'] ?? ''),
            'heating' => (string) ($item['heating'] ?? ''),
            'cooling' => (string) ($item['cooling'] ?? ''),
            'water' => (string) ($item['water'] ?? ''),
            'sewer' => (string) ($item['sewer'] ?? ''),
            'zoning' => (string) ($item['zoning'] ?? ''),
            'school_district' => (string) ($item['school_district'] ?? ''),
            'lot_features' => (string) ($item['lot_features'] ?? ''),
            'view' => (string) ($item['view'] ?? ''),
            'flood_zone' => (string) ($item['flood_zone'] ?? ''),
            'smart_home' => (string) ($item['smart_home'] ?? ''),
            'green_features' => (string) ($item['green_features'] ?? ''),
            'historic_designation' => (string) ($item['historic_designation'] ?? ''),
            'tillable_acres' => (string) ($item['tillable_acres'] ?? ''),
            'pasture_acres' => (string) ($item['pasture_acres'] ?? ''),
            'crop_acres' => (string) ($item['crop_acres'] ?? ''),
            'outbuildings' => (string) ($item['outbuildings'] ?? ''),
            'mineral_rights' => (string) ($item['mineral_rights'] ?? ''),
            'water_rights' => (string) ($item['water_rights'] ?? ''),
            'conservation_easement' => (string) ($item['conservation_easement'] ?? ''),
            'open_house_date' => (string) ($item['open_house_date'] ?? ''),
            'open_house_time' => (string) ($item['open_house_time'] ?? ''),
            'open_house_type' => (string) ($item['open_house_type'] ?? ''),
            'days_on_market' => (string) ($item['days_on_market'] ?? ''),
            'listing_source' => (string) ($item['listing_source'] ?? ''),
            'listing_office' => (string) ($item['listing_office'] ?? ''),
            'commission' => (string) ($item['commission'] ?? ''),
            'updated' => (string) ($item['updated'] ?? ''),
            'price_per_sqft' => ($sqft > 0 && $price > 0) ? (int) round($price / $sqft) : 0,
        ];
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    private static function agentFromSeed(array $item, int $id): array
    {
        $name = (string) ($item['name'] ?? 'Agent');

        return [
            'id' => $id,
            'slug' => (string) ($item['slug'] ?? 'agent-'.$id),
            'permalink' => home_url('/agents'),
            'name' => $name,
            'bio' => (string) ($item['bio'] ?? ''),
            'job_title' => (string) ($item['job_title'] ?? 'Agent'),
            'team_name' => (string) ($item['team_name'] ?? ''),
            'license_number' => (string) ($item['license_number'] ?? ''),
            'license_state' => (string) ($item['license_state'] ?? 'PA'),
            'phone' => (string) ($item['phone'] ?? ''),
            'mobile' => (string) ($item['mobile'] ?? ''),
            'email' => self::conceptEmail((string) ($item['email'] ?? '')),
            'office' => self::conceptOffice((string) ($item['office'] ?? 'Acreline')),
            'office_phone' => (string) ($item['office_phone'] ?? '(555) 010-0455'),
            'years_experience' => (string) ($item['years_experience'] ?? ''),
            'specialties' => (string) ($item['specialties'] ?? ''),
            'service_areas' => (string) ($item['service_areas'] ?? ''),
            'languages' => (string) ($item['languages'] ?? 'English'),
            'designations' => (string) ($item['designations'] ?? ''),
            'certifications' => (string) ($item['certifications'] ?? ''),
            'awards' => (string) ($item['awards'] ?? ''),
            'mls_id' => (string) ($item['mls_id'] ?? ''),
            'nrds_id' => (string) ($item['nrds_id'] ?? ''),
            'website' => (string) ($item['website'] ?? ''),
            'calendly' => (string) ($item['calendly'] ?? ''),
            'facebook' => (string) ($item['facebook'] ?? ''),
            'instagram' => (string) ($item['instagram'] ?? ''),
            'linkedin' => (string) ($item['linkedin'] ?? ''),
            'twitter' => (string) ($item['twitter'] ?? ''),
            'youtube' => (string) ($item['youtube'] ?? ''),
            'bio_video' => (string) ($item['bio_video'] ?? ''),
            'homes_sold' => (string) ($item['homes_sold'] ?? ''),
            'avg_dom' => (string) ($item['avg_dom'] ?? ''),
            'list_to_sale_ratio' => (string) ($item['list_to_sale_ratio'] ?? ''),
            'total_volume' => (string) ($item['total_volume'] ?? ''),
            'client_reviews_count' => (string) ($item['client_reviews_count'] ?? ''),
            'featured_badge' => (string) ($item['featured_badge'] ?? ''),
            'initials' => (string) ($item['initials'] ?? self::initials($name)),
            'avatar_color' => (string) ($item['avatar_color'] ?? 'var(--accent)'),
            'photo' => (string) ($item['image'] ?? ''),
            'rating' => (string) ($item['rating'] ?? ''),
            'review_snippet' => (string) ($item['review_snippet'] ?? ''),
            'review_author' => (string) ($item['review_author'] ?? ''),
            'review_location' => (string) ($item['review_location'] ?? ''),
            'tag_line' => (string) ($item['tag_line'] ?? ''),
            'process_note' => (string) ($item['process_note'] ?? ''),
            'transaction_types' => (string) ($item['transaction_types'] ?? ''),
            'featured' => self::isFeaturedFlag($item['featured'] ?? ''),
        ];
    }

    private static function conceptEmail(string $email): string
    {
        if ($email === '' || ! Identity::isRetiredBrand($email)) {
            return $email;
        }

        $user = strstr($email, '@', true);

        return ($user !== false && $user !== '' ? $user : 'hello').'@acreline-concept.test';
    }

    private static function conceptOffice(string $office): string
    {
        if ($office === '' || Identity::isRetiredBrand($office)) {
            return 'Acreline';
        }

        return $office;
    }
}
