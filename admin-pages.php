<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Add admin menus for the CRM in the correct order, handling license status.
 */
function c360_add_admin_menu() {
    $license_is_active = client360_crm_run()->is_license_active();

    // Create the main top-level menu page.
    add_menu_page(
        __( 'Real Estate CRM', 'real-estate-crm' ),
        __( 'Real Estate CRM', 'real-estate-crm' ),
        $license_is_active ? 'read' : 'manage_options', // Employees can see when active
        $license_is_active ? 'client360_dashboard' : 'client360_settings',
        $license_is_active ? 'c360_render_dashboard_page' : 'c360_render_settings_page',
        'dashicons-businessperson',
        25
    );

    // If the license is active, build the submenu.
    if ( $license_is_active ) {
        // Dashboard (Employees + Admins)
        add_submenu_page(
            'client360_dashboard',
            __( 'Dashboard', 'client360-crm' ),
            __( 'Dashboard', 'client360-crm' ),
            'read',
            'client360_dashboard'
        );

        // Leads (Employees + Admins)
        add_submenu_page(
            'client360_dashboard',
            __( 'Leads', 'client360-crm' ),
            __( 'Leads', 'client360-crm' ),
            'edit_leads',
            'edit.php?post_type=lead'
        );

        // Contacts (Employees + Admins)
        add_submenu_page(
            'client360_dashboard',
            __( 'Contacts', 'client360-crm' ),
            __( 'Contacts', 'client360-crm' ),
            'edit_contacts',
            'edit.php?post_type=contact'
        );

        // === Admin-only menus ===
        add_submenu_page( 'client360_dashboard', __( 'Campaigns', 'client360-crm' ), __( 'Campaigns', 'client360-crm' ), 'edit_posts', 'edit.php?post_type=campaign' );
        add_submenu_page( 'client360_dashboard', __( 'Properties', 'client360-crm' ), __( 'Properties', 'client360-crm' ), 'edit_posts', 'edit.php?post_type=property' );
        add_submenu_page( 'client360_dashboard', __( 'Tasks', 'client360-crm' ), __( 'Tasks', 'client360-crm' ), 'edit_posts', 'edit.php?post_type=task' );
        add_submenu_page( 'client360_dashboard', __( 'Meeting Logs', 'client360-crm' ), __( 'Meeting Logs', 'client360-crm' ), 'edit_posts', 'edit.php?post_type=meeting_log' );
        add_submenu_page( 'client360_dashboard', __( 'Call Logs', 'client360-crm' ), __( 'Call Logs', 'client360-crm' ), 'edit_posts', 'edit.php?post_type=call_log' );
        add_submenu_page( 'client360_dashboard', __( 'Email Logs', 'client360-crm' ), __( 'Email Logs', 'client360-crm' ), 'edit_posts', 'edit.php?post_type=email_log' );
        add_submenu_page( 'client360_dashboard', __( 'Payments', 'client360-crm' ), __( 'Payments', 'client360-crm' ), 'edit_posts', 'edit.php?post_type=payment' );
        add_submenu_page( 'client360_dashboard', __( 'Documents', 'client360-crm' ), __( 'Documents', 'client360-crm' ), 'edit_posts', 'client360_documents', 'c360_render_documents_page' );
        add_submenu_page( 'client360_dashboard', __( 'Employee Management', 'client360-crm' ), __( 'Employees', 'client360-crm' ), 'manage_options', 'client360_employee_management', 'c360_render_employee_management_page' );
        add_submenu_page( 'client360_dashboard', __( 'Calendar', 'client360-crm' ), __( 'Calendar', 'client360-crm' ), 'edit_posts', 'client360_calendar', 'c360_render_calendar_page' );
        add_submenu_page( 'client360_dashboard', __( 'Bulk Upload Leads', 'client360-crm' ), __( 'Bulk Upload', 'client360-crm' ), 'manage_options', 'client360_bulk_upload', 'c360_render_bulk_upload_page' );
        add_submenu_page( 'client360_dashboard', __( 'Settings', 'client360-crm' ), __( 'Settings', 'client360-crm' ), 'manage_options', 'client360_settings', 'c360_render_settings_page' );

        // Hidden pages for viewing single items (Admin-only)
        add_submenu_page( null, __( 'View Lead', 'client360-crm' ), __( 'View Lead', 'client360-crm' ), 'edit_posts', 'client360_view_lead', 'c360_render_view_lead_page' );
        add_submenu_page( null, __( 'View Contact', 'client360-crm' ), __( 'View Contact', 'client360-crm' ), 'edit_posts', 'client360_view_contact', 'c360_render_view_contact_page' );
        add_submenu_page( null, __( 'View Property', 'client360-crm' ), __( 'View Property', 'client360-crm' ), 'edit_posts', 'client360_view_property', 'c360_render_view_property_page' );
        add_submenu_page( null, __( 'View Meeting', 'client360-crm' ), __( 'View Meeting', 'client360-crm' ), 'edit_posts', 'client360_view_meeting', 'c360_render_view_meeting_page' );
        add_submenu_page( null, __( 'View Call Log', 'client360-crm' ), __( 'View Call Log', 'client360-crm' ), 'edit_posts', 'client360_view_call_log', 'c360_render_view_call_log_page' );
        add_submenu_page( null, __( 'View Email Log', 'client360-crm' ), __( 'View Email Log', 'client360-crm' ), 'edit_posts', 'client360_view_email_log', 'c360_render_view_email_log_page' );
        add_submenu_page( null, __( 'View Task', 'client360-crm' ), __( 'View Task', 'client360-crm' ), 'edit_posts', 'client360_view_task', 'c360_render_view_task_page' );
    } else {
        // If license is NOT active, only show the settings page.
        add_submenu_page(
            'client360_settings',
            __( 'Settings', 'client360-crm' ),
            __( 'Settings', 'client360-crm' ),
            'manage_options',
            'client360_settings',
            'c360_render_settings_page'
        );
    }
}
add_action( 'admin_menu', 'c360_add_admin_menu' );


/**
 * Render the main dashboard page.
 */
function c360_render_dashboard_page() {
    $data = c360_get_dashboard_data();
    ?>
    <div class="wrap c360-dashboard-wrap">
        <h1><?php _e( 'Dashboard', 'client360-crm' ); ?></h1>

        <div class="c360-kpi-cards">
            <div class="c360-kpi-card card-leads">
                <h2><?php echo esc_html($data['lead_count']); ?></h2>
                <p>Total Leads</p>
            </div>
            <div class="c360-kpi-card card-contacts">
                <h2><?php echo esc_html($data['contact_count']); ?></h2>
                <p>Total Contacts</p>
            </div>
            <div class="c360-kpi-card card-properties">
                <h2><?php echo esc_html($data['property_count']); ?></h2>
                <p>Total Properties</p>
            </div>
            <div class="c360-kpi-card card-tasks">
                <h2><?php echo esc_html($data['task_count']); ?></h2>
                <p>Total Tasks</p>
            </div>
        </div>

        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="metabox-holder columns-2">
                <div id="postbox-container-1" class="postbox-container">
                    <div class="postbox">
                        <h2 class="hndle"><span><?php _e( 'Leads vs DPS (Last 10 Days)', 'client360-crm' ); ?></span></h2>
                        <div class="inside">
                            <canvas id="dailyLeadsChart"></canvas>
                        </div>
                    </div>
                </div>
                <div id="postbox-container-2" class="postbox-container">
                    <div class="postbox">
                        <h2 class="hndle"><span><?php _e( 'Report', 'client360-crm' ); ?></span></h2>
                        <div class="inside">
                            <canvas id="leadReportChart"></canvas>
                            <a id="downloadLeadReport" class="button" style="margin-top:10px;">Download PNG</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <?php
}
/**
 * Render the Calendar page.
 */
function c360_render_calendar_page() {
    ?>
    <style>
        /* Styles for the custom "Add New" button on the calendar page */
        .c360-calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .c360-add-new-dropdown {
            position: relative;
            display: inline-block;
        }
        .c360-add-new-dropdown .page-title-action {
            padding-right: 28px; /* Space for the dropdown arrow */
        }
        .c360-add-new-dropdown .page-title-action::after {
            content: "\f140"; /* WordPress dashicon for dropdown arrow */
            font-family: dashicons;
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
        }
        .c360-dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #fff;
            min-width: 160px;
            border: 1px solid #ddd;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.1);
            z-index: 100;
            border-radius: 4px;
            padding: 5px 0;
        }
        .c360-dropdown-content a {
            color: #2c3338;
            padding: 8px 12px;
            text-decoration: none;
            display: block;
            font-size: 13px;
        }
        .c360-dropdown-content a:hover {
            background-color: #f0f0f1;
        }
    </style>

    <div class="wrap">
        <div class="c360-calendar-header">
            <h1><?php _e( 'Calendar', 'client360-crm' ); ?></h1>
            <div class="c360-add-new-dropdown">
                <button class="page-title-action"><?php _e('Add New', 'client360-crm'); ?></button>
                <div class="c360-dropdown-content">
                    <a href="<?php echo esc_url(admin_url('post-new.php?post_type=task')); ?>"><?php _e('Add Task', 'client360-crm'); ?></a>
                    <a href="<?php echo esc_url(admin_url('post-new.php?post_type=meeting_log')); ?>"><?php _e('Add Meeting', 'client360-crm'); ?></a>
                    <a href="<?php echo esc_url(admin_url('post-new.php?post_type=call_log')); ?>"><?php _e('Add Call', 'client360-crm'); ?></a>
                </div>
            </div>
        </div>

        <div id="c360-calendar"></div>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Toggle for the "Add New" dropdown button
            $('.c360-add-new-dropdown .page-title-action').on('click', function(event) {
                event.stopPropagation();
                $('.c360-dropdown-content').toggle();
            });

            // Hide dropdown if clicking anywhere else
            $(document).on('click', function() {
                $('.c360-dropdown-content').hide();
            });
        });
    </script>
    <?php
}

/**
 * Render the Employee Management page.
 */
function c360_render_employee_management_page() {
    $action = isset($_GET['action']) ? $_GET['action'] : 'list';
    ?>
    <div class="wrap">
        <?php if ($action === 'add'): ?>
            <h1 class="wp-heading-inline"><?php _e('Add New Employee', 'client360-crm'); ?></h1>
            <a href="<?php echo admin_url('admin.php?page=client360_employee_management'); ?>" class="page-title-action"><?php _e('Back to Employee List', 'client360-crm'); ?></a>
            <?php c360_render_add_employee_form(); ?>
        <?php else: ?>
            <h1 class="wp-heading-inline"><?php _e('Employee Management', 'client360-crm'); ?></h1>
            <a href="<?php echo admin_url('admin.php?page=client360_employee_management&action=add'); ?>" class="page-title-action"><?php _e('Add Employee', 'client360-crm'); ?></a>
            <?php
                $employee_list_table = new C360_Employees_List_Table();
                $employee_list_table->prepare_items();
                $employee_list_table->search_box('Search Employees', 'employee-search-input');
                $employee_list_table->display();
            ?>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Renders the form for adding a new employee.
 */
function c360_render_add_employee_form() {
    // This function handles the form display and submission logic for adding a new employee.
    if ( isset( $_POST['c360_add_employee_nonce'] ) && wp_verify_nonce( $_POST['c360_add_employee_nonce'], 'c360_add_employee_action' ) ) {
        if ( ! current_user_can('manage_options') ) { wp_die( __('You do not have permission to perform this action.') ); }
        $first_name = isset( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '';
        $last_name = isset( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '';
        $email    = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
        $password = isset( $_POST['password'] ) ? $_POST['password'] : '';
        $username = $email;
        $error = array();
        if ( empty( $first_name ) ) { $error[] = __( 'First Name is required.', 'client360-crm' ); }
        if ( empty( $last_name ) ) { $error[] = __( 'Last Name is required.', 'client360-crm' ); }
        if ( ! is_email( $email ) ) { $error[] = __( 'A valid email is required.', 'client360-crm' ); }
        if ( email_exists( $email ) ) { $error[] = __( 'Email already exists.', 'client360-crm' ); }
        if ( username_exists( $username ) ) { $error[] = __( 'A user with this email already exists.', 'client360-crm' ); }
        if ( empty( $password ) ) { $error[] = __( 'Password is required.', 'client360-crm' ); }
        if ( empty( $error ) ) {
            $user_id = wp_create_user( $username, $password, $email );
            if ( ! is_wp_error( $user_id ) ) {
                wp_update_user( array( 'ID' => $user_id, 'first_name' => $first_name, 'last_name' => $last_name, 'display_name' => $first_name . ' ' . $last_name, 'role' => 'c360_employee' ) );
                update_user_meta($user_id, 'c360_status', 'active');
                echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Employee created successfully! You can now see them in the list.', 'client360-crm' ) . '</p></div>';
            } else {
                 echo '<div class="notice notice-error is-dismissible"><p>' . $user_id->get_error_message() . '</p></div>';
            }
        } else {
             echo '<div class="notice notice-error is-dismissible"><p>' . implode( '<br>', $error ) . '</p></div>';
        }
    }
    ?>
    <form method="post" action="">
        <?php wp_nonce_field( 'c360_add_employee_action', 'c360_add_employee_nonce' ); ?>
        <table class="form-table">
             <tr valign="top">
                <th scope="row"><label for="first_name"><?php _e( 'First Name', 'client360-crm' ); ?></label></th>
                <td><input type="text" id="first_name" name="first_name" class="regular-text" required /></td>
            </tr>
             <tr valign="top">
                <th scope="row"><label for="last_name"><?php _e( 'Last Name', 'client360-crm' ); ?></label></th>
                <td><input type="text" id="last_name" name="last_name" class="regular-text" required /></td>
            </tr>
             <tr valign="top">
                <th scope="row"><label for="email"><?php _e( 'Email', 'client360-crm' ); ?></label></th>
                <td><input type="email" id="email" name="email" class="regular-text" required /></td>
            </tr>
             <tr valign="top">
                <th scope="row"><label for="password"><?php _e( 'Password', 'client360-crm' ); ?></label></th>
                <td><input type="password" id="password" name="password" class="regular-text" required /></td>
            </tr>
        </table>
        <?php submit_button( __( 'Add Employee', 'client360-crm' ), 'primary' ); ?>
    </form>
    <?php
}

/**
 * Render the single lead view page.
 */
function c360_render_view_lead_page() {
    if ( ! isset( $_GET['lead_id'] ) ) { wp_die( __( 'No lead specified.', 'client360-crm' ) ); }
    $lead_id = absint( $_GET['lead_id'] );
    $lead = get_post( $lead_id );
    if ( ! $lead || $lead->post_type !== 'lead' ) { wp_die( __( 'Invalid lead specified.', 'client360-crm' ) );}
    if ( ! current_user_can( 'read_post', $lead_id ) ) { wp_die( __( 'You do not have permission to view this lead.', 'client360-crm' ) ); }
    global $post; $post = $lead; setup_postdata( $post );
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( $lead->post_title ); ?></h1>
        <?php if( current_user_can('edit_post', $lead_id) ): ?>
        <a href="<?php echo esc_url( get_edit_post_link($lead_id) ); ?>" class="page-title-action"><?php _e('Edit Lead', 'client360-crm'); ?></a>
        <?php endif; ?>
        <hr class="wp-header-end">
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="postbox-container-1" class="postbox-container">
                     <?php c360_render_view_box( __( 'Assignment & Ownership', 'client360-crm' ), c360_get_lead_assignment_details( $lead_id ) ); ?>
                     <?php c360_render_view_box( __( 'Lead Dates & Follow-up', 'client360-crm' ), c360_get_lead_dates_details( $lead ) ); ?>
                     <?php c360_render_view_box( __( 'Lead Scoring & Nurturing', 'client360-crm' ), c360_get_lead_scoring_details( $lead_id ) ); ?>
                </div>
                <div id="postbox-container-2" class="postbox-container">
                    <?php c360_render_view_box( __( 'Basic Lead Information', 'client360-crm' ), c360_get_lead_basic_details( $lead_id ) ); ?>
                    <?php c360_render_view_box( __( 'Lead Source & Details', 'client360-crm' ), c360_get_lead_source_details( $lead_id ) ); ?>
                    <div class="postbox"><h2 class="hndle"><span><?php _e('Activity History', 'client360-crm'); ?></span></h2><div class="inside"><?php c360_related_activity_metabox_html($post); ?></div></div>
                </div>
            </div>
        </div>
    </div>
    <?php
    wp_reset_postdata();
}

/**
 * Render the single contact view page.
 */
function c360_render_view_contact_page() {
     if ( ! isset( $_GET['contact_id'] ) ) { wp_die( __( 'No contact specified.', 'client360-crm' ) ); }
    $contact_id = absint( $_GET['contact_id'] );
    $contact = get_post( $contact_id );
    if ( ! $contact || $contact->post_type !== 'contact' ) { wp_die( __( 'Invalid contact specified.', 'client360-crm' ) ); }
    if ( ! current_user_can( 'read_post', $contact_id ) ) { wp_die( __( 'You do not have permission to view this contact.', 'client360-crm' ) ); }
    global $post; $post = $contact; setup_postdata( $post );
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( $contact->post_title ); ?></h1>
         <?php if( current_user_can('edit_post', $contact_id) ): ?>
        <a href="<?php echo esc_url( get_edit_post_link($contact_id) ); ?>" class="page-title-action"><?php _e('Edit Contact', 'client360-crm'); ?></a>
        <?php endif; ?>
        <hr class="wp-header-end">
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="postbox-container-1" class="postbox-container">
                     <?php c360_render_view_box( __( 'Social Media', 'client360-crm' ), c360_get_contact_social_details( $contact_id ) ); ?>
                     <?php c360_render_view_box( __( 'Assignment & Notes', 'client360-crm' ), c360_get_contact_assignment_details( $contact_id ) ); ?>
                </div>
                <div id="postbox-container-2" class="postbox-container">
                    <?php c360_render_view_box( __( 'Contact Information', 'client360-crm' ), c360_get_contact_main_details( $contact_id ) ); ?>
                    <?php c360_render_view_box( __( 'Address Information', 'client360-crm' ), c360_get_contact_address_details( $contact_id ) ); ?>
                    <?php c360_render_view_box( __( 'Source Information', 'client360-crm' ), c360_get_contact_source_details( $contact_id ) ); ?>
                    <?php c360_render_view_box( __( 'Classifications', 'client360-crm' ), c360_get_contact_classification_details( $contact_id ) ); ?>
                    <?php c360_render_view_box( __( 'Additional Information', 'client360-crm' ), c360_get_contact_additional_details( $contact_id ) ); ?>
                    <div class="postbox"><h2 class="hndle"><span><?php _e('Activity History', 'client360-crm'); ?></span></h2><div class="inside"><?php c360_related_activity_metabox_html($post); ?></div></div>
                </div>
            </div>
        </div>
    </div>
    <?php
    wp_reset_postdata();
}

/**
 * Render the single property view page.
 */
function c360_render_view_property_page() {
    if ( ! isset( $_GET['property_id'] ) ) { wp_die( __( 'No property specified.', 'client360-crm' ) ); }
    $property_id = absint( $_GET['property_id'] );
    $property = get_post( $property_id );
    if ( ! $property || $property->post_type !== 'property' ) { wp_die( __( 'Invalid property specified.', 'client360-crm' ) ); }
    if ( ! current_user_can( 'read_post', $property_id ) ) { wp_die( __( 'You do not have permission to view this property.', 'client360-crm' ) ); }
    global $post; $post = $property; setup_postdata( $post );
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( $property->post_title ); ?></h1>
         <?php if( current_user_can('edit_post', $property_id) ): ?>
            <a href="<?php echo esc_url( get_edit_post_link($property_id) ); ?>" class="page-title-action"><?php _e('Edit Property', 'client360-crm'); ?></a>
        <?php endif; ?>
        <hr class="wp-header-end">
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                 <div id="postbox-container-1" class="postbox-container">
                    <?php c360_render_view_box( __( 'Listing Details', 'client360-crm' ), c360_get_property_listing_details( $property_id ) ); ?>
                    <?php c360_render_view_box( __( 'Dealer Details', 'client360-crm' ), c360_get_property_dealer_details( $property_id ) ); ?>
                    <?php c360_render_view_box( __( 'Media & Metadata', 'client360-crm' ), c360_get_property_media_details( $property_id ) ); ?>
                </div>
                <div id="postbox-container-2" class="postbox-container">
                    <?php c360_render_view_box( __( 'Basic Property Information', 'client360-crm' ), c360_get_property_basic_details( $property_id ) ); ?>
                    <?php c360_render_view_box( __( 'Property Details', 'client360-crm' ), c360_get_property_details_view( $property_id ) ); ?>
                    <?php c360_render_view_box( __( 'Amenities', 'client360-crm' ), [ 'Amenities' => get_post_meta($property_id, '_amenities', true) ] ); ?>
                    <?php c360_render_view_box( __( 'Available For', 'client360-crm' ), [ 'Available For' => get_post_meta($property_id, '_available_for', true) ] ); ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    wp_reset_postdata();
}

/**
 * Render the single meeting view page.
 */
function c360_render_view_meeting_page() {
    if ( ! isset( $_GET['meeting_id'] ) ) { wp_die( __( 'No meeting specified.', 'client360-crm' ) ); }
    $meeting_id = absint( $_GET['meeting_id'] );
    $meeting = get_post( $meeting_id );
    if ( ! $meeting || $meeting->post_type !== 'meeting_log' ) { wp_die( __( 'Invalid meeting specified.', 'client360-crm' ) ); }
    if ( ! current_user_can( 'read_post', $meeting_id ) ) { wp_die( __( 'You do not have permission to view this meeting.', 'client360-crm' ) ); }
    
    global $post; $post = $meeting; setup_postdata( $post );
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( $meeting->post_title ); ?></h1>
        <?php if( current_user_can('edit_post', $meeting_id) ): ?>
            <a href="<?php echo esc_url( get_edit_post_link($meeting_id) ); ?>" class="page-title-action"><?php _e('Edit Meeting', 'client360-crm'); ?></a>
        <?php endif; ?>
        <hr class="wp-header-end">
        <div id="poststuff">
            <div class="postbox">
                <h2 class="hndle"><span><?php _e('Meeting Details', 'client360-crm'); ?></span></h2>
                <div class="inside">
                    <?php c360_render_view_box( 'Details', c360_get_meeting_details_for_view( $meeting_id ) ); ?>
                    <hr>
                    <h3><?php _e('Notes', 'client360-crm'); ?></h3>
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    wp_reset_postdata();
}

/**
 * Render the single task view page.
 */
function c360_render_view_task_page() {
    if ( ! isset( $_GET['task_id'] ) ) { wp_die( __( 'No task specified.', 'client360-crm' ) ); }
    $task_id = absint( $_GET['task_id'] );
    $task = get_post( $task_id );
    if ( ! $task || $task->post_type !== 'task' ) { wp_die( __( 'Invalid task specified.', 'client360-crm' ) ); }
    if ( ! current_user_can( 'read_post', $task_id ) ) { wp_die( __( 'You do not have permission to view this task.', 'client360-crm' ) ); }

    global $post; $post = $task; setup_postdata( $post );
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( $task->post_title ); ?></h1>
        <?php if( current_user_can('edit_post', $task_id) ): ?>
            <a href="<?php echo esc_url( get_edit_post_link($task_id) ); ?>" class="page-title-action"><?php _e('Edit Task', 'client360-crm'); ?></a>
        <?php endif; ?>
        <hr class="wp-header-end">

        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="postbox-container-1" class="postbox-container">
                    <?php c360_render_view_box( __( 'Task Details', 'client360-crm' ), c360_get_task_details_for_view( $task_id ) ); ?>
                </div>
                <div id="postbox-container-2" class="postbox-container">
                     <div class="postbox">
                        <h2 class="hndle"><span><?php _e('Description & Notes', 'client360-crm'); ?></span></h2>
                        <div class="inside">
                            <?php the_content(); ?>
                            <hr>
                            <h4><?php _e('Notes', 'client360-crm'); ?></h4>
                            <p><?php echo nl2br(esc_html(get_post_meta($task_id, '_task_notes', true))); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    wp_reset_postdata();
}

/**
 * Helper function to render a read-only meta box for view pages.
 */
function c360_render_view_box( $title, $data ) {
    if ( empty( $data ) ) return;
    ?>
    <div class="postbox">
        <h2 class="hndle"><span><?php echo esc_html( $title ); ?></span></h2>
        <div class="inside">
            <table class="form-table">
                <?php foreach ( $data as $label => $value ): ?>
                    <?php 
                        if (is_array($value)) {
                            $value = implode(', ', $value);
                        }
                        if ( ! empty( $value ) ): 
                    ?>
                        <tr>
                            <th scope="row" style="width: 40%;"><strong><?php echo esc_html( $label ); ?></strong></th>
                            <td><?php echo wp_kses_post( $value ); ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
    <?php
}
/**
 * Render the single call log view page.
 */
function c360_render_view_call_log_page() {
    if ( ! isset( $_GET['log_id'] ) ) { wp_die( __( 'No log specified.', 'client360-crm' ) ); }
    $log_id = absint( $_GET['log_id'] );
    $log = get_post( $log_id );
    if ( ! $log || $log->post_type !== 'call_log' ) { wp_die( __( 'Invalid call log specified.', 'client360-crm' ) ); }
    if ( ! current_user_can( 'read_post', $log_id ) ) { wp_die( __( 'You do not have permission to view this log.', 'client360-crm' ) ); }

    global $post; $post = $log; setup_postdata( $post );
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( $log->post_title ); ?></h1>
        <?php if( current_user_can('edit_post', $log_id) ): ?>
            <a href="<?php echo esc_url( get_edit_post_link($log_id) ); ?>" class="page-title-action"><?php _e('Edit Call Log', 'client360-crm'); ?></a>
        <?php endif; ?>
        <hr class="wp-header-end">

        <div id="poststuff">
            <div class="postbox">
                <h2 class="hndle"><span><?php _e('Call Details', 'client360-crm'); ?></span></h2>
                <div class="inside">
                    <?php c360_render_view_box( 'Details', c360_get_call_details_for_view( $log_id ) ); ?>
                    <hr>
                    <h3><?php _e('Notes', 'client360-crm'); ?></h3>
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    wp_reset_postdata();
}

/**
 * Render the single email log view page.
 */
function c360_render_view_email_log_page() {
    if ( ! isset( $_GET['log_id'] ) ) { wp_die( __( 'No log specified.', 'client360-crm' ) ); }
    $log_id = absint( $_GET['log_id'] );
    $log = get_post( $log_id );
    if ( ! $log || $log->post_type !== 'email_log' ) { wp_die( __( 'Invalid email log specified.', 'client360-crm' ) ); }
    if ( ! current_user_can( 'read_post', $log_id ) ) { wp_die( __( 'You do not have permission to view this log.', 'client360-crm' ) ); }

    global $post; $post = $log; setup_postdata( $post );
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( $log->post_title ); ?></h1>
        <?php if( current_user_can('edit_post', $log_id) ): ?>
            <a href="<?php echo esc_url( get_edit_post_link($log_id) ); ?>" class="page-title-action"><?php _e('Edit Email Log', 'client360-crm'); ?></a>
        <?php endif; ?>
        <hr class="wp-header-end">

        <div id="poststuff">
            <div class="postbox">
                <h2 class="hndle"><span><?php _e('Email Details', 'client360-crm'); ?></span></h2>
                <div class="inside">
                    <?php c360_render_view_box( 'Details', c360_get_email_log_details_for_view( $log_id ) ); ?>
                    <hr>
                    <h3><?php _e('Message', 'client360-crm'); ?></h3>
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    wp_reset_postdata();
}
/**
 * Render the custom Documents page with File Explorer and Upload form.
 */
function c360_render_documents_page() {
    // --- Handle Form Submission ---
    if ( isset( $_POST['c360_upload_document_nonce'] ) && wp_verify_nonce( $_POST['c360_upload_document_nonce'], 'c360_upload_document_action' ) ) {
        if ( ! current_user_can('upload_files') ) {
            wp_die( __('You do not have permission to upload files.') );
        }

        $folder_id = 0;
        // Check if creating a new folder
        if ( isset($_POST['document_folder']) && $_POST['document_folder'] === 'new' && !empty($_POST['new_folder_name']) ) {
            $new_folder = wp_insert_term( sanitize_text_field($_POST['new_folder_name']), 'document_folder' );
            if ( ! is_wp_error($new_folder) ) {
                $folder_id = $new_folder['term_id'];
            }
        } elseif ( isset($_POST['document_folder']) && !empty($_POST['document_folder']) ) {
            $folder_id = absint($_POST['document_folder']);
        }

        if ( $folder_id === 0 ) {
             echo '<div class="notice notice-error is-dismissible"><p>Error: Folder is a mandatory field.</p></div>';
        }
        elseif ( ! empty($_FILES['document_file']['name']) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            $uploadedfile = $_FILES['document_file'];
            $upload_overrides = array( 'test_form' => false );
            $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

            if ( $movefile && ! isset( $movefile['error'] ) ) {
                $file_name = isset($_POST['file_name']) && !empty($_POST['file_name']) ? sanitize_text_field($_POST['file_name']) : sanitize_file_name($uploadedfile['name']);

                $post_data = array(
                    'post_title' => $file_name,
                    'post_status' => 'publish',
                    'post_type' => 'document',
                );
                $post_id = wp_insert_post( $post_data );

                if ($post_id) {
                    update_post_meta($post_id, '_c360_file_url', $movefile['url']);
                    if ($folder_id > 0) {
                        wp_set_object_terms($post_id, $folder_id, 'document_folder');
                    }
                    echo '<div class="notice notice-success is-dismissible"><p>File uploaded successfully!</p></div>';
                }
            } else {
                echo '<div class="notice notice-error is-dismissible"><p>File upload error: ' . esc_html($movefile['error']) . '</p></div>';
            }
        }
    }
    ?>
    <div class="wrap">
        <h1><?php _e('Documents', 'client360-crm'); ?></h1>
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <!-- Left Column: File Explorer -->
                <div id="postbox-container-1" class="postbox-container">
                    <div class="postbox">
                        <h2 class="hndle"><span><?php _e('File Explorer', 'client360-crm'); ?></span></h2>
                        <div class="inside">
                            <?php echo c360_get_file_explorer_html(); ?>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Upload Form -->
                <div id="postbox-container-2" class="postbox-container">
                    <div class="postbox">
                         <h2 class="hndle"><span><?php _e('Upload New Document', 'client360-crm'); ?></span></h2>
                         <div class="inside">
                            <form method="post" enctype="multipart/form-data" style="padding: 15px;">
                                <?php wp_nonce_field( 'c360_upload_document_action', 'c360_upload_document_nonce' ); ?>
                                <p>
                                    <label for="document_folder"><strong><?php _e('Folder Name', 'client360-crm'); ?></strong> <span style="color:red;">*</span></label><br>
                                    <?php
                                    wp_dropdown_categories(array(
                                        'taxonomy' => 'document_folder', 'name' => 'document_folder', 'hierarchical' => true,
                                        'show_option_none' => __('Select a Folder', 'client360-crm'), 'hide_empty' => false, 'id' => 'document_folder_select', 'required' => true
                                    ));
                                    ?>
                                    <a href="#" id="create_new_folder_link" style="margin-left: 10px;"><?php _e('or Create New', 'client360-crm'); ?></a>
                                </p>
                                <p id="new_folder_name_wrapper" style="display: none;">
                                     <label for="new_folder_name"><?php _e('New Folder Name', 'client360-crm'); ?></label><br>
                                     <input type="text" name="new_folder_name" id="new_folder_name" class="regular-text">
                                </p>
                                <p>
                                    <label for="document_file"><strong><?php _e('Choose File', 'client360-crm'); ?></strong> <span style="color:red;">*</span></label><br>
                                    <input type="file" name="document_file" id="document_file" required>
                                </p>
                                <p>
                                     <label for="file_name"><strong><?php _e('File Name', 'client360-crm'); ?></strong></label><br>
                                     <input type="text" name="file_name" id="file_name" class="regular-text">
                                     <span class="description"><?php _e('(Optional) If left blank, the original filename will be used.', 'client360-crm'); ?></span>
                                </p>
                                <?php submit_button(__('Publish Now', 'client360-crm')); ?>
                            </form>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function($){
            $('#create_new_folder_link').on('click', function(e){
                e.preventDefault();
                $('#new_folder_name_wrapper').show();
                $('#document_folder_select').hide();

                var newOption = '<option value="new" selected="selected">Creating New Folder...</option>';
                if ($('#document_folder_select option[value="new"]').length === 0) {
                     $('#document_folder_select').append(newOption);
                }
                 $('#document_folder_select').val('new');
            });
        });
    </script>
    <?php
}