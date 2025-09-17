<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Register all custom post types for the CRM.
 */
function c360_register_post_types() {
    // Lead Post Type - Menu position starts higher to ensure it appears after custom pages.
    register_post_type( 'lead', array(
        'labels' => array( 'name' => __( 'Leads', 'client360-crm' ), 'singular_name' => __( 'Lead', 'client360-crm' ) ),
        'public' => false, 'show_ui' => true, 'show_in_menu' => false,
        'capability_type' => array('lead', 'leads'), 'map_meta_cap' => true,
        'has_archive' => false, 'hierarchical' => false, 'menu_position' => 20,
        'supports' => array( 'author' ), 'menu_icon' => 'dashicons-businessperson',
    ));

     // Contact Post Type
    register_post_type( 'contact', array(
        'labels' => array( 'name' => __( 'Contacts', 'client360-crm' ), 'singular_name' => __( 'Contact', 'client360-crm' ) ),
        'public' => false, 'show_ui' => true, 'show_in_menu' => false,
        'capability_type' => array('contact', 'contacts'), 'map_meta_cap' => true,
        'has_archive' => false, 'hierarchical' => false, 'menu_position' => 21,
        'supports' => array( 'author' ), 'taxonomies'  => array( 'contact_tag' ), 'menu_icon' => 'dashicons-id-alt',
    ));
    
    // Campaign Post Type
    register_post_type( 'campaign', array(
        'labels' => array( 'name' => __( 'Campaigns', 'client360-crm' ), 'singular_name' => __( 'Campaign', 'client360-crm' ) ),
        'public' => false, 'show_ui' => true, 'show_in_menu' => false,
        'capability_type' => array('campaign', 'campaigns'), 'map_meta_cap' => true,
        'has_archive' => false, 'hierarchical' => false, 'menu_position' => 22,
        'supports' => array( 'title', 'editor', 'author' ), 'menu_icon' => 'dashicons-megaphone',
    ));
    
    // Property Post Type
    register_post_type( 'property', array(
        'labels' => array( 'name' => __( 'Properties', 'client360-crm' ), 'singular_name' => __( 'Property', 'client360-crm' ) ),
        'public' => false, 'show_ui' => true, 'show_in_menu' => false,
        'capability_type' => array('property', 'properties'), 'map_meta_cap' => true,
        'has_archive' => false, 'hierarchical' => false, 'menu_position' => 23,
        'supports' => array( 'title', 'author' ), 'menu_icon' => 'dashicons-admin-home',
    ));

    // Task Post Type
    register_post_type( 'task', array(
        'labels' => array( 'name' => __( 'Tasks', 'client360-crm' ), 'singular_name' => __( 'Task', 'client360-crm' ) ),
        'public' => false, 'show_ui' => true, 'show_in_menu' => false,
        'capability_type' => array('task', 'tasks'), 'map_meta_cap' => true,
        'has_archive' => false, 'hierarchical' => false, 'menu_position' => 24,
        'supports' => array( 'title', 'editor', 'author' ), 'taxonomies' => array('task_status', 'task_priority'), 'menu_icon' => 'dashicons-list-view',
    ));
    
    $log_position = 25;
    $log_types = array(
        'meeting_log' => array( 'name' => 'Meeting Logs', 'singular' => 'Meeting Log', 'icon' => 'dashicons-groups' ),
        'call_log' => array( 'name' => 'Call Logs', 'singular' => 'Call Log', 'icon' => 'dashicons-phone' ),
        'email_log' => array( 'name' => 'Email Logs', 'singular' => 'Email Log', 'icon' => 'dashicons-email-alt' ),
    );

    foreach( $log_types as $slug => $labels ) {
        register_post_type( $slug, array(
            'labels' => array( 'name' => __( $labels['name'], 'client360-crm' ), 'singular_name' => __( $labels['singular'], 'client360-crm' ) ),
            'public' => false, 'show_ui' => true, 'show_in_menu' => false,
            'capability_type' => array($slug, $slug.'s'), 'map_meta_cap' => true,
            'menu_position' => $log_position++, 'supports' => array( 'title', 'editor', 'author' ), 'menu_icon' => $labels['icon'],
        ));
    }

    register_post_type( 'payment', array(
        'labels' => array( 'name' => __( 'Payments', 'client360-crm' ), 'singular_name' => __( 'Payment', 'client360-crm' ) ),
        'public' => false, 'show_ui' => true, 'show_in_menu' => false,
        'capability_type' => array('payment', 'payments'), 'map_meta_cap' => true,
        'menu_position' => $log_position++, 'supports' => array( 'title', 'author' ), 'menu_icon' => 'dashicons-money-alt',
    ));
    
    register_post_type( 'document', array(
        'labels' => array( 'name' => __( 'Documents', 'client360-crm' ), 'singular_name' => __( 'Document', 'client360-crm' ) ),
        'public' => false, 'show_ui' => false, // Hide the default menu item
        'show_in_menu' => 'client360_dashboard',
        'capability_type' => array('document', 'documents'), 'map_meta_cap' => true,
        'supports' => array( 'title', 'author' ),
    ));
}
add_action( 'init', 'c360_register_post_types' );


/**
 * Register taxonomies for the CRM.
 */
function c360_register_taxonomies() {
    // Task Status
    register_taxonomy( 'task_status', 'task', array(
        'labels' => array( 'name' => __( 'Task Statuses', 'client360-crm' ), 'singular_name' => __( 'Task Status', 'client360-crm' ) ),
        'public' => false, 'show_ui' => true, 'show_admin_column' => true, 'hierarchical' => true, 'show_in_menu' => false,
    ));

    // Task Priority
    register_taxonomy( 'task_priority', 'task', array(
        'labels' => array( 'name' => __( 'Task Priorities', 'client360-crm' ), 'singular_name' => __( 'Task Priority', 'client360-crm' ) ),
        'public' => false, 'show_ui' => true, 'show_admin_column' => true, 'hierarchical' => true, 'show_in_menu' => false,
    ));

    // Lead Status
    register_taxonomy( 'lead_status', 'lead', array(
        'labels' => array( 'name' => __( 'Lead Statuses', 'client360-crm' ), 'singular_name' => __( 'Lead Status', 'client360-crm' ) ),
        'public' => false, 'show_ui' => true, 'show_admin_column' => true, 'hierarchical' => true,
    ));

    // Contact Tags
    register_taxonomy( 'contact_tag', 'contact', array(
        'labels' => array( 'name' => __( 'Contact Tags', 'client360-crm' ), 'singular_name' => __( 'Contact Tag', 'client360-crm' ) ),
        'public' => false, 'show_ui' => true, 'show_admin_column' => true, 'hierarchical' => false,
    ));

    // Document Folders
    register_taxonomy( 'document_folder', 'document', array(
        'labels' => array( 'name' => __( 'Folders', 'client360-crm' ), 'singular_name' => __( 'Folder', 'client360-crm' ) ),
        'public' => false, 'show_ui' => true, 'show_admin_column' => true, 'hierarchical' => true,
        'show_in_menu' => true,
    ));
}
add_action( 'init', 'c360_register_taxonomies' );

// After this:
add_action( 'init', 'c360_register_taxonomies' );

// Remove or comment out any previous admin_init / save_post fixes
// Then place the new code here:
add_action('admin_init', function() {
    if (!current_user_can('manage_options')) return;

    global $wpdb;

    $c360_post_types = [
        'lead','contact','campaign','property',
        'task','meeting_log','call_log','email_log',
        'payment','document'
    ];

    // Prepare placeholders for query
    $placeholders = implode(',', array_fill(0, count($c360_post_types), '%s'));

    // Select posts with invalid dates
    $bad_posts = $wpdb->get_results($wpdb->prepare("
        SELECT ID, post_date
        FROM {$wpdb->posts}
        WHERE post_type IN ($placeholders)
          AND (post_date IS NULL
               OR post_date = '0000-00-00 00:00:00'
               OR post_date < '1970-01-01 00:00:01'
               OR post_date > '2038-01-19 03:14:07'
               OR post_date NOT REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$')
    ", $c360_post_types));

    foreach ($bad_posts as $post) {
        $wpdb->update(
            $wpdb->posts,
            [
                'post_date'         => current_time('mysql'),
                'post_date_gmt'     => current_time('mysql', 1),
                'post_modified'     => current_time('mysql'),
                'post_modified_gmt' => current_time('mysql', 1),
            ],
            ['ID' => $post->ID]
        );
    }

    if ($bad_posts) {
        add_action('admin_notices', function() use ($bad_posts) {
            echo '<div class="notice notice-success is-dismissible">';
            echo '<p>✅ Fixed ' . count($bad_posts) . ' posts with invalid dates.</p>';
            echo '</div>';
        });
    }
});
