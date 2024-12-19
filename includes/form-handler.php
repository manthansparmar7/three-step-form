<?php
/**
 * Handle Step Form Submission
 *
 * This function processes AJAX requests for the step form submission.
 * It validates, sanitizes, and saves the form data into the WordPress database.
 *
 * @global wpdb $wpdb WordPress database object.
 */
function handle_step_form_submission() {
    global $wpdb;

    // Check if form data is provided in the request.
    if ( ! isset( $_POST['form_data'] ) ) {
        wp_send_json_error( __( 'Form data is missing.', 'text-domain' ) );
    }

    // Extract and sanitize form data.
    $form_data = $_POST['form_data'];

    // Sanitize and decode the selected skills.
    $skills        = isset( $form_data['selected_skills'] ) ? stripslashes( $form_data['selected_skills'] ) : '';
    $skills        = json_decode( $skills, true ); // Decode JSON string into an array.
    $skills_string = is_array( $skills ) ? implode( ', ', $skills ) : ''; // Convert array to a comma-separated string.

    // Sanitize other form fields.
    $option = isset( $form_data['selected_option'] ) ? sanitize_text_field( $form_data['selected_option'] ) : '';
    $name   = isset( $form_data['name'] ) ? sanitize_text_field( $form_data['name'] ) : '';
    $email  = isset( $form_data['email'] ) ? sanitize_email( $form_data['email'] ) : '';
    $phone  = isset( $form_data['phone'] ) ? sanitize_text_field( $form_data['phone'] ) : '';
    $agree  = isset( $form_data['terms_agreed'] ) ? filter_var( $form_data['terms_agreed'], FILTER_VALIDATE_BOOLEAN ) : false;

    // Validate required fields.
    if ( empty( $name ) || empty( $email ) || ! $agree ) {
        wp_send_json_error( __( 'Please fill out all required fields and agree to the terms.', 'text-domain' ) );
    }

    // Prepare data for database insertion.
    $data = array(
        'selected_skills'  => $skills_string,
        'selected_option'  => $option,
        'name'             => $name,
        'email'            => $email,
        'phone'            => $phone,
        'agree'            => $agree ? 1 : 0,
        'date_submitted'   => current_time( 'mysql' ),
    );

    // Define the table name.
    $table_name = $wpdb->prefix . 'step_form_data';

    // Insert the data into the table.
    $inserted = $wpdb->insert( $table_name, $data );

    // Check if the data was inserted successfully.
    if ( $inserted ) {
        wp_send_json_success( __( 'Form submitted and data inserted successfully!', 'text-domain' ) );
    } else {
        wp_send_json_error( __( 'There was an error saving the data.', 'text-domain' ) );
    }
}

// Hook into AJAX actions.
add_action( 'wp_ajax_step_form_handle_submission', 'handle_step_form_submission' );
add_action( 'wp_ajax_nopriv_step_form_handle_submission', 'handle_step_form_submission' );