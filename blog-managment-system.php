<?php
/**
 * Plugin Name: Blog Management System
 * Description: A blog management system using Custom Post Types, REST API, and Shortcodes.
 * Version: 1.0
 * Author: Taha Ayoubi
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin paths
define('BMS_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('BMS_PLUGIN_URL', plugin_dir_url(__FILE__));


// Load Core Classes
require_once BMS_PLUGIN_PATH . 'includes/class-blog-management.php';

// Initialize the Plugin
function bms_init_plugin() {
    new Blog_Management_System();
}
add_action('plugins_loaded', 'bms_init_plugin');


// Template Loader (Single, Archive, Search)
add_filter('template_include', 'bms_load_custom_templates');
function bms_load_custom_templates($template) {

    if (is_singular('bms_blog')) {
        $single_template = BMS_PLUGIN_PATH . 'templates/single-bms_blog.php';
        if (file_exists($single_template)) {
            return $single_template;
        }
    }

    if (is_post_type_archive('bms_blog')) {
        $archive_template = BMS_PLUGIN_PATH . 'templates/archive-bms_blog.php';
        if (file_exists($archive_template)) {
            return $archive_template;
        }
    }

    if (is_page('search-bms')) {
        $search_template = BMS_PLUGIN_PATH . 'templates/search-bms.php';
        if (file_exists($search_template)) {
            return $search_template;
        }
    }

    return $template;
}
