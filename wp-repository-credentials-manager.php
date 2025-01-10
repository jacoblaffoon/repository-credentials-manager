<?php
/*
Plugin Name: Repository Credentials Manager
Description: A plugin to store GitHub credentials securely in WordPress and make them accessible globally.
Version: 1.0.0
Author: Your Name
License: GPL2
*/

// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

// Include the main class file
require_once plugin_dir_path(__FILE__) . 'includes/class-repository-credentials-manager.php';

// Initialize the plugin
function wp_repository_credentials_manager_init() {
    $wp_repository_credentials_manager = new WP_Repository_Credentials_Manager();
    $wp_repository_credentials_manager->init();
}
add_action('plugins_loaded', 'wp_repository_credentials_manager_init');