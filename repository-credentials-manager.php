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
function repository_credentials_manager_init() {
    $repository_credentials_manager = new Repository_Credentials_Manager();
    $repository_credentials_manager->init();
}
add_action('plugins_loaded', 'repository_credentials_manager_init');