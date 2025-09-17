<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function c360_render_settings_page() {
    $client360_crm = client360_crm_run();
    $license_manager = $client360_crm->license_manager;
    $status = $license_manager->get_status();
    ?>
    <div class="wrap">
        <h1><?php _e( 'Real Estate CRM Settings', 'real-estate-crm' ); ?></h1>
        
        <div class="postbox">
            <h2 class="hndle"><span><?php _e('License Status', 'client360-crm'); ?></span></h2>
            <div class="inside">
                <?php if ($status === 'valid'): ?>
                    <p style="color: green; font-weight: bold;"><?php _e('License Type: Full (Lifetime)', 'client360-crm'); ?></p>
                <?php elseif ($status === 'demo'): ?>
                     <p style="color: orange; font-weight: bold;"><?php printf(__('License Type: Demo (%d days remaining)', 'client360-crm'), $license_manager->get_demo_days_remaining()); ?></p>
                <?php elseif ($status === 'expired'): ?>
                     <p style="color: red; font-weight: bold;"><?php _e('Your demo license has expired.', 'client360-crm'); ?></p>
                     <a href="https://example.com/upgrade-client360" class="button-primary"><?php _e('Upgrade Now', 'client360-crm'); ?></a>
                <?php else: ?>
                     <p style="color: red; font-weight: bold;"><?php _e('No valid license key entered.', 'client360-crm'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <form action="options.php" method="post">
            <?php
            settings_fields( 'c360_settings_group' );
            do_settings_sections( 'client360_settings_page' );
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function c360_register_settings() {
    register_setting( 'c360_settings_group', 'c360_options', 'c360_options_sanitize' );

    add_settings_section(
        'c360_license_section',
        __( 'License Management', 'client360-crm' ),
        'c360_license_section_callback',
        'client360_settings_page'
    );

    add_settings_field(
        'c360_license_key_field',
        __( 'License Key', 'client360-crm' ),
        'c360_license_key_callback',
        'client360_settings_page',
        'c360_license_section'
    );

    if (client360_crm_run()->is_license_active()) {
        add_settings_section(
            'c360_general_section',
            __( 'General Options', 'client360-crm' ),
            'c360_general_section_callback',
            'client360_settings_page'
        );
        add_settings_field( 'lead_statuses', __( 'Lead Statuses', 'client360-crm' ), 'c360_lead_statuses_callback', 'client360_settings_page', 'c360_general_section' );
        add_settings_field( 'task_statuses', __( 'Task Statuses', 'client360-crm' ), 'c360_task_statuses_callback', 'client360_settings_page', 'c360_general_section' );
        add_settings_field( 'task_priorities', __( 'Task Priorities', 'client360-crm' ), 'c360_task_priorities_callback', 'client360_settings_page', 'c360_general_section' );
        add_settings_field( 'property_statuses', __( 'Property Statuses', 'client360-crm' ), 'c360_property_statuses_callback', 'client360_settings_page', 'c360_general_section' );
        add_settings_field( 'payment_methods', __( 'Payment Methods', 'client360-crm' ), 'c360_payment_methods_callback', 'client360_settings_page', 'c360_general_section' );
    }
}
add_action( 'admin_init', 'c360_register_settings' );

function c360_license_section_callback() {
    echo '<p>' . __('Enter your license key to activate the plugin.', 'client360-crm') . '</p>';
}

function c360_license_key_callback() {
    $license_manager = client360_crm_run()->license_manager;
    $key = $license_manager->get_key();
    // The input name is part of an array `c360_options[license_key]`
    echo '<input type="text" name="c360_options[license_key]" value="' . esc_attr($key) . '" class="regular-text">';
}

function c360_general_section_callback() {
    echo '<p>' . __( 'Manage the dropdown options and taxonomies for the CRM.', 'client360-crm' ) . '</p>';
}

function c360_lead_statuses_callback() {
    $options = get_option('c360_options');
    $value = isset($options['lead_statuses']) ? $options['lead_statuses'] : "New\nContacted\nQualified\nProposal Sent\nNegotiation\nClosed - Won\nClosed - Lost";
    echo '<textarea name="c360_options[lead_statuses]" rows="7" cols="50" class="large-text">' . esc_textarea($value) . '</textarea>';
    echo '<p class="description">' . __('Enter each status on a new line. These will be used for the Lead Status taxonomy.', 'client360-crm') . '</p>';
}

function c360_task_statuses_callback() {
    $options = get_option('c360_options');
    $value = isset($options['task_statuses']) ? $options['task_statuses'] : "To Do\nIn Progress\nCompleted";
    echo '<textarea name="c360_options[task_statuses]" rows="5" cols="50" class="large-text">' . esc_textarea($value) . '</textarea>';
    echo '<p class="description">' . __('Enter each status on a new line. These will be synced to the Task Status taxonomy.', 'client360-crm') . '</p>';
}

function c360_task_priorities_callback() {
    $options = get_option('c360_options');
    $value = isset($options['task_priorities']) ? $options['task_priorities'] : "Low\nNormal\nHigh\nUrgent";
    echo '<textarea name="c360_options[task_priorities]" rows="5" cols="50" class="large-text">' . esc_textarea($value) . '</textarea>';
     echo '<p class="description">' . __('Enter each priority on a new line. These will be synced to the Task Priority taxonomy.', 'client360-crm') . '</p>';
}

function c360_property_statuses_callback() {
    $options = get_option('c360_options');
    $value = isset($options['property_statuses']) ? $options['property_statuses'] : "Available\nUnder Contract\nSold\nRented";
    echo '<textarea name="c360_options[property_statuses]" rows="5" cols="50" class="large-text">' . esc_textarea($value) . '</textarea>';
    echo '<p class="description">' . __('Enter each status on a new line.', 'client360-crm') . '</p>';
}

function c360_payment_methods_callback() {
     $options = get_option('c360_options');
    $value = isset($options['payment_methods']) ? $options['payment_methods'] : "credit_card|Credit Card\nbank_transfer|Bank Transfer\ncheck|Check\ncash|Cash";
    echo '<textarea name="c360_options[payment_methods]" rows="5" cols="50" class="large-text">' . esc_textarea($value) . '</textarea>';
    echo '<p class="description">' . __('Enter each method on a new line in the format: <code>key|Label</code> (e.g., <code>credit_card|Credit Card</code>).', 'client360-crm') . '</p>';
}

/**
 * Sanitize the main options array and handle license activation.
 */
function c360_options_sanitize( $input ) {
    $license_manager = client360_crm_run()->license_manager;
    
    // Handle the license key activation
    if (isset($input['license_key'])) {
        $license_manager->activate_license($input['license_key']);
    }

    // Sanitize the other options
    $sanitized_input = array();
    $other_options = array('lead_statuses', 'task_statuses', 'task_priorities', 'property_statuses', 'payment_methods');
    foreach ($other_options as $key) {
        if (isset($input[$key])) {
            $sanitized_input[$key] = sanitize_textarea_field( $input[$key] );
        }
    }
    
    c360_sync_all_terms( $sanitized_input );

    return $sanitized_input;
}

/**
 * Sync all taxonomy terms from the settings.
 */
function c360_sync_all_terms( $options ) {
    c360_sync_taxonomy_terms($options, 'lead_statuses', 'lead_status');
    c360_sync_taxonomy_terms($options, 'task_statuses', 'task_status');
    c360_sync_taxonomy_terms($options, 'task_priorities', 'task_priority');
}

/**
 * Helper to sync a specific taxonomy.
 */
function c360_sync_taxonomy_terms($options, $option_key, $taxonomy) {
    if (isset($options[$option_key])) {
        $terms_from_settings = preg_split( '/\r\n|\r|\n/', $options[$option_key] );
        $terms_from_settings = array_map('trim', $terms_from_settings);
        $terms_from_settings = array_filter($terms_from_settings); // Remove empty lines

        $existing_terms = get_terms(array('taxonomy' => $taxonomy, 'hide_empty' => false, 'fields' => 'names'));

        // Add new terms
        foreach ($terms_from_settings as $term_name) {
            if (!in_array($term_name, $existing_terms)) {
                wp_insert_term($term_name, $taxonomy);
            }
        }

        // Remove old terms
        foreach ($existing_terms as $term_name) {
            if (!in_array($term_name, $terms_from_settings)) {
                $term = get_term_by('name', $term_name, $taxonomy);
                if ($term) {
                    wp_delete_term($term->term_id, $taxonomy);
                }
            }
        }
    }
}


/**
 * Helper function to get options for dropdowns.
 */
function c360_get_options_for( $key ) {
    $options = get_option('c360_options');
    $default_values = array(
        'property_statuses' => "Available\nUnder Contract\nSold\nRented",
        'payment_methods' => "credit_card|Credit Card\nbank_transfer|Bank Transfer\ncheck|Check\ncash|Cash",
    );

    if ( !isset($options[$key]) || empty($options[$key]) ) {
        $raw_value = $default_values[$key];
    } else {
        $raw_value = $options[$key];
    }
    
    $lines = preg_split( '/\r\n|\r|\n/', $raw_value );
    $lines = array_map('trim', $lines);
    $lines = array_filter($lines);

    if ( $key === 'payment_methods' ) {
        $formatted_options = array();
        foreach ($lines as $line) {
            $parts = explode('|', $line);
            if (count($parts) === 2) {
                $formatted_options[trim($parts[0])] = trim($parts[1]);
            }
        }
        return $formatted_options;
    }

    return $lines;
}

