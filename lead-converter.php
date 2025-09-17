<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Add the lead conversion meta box.
 */
function c360_add_lead_conversion_metabox() {
    add_meta_box(
        'c360_lead_conversion',
        __( 'Lead Status', 'client360-crm' ),
        'c360_render_lead_conversion_metabox',
        'lead',
        'side',
        'high'
    );
}
add_action( 'add_meta_boxes', 'c360_add_lead_conversion_metabox' );

/**
 * Render the lead conversion meta box.
 */
function c360_render_lead_conversion_metabox( $post ) {
    $converted_contact_id = get_post_meta( $post->ID, '_converted_to_contact', true );

    if ( $converted_contact_id && get_post( $converted_contact_id ) ) {
        // Lead is already converted
        echo '<div class="c360-converted-lead">';
        echo '<h3>' . __( 'Lead Converted!', 'client360-crm' ) . '</h3>';
        echo '<p>' . sprintf( 
            __( 'This lead was converted to a contact. %s', 'client360-crm' ), 
            '<a href="' . get_edit_post_link( $converted_contact_id ) . '">' . __( 'View Contact', 'client360-crm' ) . '</a>'
        ) . '</p>';
        echo '</div>';

    } else {
        // Show conversion button
        wp_nonce_field( 'c360_convert_lead_action', 'c360_convert_lead_nonce' );
        echo '<button type="submit" name="c360_convert_lead" id="c360-convert-lead-button" value="1">';
        echo '<span class="dashicons dashicons-yes-alt"></span>' . __( 'Convert to Contact', 'client360-crm' );
        echo '</button>';
    }
}

/**
 * Handle the lead conversion process on post save.
 */
function c360_handle_lead_conversion( $post_id ) {
    // Check if our button was clicked and the nonce is valid.
    if ( ! isset( $_POST['c360_convert_lead'] ) || ! isset( $_POST['c360_convert_lead_nonce'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST['c360_convert_lead_nonce'], 'c360_convert_lead_action' ) ) {
        return;
    }

    // Don't convert on autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check the user's permissions.
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    
    // Make sure we are dealing with a lead
    if ( 'lead' !== get_post_type( $post_id ) ) {
        return;
    }

    // Avoid recursion
    remove_action( 'save_post', 'c360_handle_lead_conversion' );

    $lead = get_post( $post_id );
    
    // Get lead meta data
    $lead_email = get_post_meta( $post_id, '_lead_email', true );
    $lead_phone = get_post_meta( $post_id, '_lead_phone', true );

    // Create a new contact
    $new_contact_args = array(
        'post_title'    => $lead->post_title,
        'post_content'  => $lead->post_content,
        'post_status'   => 'publish',
        'post_type'     => 'contact',
        'post_author'   => $lead->post_author,
    );
    $new_contact_id = wp_insert_post( $new_contact_args );
    
    // If contact was created successfully, copy meta
    if ( $new_contact_id && ! is_wp_error( $new_contact_id ) ) {
        update_post_meta( $new_contact_id, '_contact_email', $lead_email );
        update_post_meta( $new_contact_id, '_contact_phone', $lead_phone );
        
        // Mark the lead as converted by linking it to the new contact
        update_post_meta( $post_id, '_converted_to_contact', $new_contact_id );

        // Add an admin notice
        add_action( 'admin_notices', function() use ($new_contact_id) {
            $link = get_edit_post_link( $new_contact_id );
            echo '<div class="notice notice-success is-dismissible">';
            echo '<p>' . sprintf( __( 'Lead successfully converted! You can now %s.', 'client360-crm' ), '<a href="' . esc_url($link) . '">' . __( 'view the new contact', 'client360-crm' ) . '</a>' ) . '</p>';
            echo '</div>';
        });
    }

    // Re-hook the action
    add_action( 'save_post', 'c360_handle_lead_conversion' );
}
add_action( 'save_post', 'c360_handle_lead_conversion' );
