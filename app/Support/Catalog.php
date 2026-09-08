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
            'lat' => 'Map pin top %',
            'lng' => 'Map pin left %',
            'photo_grad' => 'Card gradient (fallback)',
            'image' => 'Listing photo',
            'virtual_tour' => 'Virtual tour URL',
            'property_tax' => 'Annual taxes',
            'hoa' => 'HOA / dues',
            'listing_agent' => 'Listing agent (agent post ID)',
            'featured' => 'Featured on homepage (store 1 or omit)',
            'description' => 'Listing description',
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
            'image' => 'Photo',
            'job_title' => 'Title / designation',
            'license_number' => 'License number',
            'license_state' => 'License state',
            'phone' => 'Direct phone',
            'mobile' => 'Mobile',
            'email' => 'Email',
            'office' => 'Office name',
            'office_phone' => 'Office phone',
            'years_experience' => 'Years of experience',
            'specialties' => 'Specialties',
            'service_areas' => 'Service areas',
            'languages' => 'Languages',
            'designations' => 'Designations (ABR, CRS, GRI…)',
            'mls_id' => 'MLS ID',
            'nrds_id' => 'NRDS ID',
            'website' => 'Personal website',
            'calendly' => 'Booking / calendar URL',
            'facebook' => 'Facebook URL',
            'instagram' => 'Instagram URL',
            'linkedin' => 'LinkedIn URL',
            'initials' => 'Avatar initials',
            'avatar_color' => 'Avatar color',
            'featured' => 'Featured (store 1 or omit)',
            'bio' => 'Bio',
        ];
    }

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
            'notes' => 'Notes',
            'status' => 'Status',
            'agent_id' => 'Assigned agent ID',
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
            'price' => (int) self::getMeta($post->ID, 'price', 0),
            'beds' => (float) self::getMeta($post->ID, 'beds', 0),
            'baths' => (float) self::getMeta($post->ID, 'baths', 0),
            'sqft' => (int) self::getMeta($post->ID, 'sqft', 0),
            'acres' => (float) self::getMeta($post->ID, 'acres', 0),
            'year_built' => (string) self::getMeta($post->ID, 'year_built', ''),
            'mls_number' => (string) self::getMeta($post->ID, 'mls_number', ''),
            'lat' => (float) self::getMeta($post->ID, 'lat', 40),
            'lng' => (float) self::getMeta($post->ID, 'lng', 40),
            'grad' => (string) self::getMeta($post->ID, 'photo_grad', 'linear-gradient(135deg,#155539,#1f6b4a)'),
            'image' => $image,
            'virtual_tour' => (string) self::getMeta($post->ID, 'virtual_tour', ''),
            'property_tax' => (string) self::getMeta($post->ID, 'property_tax', ''),
            'hoa' => (string) self::getMeta($post->ID, 'hoa', ''),
            'listing_agent' => (int) self::getMeta($post->ID, 'listing_agent', 0),
            'featured' => self::isFeaturedFlag(self::getMeta($post->ID, 'featured', '')),
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
            'job_title' => (string) self::getMeta($post->ID, 'job_title', 'Realtor'),
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
            'mls_id' => (string) self::getMeta($post->ID, 'mls_id', ''),
            'nrds_id' => (string) self::getMeta($post->ID, 'nrds_id', ''),
            'website' => (string) self::getMeta($post->ID, 'website', ''),
            'calendly' => (string) self::getMeta($post->ID, 'calendly', ''),
            'facebook' => (string) self::getMeta($post->ID, 'facebook', ''),
            'instagram' => (string) self::getMeta($post->ID, 'instagram', ''),
            'linkedin' => (string) self::getMeta($post->ID, 'linkedin', ''),
            'initials' => (string) self::getMeta($post->ID, 'initials', self::initials(get_the_title($post))),
            'avatar_color' => (string) self::getMeta($post->ID, 'avatar_color', 'var(--accent)'),
            'photo' => $photo ?: '',
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
            'price' => (int) ($item['price'] ?? 0),
            'beds' => (float) ($item['beds'] ?? 0),
            'baths' => (float) ($item['baths'] ?? 0),
            'sqft' => (int) ($item['sqft'] ?? 0),
            'acres' => (float) ($item['acres'] ?? 0),
            'year_built' => (string) ($item['year_built'] ?? ''),
            'mls_number' => (string) ($item['mls_number'] ?? ''),
            'lat' => (float) ($item['lat'] ?? 40),
            'lng' => (float) ($item['lng'] ?? 40),
            'grad' => (string) ($item['photo_grad'] ?? 'linear-gradient(135deg,#155539,#1f6b4a)'),
            'image' => (string) ($item['image'] ?? ''),
            'virtual_tour' => (string) ($item['virtual_tour'] ?? ''),
            'property_tax' => (string) ($item['property_tax'] ?? ''),
            'hoa' => (string) ($item['hoa'] ?? ''),
            'listing_agent' => 0,
            'featured' => self::isFeaturedFlag($item['featured'] ?? ''),
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
            'job_title' => (string) ($item['job_title'] ?? 'Realtor'),
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
            'mls_id' => (string) ($item['mls_id'] ?? ''),
            'nrds_id' => (string) ($item['nrds_id'] ?? ''),
            'website' => (string) ($item['website'] ?? ''),
            'calendly' => (string) ($item['calendly'] ?? ''),
            'facebook' => (string) ($item['facebook'] ?? ''),
            'instagram' => (string) ($item['instagram'] ?? ''),
            'linkedin' => (string) ($item['linkedin'] ?? ''),
            'initials' => (string) ($item['initials'] ?? self::initials($name)),
            'avatar_color' => (string) ($item['avatar_color'] ?? 'var(--accent)'),
            'photo' => '',
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
