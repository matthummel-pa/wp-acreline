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
