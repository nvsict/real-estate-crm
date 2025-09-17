<?php
/**
 * Handles the Bulk Lead Upload functionality.
 *
 * @package Client360_CRM
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Renders the Bulk Upload page.
 */
function c360_render_bulk_upload_page() {
    ?>
    <div class="wrap">
        <h1><?php _e( 'Bulk Upload Leads', 'client360-crm' ); ?></h1>
        <p><?php _e( 'Upload a CSV file to import leads. The file should have the following columns in this order: <strong>Lead Name, Email, Phone Number</strong>.', 'client360-crm' ); ?></p>
        
        <?php
        // Check if a file has been uploaded and process it.
        if ( isset( $_POST['c360_bulk_upload_nonce'] ) && wp_verify_nonce( $_POST['c360_bulk_upload_nonce'], 'c360_bulk_upload_action' ) ) {
            c360_process_bulk_upload();
        }
        ?>

        <form method="post" action="" enctype="multipart/form-data">
            <?php wp_nonce_field( 'c360_bulk_upload_action', 'c360_bulk_upload_nonce' ); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">
                        <label for="csv_file"><?php _e( 'Upload CSV File', 'client360-crm' ); ?></label>
                    </th>
                    <td>
                        <input type="file" id="csv_file" name="csv_file" required accept=".csv" />
                    </td>
                </tr>
            </table>
            <?php submit_button( __( 'Upload Leads', 'client360-crm' ), 'primary' ); ?>
        </form>
    </div>
    <?php
}

/**
 * Processes the uploaded CSV file for bulk lead import.
 */
function c360_process_bulk_upload() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( __( 'You do not have permission to access this page.' ) );
    }

    if ( ! isset( $_FILES['csv_file'] ) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK ) {
        echo '<div class="notice notice-error"><p>' . __( 'File upload failed. Please try again.', 'client360-crm' ) . '</p></div>';
        return;
    }

    $file_path = $_FILES['csv_file']['tmp_name'];
    $file_type = mime_content_type( $file_path );

    if ( $file_type !== 'text/csv' && $file_type !== 'text/plain' ) {
         echo '<div class="notice notice-error"><p>' . __( 'Invalid file type. Please upload a CSV file.', 'client360-crm' ) . '</p></div>';
        return;
    }

    $handle = fopen( $file_path, 'r' );
    if ( $handle === false ) {
        echo '<div class="notice notice-error"><p>' . __( 'Could not open the uploaded file.', 'client360-crm' ) . '</p></div>';
        return;
    }

    $success_count = 0;
    $error_count = 0;
    $row_index = 0;

    // Skip the header row
    fgetcsv( $handle );

    while ( ( $row = fgetcsv( $handle ) ) !== false ) {
        $row_index++;
        
        $lead_name = isset( $row[0] ) ? sanitize_text_field( $row[0] ) : '';
        $lead_email = isset( $row[1] ) ? sanitize_email( $row[1] ) : '';
        $lead_phone = isset( $row[2] ) ? sanitize_text_field( $row[2] ) : '';

        if ( empty( $lead_name ) || ! is_email( $lead_email ) ) {
            $error_count++;
            continue; // Skip rows with missing name or invalid email
        }

        // Create new lead post
        $post_data = array(
            'post_title'  => $lead_name,
            'post_type'   => 'lead',
            'post_status' => 'publish',
            'post_author' => get_current_user_id(),
        );

        $post_id = wp_insert_post( $post_data );

        if ( ! is_wp_error( $post_id ) ) {
            // Save custom fields
            update_post_meta( $post_id, '_lead_email', $lead_email );
            update_post_meta( $post_id, '_lead_phone', $lead_phone );
            $success_count++;
        } else {
            $error_count++;
        }
    }

    fclose( $handle );

    // Display result notice
    echo '<div class="notice notice-success is-dismissible"><p>' . sprintf( 
        __( 'Import complete! %d leads were successfully imported.', 'client360-crm' ), 
        $success_count 
    ) . '</p></div>';

    if ( $error_count > 0 ) {
        echo '<div class="notice notice-warning is-dismissible"><p>' . sprintf( 
            __( '%d rows were skipped due to missing data or errors.', 'client360-crm' ), 
            $error_count 
        ) . '</p></div>';
    }
}

