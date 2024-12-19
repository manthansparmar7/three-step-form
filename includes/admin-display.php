<?php
/**
 * Add Admin Menu for Form Submissions
 *
 * Registers a custom admin page to display form submissions.
 */
function step_form_admin_menu() {
    add_menu_page(
        __( 'Form Submissions', 'text-domain' ),
        __( 'Form Submissions', 'text-domain' ),
        'manage_options',
        'form-submissions',
        'step_form_display_admin_table'
    );
}
add_action( 'admin_menu', 'step_form_admin_menu' );

/**
 * Display Form Submissions in an Admin Table
 *
 * Retrieves form submissions from the database and displays them in a paginated table.
 *
 * @global wpdb $wpdb WordPress database object.
 */
function step_form_display_admin_table() {
    global $wpdb;

    // Define the table name.
    $table_name = $wpdb->prefix . 'step_form_data';

    // Get the current page number from the query string, default to 1 if not set.
    $page             = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
    $records_per_page = 10;
    $offset           = ( $page - 1 ) * $records_per_page;

    // Get the total number of records.
    $total_records = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );

    // Fetch the results with pagination, ordered by ID (descending).
    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $table_name ORDER BY id DESC LIMIT %d, %d",
            $offset,
            $records_per_page
        )
    );

    // Display the total records count.
    echo '<h1>' . esc_html__( 'Form Submissions', 'text-domain' ) . '</h1>';
    echo '<p>' . esc_html__( 'Total records:', 'text-domain' ) . ' ' . esc_html( $total_records ) . '</p>';

    // Check if there are any records.
    if ( empty( $results ) ) {
        echo '<p>' . esc_html__( 'No records found.', 'text-domain' ) . '</p>';
        return;
    }

    // Start table.
    echo '<table border="1" cellspacing="0" cellpadding="5" style="width: 100%; border-collapse: collapse;">';
    echo '<tr>
            <th>' . esc_html__( '#', 'text-domain' ) . '</th>
            <th>' . esc_html__( 'Skills', 'text-domain' ) . '</th>
            <th>' . esc_html__( 'Option', 'text-domain' ) . '</th>
            <th>' . esc_html__( 'Name', 'text-domain' ) . '</th>
            <th>' . esc_html__( 'Email', 'text-domain' ) . '</th>
            <th>' . esc_html__( 'Phone', 'text-domain' ) . '</th>
            <th>' . esc_html__( 'Date', 'text-domain' ) . '</th>
          </tr>';

    // Display the table rows.
    $row_number = $offset + 1;
    foreach ( $results as $row ) {
        echo '<tr>
                <td>' . esc_html( $row_number ) . '</td>
                <td>' . ( ! empty( $row->selected_skills ) ? esc_html( $row->selected_skills ) : '-' ) . '</td>
                <td>' . ( ! empty( $row->selected_option ) ? esc_html( $row->selected_option ) : '-' ) . '</td>
                <td>' . ( ! empty( $row->name ) ? esc_html( $row->name ) : '-' ) . '</td>
                <td>' . ( ! empty( $row->email ) ? esc_html( $row->email ) : '-' ) . '</td>
                <td>' . ( ! empty( $row->phone ) ? esc_html( $row->phone ) : '-' ) . '</td>
                <td>' . ( ! empty( $row->date_submitted ) ? esc_html( $row->date_submitted ) : '-' ) . '</td>
              </tr>';
        $row_number++;
    }

    // End table.
    echo '</table>';

    // Pagination logic.
    $total_pages = ceil( $total_records / $records_per_page );
    if ( $total_pages > 1 ) {
        echo '<div class="pagination" style="margin-top: 10px; text-align: center;">';

        // Display previous page link.
        if ( $page > 1 ) {
            echo '<a href="' . esc_url( add_query_arg( 'paged', $page - 1 ) ) . '" style="padding: 5px 10px; border: 1px solid #ddd; margin-right: 5px;">&laquo; ' . esc_html__( 'Previous', 'text-domain' ) . '</a>';
        }

        // Display next page link.
        if ( $page < $total_pages ) {
            echo '<a href="' . esc_url( add_query_arg( 'paged', $page + 1 ) ) . '" style="padding: 5px 10px; border: 1px solid #ddd; margin-left: 5px;">' . esc_html__( 'Next', 'text-domain' ) . ' &raquo;</a>';
        }

        echo '</div>';
    }
}