<?php

namespace App\Support;

/**
 * Homepage (and other LCP) hero photo — URL + srcset, no plugin.
 */
class HeroImage
{
    public const DEFAULT = 'https://images.unsplash.com/photo-1570129477492-45c003edd2be';

    public const ALT = 'Sample featured property';

    /**
     * Bundled 16:9 heroes in public/images/heroes/{key}.jpg
     *
     * @var array<string, string>
     */
    public const BUNDLED_ALTS = [
        'home' => 'White farmhouse and porch across a rural pasture',
        'listings' => 'Gravel lane to a white farmhouse and bank barn',
        'areas' => 'Apple orchard rows on a rural ridge',
        'guide' => 'Farm table with a township map beside a pasture window',
        'agents' => 'Small-town brick office porch on a quiet street',
        'contact' => 'Modest brick office and steps on a tree-lined street',
        'book' => 'Walk up a stone farmhouse path at golden hour',
        'blog' => 'Desk by a window overlooking a pasture',
        'post-showing' => 'Open farmhouse door looking out to pasture',
        'post-checklist' => 'Clipboard and keys on a fence at a hay field',
        'post-land' => 'Open acreage meeting a woodlot under a wide sky',
    ];

    public static function url(?string $override = null): string
    {
        $resolved = self::resolve($override);

        return $resolved['url'];
    }

    public static function srcset(?string $override = null): string
    {
        return self::resolve($override)['srcset'];
    }

    /**
     * @return array{url: string, srcset: string}
     */
    public static function resolve(?string $override = null): array
    {
        $raw = trim((string) $override);
        if ($raw !== '' && ctype_digit($raw)) {
            $id = (int) $raw;
            $url = wp_get_attachment_image_url($id, 'full');
            if (is_string($url) && $url !== '') {
                $srcset = wp_get_attachment_image_srcset($id, 'full');

                return [
                    'url' => $url,
                    'srcset' => is_string($srcset) && $srcset !== '' ? $srcset : $url.' 1600w',
                ];
            }
        }

        $url = $raw !== '' ? $raw : self::DEFAULT;
        $attachmentId = $raw !== '' ? (int) attachment_url_to_postid($url) : 0;
        if ($attachmentId > 0) {
            $srcset = wp_get_attachment_image_srcset($attachmentId, 'full');
            if (is_string($srcset) && $srcset !== '') {
                return ['url' => $url, 'srcset' => $srcset];
            }
        }

        if (str_contains($url, 'images.unsplash.com')) {
            $base = preg_replace('/\?.*$/', '', $url) ?: $url;
            $sized = $base.'?auto=format&fit=crop&w=1600&q=75';

            return [
                'url' => $sized,
                'srcset' => $base.'?auto=format&fit=crop&w=800&q=70 800w, '
                    .$sized.' 1600w, '
                    .$base.'?auto=format&fit=crop&w=2400&q=75 2400w',
            ];
        }

        return ['url' => $url, 'srcset' => $url.' 1600w'];
    }

    public static function postBundledKey(string $slug): string
    {
        return match ($slug) {
            'book-a-home-showing' => 'post-showing',
            'first-time-buyer-checklist' => 'post-checklist',
            'land-vs-home-search' => 'post-land',
            default => '',
        };
    }

    public static function bundledUrl(string $key): string
    {
        $key = self::bundledKey($key);
        if ($key === '') {
            return '';
        }
        foreach (['public/images/heroes/'.$key.'.jpg', 'resources/images/heroes/'.$key.'.jpg'] as $relative) {
            $path = get_theme_file_path($relative);
            if (is_readable($path)) {
                return get_theme_file_uri($relative);
            }
        }

        return '';
    }

    public static function cardUrl(int $postId): string
    {
        if ($postId > 0 && has_post_thumbnail($postId)) {
            $url = get_the_post_thumbnail_url($postId, 'large');
            if (is_string($url) && $url !== '') {
                return $url;
            }
        }
        $slug = $postId > 0 ? (string) get_post_field('post_name', $postId) : '';
        $key = self::postBundledKey($slug);
        $bundled = self::bundledUrl($key !== '' ? $key : 'blog');

        return $bundled !== '' ? $bundled : self::DEFAULT.'?auto=format&fit=crop&w=900&q=70';
    }

    public static function cardAlt(int $postId): string
    {
        if ($postId > 0 && has_post_thumbnail($postId)) {
            $id = (int) get_post_thumbnail_id($postId);
            $alt = (string) get_post_meta($id, '_wp_attachment_image_alt', true);
            if ($alt !== '') {
                return $alt;
            }
        }
        $slug = $postId > 0 ? (string) get_post_field('post_name', $postId) : '';
        $key = self::postBundledKey($slug);

        return self::bundledAlt($key !== '' ? $key : 'blog');
    }

    public static function bundledAlt(string $key): string
    {
        $key = self::bundledKey($key);

        return self::BUNDLED_ALTS[$key] ?? self::ALT;
    }

    public static function bundledKey(string $key): string
    {
        $map = [
            'listings' => 'listings',
            'areas' => 'areas',
            'guide' => 'guide',
            'agents' => 'agents',
            'contact' => 'contact',
            'book' => 'book',
            'blog' => 'blog',
            'home' => 'home',
            'simple' => 'blog',
        ];

        return $map[$key] ?? (isset(self::BUNDLED_ALTS[$key]) ? $key : '');
    }

    /**
     * Page copy URL, featured image, listing photo, then a bundled page hero.
     *
     * @return array{url: string, srcset: string, alt: string}
     */
    public static function forRequest(?string $override = null, string $context = ''): array
    {
        $raw = trim((string) $override);
        $alt = self::ALT;

        if ($raw === '' && function_exists('is_singular') && is_singular('listing')) {
            $raw = Catalog::imageUrl((int) get_the_ID(), 'full');
            $alt = 'Photo of '.wp_strip_all_tags(get_the_title());
        }

        if ($raw === '' && function_exists('is_singular') && is_singular() && has_post_thumbnail()) {
            $id = (int) get_post_thumbnail_id();
            $raw = (string) $id;
            $alt = (string) get_post_meta($id, '_wp_attachment_image_alt', true) ?: wp_strip_all_tags(get_the_title());
        }

        if ($raw === '' && function_exists('is_singular') && is_singular('post')) {
            $mapped = self::postBundledKey((string) get_post_field('post_name', get_the_ID()));
            if ($mapped !== '') {
                $context = $mapped;
            }
        }

        if ($raw === '' && function_exists('is_singular') && is_singular('agent')) {
            $context = 'agents';
        }

        if ($raw === '' && function_exists('is_404') && is_404()) {
            $context = 'listings';
        }

        if ($raw === '' && $context !== '') {
            $bundled = self::bundledUrl($context);
            if ($bundled !== '') {
                $raw = $bundled;
                $alt = self::bundledAlt($context);
            }
        }

        $resolved = self::resolve($raw !== '' ? $raw : null);
        $resolved['alt'] = $alt !== '' ? $alt : self::ALT;

        return $resolved;
    }
}
