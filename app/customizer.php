<?php

/**
 * Theme Customizer — identity, colors, typography.
 */

namespace App;

use App\Support\ColorSchemes;
use App\Support\Typography;
use WP_Customize_Color_Control;
use WP_Customize_Manager;

add_action('customize_register', function (WP_Customize_Manager $wp_customize) {
    $wp_customize->add_section('ks_identity', [
        'title' => __('Identity', 'acreline'),
        'description' => __('Office name, phone, and chrome buyers change first. Concept defaults stay until you overwrite them.', 'acreline'),
        'priority' => 30,
    ]);

    $text = [
        'ks_brand_name' => [__('Brand name', 'acreline'), 'Acreline'],
        'ks_tagline' => [__('Header tagline', 'acreline'), 'Homes · neighborhoods · local agents'],
        'ks_phone' => [__('Phone', 'acreline'), '(555) 010-0455'],
        'ks_email' => [__('Email', 'acreline'), 'hello@acreline-concept.test'],
        'ks_cta_label' => [__('Header button label', 'acreline'), 'Book a showing'],
        'ks_cta_url' => [__('Header button URL', 'acreline'), ''],
        'ks_credit_text' => [__('Footer credit text', 'acreline'), 'Concept by Matt Hummel'],
        'ks_credit_url' => [__('Footer credit URL', 'acreline'), 'https://matthummel.com'],
    ];

    foreach ($text as $id => [$label, $default]) {
        $sanitize = str_contains($id, 'url') ? 'esc_url_raw' : (str_contains($id, 'email') ? 'sanitize_email' : 'sanitize_text_field');
        $wp_customize->add_setting($id, [
            'default' => $default,
            'sanitize_callback' => $sanitize,
            'transport' => 'refresh',
        ]);
        $wp_customize->add_control($id, [
            'label' => $label,
            'section' => 'ks_identity',
            'type' => 'text',
        ]);
    }

    $wp_customize->add_setting('ks_address', [
        'default' => "100 Concept Way\nSample Borough, PA 00000",
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);
    $wp_customize->add_control('ks_address', [
        'label' => __('Address', 'acreline'),
        'section' => 'ks_identity',
        'type' => 'textarea',
    ]);

    $wp_customize->add_setting('ks_hours', [
        'default' => "Mon–Fri 9:00–5:00\nSat by appointment\nSun closed (demo)",
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);
    $wp_customize->add_control('ks_hours', [
        'label' => __('Hours', 'acreline'),
        'section' => 'ks_identity',
        'type' => 'textarea',
    ]);

    $wp_customize->add_setting('ks_footer_blurb', [
        'default' => '',
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);
    $wp_customize->add_control('ks_footer_blurb', [
        'label' => __('Footer blurb', 'acreline'),
        'description' => __('Leave empty for the concept sentence while the demo banner is on. Empty (no concept line) when the demo banner is off.', 'acreline'),
        'section' => 'ks_identity',
        'type' => 'textarea',
    ]);

    $wp_customize->add_setting('ks_show_demo_chrome', [
        'default' => true,
        'sanitize_callback' => __NAMESPACE__.'\\sanitize_checkbox',
    ]);
    $wp_customize->add_control('ks_show_demo_chrome', [
        'label' => __('Show concept demo banner and author badge', 'acreline'),
        'section' => 'ks_identity',
        'type' => 'checkbox',
    ]);

    $wp_customize->add_setting('ks_show_credit', [
        'default' => true,
        'sanitize_callback' => __NAMESPACE__.'\\sanitize_checkbox',
    ]);
    $wp_customize->add_control('ks_show_credit', [
        'label' => __('Show removable author credit in the footer', 'acreline'),
        'section' => 'ks_identity',
        'type' => 'checkbox',
    ]);

    ks_register_compliance_customizer($wp_customize);

    $schemeChoices = [];
    foreach (ColorSchemes::all() as $key => $scheme) {
        $schemeChoices[$key] = $scheme['label'];
    }

    $wp_customize->add_section('ks_colors', [
        'title' => __('Colors', 'acreline'),
        'description' => __('Eight named styles. Pick a style, then tweak accent, paper, or ink. Keep body text dark on a light page.', 'acreline'),
        'priority' => 35,
    ]);

    $wp_customize->add_setting('ks_color_scheme', [
        'default' => ColorSchemes::defaultKey(),
        'sanitize_callback' => [ColorSchemes::class, 'sanitizeKey'],
        'transport' => 'refresh',
    ]);
    $wp_customize->add_control('ks_color_scheme', [
        'label' => __('Color style', 'acreline'),
        'section' => 'ks_colors',
        'type' => 'select',
        'choices' => $schemeChoices,
    ]);

    $forest = ColorSchemes::all()['forest'];
    foreach ([
        'ks_accent' => [__('Accent', 'acreline'), $forest['accent']],
        'ks_paper' => [__('Paper (page background)', 'acreline'), $forest['paper']],
        'ks_ink' => [__('Ink (text)', 'acreline'), $forest['ink']],
    ] as $id => [$label, $default]) {
        $wp_customize->add_setting($id, [
            'default' => $default,
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'refresh',
        ]);
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, $id, [
            'label' => $label,
            'section' => 'ks_colors',
        ]));
    }

    $wp_customize->add_setting('ks_show_style_switcher', [
        'default' => (string) ks_setting('ks_show_style_switcher') !== '0',
        'sanitize_callback' => __NAMESPACE__.'\\sanitize_checkbox',
        'transport' => 'refresh',
        'type' => 'theme_mod',
    ]);
    $wp_customize->add_control('ks_show_style_switcher', [
        'label' => __('Show front-end color style switcher', 'acreline'),
        'description' => __('The floating “Colors” chip on the public site. On by default for the concept demo. Turn off for a buyer site. Same control as Appearance → Acreline Settings → General.', 'acreline'),
        'section' => 'ks_colors',
        'type' => 'checkbox',
    ]);

    $wp_customize->add_section('ks_header', [
        'title' => __('Header', 'acreline'),
        'description' => __('Sticky bar is the default. Compact shortens the bar on listing-heavy pages. Homepage hero motion (photo pan and optional phone tilt) lives here too.', 'acreline'),
        'priority' => 34,
    ]);

    $wp_customize->add_setting('ks_header_style', [
        'default' => 'standard',
        'sanitize_callback' => function ($value) {
            $value = sanitize_key((string) $value);

            return in_array($value, ['standard', 'compact'], true) ? $value : 'standard';
        },
    ]);
    $wp_customize->add_control('ks_header_style', [
        'label' => __('Header size', 'acreline'),
        'section' => 'ks_header',
        'type' => 'select',
        'choices' => [
            'standard' => __('Standard', 'acreline'),
            'compact' => __('Compact', 'acreline'),
        ],
    ]);

    $wp_customize->add_setting('ks_header_sticky', [
        'default' => true,
        'sanitize_callback' => __NAMESPACE__.'\\sanitize_checkbox',
    ]);
    $wp_customize->add_control('ks_header_sticky', [
        'label' => __('Stick the header while scrolling', 'acreline'),
        'section' => 'ks_header',
        'type' => 'checkbox',
    ]);

    $wp_customize->add_setting('ks_hero_ken_burns', [
        'default' => (string) ks_setting('ks_hero_ken_burns') !== '0',
        'sanitize_callback' => __NAMESPACE__.'\\sanitize_checkbox',
        'transport' => 'postMessage',
        'type' => 'theme_mod',
    ]);
    $wp_customize->add_control('ks_hero_ken_burns', [
        'label' => __('Animate homepage hero image', 'acreline'),
        'description' => __('Slow Ken Burns pan and zoom on the homepage hero photo. Turn off for a static cover. Honors reduced-motion preferences.', 'acreline'),
        'section' => 'ks_header',
        'type' => 'checkbox',
    ]);

    $wp_customize->add_setting('ks_hero_search_tilt', [
        'default' => (string) ks_setting('ks_hero_search_tilt') !== '0',
        'sanitize_callback' => __NAMESPACE__.'\\sanitize_checkbox',
        'transport' => 'postMessage',
        'type' => 'theme_mod',
    ]);
    $wp_customize->add_control('ks_hero_search_tilt', [
        'label' => __('Tilt listing search on mobile', 'acreline'),
        'description' => __('On phones, the homepage listing search panel gently follows device tilt. Off by default. Does nothing if sensors are missing, permission is denied, or the visitor prefers reduced motion. iPhone may ask for motion access.', 'acreline'),
        'section' => 'ks_header',
        'type' => 'checkbox',
    ]);

    $wp_customize->add_section('ks_social', [
        'title' => __('Social links', 'acreline'),
        'priority' => 36,
    ]);

    foreach ([
        'facebook' => __('Facebook URL', 'acreline'),
        'instagram' => __('Instagram URL', 'acreline'),
        'youtube' => __('YouTube URL', 'acreline'),
        'linkedin' => __('LinkedIn URL', 'acreline'),
        'x' => __('X / Twitter URL', 'acreline'),
    ] as $key => $label) {
        $id = 'ks_social_'.$key;
        $wp_customize->add_setting($id, [
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
        ]);
        $wp_customize->add_control($id, [
            'label' => $label,
            'section' => 'ks_social',
            'type' => 'url',
        ]);
    }

    // ── Top Bar ──────────────────────────────────────────────────────────────
    $wp_customize->add_section('ks_top_bar', [
        'title' => __('Top Bar', 'acreline'),
        'description' => __('A slim bar above the header. Dark, Accent, and Light follow the active color style (Customize → Colors). Custom background and text stay until you change them.', 'acreline'),
        'priority' => 38,
    ]);

    // Enable
    $wp_customize->add_setting('ks_top_bar_enable', [
        'default' => false,
        'sanitize_callback' => __NAMESPACE__.'\\sanitize_checkbox',
    ]);
    $wp_customize->add_control('ks_top_bar_enable', [
        'label' => __('Show top bar', 'acreline'),
        'section' => 'ks_top_bar',
        'type' => 'checkbox',
    ]);

    // Style preset
    $wp_customize->add_setting('ks_top_bar_style', [
        'default' => 'dark',
        'sanitize_callback' => 'sanitize_key',
    ]);
    $wp_customize->add_control('ks_top_bar_style', [
        'label' => __('Color style', 'acreline'),
        'section' => 'ks_top_bar',
        'type' => 'select',
        'choices' => [
            'dark' => __('Dark (ink background)', 'acreline'),
            'accent' => __('Accent (brand color)', 'acreline'),
            'light' => __('Light (paper background)', 'acreline'),
            'custom' => __('Custom colors', 'acreline'),
        ],
    ]);

    // Custom colors (shown for any style; only applied when style = custom)
    $wp_customize->add_setting('ks_top_bar_bg', [
        'default' => '#141210',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'ks_top_bar_bg', [
        'label' => __('Custom background color', 'acreline'),
        'description' => __('Only applies when "Custom colors" is selected above.', 'acreline'),
        'section' => 'ks_top_bar',
    ]));

    $wp_customize->add_setting('ks_top_bar_text_color', [
        'default' => '#fffcf7',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'ks_top_bar_text_color', [
        'label' => __('Custom text / icon color', 'acreline'),
        'description' => __('Only applies when "Custom colors" is selected above.', 'acreline'),
        'section' => 'ks_top_bar',
    ]));

    // ── Announcement slot ─────────────────────────────────────────────────────
    $wp_customize->add_setting('ks_top_bar_badge', [
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('ks_top_bar_badge', [
        'label' => __('Announcement badge', 'acreline'),
        'description' => __('Short label shown before the message, e.g. "NEW" or "OPEN HOUSE".', 'acreline'),
        'section' => 'ks_top_bar',
        'type' => 'text',
    ]);

    $wp_customize->add_setting('ks_top_bar_message', [
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('ks_top_bar_message', [
        'label' => __('Announcement message', 'acreline'),
        'description' => __('Leave empty to hide the announcement slot.', 'acreline'),
        'section' => 'ks_top_bar',
        'type' => 'text',
    ]);

    $wp_customize->add_setting('ks_top_bar_message_url', [
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    $wp_customize->add_control('ks_top_bar_message_url', [
        'label' => __('Announcement link URL', 'acreline'),
        'description' => __('Optional — wraps the message in a link.', 'acreline'),
        'section' => 'ks_top_bar',
        'type' => 'url',
    ]);

    // ── CTA button ─────────────────────────────────────────────────────────────
    $wp_customize->add_setting('ks_top_bar_cta_label', [
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('ks_top_bar_cta_label', [
        'label' => __('CTA button label', 'acreline'),
        'description' => __('Optional button on the right side, e.g. "Book now". Leave empty to hide.', 'acreline'),
        'section' => 'ks_top_bar',
        'type' => 'text',
    ]);

    $wp_customize->add_setting('ks_top_bar_cta_url', [
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    $wp_customize->add_control('ks_top_bar_cta_url', [
        'label' => __('CTA button URL', 'acreline'),
        'section' => 'ks_top_bar',
        'type' => 'url',
    ]);

    // ── Contact items ──────────────────────────────────────────────────────────
    foreach ([
        'phone' => __('Show phone number', 'acreline'),
        'email' => __('Show email address', 'acreline'),
        'address' => __('Show office address', 'acreline'),
        'hours' => __('Show office hours', 'acreline'),
    ] as $key => $label) {
        $wp_customize->add_setting('ks_top_bar_show_'.$key, [
            'default' => $key === 'phone',
            'sanitize_callback' => __NAMESPACE__.'\\sanitize_checkbox',
        ]);
        $wp_customize->add_control('ks_top_bar_show_'.$key, [
            'label' => $label,
            'section' => 'ks_top_bar',
            'type' => 'checkbox',
        ]);
    }

    // ── Social icons ──────────────────────────────────────────────────────────
    foreach ([
        'facebook' => __('Show Facebook icon', 'acreline'),
        'instagram' => __('Show Instagram icon', 'acreline'),
        'youtube' => __('Show YouTube icon', 'acreline'),
        'linkedin' => __('Show LinkedIn icon', 'acreline'),
        'x' => __('Show X / Twitter icon', 'acreline'),
    ] as $key => $label) {
        $wp_customize->add_setting('ks_top_bar_show_'.$key, [
            'default' => false,
            'sanitize_callback' => __NAMESPACE__.'\\sanitize_checkbox',
        ]);
        $wp_customize->add_control('ks_top_bar_show_'.$key, [
            'label' => $label,
            'description' => $key === 'facebook' ? __('Social URLs are set in Appearance → Customize → Social links.', 'acreline') : '',
            'section' => 'ks_top_bar',
            'type' => 'checkbox',
        ]);
    }

    // ── Behaviour ─────────────────────────────────────────────────────────────
    $wp_customize->add_setting('ks_top_bar_dismissible', [
        'default' => false,
        'sanitize_callback' => __NAMESPACE__.'\\sanitize_checkbox',
    ]);
    $wp_customize->add_control('ks_top_bar_dismissible', [
        'label' => __('Allow visitors to dismiss the bar', 'acreline'),
        'description' => __('A close button appears; dismissed state is stored in sessionStorage.', 'acreline'),
        'section' => 'ks_top_bar',
        'type' => 'checkbox',
    ]);

    $wp_customize->add_setting('ks_top_bar_mobile_show', [
        'default' => true,
        'sanitize_callback' => __NAMESPACE__.'\\sanitize_checkbox',
    ]);
    $wp_customize->add_control('ks_top_bar_mobile_show', [
        'label' => __('Show on mobile', 'acreline'),
        'section' => 'ks_top_bar',
        'type' => 'checkbox',
    ]);

    // End of Top Bar section ───────────────────────────────────────────────────

    $wp_customize->add_section('ks_typography', [
        'title' => __('Typography', 'acreline'),
        'description' => __('Sans-serif fonts used on modern realtor sites. Inter is the default for headings and body.', 'acreline'),
        'priority' => 40,
    ]);

    $choices = [];
    foreach (Typography::fonts() as $key => $font) {
        $choices[$key] = $font['label'];
    }

    foreach (Typography::roles() as $role => $def) {
        $id = 'ks_font_'.$role;
        $wp_customize->add_setting($id, [
            'default' => $def['default'],
            'sanitize_callback' => __NAMESPACE__.'\\sanitize_font_key',
            'transport' => 'postMessage',
        ]);
        $wp_customize->add_control($id, [
            'label' => $def['label'],
            'section' => 'ks_typography',
            'type' => 'select',
            'choices' => $choices,
        ]);
    }

    $wp_customize->add_setting('ks_font_size', [
        'default' => 16,
        'sanitize_callback' => function ($value) {
            return max(14, min(20, (int) $value));
        },
        'transport' => 'postMessage',
    ]);
    $wp_customize->add_control('ks_font_size', [
        'label' => __('Base text size (px)', 'acreline'),
        'section' => 'ks_typography',
        'type' => 'number',
        'input_attrs' => ['min' => 14, 'max' => 20, 'step' => 1],
    ]);

    $wp_customize->add_setting('ks_heading_weight', [
        'default' => 700,
        'sanitize_callback' => function ($value) {
            $value = (int) $value;

            return in_array($value, [500, 600, 700], true) ? $value : 700;
        },
        'transport' => 'postMessage',
    ]);
    $wp_customize->add_control('ks_heading_weight', [
        'label' => __('Heading weight', 'acreline'),
        'section' => 'ks_typography',
        'type' => 'select',
        'choices' => [
            500 => __('Medium (500)', 'acreline'),
            600 => __('Semibold (600)', 'acreline'),
            700 => __('Bold (700)', 'acreline'),
        ],
    ]);
});

function sanitize_font_key($value): string
{
    $value = sanitize_key((string) $value);

    return array_key_exists($value, Typography::fonts()) ? $value : 'inter';
}

function sanitize_checkbox($value): bool
{
    return $value === true
        || $value === 1
        || $value === '1'
        || $value === 'true'
        || $value === 'on'
        || $value === 'yes';
}

/**
 * Brokerage ID, Fair Housing, IDX slots, privacy URLs, and form consent.
 * Tools only — not legal advice. Empty fields stay hidden on the front end.
 */
function ks_register_compliance_customizer(WP_Customize_Manager $wp_customize): void
{
    $wp_customize->add_section('ks_compliance', [
        'title' => __('Compliance', 'acreline'),
        'description' => __('Configurable tools for brokerage identification, Fair Housing, MLS/IDX disclaimers, privacy links, and form consent. Rules vary by state and MLS — check your commission and counsel. This is not legal advice.', 'acreline'),
        'priority' => 32,
    ]);

    $text = [
        'ks_brokerage_legal_name' => [__('Brokerage legal name', 'acreline'), __('Exact licensed name as registered with your state commission.', 'acreline')],
        'ks_license_number' => [__('License number (optional)', 'acreline'), __('Some states require this on first-point-of-contact materials. Texas statute bars TREC from requiring license numbers — leave blank if yours does not.', 'acreline')],
        'ks_broker_license' => [__('Broker license (optional)', 'acreline'), __('Supervising broker license when your state or board asks for it separately.', 'acreline')],
        'ks_license_jurisdiction' => [__('License jurisdiction', 'acreline'), __('e.g. Pennsylvania Real Estate Commission', 'acreline')],
        'ks_office_city' => [__('Office city', 'acreline'), __('NAR internet advertising typically wants office city and state.', 'acreline')],
        'ks_office_state' => [__('Office state', 'acreline'), ''],
        'ks_idx_copyright' => [__('MLS / IDX copyright line', 'acreline'), __('Optional. Paste your board’s copyright sentence.', 'acreline')],
    ];

    foreach ($text as $id => [$label, $description]) {
        $wp_customize->add_setting($id, [
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control($id, [
            'label' => $label,
            'description' => $description,
            'section' => 'ks_compliance',
            'type' => 'text',
        ]);
    }

    foreach ([
        'ks_show_license_footer' => [__('Show brokerage ID in the footer', 'acreline'), true],
        'ks_show_license_header' => [__('Show brokerage ID in the header / top bar', 'acreline'), false],
        'ks_eho_enable' => [__('Show Equal Housing Opportunity statement', 'acreline'), true],
        'ks_eho_show_logo' => [__('Show Equal Housing house mark (best practice, not federally required)', 'acreline'), true],
        'ks_idx_enable' => [__('Show MLS / IDX disclaimer slots on listings', 'acreline'), false],
        'ks_idx_attribution' => [__('Show listing-office attribution when a listing office is filled', 'acreline'), false],
        'ks_consent_enable' => [__('Require consent checkbox on showing and contact forms', 'acreline'), true],
    ] as $id => [$label, $default]) {
        $wp_customize->add_setting($id, [
            'default' => $default,
            'sanitize_callback' => __NAMESPACE__.'\\sanitize_checkbox',
        ]);
        $wp_customize->add_control($id, [
            'label' => $label,
            'section' => 'ks_compliance',
            'type' => 'checkbox',
        ]);
    }

    $wp_customize->add_setting('ks_eho_statement', [
        'default' => '',
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);
    $wp_customize->add_control('ks_eho_statement', [
        'label' => __('Equal Housing statement', 'acreline'),
        'description' => __('Leave empty for the built-in Equal Housing Opportunity paragraph. Logo is industry best practice; HUD does not federally mandate it.', 'acreline'),
        'section' => 'ks_compliance',
        'type' => 'textarea',
    ]);

    $wp_customize->add_setting('ks_idx_disclaimer', [
        'default' => '',
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);
    $wp_customize->add_control('ks_idx_disclaimer', [
        'label' => __('MLS / IDX disclaimer', 'acreline'),
        'description' => __('Paste your board’s exact disclaimer. Empty by default. This theme does not claim IDX compliance for every MLS.', 'acreline'),
        'section' => 'ks_compliance',
        'type' => 'textarea',
    ]);

    $wp_customize->add_setting('ks_idx_logo_url', [
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    $wp_customize->add_control('ks_idx_logo_url', [
        'label' => __('IDX / MLS logo URL', 'acreline'),
        'description' => __('Optional. Use a logo your board licenses to you — do not paste NAR, HUD, or MLS marks you do not have rights to.', 'acreline'),
        'section' => 'ks_compliance',
        'type' => 'url',
    ]);

    $wp_customize->add_setting('ks_privacy_url', [
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    $wp_customize->add_control('ks_privacy_url', [
        'label' => __('Privacy Policy URL', 'acreline'),
        'description' => __('Footer link only. This theme does not invent legal policy text.', 'acreline'),
        'section' => 'ks_compliance',
        'type' => 'url',
    ]);

    $wp_customize->add_setting('ks_terms_url', [
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    $wp_customize->add_control('ks_terms_url', [
        'label' => __('Terms of Use URL (optional)', 'acreline'),
        'section' => 'ks_compliance',
        'type' => 'url',
    ]);

    $wp_customize->add_setting('ks_consent_disclosure', [
        'default' => '',
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);
    $wp_customize->add_control('ks_consent_disclosure', [
        'label' => __('Form consent disclosure', 'acreline'),
        'description' => __('Shown next to an unchecked checkbox. Use {brokerage} for the licensed name. Leave empty for the built-in “consent not required to purchase” wording. Consult counsel before autodialed or text marketing.', 'acreline'),
        'section' => 'ks_compliance',
        'type' => 'textarea',
    ]);

    $wp_customize->add_setting('ks_consent_sms', [
        'default' => '',
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);
    $wp_customize->add_control('ks_consent_sms', [
        'label' => __('Optional SMS / call language', 'acreline'),
        'description' => __('Extra sentence under the checkbox (opt-out, message rates, etc.). Leave empty to hide.', 'acreline'),
        'section' => 'ks_compliance',
        'type' => 'textarea',
    ]);
}

add_action('wp_enqueue_scripts', function () {
    $families = Typography::googleFamilies();
    if ($families === []) {
        return;
    }
    $href = 'https://fonts.googleapis.com/css2?family='.implode('&family=', $families).'&display=swap';
    wp_enqueue_style('keystone-fonts', $href, [], null);
}, 5);

add_filter('style_loader_tag', function (string $tag, string $handle) {
    if ($handle !== 'keystone-fonts') {
        return $tag;
    }

    return '<link rel="preconnect" href="https://fonts.googleapis.com">'."\n"
        .'<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>'."\n"
        .$tag;
}, 10, 2);

add_action('wp_head', function () {
    echo '<style id="keystone-typography">'.Typography::cssVariables().'</style>'."\n";
}, 20);

add_action('customize_controls_enqueue_scripts', function () {
    $rel = 'resources/js/customizer-controls.js';
    $src = get_theme_file_uri($rel);
    $path = get_theme_file_path($rel);
    wp_enqueue_script(
        'keystone-customizer-controls',
        $src,
        ['customize-controls', 'jquery'],
        file_exists($path) ? (string) filemtime($path) : wp_get_theme()->get('Version'),
        true
    );
    wp_localize_script('keystone-customizer-controls', 'ACRELINE_SCHEMES', ColorSchemes::forJs());
});

add_action('customize_preview_init', function () {
    $rel = 'resources/js/customizer-preview.js';
    $src = get_theme_file_uri($rel);
    $path = get_theme_file_path($rel);
    wp_enqueue_script(
        'keystone-customizer-preview',
        $src,
        ['customize-preview'],
        file_exists($path) ? (string) filemtime($path) : wp_get_theme()->get('Version'),
        true
    );
    wp_localize_script('keystone-customizer-preview', 'ACRELINE_FONTS', [
        'stacks' => Typography::stacksForJs(),
        'google' => array_map(fn ($font) => $font['google'], Typography::fonts()),
        'roles' => array_map(fn ($def) => $def['css'], Typography::roles()),
    ]);
});
