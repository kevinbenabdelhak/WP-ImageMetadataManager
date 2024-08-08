<?php
/**
 * Configuration des paramètres d'admin
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_menu', 'imm_add_admin_menu');

function imm_add_admin_menu() {
    add_media_page(
        'WP-ImageMetadataManager',
        'WP-ImageMetadataManager',
        'manage_options',
        'imm-options',
        'imm_options_page'
    );
}

function imm_options_page() {
    ?>
    <div class="wrap">
        <h1>WP-ImageMetadataManager</h1>
        <p>Modifiez facilement les valeurs des attributs : <em>alt</em>, <em>caption</em>, <em>title</em>, <em>description</em>. 
        Activez la génération automatique par nom de fichier, <strong>GPT-4</strong> ou encore <strong>GPT-4o</strong>. 
        Générez en bulk et automatiquement pour vos futures images.</p>
        <form method="post" action="options.php">
            <?php
            settings_fields('imm_options_group');
            do_settings_sections('imm-options');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

add_action('admin_init', 'imm_register_settings');

function imm_register_settings() {
    register_setting('imm_options_group', 'imm_api_key');
    register_setting('imm_options_group', 'imm_default_generator');

    add_settings_section(
        'imm_settings_section',
        '',
        null,
        'imm-options'
    );

    add_settings_field(
        'imm_default_generator',
        'Génération automatique',
        'imm_default_generator_field_html',
        'imm-options',
        'imm_settings_section'
    );

    add_settings_field(
        'imm_api_key',
        'Clé OpenAI',
        'imm_api_key_field_html',
        'imm-options',
        'imm_settings_section'
    );
}

function imm_api_key_field_html() {
    $api_key = get_option('imm_api_key', '');
    echo '<input type="text" name="imm_api_key" value="' . esc_attr($api_key) . '" />';
}

function imm_default_generator_field_html() {
    $default_generator = get_option('imm_default_generator', 'current');
    ?>
    <select name="imm_default_generator">
        <option value="current" <?php selected($default_generator, 'current'); ?>>À partir du nom de fichier</option>
        <option value="gpt-4o" <?php selected($default_generator, 'gpt-4o'); ?>>À partir de GPT-4o</option>
        <option value="gpt-4o-mini" <?php selected($default_generator, 'gpt-4o-mini'); ?>>À partir de GPT-4o Mini</option>
    </select>
    <?php
}