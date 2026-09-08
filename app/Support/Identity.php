<?php

namespace App\Support;

/**
 * Buyer-facing office identity. Customizer first, concept defaults second.
 */
class Identity
{
    public static function brandName(): string
    {
        $mod = trim((string) get_theme_mod('ks_brand_name', ''));
        if ($mod !== '' && ! self::isRetiredBrand($mod)) {
            return $mod;
        }

        $name = (string) get_bloginfo('name', 'display');
        if ($name !== '' && ! self::isRetiredBrand($name)) {
            return $name;
        }

        return 'Acreline';
    }

    public static function tagline(): string
    {
        $mod = trim((string) get_theme_mod('ks_tagline', ''));
        if ($mod !== '') {
            return $mod;
        }

        $desc = (string) get_bloginfo('description', 'display');

        return $desc !== '' ? $desc : __('Farms · land · historic homes', 'acreline');
    }

    public static function phone(): string
    {
        $phone = trim((string) get_theme_mod('ks_phone', '(555) 010-0455'));

        return $phone !== '' ? $phone : '(555) 010-0455';
    }

    public static function phoneHref(): string
    {
        return Catalog::telHref(self::phone());
    }

    public static function email(): string
    {
        $email = sanitize_email((string) get_theme_mod('ks_email', 'hello@acreline-concept.test'));
        if ($email === '' || self::isRetiredBrand($email)) {
            return 'hello@acreline-concept.test';
        }

        return $email;
    }

    public static function address(): string
    {
        $address = trim((string) get_theme_mod('ks_address', "100 Concept Way\nSample Borough, PA 00000"));

        return $address !== '' ? $address : "100 Concept Way\nSample Borough, PA 00000";
    }

    public static function hours(): string
    {
        $hours = trim((string) get_theme_mod('ks_hours', "Mon–Fri 9:00–5:00\nSat by appointment\nSun closed (demo)"));

        return $hours !== '' ? $hours : "Mon–Fri 9:00–5:00\nSat by appointment\nSun closed (demo)";
    }

    public static function footerBlurb(): string
    {
        $blurb = trim((string) get_theme_mod('ks_footer_blurb', ''));
        if ($blurb !== '') {
            return $blurb;
        }

        return __('Acreline sample office by Matt Hummel. Fiction only — not a licensed brokerage or live MLS feed.', 'acreline');
    }

    public static function ctaLabel(): string
    {
        $label = trim((string) get_theme_mod('ks_cta_label', ''));

        return $label !== '' ? $label : __('Book a showing', 'acreline');
    }

    public static function bookUrl(): string
    {
        $custom = trim((string) get_theme_mod('ks_cta_url', ''));
        if ($custom !== '') {
            return $custom;
        }

        $page = get_page_by_path('book');

        return $page instanceof \WP_Post ? (string) get_permalink($page) : home_url('/book/');
    }

    public static function showDemoChrome(): bool
    {
        return (bool) get_theme_mod('ks_show_demo_chrome', true);
    }

    public static function showCredit(): bool
    {
        return (bool) get_theme_mod('ks_show_credit', true);
    }

    public static function creditText(): string
    {
        $text = trim((string) get_theme_mod('ks_credit_text', ''));

        return $text !== '' ? $text : __('Theme by Matt Hummel', 'acreline');
    }

    public static function creditUrl(): string
    {
        return trim((string) get_theme_mod('ks_credit_url', 'https://matthummel.com'));
    }

    public static function accent(): string
    {
        $scheme = ColorSchemes::current();
        $hex = sanitize_hex_color((string) get_theme_mod('ks_accent', $scheme['accent']));

        return $hex ?: $scheme['accent'];
    }

    public static function paper(): string
    {
        $scheme = ColorSchemes::current();
        $hex = sanitize_hex_color((string) get_theme_mod('ks_paper', $scheme['paper']));

        return $hex ?: $scheme['paper'];
    }

    public static function ink(): string
    {
        $scheme = ColorSchemes::current();
        $hex = sanitize_hex_color((string) get_theme_mod('ks_ink', $scheme['ink']));

        return $hex ?: $scheme['ink'];
    }

    public static function headerSticky(): bool
    {
        return (bool) get_theme_mod('ks_header_sticky', true);
    }

    public static function headerStyle(): string
    {
        $style = sanitize_key((string) get_theme_mod('ks_header_style', 'standard'));

        return in_array($style, ['standard', 'compact'], true) ? $style : 'standard';
    }

    /**
     * @return list<string>
     */
    public static function headerClasses(): array
    {
        $classes = [];
        $classes[] = self::headerSticky() ? 'is-sticky' : 'is-static';
        if (self::headerStyle() === 'compact') {
            $classes[] = 'is-compact';
        }

        return $classes;
    }

    /**
     * @return array<string, string>
     */
    public static function social(): array
    {
        $out = [];
        foreach (['facebook', 'instagram', 'youtube', 'linkedin', 'x'] as $key) {
            $url = esc_url_raw((string) get_theme_mod('ks_social_'.$key, ''));
            if ($url !== '') {
                $out[$key] = $url;
            }
        }

        return $out;
    }

    /**
     * @return array<string, mixed>
     */
    public static function toArray(): array
    {
        return [
            'brand' => self::brandName(),
            'tagline' => self::tagline(),
            'phone' => self::phone(),
            'phoneHref' => self::phoneHref(),
            'email' => self::email(),
            'address' => self::address(),
            'hours' => self::hours(),
            'footerBlurb' => self::footerBlurb(),
            'ctaLabel' => self::ctaLabel(),
            'bookUrl' => self::bookUrl(),
            'showDemoChrome' => self::showDemoChrome(),
            'showCredit' => self::showCredit(),
            'creditText' => self::creditText(),
            'creditUrl' => self::creditUrl(),
            'social' => self::social(),
            'hasLogo' => has_custom_logo(),
            'headerSticky' => self::headerSticky(),
            'headerStyle' => self::headerStyle(),
            'headerClass' => implode(' ', self::headerClasses()),
            'colorScheme' => ColorSchemes::currentKey(),
        ];
    }

    public static function isRetiredBrand(string $value): bool
    {
        $hay = strtolower($value);

        return str_contains($hay, 'keystone')
            || str_contains($hay, 'keystone-concept.test');
    }

    public static function cssVariables(): string
    {
        return self::cssFromPalette(self::accent(), self::paper(), self::ink());
    }

    public static function cssFromPalette(string $accent, string $paper, string $ink): string
    {
        $accent = sanitize_hex_color($accent) ?: '#1f6b4a';
        $paper = sanitize_hex_color($paper) ?: '#f5f4f1';
        $ink = sanitize_hex_color($ink) ?: '#141210';

        $a = self::hexToRgb($accent);
        $p = self::hexToRgb($paper);
        $i = self::hexToRgb($ink);

        $dark = self::shadeHex($accent, 0.82);
        $soft = sprintf('rgba(%d,%d,%d,.16)', $a[0], $a[1], $a[2]);
        $glow = sprintf('rgba(%d,%d,%d,.28)', $a[0], $a[1], $a[2]);
        $wash = sprintf('rgba(%d,%d,%d,.08)', $a[0], $a[1], $a[2]);
        $paper2 = self::mixHex($paper, $ink, 0.06);
        $paper3 = self::mixHex($paper, $ink, 0.12);
        $line = self::mixHex($paper, $ink, 0.22);
        $inkSoft = self::mixHex($ink, $paper, 0.28);
        $inkFaint = self::mixHex($ink, $paper, 0.48);
        $headerBg = sprintf('rgba(%d,%d,%d,.88)', min(255, $p[0] + 8), min(255, $p[1] + 8), min(255, $p[2] + 6));
        $headerBgScrolled = sprintf('rgba(%d,%d,%d,.96)', min(255, $p[0] + 8), min(255, $p[1] + 8), min(255, $p[2] + 6));
        $inkWash = sprintf('rgba(%d,%d,%d,.04)', $i[0], $i[1], $i[2]);

        return ':root{--accent:'.$accent.';--accent-dark:'.$dark.';--accent-soft:'.$soft.';--accent-glow:'.$glow.';--accent-wash:'.$wash.';--success:'.$accent.';--paper:'.$paper.';--paper-2:'.$paper2.';--paper-3:'.$paper3.';--line:'.$line.';--ink:'.$ink.';--ink-soft:'.$inkSoft.';--ink-faint:'.$inkFaint.';--field-text:'.$ink.';--header-bg:'.$headerBg.';--header-bg-scrolled:'.$headerBgScrolled.';--ink-wash:'.$inkWash.';}';
    }

    /**
     * @return array{0:int,1:int,2:int}
     */
    private static function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        $int = hexdec($hex);

        return [($int >> 16) & 255, ($int >> 8) & 255, $int & 255];
    }

    private static function shadeHex(string $hex, float $factor): string
    {
        [$r, $g, $b] = self::hexToRgb($hex);
        $r = max(0, min(255, (int) round($r * $factor)));
        $g = max(0, min(255, (int) round($g * $factor)));
        $b = max(0, min(255, (int) round($b * $factor)));

        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    private static function mixHex(string $from, string $toward, float $amount): string
    {
        [$r1, $g1, $b1] = self::hexToRgb($from);
        [$r2, $g2, $b2] = self::hexToRgb($toward);
        $r = max(0, min(255, (int) round($r1 + ($r2 - $r1) * $amount)));
        $g = max(0, min(255, (int) round($g1 + ($g2 - $g1) * $amount)));
        $b = max(0, min(255, (int) round($b1 + ($b2 - $b1) * $amount)));

        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
}
