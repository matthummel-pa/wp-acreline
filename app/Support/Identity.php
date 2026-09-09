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

    public static function heroKenBurns(): bool
    {
        if (is_customize_preview()) {
            return \App\ks_hero_value_on(get_theme_mod('ks_hero_ken_burns', true));
        }

        return (string) \App\ks_setting('ks_hero_ken_burns') !== '0';
    }

    public static function heroSearchTilt(): bool
    {
        if (is_customize_preview()) {
            return \App\ks_hero_value_on(get_theme_mod('ks_hero_search_tilt', false));
        }

        return (string) \App\ks_setting('ks_hero_search_tilt') !== '0';
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
     * Top bar configuration. Returns false when the bar is disabled.
     *
     * @return array<string, mixed>|false
     */
    public static function topBar(): array|false
    {
        if (! get_theme_mod('ks_top_bar_enable', false)) {
            return false;
        }

        $style = sanitize_key((string) get_theme_mod('ks_top_bar_style', 'dark'));
        if (! in_array($style, ['dark', 'accent', 'light', 'custom'], true)) {
            $style = 'dark';
        }

        $badge = sanitize_text_field((string) get_theme_mod('ks_top_bar_badge', ''));
        $message = sanitize_text_field((string) get_theme_mod('ks_top_bar_message', ''));
        $messageUrl = esc_url_raw((string) get_theme_mod('ks_top_bar_message_url', ''));
        $ctaLabel = sanitize_text_field((string) get_theme_mod('ks_top_bar_cta_label', ''));
        $ctaUrl = esc_url_raw((string) get_theme_mod('ks_top_bar_cta_url', ''));

        // Contact items — use values from Identity when toggled on.
        $showPhone = (bool) get_theme_mod('ks_top_bar_show_phone', true);
        $showEmail = (bool) get_theme_mod('ks_top_bar_show_email', false);
        $showAddress = (bool) get_theme_mod('ks_top_bar_show_address', false);
        $showHours = (bool) get_theme_mod('ks_top_bar_show_hours', false);

        // Social icons — only listed when the platform's URL is also set.
        $allSocial = self::social();
        $socialIcons = [];
        foreach (['facebook', 'instagram', 'youtube', 'linkedin', 'x'] as $key) {
            if (get_theme_mod('ks_top_bar_show_'.$key, false) && isset($allSocial[$key])) {
                $socialIcons[$key] = $allSocial[$key];
            }
        }

        $tokens = self::topBarTokens($style);

        return [
            'style' => $style,
            'bgColor' => $tokens['bg'],
            'textColor' => $tokens['text'],
            'cssVars' => $tokens['css'],
            'badge' => $badge,
            'message' => $message,
            'messageUrl' => $messageUrl,
            'ctaLabel' => $ctaLabel,
            'ctaUrl' => $ctaUrl,
            'showPhone' => $showPhone,
            'showEmail' => $showEmail,
            'showAddress' => $showAddress,
            'showHours' => $showHours,
            'socialIcons' => $socialIcons,
            'dismissible' => (bool) get_theme_mod('ks_top_bar_dismissible', false),
            'phone' => self::phone(),
            'phoneHref' => self::phoneHref(),
            'email' => self::email(),
            'address' => self::address(),
            'hours' => self::hours(),
        ];
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
            'heroKenBurns' => self::heroKenBurns(),
            'heroSearchTilt' => self::heroSearchTilt(),
            'headerStyle' => self::headerStyle(),
            'headerClass' => implode(' ', self::headerClasses()),
            'colorScheme' => ColorSchemes::currentKey(),
            'topBar' => self::topBar(),
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
        $headerR = min(255, $p[0] + 8);
        $headerG = min(255, $p[1] + 8);
        $headerB = min(255, $p[2] + 6);
        $headerSolid = sprintf('#%02x%02x%02x', $headerR, $headerG, $headerB);
        $headerBg = sprintf('rgba(%d,%d,%d,.88)', $headerR, $headerG, $headerB);
        $headerBgScrolled = sprintf('rgba(%d,%d,%d,.96)', $headerR, $headerG, $headerB);
        $inkWash = sprintf('rgba(%d,%d,%d,.04)', $i[0], $i[1], $i[2]);

        $palette = ['accent' => $accent, 'paper' => $paper, 'ink' => $ink];
        $navHoverBg = $paper2;
        $navCurrentBg = self::mixHex($headerSolid, $accent, 0.18);
        $navText = self::readableOn($headerSolid, $accent, $palette);
        $navHover = self::readableOn($navHoverBg, $ink, $palette);
        $navCurrent = self::readableOn($navCurrentBg, $dark, $palette);
        $navIcon = self::readableIconOn($headerSolid, $palette);

        return ':root{--accent:'.$accent.';--accent-dark:'.$dark.';--accent-soft:'.$soft.';--accent-glow:'.$glow.';--accent-wash:'.$wash.';--success:'.$accent.';--paper:'.$paper.';--paper-2:'.$paper2.';--paper-3:'.$paper3.';--line:'.$line.';--ink:'.$ink.';--ink-soft:'.$inkSoft.';--ink-faint:'.$inkFaint.';--field-text:'.$ink.';--header-bg:'.$headerBg.';--header-bg-scrolled:'.$headerBgScrolled.';--ink-wash:'.$inkWash.';--nav-text:'.$navText.';--nav-hover:'.$navHover.';--nav-hover-bg:'.$navHoverBg.';--nav-current:'.$navCurrent.';--nav-current-bg:'.$navCurrentBg.';--nav-icon:'.$navIcon.';--nav-surface:'.$headerSolid.';--nav-drawer-bg:'.$paper.';}';
    }

    /**
     * Theme-color tokens for the top bar (desktop + mobile) with WCAG AA text.
     * Uses ks_accent / ks_paper / ks_ink (active color scheme). Custom override stays.
     *
     * @return array{bg:string,text:string,css:string}
     */
    public static function topBarTokens(string $style): array
    {
        $accent = self::accent();
        $paper = self::paper();
        $ink = self::ink();
        $paper2 = self::mixHex($paper, $ink, 0.06);
        $inkSoft = self::mixHex($ink, $paper, 0.28);

        $customBg = sanitize_hex_color((string) get_theme_mod('ks_top_bar_bg', '#141210')) ?: $ink;
        $customText = sanitize_hex_color((string) get_theme_mod('ks_top_bar_text_color', '#fffcf7')) ?: $paper;

        if ($style === 'custom') {
            $bg = $customBg;
            $preferred = $customText;
        } elseif ($style === 'accent') {
            $bg = $accent;
            $preferred = $paper;
        } elseif ($style === 'light') {
            $bg = $paper2;
            $preferred = $inkSoft;
        } else {
            $bg = $ink;
            $preferred = $paper;
        }

        $textStrong = self::readableOn($bg, $preferred);
        $text = self::mixHex($textStrong, $bg, 0.12);
        if (self::contrastRatio($bg, $text) < 4.5) {
            $text = $textStrong;
        }
        $icon = self::readableIconOn($bg);
        $line = self::mixHex($textStrong, $bg, 0.78);
        $badgeBg = self::mixHex($textStrong, $bg, 0.84);
        $ctaBg = self::mixHex($textStrong, $bg, 0.86);
        $ctaHover = self::mixHex($textStrong, $bg, 0.74);

        $css = '--tb-bg:'.$bg
            .';--tb-text:'.$text
            .';--tb-text-strong:'.$textStrong
            .';--tb-icon:'.$icon
            .';--tb-line:'.$line
            .';--tb-badge-bg:'.$badgeBg
            .';--tb-cta-bg:'.$ctaBg
            .';--tb-cta-hover:'.$ctaHover;

        return [
            'bg' => $bg,
            'text' => $textStrong,
            'css' => $css,
        ];
    }

    /**
     * First theme color (preferred, then paper / ink / accent) that meets 4.5:1 on $bg.
     *
     * @param  array{accent?: string, paper?: string, ink?: string}|null  $palette
     */
    public static function readableOn(string $bg, ?string $preferred = null, ?array $palette = null): string
    {
        $accent = sanitize_hex_color((string) ($palette['accent'] ?? '')) ?: self::accent();
        $paper = sanitize_hex_color((string) ($palette['paper'] ?? '')) ?: self::paper();
        $ink = sanitize_hex_color((string) ($palette['ink'] ?? '')) ?: self::ink();

        $candidates = [];
        foreach ([$preferred, $paper, $ink, $accent, '#ffffff', '#141210'] as $hex) {
            if (! is_string($hex) || $hex === '') {
                continue;
            }
            $clean = sanitize_hex_color($hex);
            if ($clean && ! in_array($clean, $candidates, true)) {
                $candidates[] = $clean;
            }
        }

        $best = $candidates[0] ?? '#ffffff';
        $bestRatio = 0.0;
        foreach ($candidates as $fg) {
            $ratio = self::contrastRatio($bg, $fg);
            if ($ratio >= 4.5) {
                return $fg;
            }
            if ($ratio > $bestRatio) {
                $bestRatio = $ratio;
                $best = $fg;
            }
        }

        return $best;
    }

    /**
     * Accent on the bar when it contrasts; otherwise the readable text color.
     * Covers accent-on-paper (light bar) and paper-on-accent (accent bar).
     *
     * @param  array{accent?: string, paper?: string, ink?: string}|null  $palette
     */
    public static function readableIconOn(string $bg, ?array $palette = null): string
    {
        $accent = sanitize_hex_color((string) ($palette['accent'] ?? '')) ?: self::accent();
        if (self::contrastRatio($bg, $accent) >= 3.0) {
            return $accent;
        }

        return self::readableOn($bg, null, $palette);
    }

    private static function hexLuminance(string $hex): float
    {
        [$r, $g, $b] = self::hexToRgb($hex);
        $channel = static function (int $c): float {
            $n = $c / 255;

            return $n <= 0.03928 ? $n / 12.92 : (($n + 0.055) / 1.055) ** 2.4;
        };

        return 0.2126 * $channel($r) + 0.7152 * $channel($g) + 0.0722 * $channel($b);
    }

    private static function contrastRatio(string $a, string $b): float
    {
        $l1 = self::hexLuminance($a);
        $l2 = self::hexLuminance($b);
        $hi = max($l1, $l2);
        $lo = min($l1, $l2);

        return ($hi + 0.05) / ($lo + 0.05);
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
