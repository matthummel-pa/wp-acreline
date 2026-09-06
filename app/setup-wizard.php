<?php

/**
 * Multi-step Acreline Setup wizard (Appearance → Acreline Setup).
 *
 * Buyer onboarding only — no upsells, license locks, or external nags.
 */

namespace App;

use App\Support\ColorSchemes;
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
    return ['welcome', 'identity', 'colors', 'demo', 'finish'];
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
