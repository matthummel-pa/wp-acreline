<?php

/**
 * Custom Block Generator — Tools → Block Generator.
 *
 * Lets admins create new Acreline custom blocks without writing PHP or JS.
 * Block definitions are stored in wp_options as 'ks_custom_blocks'.
 * The acreline/custom block type renders them server-side using render_callback.
 */

namespace App;

add_action('admin_menu', function (): void {
    add_management_page(
        __('Block Generator', 'acreline'),
        __('Block Generator', 'acreline'),
        'manage_options',
        'ks-block-generator',
        __NAMESPACE__.'\\ks_block_generator_page'
    );
});

function ks_block_generator_page(): void
{
    if (! current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have permission to access this page.', 'acreline'));
    }

    $defs = ks_get_custom_block_definitions();
    $saved = false;
    $errors = [];

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ks_block_gen_nonce'])) {
        if (! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['ks_block_gen_nonce'])), 'ks_block_gen')) {
            wp_die(esc_html__('Security check failed.', 'acreline'));
        }

        $action = sanitize_key((string) ($_POST['ks_action'] ?? ''));

        if ($action === 'delete' && isset($_POST['ks_delete_id'])) {
            $delId = sanitize_key((string) $_POST['ks_delete_id']);
            unset($defs[$delId]);
            update_option('ks_custom_blocks', $defs, false);
            $saved = true;
        } elseif ($action === 'save') {
            $id = sanitize_key((string) ($_POST['ks_block_id'] ?? ''));
            $title = sanitize_text_field((string) ($_POST['ks_block_title'] ?? ''));
            $desc = sanitize_text_field((string) ($_POST['ks_block_description'] ?? ''));
            $icon = sanitize_key((string) ($_POST['ks_block_icon'] ?? 'star-filled'));

            if ($id === '') {
                $errors[] = __('Block ID is required.', 'acreline');
            } elseif (! preg_match('/^[a-z][a-z0-9_]*$/', $id)) {
                $errors[] = __('Block ID must start with a letter and contain only lowercase letters, numbers, and underscores.', 'acreline');
            }

            if ($title === '') {
                $errors[] = __('Block title is required.', 'acreline');
            }

            if (empty($errors)) {
                $fieldNames = array_map('sanitize_key', (array) ($_POST['ks_field_name'] ?? []));
                $fieldLabels = array_map('sanitize_text_field', (array) ($_POST['ks_field_label'] ?? []));
                $fieldTypes = (array) ($_POST['ks_field_type'] ?? []);
                $fieldDefaults = array_map('sanitize_text_field', (array) ($_POST['ks_field_default'] ?? []));

                $fields = [];
                foreach ($fieldNames as $i => $fname) {
                    if ($fname === '') {
                        continue;
                    }
                    $ftype = sanitize_key((string) ($fieldTypes[$i] ?? 'text'));
                    if (! in_array($ftype, ['text', 'textarea', 'url', 'image', 'toggle'], true)) {
                        $ftype = 'text';
                    }
                    $fields[] = [
                        'name' => $fname,
                        'label' => $fieldLabels[$i] ?? $fname,
                        'type' => $ftype,
                        'default' => $fieldDefaults[$i] ?? '',
                    ];
                }

                $defs[$id] = [
                    'id' => $id,
                    'title' => $title,
                    'description' => $desc,
                    'icon' => $icon,
                    'fields' => $fields,
                ];
                update_option('ks_custom_blocks', $defs, false);
                $saved = true;
            }
        }
    }

    $icons = [
        'star-filled' => __('Star', 'acreline'),
        'admin-page' => __('Page', 'acreline'),
        'layout' => __('Layout', 'acreline'),
        'images-alt' => __('Images', 'acreline'),
        'text' => __('Text', 'acreline'),
        'email' => __('Email', 'acreline'),
        'location' => __('Location', 'acreline'),
        'phone' => __('Phone', 'acreline'),
        'groups' => __('Team', 'acreline'),
        'tag' => __('Tag', 'acreline'),
        'lightbulb' => __('Idea', 'acreline'),
        'chart-line' => __('Chart', 'acreline'),
    ];

    $fieldTypes = [
        'text' => __('Single line text', 'acreline'),
        'textarea' => __('Multi-line text', 'acreline'),
        'url' => __('URL / Link', 'acreline'),
        'image' => __('Image URL', 'acreline'),
        'toggle' => __('Toggle (yes/no)', 'acreline'),
    ];
    ?>
    <div class="wrap ks-block-generator">
      <h1><?php esc_html_e('Block Generator', 'acreline'); ?></h1>
      <p class="description"><?php esc_html_e('Create custom Gutenberg blocks without writing code. Each generated block uses the acreline/custom renderer and stores its field values as block attributes.', 'acreline'); ?></p>

      <?php if ($saved && empty($errors)) { ?>
        <div class="notice notice-success is-dismissible"><p><?php esc_html_e('Block saved successfully.', 'acreline'); ?></p></div>
      <?php } ?>
      <?php foreach ($errors as $error) { ?>
        <div class="notice notice-error"><p><?php echo esc_html($error); ?></p></div>
      <?php } ?>

      <div class="ks-gen-layout">

        <!-- ===== CREATE FORM ===== -->
        <section class="ks-gen-form card">
          <h2><?php esc_html_e('Create or edit a custom block', 'acreline'); ?></h2>
          <form method="post" id="ksBlockForm">
            <?php wp_nonce_field('ks_block_gen', 'ks_block_gen_nonce'); ?>
            <input type="hidden" name="ks_action" value="save">

            <table class="form-table" role="presentation">
              <tr>
                <th><label for="ks_block_id"><?php esc_html_e('Block ID', 'acreline'); ?> <span class="required">*</span></label></th>
                <td>
                  <input type="text" id="ks_block_id" name="ks_block_id" class="regular-text" required
                         pattern="[a-z][a-z0-9_]*"
                         placeholder="my_block"
                         value="<?php echo esc_attr((string) ($_POST['ks_block_id'] ?? '')); ?>">
                  <p class="description"><?php esc_html_e('Lowercase letters, numbers, underscores. Example: testimonial_grid', 'acreline'); ?></p>
                </td>
              </tr>
              <tr>
                <th><label for="ks_block_title"><?php esc_html_e('Block title', 'acreline'); ?> <span class="required">*</span></label></th>
                <td><input type="text" id="ks_block_title" name="ks_block_title" class="regular-text" required
                           placeholder="<?php esc_attr_e('Testimonial Grid', 'acreline'); ?>"
                           value="<?php echo esc_attr((string) ($_POST['ks_block_title'] ?? '')); ?>"></td>
              </tr>
              <tr>
                <th><label for="ks_block_description"><?php esc_html_e('Description', 'acreline'); ?></label></th>
                <td><input type="text" id="ks_block_description" name="ks_block_description" class="large-text"
                           value="<?php echo esc_attr((string) ($_POST['ks_block_description'] ?? '')); ?>"></td>
              </tr>
              <tr>
                <th><label for="ks_block_icon"><?php esc_html_e('Icon', 'acreline'); ?></label></th>
                <td>
                  <select id="ks_block_icon" name="ks_block_icon">
                    <?php foreach ($icons as $slug => $label) { ?>
                      <option value="<?php echo esc_attr($slug); ?>"><?php echo esc_html($label); ?></option>
                    <?php } ?>
                  </select>
                </td>
              </tr>
            </table>

            <h3><?php esc_html_e('Fields', 'acreline'); ?></h3>
            <p class="description"><?php esc_html_e('Add the editable fields for this block. Editors fill these in via the block sidebar when the block is placed on a page.', 'acreline'); ?></p>

            <div id="ksFieldRows">
              <div class="ks-field-row ks-field-row--head">
                <span><?php esc_html_e('Field ID', 'acreline'); ?></span>
                <span><?php esc_html_e('Label', 'acreline'); ?></span>
                <span><?php esc_html_e('Type', 'acreline'); ?></span>
                <span><?php esc_html_e('Default value', 'acreline'); ?></span>
                <span></span>
              </div>
              <div class="ks-field-row ks-field-row--template" style="display:none" aria-hidden="true">
                <input type="text" name="ks_field_name[]" placeholder="field_id" class="regular-text">
                <input type="text" name="ks_field_label[]" placeholder="<?php esc_attr_e('Label', 'acreline'); ?>" class="regular-text">
                <select name="ks_field_type[]">
                  <?php foreach ($fieldTypes as $val => $lbl) { ?>
                    <option value="<?php echo esc_attr($val); ?>"><?php echo esc_html($lbl); ?></option>
                  <?php } ?>
                </select>
                <input type="text" name="ks_field_default[]" placeholder="<?php esc_attr_e('Default…', 'acreline'); ?>" class="regular-text">
                <button type="button" class="button-link ks-remove-field" aria-label="<?php esc_attr_e('Remove field', 'acreline'); ?>">✕</button>
              </div>
            </div>
            <button type="button" class="button" id="ksAddField">+ <?php esc_html_e('Add field', 'acreline'); ?></button>

            <p class="submit">
              <button type="submit" class="button button-primary"><?php esc_html_e('Save block', 'acreline'); ?></button>
            </p>
          </form>
        </section>

        <!-- ===== EXISTING BLOCKS ===== -->
        <section class="ks-gen-list card">
          <h2><?php esc_html_e('Custom blocks', 'acreline'); ?> <span class="count">(<?php echo count($defs); ?>)</span></h2>

          <?php if (empty($defs)) { ?>
            <p class="empty-state"><?php esc_html_e('No custom blocks yet. Create one with the form on the left.', 'acreline'); ?></p>
          <?php } else { ?>
            <div class="ks-block-list">
              <?php foreach ($defs as $def) { ?>
                <div class="ks-block-card">
                  <div class="ks-block-card-meta">
                    <strong><?php echo esc_html($def['title'] ?? $def['id']); ?></strong>
                    <code>acreline/custom · <?php echo esc_html($def['id']); ?></code>
                    <?php if (! empty($def['description'])) { ?>
                      <p><?php echo esc_html($def['description']); ?></p>
                    <?php } ?>
                    <p class="ks-block-fields-summary">
                      <?php
                      $fieldList = array_map(
                          fn ($f) => esc_html($f['label'] ?? $f['name'] ?? ''),
                          (array) ($def['fields'] ?? [])
                      );
                  echo esc_html(
                      count($fieldList)
                          ? sprintf(__('%d field(s): %s', 'acreline'), count($fieldList), implode(', ', $fieldList))
                          : __('No fields defined', 'acreline')
                  );
                  ?>
                    </p>
                  </div>
                  <div class="ks-block-card-actions">
                    <form method="post" style="display:inline">
                      <?php wp_nonce_field('ks_block_gen', 'ks_block_gen_nonce'); ?>
                      <input type="hidden" name="ks_action" value="delete">
                      <input type="hidden" name="ks_delete_id" value="<?php echo esc_attr($def['id']); ?>">
                      <button type="submit" class="button button-small"
                              onclick="return confirm('<?php esc_attr_e('Delete this block definition? Existing block instances on pages will not render.', 'acreline'); ?>')"
                      ><?php esc_html_e('Delete', 'acreline'); ?></button>
                    </form>
                    <button type="button" class="button button-small ks-copy-pattern"
                            data-pattern="<!-- wp:acreline/custom {&quot;blockId&quot;:&quot;<?php echo esc_attr($def['id']); ?>&quot;} /-->"
                    ><?php esc_html_e('Copy block markup', 'acreline'); ?></button>
                  </div>
                </div>
              <?php } ?>
            </div>
          <?php } ?>
        </section>
      </div><!-- /.ks-gen-layout -->
    </div>

    <style>
      .ks-block-generator .ks-gen-layout{display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-top:24px}
      .ks-block-generator .card{background:#fff;border:1px solid #c3c4c7;border-radius:4px;padding:20px}
      .ks-block-generator .ks-field-row{display:grid;grid-template-columns:1fr 1fr 1fr 1fr auto;gap:8px;align-items:center;margin-bottom:8px}
      .ks-block-generator .ks-field-row--head{font-weight:600;font-size:.8rem;color:#646970}
      .ks-block-generator .ks-remove-field{color:#b32d2e;font-size:1rem;line-height:1;border:none;background:none;cursor:pointer}
      .ks-block-generator .ks-block-list{display:flex;flex-direction:column;gap:12px}
      .ks-block-generator .ks-block-card{border:1px solid #ddd;border-radius:4px;padding:12px 16px;display:flex;justify-content:space-between;align-items:flex-start;gap:12px}
      .ks-block-generator .ks-block-card-meta code{display:block;font-size:.75rem;color:#646970;margin:2px 0 6px}
      .ks-block-generator .ks-block-card-meta p{margin:.25rem 0 0;font-size:.8rem;color:#444}
      .ks-block-generator .ks-block-card-actions{display:flex;flex-direction:column;gap:6px;flex-shrink:0}
      .ks-block-generator .ks-block-fields-summary{color:#646970!important}
      .ks-block-generator .count{font-size:.85rem;font-weight:normal;color:#646970}
      @media(max-width:1024px){.ks-block-generator .ks-gen-layout{grid-template-columns:1fr}}
    </style>

    <script>
    (function(){
      var tmpl = document.querySelector('.ks-field-row--template');
      var rows = document.getElementById('ksFieldRows');

      function addRow(name, label, type, def) {
        var clone = tmpl.cloneNode(true);
        clone.removeAttribute('style');
        clone.removeAttribute('aria-hidden');
        clone.classList.remove('ks-field-row--template');
        if (name)  clone.querySelector('[name="ks_field_name[]"]').value   = name;
        if (label) clone.querySelector('[name="ks_field_label[]"]').value  = label;
        if (type)  clone.querySelector('[name="ks_field_type[]"]').value   = type;
        if (def)   clone.querySelector('[name="ks_field_default[]"]').value = def;
        clone.querySelector('.ks-remove-field').addEventListener('click', function(){
          clone.remove();
        });
        rows.appendChild(clone);
      }

      document.getElementById('ksAddField').addEventListener('click', function(){
        addRow('', '', 'text', '');
      });

      // Copy markup button
      document.querySelectorAll('.ks-copy-pattern').forEach(function(btn){
        btn.addEventListener('click', function(){
          navigator.clipboard.writeText(btn.dataset.pattern).then(function(){
            btn.textContent = '<?php echo esc_js(__('Copied!', 'acreline')); ?>';
            setTimeout(function(){ btn.textContent = '<?php echo esc_js(__('Copy block markup', 'acreline')); ?>'; }, 2000);
          });
        });
      });
    })();
    </script>
    <?php
}
