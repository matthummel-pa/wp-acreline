<?php

namespace App\Support;

/**
 * Fictional partner lockups for the logo-strip demo.
 *
 * Theme files live in public/images/partners/. Seed / force-rebuild copies
 * them into the media library so the block inspector MediaUpload can replace
 * each mark the same way a buyer would.
 */
class PartnerLogos
{
    /**
     * @return list<array{slug: string, file: string, label: string, alt: string}>
     */
    public static function catalog(): array
    {
        return [
            [
                'slug' => 'sample-credit-union',
                'file' => 'sample-credit-union.svg',
                'label' => 'Sample Credit Union',
                'alt' => 'Sample Credit Union — fictional partner mark',
            ],
            [
                'slug' => 'county-title',
                'file' => 'county-title.svg',
                'label' => 'County Title Co.',
                'alt' => 'County Title Co. — fictional partner mark',
            ],
            [
                'slug' => 'north-ridge-inspect',
                'file' => 'north-ridge-inspect.svg',
                'label' => 'North Ridge Inspect',
                'alt' => 'North Ridge Inspect — fictional partner mark',
            ],
            [
                'slug' => 'mill-creek-lending',
                'file' => 'mill-creek-lending.svg',
                'label' => 'Mill Creek Lending',
                'alt' => 'Mill Creek Lending — fictional partner mark',
            ],
            [
                'slug' => 'oak-hollow-photo',
                'file' => 'oak-hollow-photo.svg',
                'label' => 'Oak Hollow Photo',
                'alt' => 'Oak Hollow Photo — fictional partner mark',
            ],
            [
                'slug' => 'borough-insurance',
                'file' => 'borough-insurance.svg',
                'label' => 'Borough Insurance',
                'alt' => 'Borough Insurance — fictional partner mark',
            ],
        ];
    }

    /**
     * Theme-file partners (no media ID). Used as block defaults and fallbacks.
     *
     * @return list<array{id: int, url: string, alt: string, label: string, link: string}>
     */
    public static function themeFilePartners(): array
    {
        $partners = [];
        foreach (self::catalog() as $item) {
            $partners[] = [
                'id' => 0,
                'url' => self::themeFileUrl($item['file']),
                'alt' => $item['alt'],
                'label' => $item['label'],
                'link' => '',
            ];
        }

        return $partners;
    }

    /**
     * Import bundled SVGs into the media library and return picker-ready items.
     *
     * @return list<array{id: int, url: string, alt: string, label: string, link: string}>
     */
    public static function seedPartners(): array
    {
        $partners = [];
        foreach (self::catalog() as $item) {
            $imported = self::importOne($item);
            $partners[] = $imported ?? [
                'id' => 0,
                'url' => self::themeFileUrl($item['file']),
                'alt' => $item['alt'],
                'label' => $item['label'],
                'link' => '',
            ];
        }

        return $partners;
    }

    public static function themeFileUrl(string $file): string
    {
        $file = basename($file);
        $relative = 'public/images/partners/'.$file;
        if (is_readable(get_theme_file_path($relative))) {
            return get_theme_file_uri($relative);
        }

        return '';
    }

    /**
     * @param  array{slug: string, file: string, label: string, alt: string}  $item
     * @return array{id: int, url: string, alt: string, label: string, link: string}|null
     */
    private static function importOne(array $item): ?array
    {
        $title = 'Partner · '.$item['label'];
        $existing = get_posts([
            'post_type' => 'attachment',
            'title' => $title,
            'posts_per_page' => 1,
            'post_status' => 'inherit',
            'fields' => 'ids',
        ]);
        if ($existing) {
            $id = (int) $existing[0];
            $url = wp_get_attachment_url($id);
            if (is_string($url) && $url !== '') {
                return [
                    'id' => $id,
                    'url' => $url,
                    'alt' => $item['alt'],
                    'label' => $item['label'],
                    'link' => '',
                ];
            }
        }

        $path = get_theme_file_path('public/images/partners/'.$item['file']);
        if (! is_readable($path)) {
            return null;
        }

        if (! function_exists('media_handle_sideload')) {
            require_once ABSPATH.'wp-admin/includes/file.php';
            require_once ABSPATH.'wp-admin/includes/media.php';
            require_once ABSPATH.'wp-admin/includes/image.php';
        }

        $mime = function (array $mimes): array {
            $mimes['svg'] = 'image/svg+xml';

            return $mimes;
        };
        $filetype = function (array $data, string $file, string $filename): array {
            if (strtolower((string) pathinfo($filename, PATHINFO_EXTENSION)) === 'svg') {
                $data['ext'] = 'svg';
                $data['type'] = 'image/svg+xml';
            }

            return $data;
        };

        add_filter('upload_mimes', $mime);
        add_filter('wp_check_filetype_and_ext', $filetype, 10, 3);

        $tmp = wp_tempnam($item['file']);
        if (! $tmp || ! copy($path, $tmp)) {
            remove_filter('upload_mimes', $mime);
            remove_filter('wp_check_filetype_and_ext', $filetype, 10);

            return null;
        }

        $file = [
            'name' => $item['file'],
            'tmp_name' => $tmp,
            'type' => 'image/svg+xml',
            'error' => 0,
            'size' => (int) filesize($tmp),
        ];
        $id = media_handle_sideload($file, 0, $title);

        remove_filter('upload_mimes', $mime);
        remove_filter('wp_check_filetype_and_ext', $filetype, 10);

        if (is_wp_error($id)) {
            @unlink($tmp);

            return null;
        }

        wp_update_post([
            'ID' => (int) $id,
            'post_title' => $title,
        ]);
        update_post_meta((int) $id, '_wp_attachment_image_alt', $item['alt']);

        $url = wp_get_attachment_url((int) $id);
        if (! is_string($url) || $url === '') {
            return null;
        }

        return [
            'id' => (int) $id,
            'url' => $url,
            'alt' => $item['alt'],
            'label' => $item['label'],
            'link' => '',
        ];
    }
}
