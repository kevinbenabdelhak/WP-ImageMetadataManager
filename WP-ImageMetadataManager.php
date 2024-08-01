<?php
/*
Plugin Name: WP ImageMetadataManager
Plugin URI: https://kevin-benabdelhak.fr/plugins/wp-imagemetadata-manager/
Description: Automatise l'ajout des attributs alt, titres, légendes et descriptions pour les images lors de leur téléversement dans la bibliothèque multimédia de WordPress.
Version: 1.0
Author: Kevin BENABDELHAK
Author URI: https://kevin-benabdelhak.fr
License: GPLv3

*/

// Sécuriser le plugin 
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Fonction pour ajouter les métadonnées aux images lors de leur téléchargement.
 *
 * @param int $attachment_id L'identifiant de l'attachement.
 */
function wp_imagemetadatamanager_add_metadata( $attachment_id ) {
    // Récupérer les informations de l'attachement.
    $attachment = get_post( $attachment_id );

    // Vérifier que c'est une image
    if ( $attachment->post_type === 'attachment' && strpos( $attachment->post_mime_type, 'image/' ) === 0 ) {
        // Obtenir le nom du fichier sans l'extension.
        $file_name = pathinfo( $attachment->guid, PATHINFO_FILENAME );

        // Remplacer les tirets par des espaces + mettre la 1ere lettre en majuscule.
        $formatted_name = ucfirst( str_replace( '-', ' ', $file_name ) );

        // Mettre à jour l'attribut alt.
        update_post_meta( $attachment_id, '_wp_attachment_image_alt', $formatted_name );

        // Mettre à jour le titre 
        $attachment->post_title = $formatted_name;
        wp_update_post( $attachment );

        // Mettre à jour la légende 
        $attachment->post_excerpt = $formatted_name; // Légende
        wp_update_post( $attachment );

        // Mettre à jour la description
        $attachment->post_content = $formatted_name; // Description
        wp_update_post( $attachment );
    }
}

// hook téléchargement des médias.
add_action( 'add_attachment', 'wp_imagemetadatamanager_add_metadata' );