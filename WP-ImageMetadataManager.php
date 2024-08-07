<?php
/*
Plugin Name: WP ImageMetadataManager
Plugin URI: https://kevin-benabdelhak.fr/plugins/wp-imagemetadata-manager/
Description: Automatise l'ajout des attributs alt, titres, légendes et descriptions pour les images lors de leur téléversement dans la bibliothèque multimédia de WordPress. Permet également de modifier les valeurs des attributs d'anciennes images en mode bulk (vue liste)
Version: 1.2
Author: Kevin BENABDELHAK
Author URI: https://kevin-benabdelhak.fr
License: GPLv3
*/

if (!defined('ABSPATH')) {
    exit;
}

// Inclure les fichiers nécessaires
require_once plugin_dir_path(__FILE__) . 'image-meta-functions.php';
require_once plugin_dir_path(__FILE__) . 'bulk-actions.php';