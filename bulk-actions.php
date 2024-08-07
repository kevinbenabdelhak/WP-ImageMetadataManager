<?php

if (!defined('ABSPATH')) {
    exit;
}

function imm_add_generate_bulk_action($bulk_actions)
{
    $bulk_actions['generate_attributes'] = __('Remplir les informations (à partir du nom de fichier)', 'wp-imagemetadata-manager');
    return $bulk_actions;
}
add_filter('bulk_actions-upload', 'imm_add_generate_bulk_action');

function imm_handle_generate_bulk_action($redirect_to, $doaction, $post_ids)
{
    if ($doaction !== 'generate_attributes') {
        return $redirect_to;
    }

    foreach ($post_ids as $post_id) {
        set_image_meta_same_title($post_id);
    }

    $redirect_to = add_query_arg('attributes_generated', count($post_ids), $redirect_to);
    return $redirect_to;
}
add_filter('handle_bulk_actions-upload', 'imm_handle_generate_bulk_action', 10, 3);

function imm_bulk_action_admin_notice()
{
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