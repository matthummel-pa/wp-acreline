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

function ks_wizard_step_colors(): void
{
    $current = ColorSchemes::currentKey();
    $demoOn = (bool) get_theme_mod('ks_show_demo_chrome', true);
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
