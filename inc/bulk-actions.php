<?php
/**
 * Gestion des actions bulk
 */

if (!defined('ABSPATH')) {
    exit;
}
function imm_add_generate_bulk_action($bulk_actions) {
    $bulk_actions['generate_attributes_file'] = __('Générer à partir du nom de fichier', 'wp-imagemetadata-manager');

    $api_key = get_option('imm_api_key', '');
    if (!empty($api_key)) {
        $bulk_actions['generate_attributes_gpt-4o'] = __('Générer avec l\'IA (GPT-4o)', 'wp-imagemetadata-manager');
        $bulk_actions['generate_attributes_gpt-4o-mini'] = __('Générer avec l\'IA (GPT-4o Mini)', 'wp-imagemetadata-manager');
    }

    return $bulk_actions;
}
add_filter('bulk_actions-upload', 'imm_add_generate_bulk_action');

function imm_handle_generate_bulk_action_ajax() {
    check_ajax_referer('imm_bulk_action_nonce', 'nonce');

    $doaction = sanitize_text_field($_POST['action_type']);
    $post_ids = array_map('intval', $_POST['post_ids']);

    if ($doaction === 'generate_attributes_file') {
        foreach ($post_ids as $post_id) {
            set_image_meta_same_title($post_id);
        }
    } else {
        foreach ($post_ids as $post_id) {
            set_image_meta_ai($post_id);
        }
    }

    wp_send_json_success(array('message' => 'Attributs générés avec succès.'));
}
add_action('wp_ajax_imm_handle_generate_bulk_action', 'imm_handle_generate_bulk_action_ajax');

function imm_bulk_action_admin_notice() {
    if (!empty($_REQUEST['attributes_generated'])) {
        printf(
            '<div id="message" class="updated fade"><p>' .
            _n('Valeurs générées pour %s image.', 'Valeurs générées pour %s images.', $_REQUEST['attributes_generated'], 'wp-imagemetadata-manager') .
            '</p></div>',
            intval($_REQUEST['attributes_generated'])
        );
    }
}
add_action('admin_notices', 'imm_bulk_action_admin_notice');
