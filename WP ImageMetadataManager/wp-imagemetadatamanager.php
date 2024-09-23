<?php
/*
Plugin Name: WP ImageMetadataManager
Plugin URI: https://kevin-benabdelhak.fr/plugins/wp-imagemetadata-manager/
Description: Automatise l'ajout des attributs alt, titres, légendes et descriptions pour les images lors de leur téléversement dans la bibliothèque multimédia de WordPress. Permet également de modifier les valeurs des attributs d'anciennes images en mode bulk (vue liste)
Version: 1.4
Author: Kevin BENABDELHAK
Author URI: https://kevin-benabdelhak.fr
License: GPLv3
*/

if (!defined('ABSPATH')) {
    exit;
}
require_once plugin_dir_path(__FILE__) . 'inc/image-meta-functions.php';
require_once plugin_dir_path(__FILE__) . 'inc/ai-functions.php';
require_once plugin_dir_path(__FILE__) . 'inc/admin-settings.php';
require_once plugin_dir_path(__FILE__) . 'inc/enqueue-scripts.php';
require_once plugin_dir_path(__FILE__) . 'inc/bulk-actions.php';
