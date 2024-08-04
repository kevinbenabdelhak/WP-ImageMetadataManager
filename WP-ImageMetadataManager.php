<?php
/*
Plugin Name: WP ImageMetadataManager
Plugin URI: https://kevin-benabdelhak.fr/plugins/wp-imagemetadata-manager/
Description: Automatise l'ajout des attributs alt, titres, légendes et descriptions pour les images lors de leur téléversement dans la bibliothèque multimédia de WordPress.
Version: 1.1
Author: Kevin BENABDELHAK
Author URI: https://kevin-benabdelhak.fr
License: GPLv3
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * @param int $attachment_id L'identifiant de l'attachement.
 */

function set_image_meta_same_title( $attachment_id ) {
    $attachment_title = get_the_title( $attachment_id );
    update_post_meta( $attachment_id, '_wp_attachment_image_alt', $attachment_title );
    wp_update_post( array(
        'ID' => $attachment_id,
        'post_excerpt' => $attachment_title,
        'post_content' => $attachment_title,
    ) );
}
add_action( 'add_attachment', 'set_image_meta_same_title' );
