<?php

/**
 * Multi-step Acreline Setup wizard (Appearance → Acreline Setup).
 *
 * Buyer onboarding only — no upsells, license locks, or external nags.
 */

namespace App;

use App\Support\ColorSchemes;
use App\Support\Compliance;
use App\Support\DemoContent;
use App\Support\Identity;

const KS_WIZARD_OPTION = 'ks_setup_wizard_complete';
const KS_WIZARD_STEP_OPTION = 'ks_setup_wizard_step';
const KS_WIZARD_PAGE = 'keystone-setup';

/**
 * @return list<string>
 */
function ks_wizard_steps(): array
{
    return ['welcome', 'identity', 'compliance', 'colors', 'demo', 'finish'];
}

function ks_wizard_is_complete(): bool
{
    return (string) get_option(KS_WIZARD_OPTION, '') === '1';
}

function ks_wizard_current_step(): string
{
    $requested = isset($_GET['step']) ? sanitize_key((string) $_GET['step']) : '';
    if (in_array($requested, ks_wizard_steps(), true)) {
        return $requested;
    }

    $saved = sanitize_key((string) get_option(KS_WIZARD_STEP_OPTION, 'welcome'));

    return in_array($saved, ks_wizard_steps(), true) ? $saved : 'welcome';
}

function ks_wizard_url(string $step = ''): string
{
    $args = ['page' => KS_WIZARD_PAGE];
    if ($step !== '' && in_array($step, ks_wizard_steps(), true)) {
        $args['step'] = $step;
    }

    return add_query_arg($args, admin_url('themes.php'));
}

function ks_wizard_next_step(string $step): string
{
    $steps = ks_wizard_steps();
    $index = array_search($step, $steps, true);
    if ($index === false || $index >= count($steps) - 1) {
        return 'finish';
    }

    return $steps[$index + 1];
}

function ks_wizard_prev_step(string $step): string
{
    $steps = ks_wizard_steps();
    $index = array_search($step, $steps, true);
    if ($index === false || $index <= 0) {
        return 'welcome';
    }

    return $steps[$index - 1];
}

add_action('admin_menu', function (): void {
    add_theme_page(
        __('Acreline Setup', 'acreline'),
        __('Acreline Setup', 'acreline'),
        'edit_theme_options',
        KS_WIZARD_PAGE,
        __NAMESPACE__.'\\ks_render_setup_wizard'
    );
});

add_action('after_switch_theme', function (): void {
    if (get_template() !== 'acreline') {
        return;
    }
    if (ks_wizard_is_complete()) {
        return;
    }
    set_transient('ks_setup_wizard_redirect', '1', MINUTE_IN_SECONDS);
});

add_action('admin_init', function (): void {
    if (! get_transient('ks_setup_wizard_redirect')) {
        return;
    }
    delete_transient('ks_setup_wizard_redirect');
    if (! current_user_can('edit_theme_options') || ks_wizard_is_complete()) {
        return;
    }
    wp_safe_redirect(ks_wizard_url('welcome'));
    exit;
});

add_action('admin_notices', function (): void {
    if (! current_user_can('edit_theme_options') || ks_wizard_is_complete()) {
        return;
    }
    $screen = get_current_screen();
    if ($screen && $screen->id === 'appearance_page_'.KS_WIZARD_PAGE) {
        return;
    }
    echo '<div class="notice notice-info is-dismissible"><p>';
    echo esc_html__('Finish Acreline Setup to brand your office and load demo listings.', 'acreline');
    echo ' <a href="'.esc_url(ks_wizard_url()).'">'.esc_html__('Open setup wizard', 'acreline').'</a>';
    echo '</p></div>';
});

add_action('admin_enqueue_scripts', function (string $hook): void {
    if ($hook !== 'appearance_page_'.KS_WIZARD_PAGE) {
        return;
    }
    $css = '
.ks-wizard{max-width:52rem;margin:1.5rem 0 3rem}
.ks-wizard__intro{max-width:44rem;color:#50575e;font-size:14px;line-height:1.6}
.ks-wizard__steps{display:flex;flex-wrap:wrap;gap:.5rem;margin:1.25rem 0 1.75rem;padding:0;list-style:none}
.ks-wizard__steps li{display:flex;align-items:center;gap:.4rem;padding:.4rem .75rem;border-radius:999px;background:#f0f0f1;color:#50575e;font-size:12px;font-weight:600}
.ks-wizard__steps li.is-current{background:#1f6b4a;color:#fff}
.ks-wizard__steps li.is-done{background:#d8efe4;color:#155539}
.ks-wizard__panel{background:#fff;border:1px solid #dcdcde;border-radius:8px;padding:1.5rem 1.75rem;box-shadow:0 1px 1px rgba(0,0,0,.04)}
.ks-wizard__panel h2{margin-top:0}
.ks-wizard__grid{display:grid;gap:1rem}
@media (min-width:782px){.ks-wizard__grid--2{grid-template-columns:1fr 1fr}}
.ks-wizard__field label{display:block;font-weight:600;margin-bottom:.35rem}
.ks-wizard__field input[type=text],
.ks-wizard__field input[type=email],
.ks-wizard__field input[type=url],
.ks-wizard__field textarea,
.ks-wizard__field select{width:100%;max-width:100%}
.ks-wizard__schemes{display:grid;grid-template-columns:repeat(auto-fill,minmax(9rem,1fr));gap:.75rem;margin:1rem 0}
.ks-wizard__scheme{position:relative;border:2px solid #dcdcde;border-radius:8px;padding:.75rem;cursor:pointer;background:#fff}
.ks-wizard__scheme input{position:absolute;opacity:0;pointer-events:none}
.ks-wizard__scheme.is-selected,
.ks-wizard__scheme:has(input:checked){border-color:#1f6b4a;box-shadow:0 0 0 1px #1f6b4a}
.ks-wizard__swatch{display:flex;height:2rem;border-radius:4px;overflow:hidden;margin-bottom:.5rem}
.ks-wizard__swatch span{flex:1}
.ks-wizard__actions{display:flex;flex-wrap:wrap;gap:.75rem;align-items:center;margin-top:1.5rem}
.ks-wizard__actions .button-link{margin-left:auto}
.ks-wizard__checklist{margin:0;padding-left:1.2rem;line-height:1.7}
.ks-wizard__note{max-width:44rem;color:#646970;font-size:13px}
';
    wp_register_style('ks-setup-wizard', false, [], '1.3.0');
    wp_enqueue_style('ks-setup-wizard');
    wp_add_inline_style('ks-setup-wizard', $css);
});

add_action('admin_post_ks_setup_wizard', __NAMESPACE__.'\\ks_handle_setup_wizard');

function ks_handle_setup_wizard(): void
{
    if (! current_user_can('edit_theme_options')) {
        wp_die(esc_html__('You do not have permission to edit theme options.', 'acreline'));
    }

    check_admin_referer('ks_setup_wizard', 'ks_wizard_nonce');

    $step = isset($_POST['ks_wizard_step']) ? sanitize_key((string) $_POST['ks_wizard_step']) : 'welcome';
    if (! in_array($step, ks_wizard_steps(), true)) {
        $step = 'welcome';
    }

    $action = isset($_POST['ks_wizard_action']) ? sanitize_key((string) $_POST['ks_wizard_action']) : 'next';

    if ($action === 'skip') {
        update_option(KS_WIZARD_OPTION, '1');
        update_option(KS_WIZARD_STEP_OPTION, 'finish');
        wp_safe_redirect(ks_wizard_url('finish'));
        exit;
    }

    if ($action === 'back') {
        $prev = ks_wizard_prev_step($step);
        update_option(KS_WIZARD_STEP_OPTION, $prev);
        wp_safe_redirect(ks_wizard_url($prev));
        exit;
    }

    if ($step === 'identity') {
        $fields = [
            'ks_brand_name' => 'sanitize_text_field',
            'ks_tagline' => 'sanitize_text_field',
            'ks_phone' => 'sanitize_text_field',
            'ks_email' => 'sanitize_email',
            'ks_cta_label' => 'sanitize_text_field',
            'ks_cta_url' => 'esc_url_raw',
            'ks_address' => 'sanitize_textarea_field',
            'ks_hours' => 'sanitize_textarea_field',
        ];
        foreach ($fields as $key => $sanitize) {
            if (! isset($_POST[$key])) {
                continue;
            }
            $raw = wp_unslash((string) $_POST[$key]);
            set_theme_mod($key, $sanitize($raw));
        }
        if (isset($_POST['ks_brand_name'])) {
            $brand = sanitize_text_field(wp_unslash((string) $_POST['ks_brand_name']));
            if ($brand !== '') {
                update_option('blogname', $brand);
            }
        }
        if (isset($_POST['ks_tagline'])) {
            $tagline = sanitize_text_field(wp_unslash((string) $_POST['ks_tagline']));
            update_option('blogdescription', $tagline);
        }
    }

    if ($step === 'compliance') {
        $wizardKeys = [
            'ks_brokerage_legal_name',
            'ks_license_number',
            'ks_broker_license',
            'ks_license_jurisdiction',
            'ks_office_city',
            'ks_office_state',
            'ks_privacy_url',
            'ks_eho_enable',
            'ks_eho_show_logo',
            'ks_consent_enable',
            'ks_show_license_footer',
        ];
        $modRaw = [];
        foreach ($wizardKeys as $key) {
            if (isset($_POST[$key])) {
                $modRaw[$key] = $_POST[$key];
            }
        }
        Compliance::saveFromPost($modRaw, $wizardKeys);
    }

    if ($step === 'colors') {
        $schemeKey = ColorSchemes::sanitizeKey($_POST['ks_color_scheme'] ?? ColorSchemes::defaultKey());
        $scheme = ColorSchemes::all()[$schemeKey];
        set_theme_mod('ks_color_scheme', $schemeKey);
        set_theme_mod('ks_accent', $scheme['accent']);
        set_theme_mod('ks_paper', $scheme['paper']);
        set_theme_mod('ks_ink', $scheme['ink']);
        set_theme_mod('ks_show_demo_chrome', ! empty($_POST['ks_hide_demo_chrome']) ? false : true);
        set_theme_mod('ks_show_credit', ! empty($_POST['ks_hide_credit']) ? false : true);
    }

    if ($step === 'demo' && ! empty($_POST['ks_load_demo']) && current_user_can('manage_options')) {
        DemoContent::seed();
        update_option(DemoContent::OPTION, '1');
    }

    if ($step === 'finish' || $action === 'finish') {
        update_option(KS_WIZARD_OPTION, '1');
        update_option(KS_WIZARD_STEP_OPTION, 'finish');
        wp_safe_redirect(ks_wizard_url('finish'));
        exit;
    }

    $next = ks_wizard_next_step($step);
    update_option(KS_WIZARD_STEP_OPTION, $next);
    if ($next === 'finish') {
        update_option(KS_WIZARD_OPTION, '1');
    }
    wp_safe_redirect(ks_wizard_url($next));
    exit;
}

function ks_render_setup_wizard(): void
{
    if (! current_user_can('edit_theme_options')) {
        wp_die(esc_html__('You do not have permission to edit theme options.', 'acreline'));
    }

    $step = ks_wizard_current_step();
    $steps = ks_wizard_steps();
    $labels = [
        'welcome' => __('Welcome', 'acreline'),
        'identity' => __('Identity', 'acreline'),
        'compliance' => __('Compliance', 'acreline'),
        'colors' => __('Colors', 'acreline'),
        'demo' => __('Demo', 'acreline'),
        'finish' => __('Done', 'acreline'),
    ];
    $currentIndex = (int) array_search($step, $steps, true);

    echo '<div class="wrap ks-wizard">';
    echo '<h1>'.esc_html__('Acreline Setup', 'acreline').'</h1>';
    echo '<p class="ks-wizard__intro">'.esc_html__('A short wizard to brand your office, pick a color style, and optionally load demo listings. No upsells — everything stays in this theme.', 'acreline').'</p>';

    echo '<ol class="ks-wizard__steps" aria-label="'.esc_attr__('Setup steps', 'acreline').'">';
    foreach ($steps as $index => $key) {
        $class = $index < $currentIndex ? 'is-done' : ($index === $currentIndex ? 'is-current' : '');
        echo '<li class="'.esc_attr($class).'">';
        echo '<span>'.esc_html((string) ($index + 1)).'</span>';
        echo '<span>'.esc_html($labels[$key]).'</span>';
        echo '</li>';
    }
    echo '</ol>';

    echo '<div class="ks-wizard__panel">';
    echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'">';
    echo '<input type="hidden" name="action" value="ks_setup_wizard">';
    echo '<input type="hidden" name="ks_wizard_step" value="'.esc_attr($step).'">';
    wp_nonce_field('ks_setup_wizard', 'ks_wizard_nonce');

    match ($step) {
        'identity' => ks_wizard_step_identity(),
        'compliance' => ks_wizard_step_compliance(),
        'colors' => ks_wizard_step_colors(),
        'demo' => ks_wizard_step_demo(),
        'finish' => ks_wizard_step_finish(),
        default => ks_wizard_step_welcome(),
    };

    if ($step !== 'finish') {
        echo '<div class="ks-wizard__actions">';
        if ($step !== 'welcome') {
            echo '<button type="submit" name="ks_wizard_action" value="back" class="button">'.esc_html__('Back', 'acreline').'</button>';
        }
        $nextLabel = $step === 'demo' ? __('Finish setup', 'acreline') : __('Continue', 'acreline');
        echo '<button type="submit" name="ks_wizard_action" value="next" class="button button-primary">'.esc_html($nextLabel).'</button>';
        echo '<button type="submit" name="ks_wizard_action" value="skip" class="button-link">'.esc_html__('Skip wizard', 'acreline').'</button>';
        echo '</div>';
    }

    echo '</form>';
    echo '</div>';
    echo '</div>';
}

function ks_wizard_step_welcome(): void
{
    $coreActive = defined('KEYSTONE_CORE_VERSION');
    echo '<h2>'.esc_html__('Welcome to Acreline', 'acreline').'</h2>';
    echo '<p>'.esc_html__('This wizard walks the first branding pass so your real estate site looks like your office — not the concept demo.', 'acreline').'</p>';
    echo '<ul class="ks-wizard__checklist">';
    echo '<li>'.esc_html__('Set office name, phone, email, and hours', 'acreline').'</li>';
    echo '<li>'.esc_html__('Add brokerage legal name, optional license, and form consent', 'acreline').'</li>';
    echo '<li>'.esc_html__('Pick one of eight color styles', 'acreline').'</li>';
    echo '<li>'.esc_html__('Optionally load demo pages, listings, and agents', 'acreline').'</li>';
    echo '</ul>';
    echo '<p class="ks-wizard__note">';
    echo $coreActive
        ? esc_html__('Acreline Core is active — listings stay if you switch themes later.', 'acreline')
        : esc_html__('Optional: install Acreline Core from your marketplace pack so listings survive a theme switch.', 'acreline');
    echo '</p>';
}

function ks_wizard_step_identity(): void
{
    echo '<h2>'.esc_html__('Office identity', 'acreline').'</h2>';
    echo '<p>'.esc_html__('These values power the header, footer, and contact blocks. You can change them again anytime under Customize → Identity.', 'acreline').'</p>';
    echo '<div class="ks-wizard__grid ks-wizard__grid--2">';
    $fields = [
        'ks_brand_name' => [__('Brand name', 'acreline'), Identity::brandName(), 'text'],
        'ks_tagline' => [__('Header tagline', 'acreline'), (string) get_theme_mod('ks_tagline', Identity::tagline()), 'text'],
        'ks_phone' => [__('Phone', 'acreline'), Identity::phone(), 'text'],
        'ks_email' => [__('Email', 'acreline'), Identity::email(), 'email'],
        'ks_cta_label' => [__('Header button label', 'acreline'), (string) get_theme_mod('ks_cta_label', __('Book a showing', 'acreline')), 'text'],
        'ks_cta_url' => [__('Header button URL', 'acreline'), (string) get_theme_mod('ks_cta_url', ''), 'url'],
    ];
    foreach ($fields as $name => [$label, $value, $type]) {
        echo '<div class="ks-wizard__field">';
        echo '<label for="'.esc_attr($name).'">'.esc_html($label).'</label>';
        printf(
            '<input type="%1$s" name="%2$s" id="%2$s" value="%3$s" class="regular-text">',
            esc_attr($type),
            esc_attr($name),
            esc_attr($value)
        );
        echo '</div>';
    }
    echo '</div>';
    echo '<div class="ks-wizard__grid" style="margin-top:1rem">';
    echo '<div class="ks-wizard__field">';
    echo '<label for="ks_address">'.esc_html__('Address', 'acreline').'</label>';
    echo '<textarea name="ks_address" id="ks_address" rows="3">'.esc_textarea(Identity::address()).'</textarea>';
    echo '</div>';
    echo '<div class="ks-wizard__field">';
    echo '<label for="ks_hours">'.esc_html__('Hours', 'acreline').'</label>';
    echo '<textarea name="ks_hours" id="ks_hours" rows="3">'.esc_textarea(Identity::hours()).'</textarea>';
    echo '</div>';
    echo '</div>';
}

function ks_wizard_step_compliance(): void
{
    echo '<h2>'.esc_html__('Brokerage ID &amp; compliance', 'acreline').'</h2>';
    echo '<p>'.esc_html__('Not legal advice. Almost every state wants the brokerage’s licensed name on the website. Extra ID (license number, city/state) varies — check your commission. Empty fields stay hidden.', 'acreline').'</p>';
    echo '<div class="ks-wizard__grid ks-wizard__grid--2">';
    $fields = [
        'ks_brokerage_legal_name' => [__('Brokerage legal name', 'acreline'), Compliance::text('ks_brokerage_legal_name'), 'text'],
        'ks_license_number' => [__('License number (optional)', 'acreline'), Compliance::text('ks_license_number'), 'text'],
        'ks_broker_license' => [__('Broker license (optional)', 'acreline'), Compliance::text('ks_broker_license'), 'text'],
        'ks_license_jurisdiction' => [__('License jurisdiction', 'acreline'), Compliance::text('ks_license_jurisdiction'), 'text'],
        'ks_office_city' => [__('Office city', 'acreline'), Compliance::text('ks_office_city'), 'text'],
        'ks_office_state' => [__('Office state', 'acreline'), Compliance::text('ks_office_state'), 'text'],
        'ks_privacy_url' => [__('Privacy Policy URL', 'acreline'), Compliance::text('ks_privacy_url'), 'url'],
    ];
    foreach ($fields as $name => [$label, $value, $type]) {
        echo '<div class="ks-wizard__field">';
        echo '<label for="'.esc_attr($name).'">'.esc_html($label).'</label>';
        printf(
            '<input type="%1$s" name="%2$s" id="%2$s" value="%3$s" class="regular-text">',
            esc_attr($type),
            esc_attr($name),
            esc_attr($value)
        );
        echo '</div>';
    }
    echo '</div>';
    echo '<p><label><input type="checkbox" name="ks_eho_enable" value="1" '.checked(Compliance::flag('ks_eho_enable'), true, false).'> ';
    echo esc_html__('Show Equal Housing Opportunity statement in the footer', 'acreline').'</label></p>';
    echo '<p><label><input type="checkbox" name="ks_eho_show_logo" value="1" '.checked(Compliance::flag('ks_eho_show_logo'), true, false).'> ';
    echo esc_html__('Show Equal Housing house mark', 'acreline').'</label></p>';
    echo '<p><label><input type="checkbox" name="ks_consent_enable" value="1" '.checked(Compliance::flag('ks_consent_enable'), true, false).'> ';
    echo esc_html__('Require consent checkbox on showing and contact forms', 'acreline').'</label></p>';
    echo '<p><label><input type="checkbox" name="ks_show_license_footer" value="1" '.checked(Compliance::flag('ks_show_license_footer'), true, false).'> ';
    echo esc_html__('Show brokerage ID in the footer when fields are filled', 'acreline').'</label></p>';
    echo '<p class="ks-wizard__note">'.esc_html__('MLS/IDX disclaimer, custom Fair Housing copy, and SMS language live under Customize → Compliance or Appearance → Acreline Settings → Compliance. REALTOR® is a trademark — use it only if you are a member.', 'acreline').'</p>';
}

function ks_wizard_step_colors(): void
{
    $current = ColorSchemes::currentKey();
    $demoOn = \App\ks_hero_value_on(get_theme_mod('ks_show_demo_chrome', true));
    $creditOn = (bool) get_theme_mod('ks_show_credit', true);

    echo '<h2>'.esc_html__('Color style', 'acreline').'</h2>';
    echo '<p>'.esc_html__('Pick a named style. Accent, paper, and ink update together. Fine-tune later in Customize → Colors.', 'acreline').'</p>';
    echo '<div class="ks-wizard__schemes" role="radiogroup" aria-label="'.esc_attr__('Color styles', 'acreline').'">';
    foreach (ColorSchemes::all() as $key => $scheme) {
        $selected = $key === $current ? ' is-selected' : '';
        echo '<label class="ks-wizard__scheme'.esc_attr($selected).'">';
        printf(
            '<input type="radio" name="ks_color_scheme" value="%1$s"%2$s>',
            esc_attr($key),
            checked($key, $current, false)
        );
        echo '<span class="ks-wizard__swatch" aria-hidden="true">';
        echo '<span style="background:'.esc_attr($scheme['accent']).'"></span>';
        echo '<span style="background:'.esc_attr($scheme['paper']).'"></span>';
        echo '<span style="background:'.esc_attr($scheme['ink']).'"></span>';
        echo '</span>';
        echo '<strong>'.esc_html($scheme['label']).'</strong>';
        echo '</label>';
    }
    echo '</div>';
    echo '<p><label><input type="checkbox" name="ks_hide_demo_chrome" value="1" '.checked(! $demoOn, true, false).'> ';
    echo esc_html__('Hide the concept demo banner', 'acreline').'</label></p>';
    echo '<p><label><input type="checkbox" name="ks_hide_credit" value="1" '.checked(! $creditOn, true, false).'> ';
    echo esc_html__('Hide the removable author credit in the footer', 'acreline').'</label></p>';
}

function ks_wizard_step_demo(): void
{
    $seeded = DemoContent::isComplete();
    $canSeed = current_user_can('manage_options');
    $menus = get_nav_menu_locations();
    $hasPrimary = ! empty($menus['primary_navigation']);

    echo '<h2>'.esc_html__('Demo content', 'acreline').'</h2>';
    echo '<p>'.esc_html__('Load sample pages, listings, agents, and menus so the site matches the live demo. Safe to re-run — existing slugs update instead of duplicating.', 'acreline').'</p>';
    if ($seeded) {
        echo '<div class="notice notice-success inline"><p>'.esc_html__('Demo content is already loaded on this site.', 'acreline').'</p></div>';
    }
    if ($canSeed) {
        echo '<p><label><input type="checkbox" name="ks_load_demo" value="1" '.checked(! $seeded, true, false).'> ';
        echo esc_html__('Load / refresh Acreline demo content now', 'acreline').'</label></p>';
    } else {
        echo '<p class="ks-wizard__note">'.esc_html__('You need the manage_options capability to seed demo content. An administrator can run Tools → Seed Acreline demo.', 'acreline').'</p>';
    }
    echo '<p class="ks-wizard__note">';
    echo $hasPrimary
        ? esc_html__('Primary navigation is assigned.', 'acreline')
        : esc_html__('Seeding also creates Primary and Footer menus when missing.', 'acreline');
    echo '</p>';
}

function ks_wizard_step_finish(): void
{
    $customizer = admin_url('customize.php');
    $logoUrl = add_query_arg('autofocus[control]', 'custom_logo', $customizer);
    $front = home_url('/');

    echo '<h2>'.esc_html__('You are set', 'acreline').'</h2>';
    echo '<p>'.esc_html__('Acreline Setup is complete. Next steps that usually matter:', 'acreline').'</p>';
    echo '<ul class="ks-wizard__checklist">';
    echo '<li><a href="'.esc_url($logoUrl).'">'.esc_html__('Upload your logo', 'acreline').'</a> '.esc_html__('under Site Identity', 'acreline').'</li>';
    echo '<li><a href="'.esc_url(add_query_arg('autofocus[section]', 'ks_compliance', $customizer)).'">'.esc_html__('Finish compliance fields', 'acreline').'</a> '.esc_html__('(MLS disclaimer, consent copy, privacy URL)', 'acreline').'</li>';
    echo '<li><a href="'.esc_url($customizer).'">'.esc_html__('Open the Customizer', 'acreline').'</a> '.esc_html__('for header, typography, and social links', 'acreline').'</li>';
    echo '<li><a href="'.esc_url($front).'" target="_blank" rel="noopener noreferrer">'.esc_html__('View the front end', 'acreline').'</a></li>';
    echo '</ul>';
    echo '<div class="ks-wizard__actions">';
    echo '<a class="button button-primary" href="'.esc_url($front).'">'.esc_html__('View site', 'acreline').'</a>';
    echo '<a class="button" href="'.esc_url(wp_nonce_url(admin_url('admin-post.php?action=ks_setup_wizard_restart'), 'ks_setup_wizard_restart')).'">'.esc_html__('Run wizard again', 'acreline').'</a>';
    echo '</div>';
    echo '<p class="ks-wizard__note">'.esc_html__('Re-open this screen anytime from Appearance → Acreline Setup.', 'acreline').'</p>';
}

add_action('admin_post_ks_setup_wizard_restart', function (): void {
    if (! current_user_can('edit_theme_options')) {
        wp_die(esc_html__('You do not have permission to edit theme options.', 'acreline'));
    }
    check_admin_referer('ks_setup_wizard_restart');
    delete_option(KS_WIZARD_OPTION);
    update_option(KS_WIZARD_STEP_OPTION, 'welcome');
    wp_safe_redirect(ks_wizard_url('welcome'));
    exit;
});
