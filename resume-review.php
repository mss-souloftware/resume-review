<?php
/*
Plugin Name: Resume Review
Plugin URI: https://9to5web.com.au/
Description: A custom WordPress plugin for resume review and email functionality.
Version: 1.0.0
Author: 9to5 Web
Author URI: https://9to5web.com.au/
*/

// Plugin activation and deactivation hooks
register_activation_hook(__FILE__, 'resume_review_activate');
register_deactivation_hook(__FILE__, 'resume_review_deactivate');


// Include the front-end functionality
require_once plugin_dir_path(__FILE__) . 'includes/frontend.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin.php';

// Activation hook callback
function resume_review_activate() {
    // Perform any activation tasks if needed
}

// Deactivation hook callback
function resume_review_deactivate() {
    // Perform any deactivation tasks if needed
}
