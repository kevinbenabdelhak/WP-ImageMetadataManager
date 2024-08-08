<?php
/**
 * Fonctions de génération de métadonnées avec l'IA
 */

if (!defined('ABSPATH')) {
    exit;
}

function set_image_meta_ai($attachment_id) {
    $result = generate_image_meta_with_gpt4o($attachment_id);
    if ($result !== false) {
        $cleaned_title = clean_text($result['title']);
        $cleaned_alt = clean_text($result['alt']);
        $cleaned_description = clean_text($result['description']);

        wp_update_post(array(
            'ID' => $attachment_id,
            'post_title' => $cleaned_title,
            'post_excerpt' => $cleaned_description,
            'post_content' => $cleaned_description,
        ));
        update_post_meta($attachment_id, '_wp_attachment_image_alt', $cleaned_alt);
    } else {
        set_image_meta_same_title($attachment_id);
    }
}

function clean_text($text) {
    if (is_array($text)) {
        $text = implode(' ', $text);
    }

    // Allows most characters, including apostrophes and accented letters
    $cleaned_text = preg_replace('/[^\p{L}0-9\s,\'\"-]/u', '', $text);
    return $cleaned_text;
}

function generate_image_meta_with_gpt4o($attachment_id) {
    $api_key = get_option('imm_api_key', '');
    if (empty($api_key)) {
        return false;
    }

    // Get the thumbnail URL
    $thumbnail_url_array = wp_get_attachment_image_src($attachment_id, 'thumbnail');
    // Check that we got a valid thumbnail URL
    if (!$thumbnail_url_array || !isset($thumbnail_url_array[0])) {
        return false;
    }
    
    $thumbnail_url = $thumbnail_url_array[0];
    $image_data = file_get_contents($thumbnail_url);
    $base64 = 'data:image/png;base64,' . base64_encode($image_data);

    $prompt = "Génère les informations suivantes en français au format JSON sans introduction ni conclusion (10 à 150 caractères max par attribut) sous cette forme : {\"title\": \"le titre de l'image\", \"alt\": \"le texte alternatif (alt) de l'image\", \"description\": \"la description de l'image\"}. Respecte la forme indiqué et le nom des éléments du json.";
    
    $default_generator = get_option('imm_default_generator', 'current');
    $model = 'gpt-4o';
    if ($default_generator === 'gpt-4o-mini') {
        $model = 'gpt-4o-mini';
    }

    $query = array(
        'model' => $model,
        'messages' => array(
            array(
                'role' => 'user',
                'content' => array(
                    array(
                        'type' => 'image_url',
                        'image_url' => array(
                            'url' => $base64
                        )
                    ),
                    array(
                        'type' => 'text',
                        'text' => $prompt
                    )
                )
            )
        ),
        'temperature' => 1,
        'max_tokens' => 2000,
        'top_p' => 1,
        'frequency_penalty' => 0,
        'presence_penalty' => 0,
        'response_format' => array('type' => 'json_object')
    );

    $response = wp_remote_post('https://api.openai.com/v1/chat/completions', array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json'
        ),
        'body' => json_encode($query)
    ));

    if (is_wp_error($response)) {
        return false;
    }
    
    $body = wp_remote_retrieve_body($response);
    $result = json_decode($body, true);

    if (isset($result['choices'][0]['message']['content'])) {
        $meta_data = json_decode($result['choices'][0]['message']['content'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        return array(
            'title' => sanitize_text_field($meta_data['title'] ?? ''),
            'alt' => sanitize_text_field($meta_data['alt'] ?? ''),
            'description' => sanitize_text_field($meta_data['description'] ?? ''),
        );
    } else {
        return false;
    }
}

// Attendre que les métadonnées soient générées
add_action('wp_generate_attachment_metadata', 'imm_set_meta_on_upload_after_metadata', 30, 2);

function imm_set_meta_on_upload_after_metadata($metadata, $attachment_id) {
    $default_generator = get_option('imm_default_generator', 'current');
    if ($default_generator === 'gpt-4o') {
        set_image_meta_ai($attachment_id);
    } else {
        set_image_meta_same_title($attachment_id);
    }
    return $metadata;
}