<?php
// Ensure direct access is prevented
if (!defined('ABSPATH')) {
    exit;
}

// Drop the table when the plugin is uninstalled
function step_form_uninstall() {
    global $wpdb;

    // Define the table name
    $table_name = $wpdb->prefix . 'step_form_submissions';

    // Check if the table exists and drop it
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}

// Call the function on plugin uninstall
step_form_uninstall();
