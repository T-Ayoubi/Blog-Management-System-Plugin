<?php
if (!defined('ABSPATH')) {
    exit;
}

class Blog_Management_System {
    
    public function __construct() {
        // Load dependencies
        require_once BMS_PLUGIN_PATH . 'includes/class-cpt-blog.php';
        require_once BMS_PLUGIN_PATH . 'includes/class-rest-api.php';
        require_once BMS_PLUGIN_PATH . 'includes/class-shortcodes.php';
        require_once BMS_PLUGIN_PATH . 'includes/ajax-handlers.php';

        // Initialize core classes
        new BMS_CPT_Blog();
        new BMS_REST_API();
        new BMS_Shortcodes();

        // Enqueue frontend assets (CSS/JS)
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);

        // AJAX handlers
        add_action('wp_ajax_bms_add_post', [$this, 'handle_add_post']); 
    }

    /**
     * Enqueue CSS & JS assets
     */
    public function enqueue_assets() {

        // Enqueue CSS
        wp_enqueue_style('bms-style',BMS_PLUGIN_URL . 'assets/css/bms-style.css',[],'1.0');

        // Enqueue JS
        wp_enqueue_script('bms-add-post',BMS_PLUGIN_URL . 'assets/js/bms-script.js',['jquery'],'1.0',true);

        // Pass ajax_url to JS
        wp_localize_script('bms-add-post', 'bmsAddPost', array('ajax_url' => admin_url('admin-ajax.php'),));
    }

}
