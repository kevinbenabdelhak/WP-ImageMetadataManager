<?php


if (!defined('ABSPATH')) {
    exit;
}



function set_image_meta_same_title($attachment_id)
{
    $attachment = get_post($attachment_id);
    if (empty($attachment->post_title)) {
        $file_path = get_attached_file($attachment_id);
        $file_name = pathinfo($file_path, PATHINFO_FILENAME);
        $attachment_title = sanitize_text_field($file_name);
        wp_update_post(array(
            'ID' => $attachment_id,
            'post_title' => $attachment_title,
        ));
    } else {
        $attachment_title = $attachment->post_title;
    }

    update_post_meta($attachment_id, '_wp_attachment_image_alt', $attachment_title);

    wp_update_post(array(
        'ID' => $attachment_id,
        'post_excerpt' => $attachment_title,
        'post_content' => $attachment_title,
    ));
}
add_action('add_attachment', 'set_image_meta_same_title');