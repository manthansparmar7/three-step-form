<?php
// Add admin menu
function step_form_admin_menu() {
    add_menu_page('Form Submissions', 'Form Submissions', 'manage_options', 'form-submissions', 'step_form_display_admin_table');
}
add_action('admin_menu', 'step_form_admin_menu');

// Display form submissions in a table
function step_form_display_admin_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'step_form_data';
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    echo '<h1>Form Submissions</h1>';
    echo '<table border="1" cellspacing="0" cellpadding="5">';
    echo '<tr><th>ID</th><th>Skills</th><th>Option</th><th>Name</th><th>Email</th><th>Phone</th><th>Date</th></tr>';
    foreach ($results as $row) {
        echo "<tr>
            <td>{$row->id}</td>
            <td>{$row->selected_skills}</td>
            <td>{$row->selected_option}</td>
            <td>{$row->name}</td>
            <td>{$row->email}</td>
            <td>{$row->phone}</td>
            <td>{$row->date_submitted}</td>
        </tr>";
    }
    echo '</table>';
}
