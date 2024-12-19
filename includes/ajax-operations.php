<?php

// Handle form submission (AJAX)
function step_form_handle_submission() {
    if (isset($_POST['name'], $_POST['email'], $_POST['phone'], $_POST['country_code'])) {
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $country_code = sanitize_text_field($_POST['country_code']);

        // Handle the form data, save to database, or send email
        // For now, we just output the data
        echo "Form submitted successfully: $name, $email, $phone, $country_code";
    }
    wp_die();
}
add_action('wp_ajax_step_form_handle_submission', 'step_form_handle_submission');
add_action('wp_ajax_nopriv_step_form_handle_submission', 'step_form_handle_submission');
