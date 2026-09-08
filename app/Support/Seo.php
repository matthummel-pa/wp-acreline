<?php

namespace App\Support;

/**
 * Native title, description, canonical, Open Graph, Twitter, and JSON-LD.
 * Yields when a major SEO plugin is active.
 */
class Seo
{
    public const SITE = 'Acreline';

    public const FALLBACK = 'Farms, historic houses, and acreage in a sample rural market. Filter listings by area and book a showing with a sample agent.';

    public static function siteName(): string
    {
        $name = Identity::brandName();

        return $name !== '' ? $name : self::SITE;
    }

    public static function boot(): void
    {
        add_filter('document_title_parts', [self::class, 'titleParts']);
        add_filter('document_title_separator', fn () => '|');
        add_action('wp_head', [self::class, 'printTags'], 2);
    }

    public static function pluginOwnsHead(): bool
    {
        return defined('WPSEO_VERSION')
            || defined('RANK_MATH_VERSION')
            || defined('AIOSEO_VERSION')
            || defined('SEOPRESS_VERSION')
            || defined('THE_SEO_FRAMEWORK_VERSION');
    }

    /**
     * @param  array<string, string>  $parts
     * @return array<string, string>
     */
    public static function titleParts(array $parts): array
    {
        if (self::pluginOwnsHead()) {
            return $parts;
        }

        if (is_front_page()) {
            return [
                'title' => self::siteName().' | Homes, Farms & Land',
                'tagline' => '',
                'site' => '',
            ];
        }

        $copy = PageCopy::all();
        if (is_404()) {
            $parts['title'] = 'Page not found';
        } elseif (is_singular('listing') || is_singular('agent') || is_singular('post')) {
            $parts['title'] = self::clip(self::plain((string) get_the_title()), 42);
        } elseif (! empty($copy['hero_title'])) {
            $parts['title'] = self::clip(self::plain($copy['hero_title']), 42);
        }

        $parts['site'] = self::siteName();
        unset($parts['tagline']);

        return $parts;
    }

    public static function printTags(): void
    {
        if (is_admin() || wp_doing_ajax() || (function_exists('wp_is_serving_rest_request') && wp_is_serving_rest_request())) {
            return;
        }
        if (defined('REST_REQUEST') && REST_REQUEST) {
            return;
        }
        if (is_feed()) {
            return;
        }
        if (self::pluginOwnsHead()) {
            return;
        }

        $meta = self::meta();

        echo '<meta name="description" content="'.esc_attr($meta['description']).'">'."\n";
        echo '<link rel="canonical" href="'.esc_url($meta['url']).'">'."\n";
        echo '<meta property="og:locale" content="en_US">'."\n";
        echo '<meta property="og:site_name" content="'.esc_attr(self::siteName()).'">'."\n";
        echo '<meta property="og:type" content="'.esc_attr($meta['type']).'">'."\n";
        echo '<meta property="og:title" content="'.esc_attr($meta['og_title']).'">'."\n";
        echo '<meta property="og:description" content="'.esc_attr($meta['description']).'">'."\n";
        echo '<meta property="og:url" content="'.esc_url($meta['url']).'">'."\n";
        echo '<meta property="og:image" content="'.esc_url($meta['image']).'">'."\n";
        echo '<meta property="og:image:width" content="1600">'."\n";
        echo '<meta property="og:image:height" content="900">'."\n";
        echo '<meta property="og:image:alt" content="'.esc_attr($meta['image_alt']).'">'."\n";
        echo '<meta name="twitter:card" content="summary_large_image">'."\n";
        echo '<meta name="twitter:title" content="'.esc_attr($meta['og_title']).'">'."\n";
        echo '<meta name="twitter:description" content="'.esc_attr($meta['description']).'">'."\n";
        echo '<meta name="twitter:image" content="'.esc_url($meta['image']).'">'."\n";

        if ($meta['jsonld'] !== []) {
            echo '<script type="application/ld+json">'.wp_json_encode($meta['jsonld'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).'</script>'."\n";
        }
    }

    /**
     * @return array{url: string, description: string, og_title: string, image: string, image_alt: string, type: string, jsonld: list<array<string, mixed>>}
     */
    public static function meta(): array
    {
        $copy = PageCopy::all();
        $context = PageCopy::schemaKeyForContext();
        $hero = HeroImage::forRequest(is_array($copy) ? ($copy['hero_image'] ?? '') : '', $context);
        $url = self::canonical();
        $description = self::description($copy);
        $ogTitle = self::ogTitle($copy);

        return [
            'url' => $url,
            'description' => $description,
            'og_title' => $ogTitle,
            'image' => $hero['url'],
            'image_alt' => $hero['alt'],
            'type' => is_singular('post') ? 'article' : 'website',
            'jsonld' => self::jsonLd($url, $ogTitle, $description, $hero),
        ];
    }

    public static function canonical(): string
    {
        if (is_front_page()) {
            return home_url('/');
        }
        if (is_home()) {
            $id = (int) get_option('page_for_posts');

            return $id > 0 ? (string) get_permalink($id) : home_url('/blog/');
        }
        if (is_singular()) {
            return (string) get_permalink();
        }

        return home_url('/');
    }

    /**
     * @param  array<string, string>  $copy
     */
    public static function description(array $copy): string
    {
        if (is_singular('listing')) {
            $listing = Catalog::listing((int) get_the_ID());
            if ($listing && $listing['desc'] !== '') {
                return self::fitDescription($listing['desc']);
            }
        }
        if (is_singular('agent')) {
            $agent = Catalog::agent((int) get_the_ID());
            if ($agent && $agent['bio'] !== '') {
                return self::fitDescription($agent['bio']);
            }
        }
        if (is_singular('post')) {
            if (has_excerpt()) {
                return self::fitDescription(get_the_excerpt());
            }
        }
        if (is_404()) {
            return 'That page is not available. Browse sample listings, schedule a showing, or return home.';
        }

        $fromCopy = self::plain($copy['hero_text'] ?? '');
        if ($fromCopy !== '') {
            return self::fitDescription($fromCopy);
        }

        $context = PageCopy::schemaKeyForContext();
        $defaults = self::defaultDescriptions();

        return $defaults[$context] ?? self::FALLBACK;
    }

    /**
     * @param  array<string, string>  $copy
     */
    public static function ogTitle(array $copy): string
    {
        if (is_front_page()) {
            return self::siteName().' | Homes, Farms & Land';
        }
        if (is_singular()) {
            return self::clip(self::plain((string) get_the_title()).' | '.self::siteName(), 70);
        }
        if (! empty($copy['hero_title'])) {
            return self::clip(self::plain($copy['hero_title']).' | '.self::siteName(), 70);
        }

        return self::siteName();
    }

    /**
     * @param  array{url: string, alt: string}  $hero
     * @return list<array<string, mixed>>
     */
    public static function jsonLd(string $url, string $title, string $description, array $hero): array
    {
        $graph = [];
        $crumbs = self::breadcrumbs($url);
        if ($crumbs !== []) {
            $graph[] = $crumbs;
        }

        $faq = self::faqPage();
        if ($faq !== []) {
            $graph[] = $faq;
        }

        if (is_singular('post')) {
            $graph[] = [
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => self::plain((string) get_the_title()),
                'description' => $description,
                'image' => $hero['url'],
                'url' => $url,
                'datePublished' => get_the_date('c'),
                'dateModified' => get_the_modified_date('c'),
                'author' => [
                    '@type' => 'Organization',
                    'name' => self::siteName(),
                ],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => self::siteName(),
                    'url' => home_url('/'),
                ],
            ];
        } elseif (! is_singular('listing') && ! is_front_page()) {
            $graph[] = [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => $title,
                'description' => $description,
                'url' => $url,
                'image' => $hero['url'],
                'isPartOf' => [
                    '@type' => 'WebSite',
                    'name' => self::siteName(),
                    'url' => home_url('/'),
                ],
            ];
        }

        return $graph;
    }

    /**
     * @return array<string, mixed>
     */
    public static function faqPage(): array
    {
        $faqs = Faqs::forContext();
        if ($faqs === []) {
            return [];
        }

        $entities = [];
        foreach ($faqs as $faq) {
            $entities[] = [
                '@type' => 'Question',
                'name' => self::plain($faq['q']),
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => self::plain($faq['a']),
                ],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $entities,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function breadcrumbs(string $currentUrl): array
    {
        $items = [
            ['name' => 'Home', 'url' => home_url('/')],
        ];

        if (is_front_page()) {
            return [];
        }
        if (is_singular('listing')) {
            $items[] = ['name' => 'Listings', 'url' => home_url('/listings')];
            $items[] = ['name' => self::plain((string) get_the_title()), 'url' => $currentUrl];
        } elseif (is_singular('agent')) {
            $items[] = ['name' => 'Agents', 'url' => home_url('/agents')];
            $items[] = ['name' => self::plain((string) get_the_title()), 'url' => $currentUrl];
        } elseif (is_singular('post') || is_home()) {
            $items[] = ['name' => 'Blog', 'url' => home_url('/blog')];
            if (is_singular('post')) {
                $items[] = ['name' => self::plain((string) get_the_title()), 'url' => $currentUrl];
            }
        } elseif (is_404()) {
            $items[] = ['name' => 'Page not found', 'url' => $currentUrl];
        } else {
            $items[] = ['name' => self::plain((string) (PageCopy::all()['hero_title'] ?? get_the_title())), 'url' => $currentUrl];
        }

        $list = [];
        foreach ($items as $i => $item) {
            $list[] = [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $item['name'],
                'item' => $item['url'],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $list,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function defaultDescriptions(): array
    {
        return [
            'home' => 'Farms, historic houses, and acreage in a sample rural market. Filter by area, review sample listings, and book a showing with a sample agent.',
            'listings' => 'Browse sample farms, historic houses, and acreage. Filter by type, price, acres, and area, then schedule a showing.',
            'areas' => 'Sample-market notes: orchards, farms, and wooded lots from Oak Hollow to Border Farms. Context before you tour a listing — rename the areas for your county.',
            'guide' => 'Guidance on wells, septic, access, and land loans, plus calculators and a path to schedule a parcel showing.',
            'agents' => 'Meet the sample team. Agents focused on farms, orchards, and historic houses across a rural demo market.',
            'contact' => 'Contact the sample office at 100 Concept Way, Sample Borough. Call (555) 010-0455 or book a house showing online.',
            'book' => 'Choose a listing, date, and time. Showing requests are saved for the listing agent so you can review the booking flow.',
            'blog' => 'Notes on house showings, first-time buyer checklists, and land versus home search for rural buyers and sellers.',
            'simple' => self::FALLBACK,
        ];
    }

    public static function plain(string $html): string
    {
        $text = wp_strip_all_tags(html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        return trim((string) preg_replace('/\s+/', ' ', $text));
    }

    public static function clip(string $text, int $max): string
    {
        if (mb_strlen($text) <= $max) {
            return $text;
        }
        $cut = mb_substr($text, 0, $max - 1);
        $space = mb_strrpos($cut, ' ');
        if ($space !== false && $space > (int) ($max * 0.55)) {
            $cut = mb_substr($cut, 0, $space);
        }

        return rtrim($cut, '.,;: ').'…';
    }

    public static function fitDescription(string $text): string
    {
        $text = self::plain($text);
        $len = mb_strlen($text);
        if ($len >= 140 && $len <= 160) {
            return $text;
        }
        if ($len > 160) {
            return self::clip($text, 160);
        }

        return $text !== '' ? $text : self::FALLBACK;
    }
}
