<?php

// function save_step_form_data() {
//     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//         // Get form data
//         $selected_skills = isset($_POST['selected_skills']) ? sanitize_text_field($_POST['selected_skills']) : '';
//         $selected_option = isset($_POST['selected_option']) ? sanitize_text_field($_POST['selected_option']) : '';
//         $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
//         $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
//         $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';

//         // Insert data into the database (example)
//         global $wpdb;
//         $table_name = $wpdb->prefix . 'step_form_data'; // Create a custom table if needed

//         $wpdb->insert(
//             $table_name,
//             array(
//                 'selected_skills' => $selected_skills,
//                 'selected_option' => $selected_option,
//                 'name' => $name,
//                 'email' => $email,
//                 'phone' => $phone,
//                 'date_submitted' => current_time('mysql'),
//             )
//         );
//     }
// }
// add_action('init', 'save_step_form_data');

function handle_step_form_submission() {
    global $wpdb;

    // Extract and sanitize form data from $_POST['form_data']
    $form_data = $_POST['form_data'];

    // Decode and process form fields
    $skills = isset($form_data['selected_skills']) ? stripslashes($form_data['selected_skills']) : '';
    $skills = json_decode($skills, true); // Decode JSON string into an array
    $skills_string = is_array($skills) ? implode(', ', $skills) : ''; // Convert array to a comma-separated string

    $option = isset($form_data['selected_option']) ? sanitize_text_field($form_data['selected_option']) : '';
    $name = isset($form_data['name']) ? sanitize_text_field($form_data['name']) : '';
    $email = isset($form_data['email']) ? sanitize_email($form_data['email']) : '';
    $phone = isset($form_data['phone']) ? sanitize_text_field($form_data['phone']) : '';
    $agree = isset($form_data['terms_agreed']) ? filter_var($form_data['terms_agreed'], FILTER_VALIDATE_BOOLEAN) : false;

    // Validation
    if (empty($name) || empty($email) || empty($phone) || !$agree) {
        wp_send_json_error('Please fill out all required fields and agree to the terms.');
    }

    // Prepare data for insertion
    $data = array(
        'selected_skills' => $skills_string,
        'selected_option' => $option,
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'agree' => $agree ? 1 : 0,
        'date_submitted' => current_time('mysql'),
    );

    // Define the table name
    $table_name = $wpdb->prefix . 'step_form_data';

    // Insert the data into the table
    $inserted = $wpdb->insert($table_name, $data);

    // Check if the data was inserted successfully
    if ($inserted) {
        wp_send_json_success('Form submitted and data inserted successfully!');
    } else {
        wp_send_json_error('There was an error saving the data.');
    }
}

// Hook into AJAX actions
add_action('wp_ajax_step_form_handle_submission', 'handle_step_form_submission');
add_action('wp_ajax_nopriv_step_form_handle_submission', 'handle_step_form_submission');

// Hook the function to handle the AJAX request
add_action('wp_ajax_step_form_handle_submission', 'handle_step_form_submission');
add_action('wp_ajax_nopriv_step_form_handle_submission', 'handle_step_form_submission');