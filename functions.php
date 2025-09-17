<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Get data for the CRM dashboard.
 * @return array
 */
function c360_get_dashboard_data() {

    $user_id = get_current_user_id();
    $is_admin = current_user_can('manage_options');

    // Base query args
    $lead_args = array('post_type' => 'lead', 'posts_per_page' => -1, 'fields' => 'ids');
    $contact_args = array('post_type' => 'contact', 'posts_per_page' => -1, 'fields' => 'ids');
    $property_args = array('post_type' => 'property', 'posts_per_page' => -1, 'fields' => 'ids');
    $task_args = array(
        'post_type' => 'task', 
        'posts_per_page' => -1, 
        'fields' => 'ids',
        'tax_query' => array(
            array(
                'taxonomy' => 'task_status',
                'field'    => 'slug',
                'terms'    => array('completed', 'done'),
                'operator' => 'NOT IN',
            ),
        ),
    );

    // If user is not an admin, modify the query to only show their own posts
    if ( ! $is_admin ) {
        $lead_args['author'] = $user_id;
        $contact_args['author'] = $user_id;
        $property_args['author'] = $user_id;
        $task_args['author'] = $user_id;
    }

    $leads = new WP_Query($lead_args);
    $contacts = new WP_Query($contact_args);
    $properties = new WP_Query($property_args);
    $tasks = new WP_Query($task_args);

    return array(
        'lead_count' => $leads->post_count,
        'contact_count' => $contacts->post_count,
        'property_count' => $properties->post_count,
        'task_count' => $tasks->post_count,
    );
}

/**
 * Get data for the dashboard chart.
 */
function c360_get_lead_status_chart_data() {
    $chart_data = array( 'labels' => array(), 'data' => array() );
    $lead_statuses = get_terms( array( 'taxonomy' => 'lead_status', 'hide_empty' => false ) );
    if ( ! empty($lead_statuses) && ! is_wp_error($lead_statuses) ) {
        foreach ($lead_statuses as $status) {
            $args = array( 'post_type' => 'lead', 'posts_per_page' => -1, 'tax_query' => array( array( 'taxonomy' => 'lead_status', 'field' => 'term_id', 'terms' => $status->term_id ) ) );
            $query = new WP_Query($args);
            $chart_data['labels'][] = $status->name;
            $chart_data['data'][] = $query->post_count;
        }
    }
    return $chart_data;
}

/**
 * Get events for the FullCalendar instance.
 */
function c360_get_calendar_events() {
    $events = array();
    $user_id = get_current_user_id();
    $is_admin = current_user_can('manage_options');

    // --- Get Tasks ---
    $task_args = array( 
        'post_type' => 'task', 
        'posts_per_page' => -1, 
        'meta_key' => '_task_start_date', 
        'meta_value' => '', 
        'meta_compare' => '!=' 
    );
    if (!$is_admin) { $task_args['author'] = $user_id; }
    $tasks = get_posts($task_args);
    foreach ($tasks as $task) {
        $view_url = add_query_arg(array('page' => 'client360_view_task', 'task_id' => $task->ID), admin_url('admin.php'));
        $events[] = array(
            'title' => get_the_title($task->ID),
            'start' => get_post_meta($task->ID, '_task_start_date', true),
            'end' => get_post_meta($task->ID, '_task_end_date', true),
            'url' => $view_url, // Corrected: Removed esc_url() which was causing double encoding
            'backgroundColor' => '#3a86ff', // Blue for tasks
            'borderColor' => '#3a86ff'
        );
    }
    
    // --- Get Meetings ---
    $meeting_args = array( 
        'post_type' => 'meeting_log', 
        'posts_per_page' => -1, 
        'meta_key' => '_meeting_datetime', 
        'meta_value' => '', 
        'meta_compare' => '!=' 
    );
    if (!$is_admin) { $meeting_args['author'] = $user_id; }
    $meetings = get_posts($meeting_args);
    foreach ($meetings as $meeting) {
        $view_url = add_query_arg(array('page' => 'client360_view_meeting', 'meeting_id' => $meeting->ID), admin_url('admin.php'));
        $events[] = array(
            'title' => get_the_title($meeting->ID),
            'start' => get_post_meta($meeting->ID, '_meeting_datetime', true),
            'url' => $view_url, // Corrected: Removed esc_url()
            'backgroundColor' => '#3c9a55', // Green for meetings
            'borderColor' => '#3c9a55'
        );
    }

    // --- Get Calls ---
    $call_args = array( 
        'post_type' => 'call_log', 
        'posts_per_page' => -1, 
        'meta_key' => '_call_start_date', 
        'meta_value' => '', 
        'meta_compare' => '!=' 
    );
    if (!$is_admin) { $call_args['author'] = $user_id; }
    $calls = get_posts($call_args);
    foreach ($calls as $call) {
        $view_url = add_query_arg(array('page' => 'client360_view_call_log', 'log_id' => $call->ID), admin_url('admin.php'));
        $events[] = array(
            'title' => get_the_title($call->ID),
            'start' => get_post_meta($call->ID, '_call_start_date', true),
            'end' => get_post_meta($call->ID, '_call_end_date', true),
            'url' => $view_url, // Corrected: Removed esc_url()
            'backgroundColor' => '#ff8c00', // Orange for calls
            'borderColor' => '#ff8c00'
        );
    }

    return $events;
}
/**
 * Filter the main query for CRM post types for employees.
 */
function c360_filter_query_for_employees( $query ) {
    if ( is_admin() && $query->is_main_query() && ! current_user_can('manage_options') ) {
        $post_types = array('lead', 'contact', 'property', 'task', 'meeting_log', 'call_log', 'email_log', 'payment', 'document');
        if ( in_array( $query->get('post_type'), $post_types ) ) {
            $query->set( 'author', get_current_user_id() );
        }
    }
}
add_action( 'pre_get_posts', 'c360_filter_query_for_employees' );


// --- LEAD LIST TABLE CUSTOMIZATIONS ---

// Add "Bulk Upload" button to the lead list page
function c360_add_bulk_upload_button_to_leads( $which ) {
    global $typenow;
    if ( $typenow === 'lead' && $which === 'top' ) {
        $upload_url = admin_url('admin.php?page=client360_bulk_upload');
        ?>
        <a href="<?php echo esc_url($upload_url); ?>" class="page-title-action"><?php _e('Bulk Upload Leads', 'client360-crm'); ?></a>
        <?php
    }
}
add_action('manage_posts_extra_tablenav', 'c360_add_bulk_upload_button_to_leads');

// Add/modify columns to the lead list table
function c360_add_lead_columns($columns) {
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = __('Lead Name', 'client360-crm');
    $new_columns['email'] = __('Email', 'client360-crm');
    $new_columns['phone'] = __('Phone Number', 'client360-crm');
    $new_columns['lead_status'] = __('Status', 'client360-crm');
    $new_columns['agent_name'] = __('Agent Name', 'client360-crm');
    $new_columns['call_created'] = __('Call Created', 'client360-crm');
    $new_columns['change_status'] = __('Change Status', 'client360-crm');
    $new_columns['date'] = __('Date', 'client360-crm');
    return $new_columns;
}
add_filter('manage_lead_posts_columns', 'c360_add_lead_columns');


// Populate the custom lead columns
function c360_lead_custom_column_content($column, $post_id) {
    switch ($column) {
        case 'email':
            echo esc_html(get_post_meta($post_id, '_lead_email', true));
            break;
        case 'phone':
            echo esc_html(get_post_meta($post_id, '_lead_phone', true));
            break;
        case 'lead_status':
            $terms = get_the_terms($post_id, 'lead_status');
            if ($terms && !is_wp_error($terms)) {
                echo esc_html($terms[0]->name);
            }
            break;
         case 'call_created':
    $related_calls = get_posts(array(
        'post_type' => 'call_log',
        'posts_per_page' => 1,
        'meta_key' => '_call_related_to', // This was the incorrect key
        'meta_value' => $post_id,
    ));
    echo !empty($related_calls) ? 'Yes' : 'No';
    break;
        case 'agent_name':
            $agent_id = get_post_meta($post_id, '_lead_assigned_agent', true);
            if ($agent_id) {
                $user = get_userdata($agent_id);
                echo esc_html($user->display_name);
            } else { echo '—'; }
            break;
        case 'change_status':
            wp_nonce_field('c360_change_status_nonce', 'c360_change_status_nonce_field_' . $post_id);
            echo '<select class="c360-change-status-dropdown" data-lead-id="' . $post_id . '">';
            echo '<option value="">' . __('Select Status', 'client360-crm') . '</option>';
            $statuses = get_terms(array('taxonomy' => 'lead_status', 'hide_empty' => false));
            $current_status_terms = wp_get_object_terms($post_id, 'lead_status', array('fields' => 'ids'));
            $current_status = !empty($current_status_terms) ? $current_status_terms[0] : '';
            if ( ! is_wp_error( $statuses ) && ! empty( $statuses ) ) {
                foreach ($statuses as $status) {
                     echo '<option value="' . esc_attr($status->term_id) . '" ' . selected( $current_status, $status->term_id, false ) . '>' . esc_html($status->name) . '</option>';
                }
            }
            echo '</select>';
            break;
    }
}
add_action('manage_lead_posts_custom_column', 'c360_lead_custom_column_content', 10, 2);

// Add custom row actions for leads
function c360_add_lead_row_actions($actions, $post) {
    if ($post->post_type === 'lead') {
        $email = get_post_meta($post->ID, '_lead_email', true);

        $view_url = add_query_arg(array('page' => 'client360_view_lead', 'lead_id' => $post->ID), admin_url('admin.php'));
        $actions['view_lead'] = '<a href="' . esc_url($view_url) . '">' . __('View', 'client360-crm') . '</a>';

        $create_call_url = add_query_arg(array('post_type' => 'call_log', 'related_lead' => $post->ID), admin_url('post-new.php'));
        $actions['create_call'] = '<a href="' . esc_url($create_call_url) . '">' . __('Create Call', 'client360-crm') . '</a>';

        $create_email_url = add_query_arg(array('post_type' => 'email_log', 'related_lead' => $post->ID), admin_url('post-new.php'));
         $actions['create_email_log'] = '<a href="' . esc_url($create_email_url) . '">' . __('Create Email Log', 'client360-crm') . '</a>';

        if ($email) {
            $actions['send_email'] = '<a href="mailto:' . esc_attr($email) . '">' . __('Send Email', 'client360-crm') . '</a>';
        }
    }
    return $actions;
}
add_filter('post_row_actions', 'c360_add_lead_row_actions', 10, 2);


// Add filters to the lead list table
function c360_add_lead_filters() {
    global $typenow;
    if ($typenow == 'lead') {
        wp_dropdown_categories(array(
            'show_option_all' => 'All Statuses', 'taxonomy' => 'lead_status', 'name' => 'lead_status_filter',
            'orderby' => 'name', 'selected' => isset($_GET['lead_status_filter']) ? $_GET['lead_status_filter'] : '',
            'hierarchical' => true, 'show_count' => true, 'hide_empty' => false,
        ));
        wp_dropdown_users(array(
            'name' => 'lead_agent_filter', 'show_option_all' => 'All Agents', 'role__in' => array('administrator', 'c360_employee'),
            'selected' => isset($_GET['lead_agent_filter']) ? $_GET['lead_agent_filter'] : ''
        ));
    }
}
add_action('restrict_manage_posts', 'c360_add_lead_filters');

// Handle the lead filter and search logic
function c360_process_lead_filters_and_search($query) {
    global $pagenow;
    if ( is_admin() && $query->is_main_query() && $pagenow == 'edit.php' && $query->get('post_type') == 'lead' ) {
        if (isset($_GET['lead_status_filter']) && (int)$_GET['lead_status_filter'] > 0) {
            $query->set('tax_query', array( array( 'taxonomy' => 'lead_status', 'field' => 'term_id', 'terms' => (int)$_GET['lead_status_filter'] ) ));
        }
        if (isset($_GET['lead_agent_filter']) && (int)$_GET['lead_agent_filter'] > 0) {
             $query->set('meta_key', '_lead_assigned_agent');
             $query->set('meta_value', (int)$_GET['lead_agent_filter']);
        }
        $search_term = $query->get('s');
        if ($search_term) {
            $query->set('c360_search_title', $search_term);
             $search_meta_query = array( 'relation' => 'OR',
                 array( 'key' => '_lead_email', 'value' => $search_term, 'compare' => 'LIKE' ),
                 array( 'key' => '_lead_phone', 'value' => $search_term, 'compare' => 'LIKE' )
            );
            $query->set('meta_query', $search_meta_query);
            $query->set('s', ''); 
            add_filter('posts_where', 'c360_title_filter_for_search', 10, 2);
        }
    }
}
add_action('parse_query', 'c360_process_lead_filters_and_search');

function c360_title_filter_for_search( $where, $wp_query ) {
    global $wpdb;
    if ( $search_term = $wp_query->get( 'c360_search_title' ) ) {
        $where .= ' OR ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $wpdb->esc_like( $search_term ) ) . '%\'';
    }
    remove_filter('posts_where', 'c360_title_filter_for_search', 10, 2);
    return $where;
}

// --- BULK ACTIONS ---
function c360_add_lead_bulk_edit_fields($column_name, $post_type) {
    if ($post_type == 'lead' && $column_name === 'lead_status') {
        wp_nonce_field('c360_bulk_edit_nonce', 'c360_bulk_edit_nonce_field');
        ?>
        <fieldset class="inline-edit-col-right">
            <div class="inline-edit-col">
                <div class="inline-edit-group wp-clearfix">
                    <label class="alignleft">
                        <span class="title">Status</span>
                        <?php wp_dropdown_categories(array('show_option_none' => '— No Change —', 'taxonomy' => 'lead_status', 'name' => 'bulk_lead_status', 'hide_empty' => false,)); ?>
                    </label>
                     <label class="alignleft">
                        <span class="title">Agent</span>
                         <?php wp_dropdown_users(array('show_option_none' => '— No Change —', 'name' => 'bulk_lead_agent', 'role__in' => array('administrator', 'c360_employee'),)); ?>
                    </label>
                </div>
            </div>
        </fieldset>
        <?php
    }
}
add_action('bulk_edit_custom_box', 'c360_add_lead_bulk_edit_fields', 10, 2);

function c360_save_lead_bulk_edit_data( $post_id, $post ) {
    if ( $post->post_type !== 'lead' || !isset($_REQUEST['c360_bulk_edit_nonce_field']) ) return;
    if ( !wp_verify_nonce( $_REQUEST['c360_bulk_edit_nonce_field'], 'c360_bulk_edit_nonce' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( !current_user_can( 'edit_post', $post_id ) ) return;

    if ( isset( $_REQUEST['bulk_lead_status'] ) && (int)$_REQUEST['bulk_lead_status'] > 0 ) {
        wp_set_object_terms( $post_id, (int)$_REQUEST['bulk_lead_status'], 'lead_status' );
    }
    if ( isset( $_REQUEST['bulk_lead_agent'] ) && (int)$_REQUEST['bulk_lead_agent'] > 0 ) {
        update_post_meta( $post_id, '_lead_assigned_agent', (int)$_REQUEST['bulk_lead_agent'] );
    }
}
add_action('save_post_lead', 'c360_save_lead_bulk_edit_data', 10, 2);


// --- CONTACT LIST TABLE CUSTOMIZATIONS ---
function c360_add_contact_columns($columns) {
    unset($columns['title'], $columns['date']);
    $new_columns['cb'] = $columns['cb'];
    $new_columns['full_name'] = __('Name', 'client360-crm');
    $new_columns['email'] = __('Email Address', 'client360-crm');
    $new_columns['phone'] = __('Phone Number', 'client360-crm');
    $new_columns['contact_method'] = __('Contact Method', 'client360-crm');
    $new_columns['date'] = __('Date Created', 'client360-crm');
    return $new_columns;
}
add_filter('manage_contact_posts_columns', 'c360_add_contact_columns');

function c360_contact_custom_column_content($column, $post_id) {
    switch ($column) {
        case 'full_name':
            $title = get_post_meta($post_id, '_contact_title', true);
            $first_name = get_post_meta($post_id, '_contact_first_name', true);
            $last_name = get_post_meta($post_id, '_contact_last_name', true);
            $full_name = trim($title . ' ' . $first_name . ' ' . $last_name);
            echo '<strong><a class="row-title" href="' . esc_url(get_edit_post_link($post_id)) . '">' . esc_html($full_name) . '</a></strong>';
            break;
        case 'email': echo esc_html(get_post_meta($post_id, '_contact_email', true)); break;
        case 'phone': echo esc_html(get_post_meta($post_id, '_contact_phone', true)); break;
        case 'contact_method': echo esc_html(get_post_meta($post_id, '_contact_preferred_method', true)); break;
    }
}
add_action('manage_contact_posts_custom_column', 'c360_contact_custom_column_content', 10, 2);

function c360_add_contact_row_actions($actions, $post) {
    if ($post->post_type === 'contact') {
        $view_url = add_query_arg(array('page' => 'client360_view_contact', 'contact_id' => $post->ID), admin_url('admin.php'));
        $actions['view_contact'] = '<a href="' . esc_url($view_url) . '">' . __('View', 'client360-crm') . '</a>';
        $create_call_url = add_query_arg(array('post_type' => 'call_log', 'related_lead' => $post->ID), admin_url('post-new.php'));
        $actions['create_call'] = '<a href="' . esc_url($create_call_url) . '">' . __('Create Call', 'client360-crm') . '</a>';
        $create_email_url = add_query_arg(array('post_type' => 'email_log', 'related_lead' => $post->ID), admin_url('post-new.php'));
        $actions['create_email_log'] = '<a href="' . esc_url($create_email_url) . '">' . __('Create Email Log', 'client360-crm') . '</a>';
    }
    return $actions;
}
add_filter('post_row_actions', 'c360_add_contact_row_actions', 10, 2);

function c360_process_contact_search($query) {
    global $pagenow;
    if ( is_admin() && $query->is_main_query() && $pagenow == 'edit.php' && $query->get('post_type') == 'contact' ) {
        $search_term = $query->get('s');
        if ($search_term) {
            $query->set('c360_search_title', $search_term);
             $search_meta_query = array( 'relation' => 'OR',
                 array( 'key' => '_contact_first_name', 'value' => $search_term, 'compare' => 'LIKE' ),
                 array( 'key' => '_contact_last_name', 'value' => $search_term, 'compare' => 'LIKE' ),
                 array( 'key' => '_contact_email', 'value' => $search_term, 'compare' => 'LIKE' ),
                 array( 'key' => '_contact_phone', 'value' => $search_term, 'compare' => 'LIKE' )
            );
            $query->set('meta_query', $search_meta_query);
            $query->set('s', ''); 
            add_filter('posts_where', 'c360_title_filter_for_search', 10, 2);
        }
    }
}
add_action('parse_query', 'c360_process_contact_search');

function c360_add_contact_bulk_edit_fields($column_name, $post_type) {
    if ($post_type == 'contact' && $column_name === 'contact_method') {
        wp_nonce_field('c360_bulk_edit_nonce', 'c360_bulk_edit_contact_nonce_field');
        ?>
        <fieldset class="inline-edit-col-right">
            <div class="inline-edit-col">
                <div class="inline-edit-group wp-clearfix">
                     <label class="alignleft">
                        <span class="title">Agent</span>
                         <?php wp_dropdown_users(array('show_option_none' => '— No Change —', 'name' => 'bulk_contact_agent', 'role__in' => array('administrator', 'c360_employee'),)); ?>
                    </label>
                </div>
            </div>
        </fieldset>
        <?php
    }
}
add_action('bulk_edit_custom_box', 'c360_add_contact_bulk_edit_fields', 10, 2);

function c360_save_contact_bulk_edit_data( $post_id, $post ) {
    if ( $post->post_type !== 'contact' || !isset($_REQUEST['c360_bulk_edit_contact_nonce_field']) ) return;
    if ( !wp_verify_nonce( $_REQUEST['c360_bulk_edit_contact_nonce_field'], 'c360_bulk_edit_nonce' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( !current_user_can( 'edit_post', $post_id ) ) return;
    if ( isset( $_REQUEST['bulk_contact_agent'] ) && (int)$_REQUEST['bulk_contact_agent'] > 0 ) {
        update_post_meta( $post_id, '_contact_assigned_agent', (int)$_REQUEST['bulk_contact_agent'] );
    }
}
add_action('save_post_contact', 'c360_save_contact_bulk_edit_data', 10, 2);


// --- PROPERTY LIST TABLE CUSTOMIZATIONS ---
function c360_add_property_columns($columns) {
    unset($columns['date'], $columns['author']);
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = __('Property Name', 'client360-crm');
    $new_columns['property_type'] = __('Property Type', 'client360-crm');
    $new_columns['buying_type'] = __('Type', 'client360-crm');
    $new_columns['locality'] = __('Locality', 'client360-crm');
    $new_columns['city'] = __('City', 'client360-crm');
    $new_columns['price'] = __('Price', 'client360-crm');
    $new_columns['area_sqft'] = __('Sq Ft', 'client360-crm');
    $new_columns['bedrooms'] = __('Bedrooms', 'client360-crm');
    $new_columns['bathrooms'] = __('Bathrooms', 'client360-crm');
    $new_columns['furnishing'] = __('Furnishing', 'client360-crm');
    $new_columns['actions'] = __('Actions', 'client360-crm');
    return $new_columns;
}
add_filter('manage_property_posts_columns', 'c360_add_property_columns');

function c360_property_custom_column_content($column, $post_id) {
    switch ($column) {
        case 'property_type': echo esc_html(get_post_meta($post_id, '_property_type', true)); break;
        case 'buying_type': echo esc_html(get_post_meta($post_id, '_buying_type', true)); break;
        case 'locality': echo esc_html(get_post_meta($post_id, '_locality', true)); break;
        case 'city': echo esc_html(get_post_meta($post_id, '_city', true)); break;
        case 'price':
            $price = get_post_meta($post_id, '_listing_price', true);
            if (is_numeric($price)) { echo '$' . esc_html(number_format_i18n($price, 2)); } 
            else { echo '—'; }
            break;
        case 'area_sqft': echo esc_html(get_post_meta($post_id, '_area_sqft', true)); break;
        case 'bedrooms': echo esc_html(get_post_meta($post_id, '_bedrooms', true)); break;
        case 'bathrooms': echo esc_html(get_post_meta($post_id, '_bathrooms', true)); break;
        case 'furnishing': echo esc_html(get_post_meta($post_id, '_furnishing', true)); break;
        case 'actions':
            $view_url = add_query_arg(array('page' => 'client360_view_property', 'property_id' => $post_id), admin_url('admin.php'));
            printf('<a href="%s" class="button">View</a> ', esc_url($view_url));
            printf('<a href="%s" class="button">Edit</a> ', esc_url(get_edit_post_link($post_id)));
            printf('<a href="%s" class="button" onclick="return confirm(\'Are you sure?\')">Delete</a>', esc_url(get_delete_post_link($post_id)));
            break;
    }
}
add_action('manage_property_posts_custom_column', 'c360_property_custom_column_content', 10, 2);

function c360_make_property_columns_sortable($columns) {
    $columns['price'] = 'price'; $columns['status'] = 'status';
    return $columns;
}
add_filter('manage_edit-property_sortable_columns', 'c360_make_property_columns_sortable');

function c360_property_column_sorting($query) {
    if (!is_admin() || !$query->is_main_query() || $query->get('post_type') !== 'property') { return; }
    $orderby = $query->get('orderby');
    if ('price' == $orderby) { $query->set('meta_key', '_listing_price'); $query->set('orderby', 'meta_value_num'); } 
    elseif ('status' == $orderby) { $query->set('meta_key', '_property_status'); $query->set('orderby', 'meta_value'); }
}
add_action('pre_get_posts', 'c360_property_column_sorting');

function c360_add_property_filters() {
    global $typenow;
    if ($typenow == 'property') {
        $statuses = c360_get_options_for('property_statuses');
        echo '<select name="property_status_filter" id="property_status_filter"><option value="">All Statuses</option>';
        foreach ($statuses as $status) {
            $selected = isset($_GET['property_status_filter']) && $_GET['property_status_filter'] == $status ? ' selected="selected"' : '';
            echo '<option value="' . esc_attr($status) . '"' . $selected . '>' . esc_html(ucfirst($status)) . '</option>';
        }
        echo '</select>';
    }
}
add_action('restrict_manage_posts', 'c360_add_property_filters');

function c360_process_property_filters($query) {
    global $pagenow, $typenow;
    if ($pagenow == 'edit.php' && $typenow == 'property' && $query->is_main_query()) {
        if (isset($_GET['property_status_filter']) && !empty($_GET['property_status_filter'])) {
            $query->set('meta_key', '_property_status');
            $query->set('meta_value', sanitize_text_field($_GET['property_status_filter']));
        }
    }
}
add_action('pre_get_posts', 'c360_process_property_filters');

function c360_add_property_bulk_edit_fields($column_name, $post_type) {
    if ($post_type == 'property' && $column_name === 'status') {
        wp_nonce_field('c360_bulk_edit_nonce', 'c360_bulk_edit_property_nonce_field');
        ?>
        <fieldset class="inline-edit-col-right"><div class="inline-edit-col"><div class="inline-edit-group wp-clearfix">
            <label class="alignleft"><span class="title">Status</span>
                <select name="bulk_property_status">
                    <option value="">— No Change —</option>
                    <?php $statuses = c360_get_options_for('property_statuses');
                    foreach ($statuses as $status) : ?>
                        <option value="<?php echo esc_attr($status); ?>"><?php echo esc_html(ucfirst($status)); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div></div></fieldset>
        <?php
    }
}
add_action('bulk_edit_custom_box', 'c360_add_property_bulk_edit_fields', 10, 2);

function c360_save_property_bulk_edit_data($post_id, $post) {
    if ($post->post_type !== 'property' || !isset($_REQUEST['c360_bulk_edit_property_nonce_field'])) return;
    if (!wp_verify_nonce($_REQUEST['c360_bulk_edit_property_nonce_field'], 'c360_bulk_edit_nonce')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_REQUEST['bulk_property_status']) && !empty($_REQUEST['bulk_property_status'])) {
        update_post_meta($post_id, '_property_status', sanitize_key($_REQUEST['bulk_property_status']));
    }
}
add_action('save_post_property', 'c360_save_property_bulk_edit_data', 10, 2);


// --- TASK LIST TABLE CUSTOMIZATIONS ---
function c360_add_task_columns($columns) {
    $columns['related_to'] = __('Related To', 'client360-crm');
    $columns['due_date'] = __('Due Date', 'client360-crm');
    return $columns;
}
add_filter('manage_task_posts_columns', 'c360_add_task_columns');

function c360_task_custom_column_content($column, $post_id) {
    switch ($column) {
        case 'related_to':
            $related_id = get_post_meta($post_id, '_task_related_to', true);
            if ($related_id && get_post($related_id)) {
                echo '<a href="' . get_edit_post_link($related_id) . '">' . get_the_title($related_id) . '</a>';
            } else { echo '—'; }
            break;
        case 'due_date':
            $due_date = get_post_meta($post_id, '_task_due_date', true);
            if ($due_date) { echo date_i18n(get_option('date_format'), strtotime($due_date)); } 
            else { echo '—'; }
            break;
    }
}
add_action('manage_task_posts_custom_column', 'c360_task_custom_column_content', 10, 2);

function c360_make_task_columns_sortable($columns) {
    $columns['due_date'] = 'due_date';
    return $columns;
}
add_filter('manage_edit-task_sortable_columns', 'c360_make_task_columns_sortable');

function c360_task_column_sorting($query) {
    if (!is_admin() || !$query->is_main_query()) { return; }
    $orderby = $query->get('orderby');
    if ('due_date' == $orderby) {
        $query->set('meta_key', '_task_due_date');
        $query->set('orderby', 'meta_value');
        $query->set('meta_type', 'DATE');
    }
}
add_action('pre_get_posts', 'c360_task_column_sorting');

function c360_add_task_filters() {
    global $typenow;
    if ($typenow == 'task') {
        wp_dropdown_categories(array( 'show_option_all' => 'All Statuses', 'taxonomy' => 'task_status', 'name' => 'task_status_filter', 'orderby' => 'name', 'selected' => isset($_GET['task_status_filter']) ? $_GET['task_status_filter'] : '', 'hierarchical' => true, 'show_count' => true, 'hide_empty' => false, ));
        wp_dropdown_categories(array( 'show_option_all' => 'All Priorities', 'taxonomy' => 'task_priority', 'name' => 'task_priority_filter', 'orderby' => 'name', 'selected' => isset($_GET['task_priority_filter']) ? $_GET['task_priority_filter'] : '', 'hierarchical' => true, 'show_count' => true, 'hide_empty' => false, ));
    }
}
add_action('restrict_manage_posts', 'c360_add_task_filters');

function c360_process_task_filters($query) {
    global $pagenow, $typenow;
    if ($pagenow == 'edit.php' && $typenow == 'task' && $query->is_main_query()) {
        $tax_query = $query->get('tax_query') ?: array();
        if (isset($_GET['task_status_filter']) && (int)$_GET['task_status_filter'] > 0) {
            $tax_query[] = array( 'taxonomy' => 'task_status', 'field' => 'term_id', 'terms' => (int)$_GET['task_status_filter'] );
        }
        if (isset($_GET['task_priority_filter']) && (int)$_GET['task_priority_filter'] > 0) {
            $tax_query[] = array( 'taxonomy' => 'task_priority', 'field' => 'term_id', 'terms' => (int)$_GET['task_priority_filter'] );
        }
        if (!empty($tax_query)) { $query->set('tax_query', $tax_query); }
    }
}
add_action('parse_query', 'c360_process_task_filters');


// --- PAYMENT LIST TABLE CUSTOMIZATIONS ---
function c360_add_payment_columns($columns) {
    unset($columns['date']);
    $columns['related_contact'] = __('Related Contact', 'client360-crm');
    $columns['amount'] = __('Amount', 'client360-crm');
    $columns['method'] = __('Method', 'client360-crm');
    $columns['payment_date'] = __('Payment Date', 'client360-crm');
    return $columns;
}
add_filter('manage_payment_posts_columns', 'c360_add_payment_columns');

function c360_payment_custom_column_content($column, $post_id) {
    switch ($column) {
        case 'related_contact':
            $related_id = get_post_meta($post_id, '_payment_related_contact', true);
            if ($related_id && get_post($related_id)) {
                echo '<a href="' . get_edit_post_link($related_id) . '">' . get_the_title($related_id) . '</a>';
            } else { echo '—'; }
            break;
        case 'amount':
            $amount = get_post_meta($post_id, '_payment_amount', true);
            echo '$' . esc_html(number_format_i18n($amount, 2));
            break;
        case 'method':
            echo esc_html(ucfirst(get_post_meta($post_id, '_payment_method', true)));
            break;
        case 'payment_date':
            $payment_date = get_post_meta($post_id, '_payment_date', true);
            if ($payment_date) { echo date_i18n(get_option('date_format'), strtotime($payment_date)); }
            else { echo '—'; }
            break;
    }
}
add_action('manage_payment_posts_custom_column', 'c360_payment_custom_column_content', 10, 2);

function c360_make_payment_columns_sortable($columns) {
    $columns['amount'] = 'amount';
    $columns['payment_date'] = 'payment_date';
    return $columns;
}
add_filter('manage_edit-payment_sortable_columns', 'c360_make_payment_columns_sortable');

function c360_payment_column_sorting($query) {
    if (!is_admin() || !$query->is_main_query()) { return; }
    $orderby = $query->get('orderby');
    if ('amount' == $orderby) { $query->set('meta_key', '_payment_amount'); $query->set('orderby', 'meta_value_num'); }
    elseif ('payment_date' == $orderby) { $query->set('meta_key', '_payment_date'); $query->set('orderby', 'meta_value'); $query->set('meta_type', 'DATE'); }
}
add_action('pre_get_posts', 'c360_payment_column_sorting');


// --- LOGS LIST TABLE CUSTOMIZATIONS ---
function c360_add_log_columns($columns) {
    unset($columns['date']);
    if ($columns['cb']) {
        $new_columns['cb'] = $columns['cb'];
    }
    $new_columns['title'] = __('Agenda / Title', 'client360-crm');
    $new_columns['related_to'] = __('Related To', 'client360-crm');
    $new_columns['log_datetime'] = __('Date & Time', 'client360-crm');
    $new_columns['timestamp'] = __('Time Stamp', 'client360-crm');
    $new_columns['author'] = __('Created By', 'client360-crm');
    return $new_columns;
}

function c360_log_custom_column_content($column, $post_id) {
    switch ($column) {
        case 'related_to':
            $related_id = get_post_meta($post_id, '_log_related_to', true);
            if ($related_id && get_post($related_id)) {
                echo '<a href="' . get_edit_post_link($related_id) . '">' . get_the_title($related_id) . '</a>';
            } else { echo '—'; }
            break;
        case 'log_datetime':
            $datetime_key = (get_post_type($post_id) === 'meeting_log') ? '_meeting_datetime' : '_log_datetime';
            $datetime = get_post_meta($post_id, $datetime_key, true);
            if ($datetime) {
                $date_format = get_option('date_format');
                echo date_i18n("{$date_format}", strtotime($datetime));
            } else { echo '—'; }
            break;
        case 'timestamp':
             $datetime_key = (get_post_type($post_id) === 'meeting_log') ? '_meeting_datetime' : '_log_datetime';
             $datetime = get_post_meta($post_id, $datetime_key, true);
             if ($datetime) {
                $time_format = get_option('time_format');
                echo date_i18n("{$time_format}", strtotime($datetime));
             } else { echo '—'; }
            break;
    }
}
add_action('manage_meeting_log_posts_custom_column', 'c360_log_custom_column_content', 10, 2);
add_action('manage_call_log_posts_custom_column', 'c360_log_custom_column_content', 10, 2);
add_action('manage_email_log_posts_custom_column', 'c360_log_custom_column_content', 10, 2);

function c360_make_log_columns_sortable($columns) {
    $columns['log_datetime'] = 'log_datetime';
    return $columns;
}
add_filter('manage_edit-meeting_log_sortable_columns', 'c360_make_log_columns_sortable');
add_filter('manage_edit-call_log_sortable_columns', 'c360_make_log_columns_sortable');
add_filter('manage_edit-email_log_sortable_columns', 'c360_make_log_columns_sortable');

function c360_log_column_sorting($query) {
    if (!is_admin() || !$query->is_main_query()) { return; }
    $post_type = $query->get('post_type');
    if (in_array($post_type, array('meeting_log', 'call_log', 'email_log'))) {
        $orderby = $query->get('orderby');
        if ('log_datetime' == $orderby) {
            $query->set('meta_key', '_log_datetime');
            $query->set('orderby', 'meta_value');
            $query->set('meta_type', 'DATETIME');
        }
    }
}
add_action('pre_get_posts', 'c360_log_column_sorting');


// --- DOCUMENT LIST TABLE CUSTOMIZATIONS ---
function c360_add_document_columns($columns) {
    $columns['related_to'] = __('Related To', 'client360-crm');
    $columns['file_link'] = __('File', 'client360-crm');
    return $columns;
}
add_filter('manage_document_posts_columns', 'c360_add_document_columns');

function c360_document_custom_column_content($column, $post_id) {
    switch ($column) {
        case 'related_to':
            $related_id = get_post_meta($post_id, '_document_related_to', true);
            if ($related_id && get_post($related_id)) {
                echo '<a href="' . get_edit_post_link($related_id) . '">' . get_the_title($related_id) . '</a>';
            } else { echo '—'; }
            break;
        case 'file_link':
            $file_url = get_post_meta($post_id, '_document_file_url', true);
            if ($file_url) { echo '<a href="' . esc_url($file_url) . '" target="_blank">View File</a>'; } 
            else { echo '—'; }
            break;
    }
}
add_action('manage_document_posts_custom_column', 'c360_document_custom_column_content', 10, 2);


// --- AJAX HANDLERS & ENQUEUE SCRIPTS ---
function c360_change_lead_status_ajax() {
    check_ajax_referer('c360_change_status_nonce', 'nonce');
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $status_id = isset($_POST['status_id']) ? intval($_POST['status_id']) : 0;
    if ($post_id > 0 && $status_id > 0 && current_user_can('edit_post', $post_id)) {
        wp_set_object_terms($post_id, $status_id, 'lead_status');
        wp_send_json_success();
    } else {
        wp_send_json_error(array('message' => 'Permission denied or invalid data.'));
    }
}
add_action('wp_ajax_c360_change_lead_status', 'c360_change_lead_status_ajax');


function c360_admin_init_hooks() {
    add_action('admin_notices', 'c360_bulk_action_admin_notices');
}
add_action('admin_init', 'c360_admin_init_hooks');

function c360_bulk_action_admin_notices() {
    if (!empty($_REQUEST['bulk_status_changed'])) {
        $count = intval($_REQUEST['bulk_status_changed']);
        printf('<div id="message" class="updated fade"><p>' . _n('%s lead status updated.', '%s leads status updated.', $count, 'client360-crm') . '</p></div>', $count);
    }
    if (!empty($_REQUEST['bulk_agent_assigned'])) {
        $count = intval($_REQUEST['bulk_agent_assigned']);
        printf('<div id="message" class="updated fade"><p>' . _n('%s lead assigned to new agent.', '%s leads assigned to new agent.', $count, 'client360-crm') . '</p></div>', $count);
    }
}

function c360_prefill_related_log_field() {
    global $post_type;
    $related_id = isset( $_GET['related_lead'] ) ? absint( $_GET['related_lead'] ) : 0;

    if ( !$related_id ) {
        return;
    }

    $meta_key = '';
    if ( $post_type === 'call_log' ) $meta_key = '_call_related_to';
    if ( $post_type === 'email_log' ) $meta_key = '_email_related_to';
    if ( $post_type === 'meeting_log' ) $meta_key = '_meeting_related_to';

    if ( $meta_key ) {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('select[name="<?php echo esc_attr($meta_key); ?>"]').val('<?php echo esc_js($related_id); ?>');
            });
        </script>
        <?php
    }
}
add_action( 'admin_footer-post-new.php', 'c360_prefill_related_log_field' );

function c360_enqueue_lead_list_scripts($hook) {
    global $post_type;
    if ('edit.php' === $hook && 'lead' === $post_type) {
         wp_add_inline_script('wp-util', '
            jQuery(function($){
                $("body").on("change", ".c360-change-status-dropdown", function(){
                    var dropdown = $(this); var leadId = dropdown.data("lead-id");
                    var statusId = dropdown.val(); var nonce = $("#c360_change_status_nonce_field_" + leadId).val();
                    if (!statusId) return; dropdown.prop("disabled", true);
                    $.post(ajaxurl, { action: "c360_change_lead_status", post_id: leadId, status_id: statusId, nonce: nonce
                    }).done(function(response){
                        if(response.success){ window.location.reload(); } 
                        else { alert("Failed to update status."); dropdown.prop("disabled", false); }
                    }).fail(function(){ alert("An error occurred."); dropdown.prop("disabled", false); });
                });
            });
        ');
    }
}
add_action('admin_enqueue_scripts', 'c360_enqueue_lead_list_scripts');

// --- EMPLOYEE MANAGEMENT ACTIONS ---
function c360_handle_employee_actions() {
    if (isset($_GET['page']) && $_GET['page'] === 'client360_employee_management' && isset($_GET['action']) && current_user_can('manage_options')) {
        
        $user_id = isset($_GET['user_id']) ? absint($_GET['user_id']) : 0;
        
        // Handle Status Change
        if ($_GET['action'] === 'c360_change_status' && $user_id > 0) {
            check_admin_referer('c360_change_status_nonce_' . $user_id);
            $new_status = sanitize_key($_GET['new_status']);
            if (in_array($new_status, ['active', 'inactive'])) {
                update_user_meta($user_id, 'c360_status', $new_status);
            }
            wp_redirect(admin_url('admin.php?page=client360_employee_management'));
            exit;
        }

        // Handle Password Change (triggered by JS prompt)
        if ($_GET['action'] === 'c360_change_password' && $user_id > 0) {
             check_admin_referer('c360_change_pass_nonce_' . $user_id);
             if(isset($_GET['new_password'])) {
                $new_pass = $_GET['new_password'];
                wp_set_password($new_pass, $user_id);
                // Redirect back with a success message
                 wp_redirect(add_query_arg('password_changed', '1', admin_url('admin.php?page=client360_employee_management')));
                exit;
             }
        }
    }
}
add_action('admin_init', 'c360_handle_employee_actions');

function c360_employee_admin_notices() {
    $screen = get_current_screen();
    if ($screen && $screen->id === 'client360_page_client360_employee_management' && isset($_GET['password_changed']) && $_GET['password_changed'] == '1') {
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Password changed successfully.', 'client360-crm') . '</p></div>';
    }
}
add_action('admin_notices', 'c360_employee_admin_notices');


function c360_add_employee_management_scripts() {
    $screen = get_current_screen();
    // Use the correct screen ID for the submenu page.
    if ($screen && $screen->id === 'client360_page_client360_employee_management') {
        ?>
        <script type="text/javascript">
            function c360_change_password_prompt(event) {
                event.preventDefault(); // Stop the link from navigating immediately
                var newPassword = prompt("<?php _e('Enter the new password for this employee:', 'client360-crm'); ?>");
                if (newPassword != null && newPassword.trim() !== "") {
                    // Find the original URL from the link that was clicked
                    var originalUrl = event.target.href;
                    // Append the new password to the URL and navigate
                    window.location.href = originalUrl + '&new_password=' + encodeURIComponent(newPassword);
                }
                return false; // Prevent navigation if prompt is cancelled
            }
        </script>
        <?php
    }
}
add_action('admin_head', 'c360_add_employee_management_scripts');

function c360_get_lead_basic_details($lead_id) {
    return array(
        'Email' => get_post_meta($lead_id, '_lead_email', true),
        'Phone Number' => get_post_meta($lead_id, '_lead_phone', true),
        'Address' => nl2br(get_post_meta($lead_id, '_lead_address', true)),
    );
}

function c360_get_lead_source_details($lead_id) {
     $status_terms = get_the_terms( $lead_id, 'lead_status' );
    return array(
        'Lead Status' => ( ! empty( $status_terms ) && ! is_wp_error( $status_terms ) ) ? $status_terms[0]->name : '',
        'Lead Source' => get_post_meta($lead_id, '_lead_source', true),
        'Source Details' => get_post_meta($lead_id, '_lead_source_details', true),
        'Campaign' => get_post_meta($lead_id, '_lead_campaign', true),
        'Source Channel' => get_post_meta($lead_id, '_lead_source_channel', true),
        'Source Medium' => get_post_meta($lead_id, '_lead_source_medium', true),
        'Source Campaign' => get_post_meta($lead_id, '_lead_source_campaign', true),
        'Referral' => get_post_meta($lead_id, '_lead_source_referral', true),
    );
}

function c360_get_lead_assignment_details($lead_id) {
    $agent_id = get_post_meta( $lead_id, '_lead_assigned_agent', true );
    $lead_post = get_post($lead_id);
    return array(
        'Assigned Agent' => $agent_id ? get_userdata($agent_id)->display_name : '',
        'Lead Owner' => get_the_author_meta('display_name', $lead_post->post_author),
    );
}

function c360_get_lead_dates_details($lead_post) {
    return array(
        'Creation Date' => $lead_post->post_date,
        'Conversion Date' => get_post_meta($lead_post->ID, '_lead_conversion_date', true),
        'Follow-up Date' => get_post_meta($lead_post->ID, '_lead_followup_date', true),
        'Follow-up Status' => get_post_meta($lead_post->ID, '_lead_followup_status', true),
    );
}

function c360_get_lead_scoring_details($lead_id) {
    return array(
        'Lead Score' => get_post_meta($lead_id, '_lead_score', true),
        'Nurturing Workflow' => get_post_meta($lead_id, '_lead_nurturing_workflow', true),
        'Engagement Level' => get_post_meta($lead_id, '_lead_engagement_level', true),
        'Conversion Rate (%)' => get_post_meta($lead_id, '_lead_conversion_rate', true),
        'Nurturing Stage' => get_post_meta($lead_id, '_lead_nurturing_stage', true),
        'Next Action' => get_post_meta($lead_id, '_lead_next_action', true),
    );
}

function c360_get_contact_main_details($contact_id) {
    return array(
        'First Name' => get_post_meta($contact_id, '_contact_first_name', true),
        'Last Name' => get_post_meta($contact_id, '_contact_last_name', true),
        'Title' => get_post_meta($contact_id, '_contact_title', true),
        'Email' => get_post_meta($contact_id, '_contact_email', true),
        'Phone Number' => get_post_meta($contact_id, '_contact_phone', true),
        'Mobile Number' => get_post_meta($contact_id, '_contact_mobile', true),
        'Preferred Contact Method' => get_post_meta($contact_id, '_contact_preferred_method', true),
    );
}
function c360_get_contact_address_details($contact_id) {
    return array(
        'Physical Address' => nl2br(get_post_meta($contact_id, '_contact_physical_address', true)),
        'Mailing Address' => nl2br(get_post_meta($contact_id, '_contact_mailing_address', true)),
    );
}
function c360_get_contact_source_details($contact_id) {
    return array(
        'Lead Source' => get_post_meta($contact_id, '_contact_lead_source', true),
        'Referral Source' => get_post_meta($contact_id, '_contact_referral_source', true),
        'Campaign Source' => get_post_meta($contact_id, '_contact_campaign_source', true),
    );
}
function c360_get_contact_classification_details($contact_id) {
    return array(
        'Lead Status (if applicable)' => get_post_meta($contact_id, '_contact_lead_status', true),
        'Lead Rating' => get_post_meta($contact_id, '_contact_lead_rating', true),
        'Conversion Probability (%)' => get_post_meta($contact_id, '_contact_conversion_probability', true),
    );
}
function c360_get_contact_additional_details($contact_id) {
    return array(
        'Birthday' => get_post_meta($contact_id, '_contact_birthday', true),
        'Anniversary' => get_post_meta($contact_id, '_contact_anniversary', true),
        'Key Milestones' => nl2br(get_post_meta($contact_id, '_contact_key_milestones', true)),
        'Occupation' => get_post_meta($contact_id, '_contact_occupation', true),
        'Interests/Hobbies' => get_post_meta($contact_id, '_contact_hobbies', true),
        'Gender' => ucfirst(get_post_meta($contact_id, '_contact_gender', true)),
        'Date of Birth' => get_post_meta($contact_id, '_contact_dob', true),
        'Communication Frequency' => get_post_meta($contact_id, '_contact_communication_frequency', true),
    );
}
function c360_get_contact_social_details($contact_id) {
    return array(
        'LinkedIn' => get_post_meta($contact_id, '_contact_linkedin', true),
        'Facebook' => get_post_meta($contact_id, '_contact_facebook', true),
        'Twitter' => get_post_meta($contact_id, '_contact_twitter', true),
        'Other Social' => get_post_meta($contact_id, '_contact_other_social', true),
    );
}
function c360_get_contact_assignment_details($contact_id) {
    $agent_id = get_post_meta( $contact_id, '_contact_assigned_agent', true );
    return array(
        'Assigned Agent' => $agent_id ? get_userdata($agent_id)->display_name : '',
        'Internal Notes' => nl2br(get_post_meta($contact_id, '_contact_internal_notes', true)),
    );
}

function c360_get_property_basic_details($property_id) {
    return array(
        'Property Type' => get_post_meta($property_id, '_property_type', true),
        'Buying Type' => get_post_meta($property_id, '_buying_type', true),
        'Locality' => get_post_meta($property_id, '_locality', true),
        'Address' => get_post_meta($property_id, '_property_address', true),
        'City' => get_post_meta($property_id, '_city', true),
        'State' => get_post_meta($property_id, '_state', true),
        'Pin Code' => get_post_meta($property_id, '_pin_code', true),
    );
}

function c360_get_property_details_view($property_id) {
    return array(
        'Area (sqft)' => get_post_meta($property_id, '_area_sqft', true),
        'Bedrooms' => get_post_meta($property_id, '_bedrooms', true),
        'Bathrooms' => get_post_meta($property_id, '_bathrooms', true),
        'Year Built' => get_post_meta($property_id, '_year_built', true),
        'Property Age' => get_post_meta($property_id, '_property_age', true),
        'Furnishing' => get_post_meta($property_id, '_furnishing', true),
    );
}

function c360_get_property_listing_details($property_id) {
    $price = get_post_meta($property_id, '_listing_price', true);
    return array(
        'Listing Price' => is_numeric($price) ? '$' . number_format_i18n($price, 2) : $price,
        'Available From' => get_post_meta($property_id, '_available_from', true),
    );
}

function c360_get_property_dealer_details($property_id) {
    return array(
        'Dealer Name' => get_post_meta($property_id, '_dealer_name', true),
        'Dealer Phone' => get_post_meta($property_id, '_dealer_phone', true),
        'Dealer Email' => get_post_meta($property_id, '_dealer_email', true),
    );
}

function c360_get_property_media_details($property_id) {
    return array(
        'Posted By' => get_post_meta($property_id, '_posted_by', true),
        'Is Verified?' => ucfirst(get_post_meta($property_id, '_is_verified', true)),
        'Has Photos?' => ucfirst(get_post_meta($property_id, '_has_photos', true)),
        'Photos (URLs)' => get_post_meta($property_id, '_property_photos', true),
        'Description' => nl2br(get_post_meta($property_id, '_description', true)),
        'Internal Notes' => nl2br(get_post_meta($property_id, '_internal_notes', true)),
    );
}

// --- PROPERTY FORM HELPER FUNCTIONS ---
function c360_get_amenities_options() {
    return ['Swimming Pool', 'Gym/Fitness Center', 'Parking', 'Security', 'Power Backup', 'Lift/Elevator', 'Garden/Park', 'Club House', 'Children Play Area', 'Internet/WiFi', 'Air Conditioning', 'Servant Room', 'Study Room', 'Store Room', 'Balcony'];
}

function c360_get_availability_options() {
    return ['Family', 'Bachelor Boys', 'Bachelor Girls', 'Company', 'Anyone'];
}

function c360_render_property_fields($post_id, $fields) {
    foreach ($fields as $meta_key => $label) {
        $value = get_post_meta($post_id, $meta_key, true);
        echo '<tr><th><label for="' . esc_attr($meta_key) . '">' . esc_html($label) . '</label></th><td>';
        
        if ($meta_key === '_property_type') {
            $options = ['Apartment', 'Villa', 'House', 'Independent Floor', 'Plot', 'Studio', 'Other'];
            echo '<select id="' . esc_attr($meta_key) . '" name="' . esc_attr($meta_key) . '">';
            foreach($options as $option) { echo '<option value="' . esc_attr(strtolower($option)) . '" ' . selected($value, strtolower($option), false) . '>' . esc_html($option) . '</option>'; }
            echo '</select>';
        } elseif ($meta_key === '_buying_type') {
            $options = ['Buy', 'Rent'];
            echo '<select id="' . esc_attr($meta_key) . '" name="' . esc_attr($meta_key) . '">';
             foreach($options as $option) { echo '<option value="' . esc_attr(strtolower($option)) . '" ' . selected($value, strtolower($option), false) . '>' . esc_html($option) . '</option>'; }
            echo '</select>';
        } elseif ($meta_key === '_furnishing') {
            $options = ['Furnished', 'Semi-Furnished', 'Unfurnished'];
             echo '<select id="' . esc_attr($meta_key) . '" name="' . esc_attr($meta_key) . '">';
             foreach($options as $option) { echo '<option value="' . esc_attr(strtolower($option)) . '" ' . selected($value, strtolower($option), false) . '>' . esc_html($option) . '</option>'; }
            echo '</select>';
        } elseif ($meta_key === '_property_address') {
            echo '<textarea id="' . esc_attr($meta_key) . '" name="' . esc_attr($meta_key) . '" class="large-text">' . esc_textarea($value) . '</textarea>';
        } else {
             echo '<input type="text" id="' . esc_attr($meta_key) . '" name="' . esc_attr($meta_key) . '" class="regular-text" value="' . esc_attr($value) . '">';
        }
        echo '</td></tr>';
    }
}

function c360_render_property_side_fields($post_id, $fields) {
     foreach ($fields as $meta_key => $label) {
        $value = get_post_meta($post_id, $meta_key, true);
        echo '<p><label for="' . esc_attr($meta_key) . '"><strong>' . esc_html($label) . '</strong></label></p>';

        if ($meta_key === '_is_verified' || $meta_key === '_has_photos') {
            echo '<p><input type="checkbox" id="' . esc_attr($meta_key) . '" name="' . esc_attr($meta_key) . '" value="yes" ' . checked($value, 'yes', false) . '></p>';
        } elseif ($meta_key === '_description' || $meta_key === '_internal_notes' || $meta_key === '_property_photos') {
            echo '<p><textarea id="' . esc_attr($meta_key) . '" name="' . esc_attr($meta_key) . '" style="width:95%;" rows="4">' . esc_textarea($value) . '</textarea></p>';
             if($meta_key === '_property_photos') { echo '<p class="description">Comma-separated URLs</p>'; }
        } else {
            $type = ($meta_key === '_available_from') ? 'date' : 'text';
            $type = (strpos($meta_key, 'price') !== false || strpos($meta_key, 'pin_code') !== false || strpos($meta_key, 'age') !== false || strpos($meta_key, 'sqft') !== false || strpos($meta_key, 'rooms') !== false) ? 'number' : $type;
            $type = (strpos($meta_key, 'email') !== false) ? 'email' : $type;
            $type = (strpos($meta_key, 'phone') !== false) ? 'tel' : $type;
            echo '<p><input type="' . $type . '" id="' . esc_attr($meta_key) . '" name="' . esc_attr($meta_key) . '" value="' . esc_attr($value) . '" style="width:95%;"></p>';
        }
    }
}

function c360_render_property_checkboxes($post_id, $meta_key, $options) {
    $saved_values = get_post_meta($post_id, $meta_key, true);
    if (!is_array($saved_values)) { $saved_values = array(); }
    echo '<ul style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">';
    foreach($options as $option) {
        echo '<li><label>';
        echo '<input type="checkbox" name="' . esc_attr($meta_key) . '[]" value="' . esc_attr($option) . '" ' . checked(in_array($option, $saved_values), true, false) . '>';
        echo ' ' . esc_html($option);
        echo '</label></li>';
    }
    echo '</ul>';
}

// --- TASK LIST AND VIEW PAGE ---

/**
 * Adds a "View" link to the row actions for tasks.
 */
function c360_add_task_row_actions($actions, $post) {
    if ($post->post_type === 'task') {
        $view_url = add_query_arg(array('page' => 'client360_view_task', 'task_id' => $post->ID), admin_url('admin.php'));
        $new_actions = array();
        $new_actions['edit'] = $actions['edit'];
        $new_actions['inline hide-if-no-js'] = $actions['inline hide-if-no-js']; // Quick Edit
        $new_actions['view_task'] = '<a href="' . esc_url($view_url) . '">' . __('View', 'client360-crm') . '</a>';
        $new_actions['trash'] = $actions['trash'];
        return $new_actions;
    }
    return $actions;
}
add_filter('post_row_actions', 'c360_add_task_row_actions', 10, 2);

/**
 * Gets all the data for the read-only task view page.
 */
function c360_get_task_details_for_view($task_id) {
    $related_id = get_post_meta($task_id, '_task_related_to', true);
    $related_post = $related_id ? get_post($related_id) : null;
    $related_link = $related_post ? '<a href="' . get_edit_post_link($related_post->ID) . '">' . esc_html($related_post->post_title) . '</a>' : 'N/A';

    $status_terms = get_the_terms($task_id, 'task_status');
    $priority_terms = get_the_terms($task_id, 'task_priority');

    return array(
        'Related To' => get_post_meta($task_id, '_task_related_to_type', true),
        'Assignment' => $related_link,
        'Start Date' => get_post_meta($task_id, '_task_start_date', true),
        'End Date' => get_post_meta($task_id, '_task_end_date', true),
        'URL' => get_post_meta($task_id, '_task_url', true),
        'Status' => (!empty($status_terms) && !is_wp_error($status_terms)) ? $status_terms[0]->name : 'N/A',
        'Priority' => (!empty($priority_terms) && !is_wp_error($priority_terms)) ? $priority_terms[0]->name : 'N/A',
    );
}

// --- MEETING VIEW AND ACTIONS ---
function c360_add_meeting_row_actions($actions, $post) {
    if ($post->post_type === 'meeting_log') {
        $view_url = add_query_arg(array('page' => 'client360_view_meeting', 'meeting_id' => $post->ID), admin_url('admin.php'));
        $new_actions = array();
        $new_actions['edit'] = $actions['edit'];
        $new_actions['view_meeting'] = '<a href="' . esc_url($view_url) . '">' . __('View', 'client360-crm') . '</a>';
        $new_actions['trash'] = $actions['trash'];
        return $new_actions;
    }
    return $actions;
}
add_filter('post_row_actions', 'c360_add_meeting_row_actions', 10, 2);

function c360_get_meeting_details_for_view($meeting_id) {
    $related_id = get_post_meta($meeting_id, '_meeting_related_to', true);
    $related_post = $related_id ? get_post($related_id) : null;
    $related_link = $related_post ? '<a href="' . get_edit_post_link($related_post->ID) . '">' . esc_html($related_post->post_title) . '</a>' : 'N/A';

    $datetime = get_post_meta($meeting_id, '_meeting_datetime', true);

    return array(
        'Related To' => get_post_meta($meeting_id, '_meeting_related_to_type', true),
        'Attendees' => $related_link,
        'Location' => get_post_meta($meeting_id, '_meeting_location', true),
        'Date' => $datetime ? date_i18n(get_option('date_format'), strtotime($datetime)) : 'N/A',
        'Time' => $datetime ? date_i18n(get_option('time_format'), strtotime($datetime)) : 'N/A',
    );
}
// --- CALL LOG VIEW AND ACTIONS ---

/**
 * Adds a "View" link to the row actions for call logs.
 */
function c360_add_call_log_row_actions($actions, $post) {
    if ($post->post_type === 'call_log') {
        $view_url = add_query_arg(array('page' => 'client360_view_call_log', 'log_id' => $post->ID), admin_url('admin.php'));
        $new_actions = array();
        $new_actions['edit'] = $actions['edit'];
        $new_actions['view_log'] = '<a href="' . esc_url($view_url) . '">' . __('View', 'client360-crm') . '</a>';
        $new_actions['trash'] = $actions['trash'];
        return $new_actions;
    }
    return $actions;
}
add_filter('post_row_actions', 'c360_add_call_log_row_actions', 10, 2);

/**
 * Gets all the data for the read-only call log view page.
 */
function c360_get_call_details_for_view($log_id) {
    $related_id = get_post_meta($log_id, '_call_related_to', true);
    $related_post = $related_id ? get_post($related_id) : null;
    $related_link = $related_post ? '<a href="' . get_edit_post_link($related_post->ID) . '">' . esc_html($related_post->post_title) . '</a>' : 'N/A';

    return array(
        'Related To Type' => get_post_meta($log_id, '_call_related_to_type', true),
        'Recipient' => $related_link,
        'Start Date' => get_post_meta($log_id, '_call_start_date', true),
        'End Date' => get_post_meta($log_id, '_call_end_date', true),
        'Call Duration' => get_post_meta($log_id, '_call_duration', true),
    );
}

// --- EMAIL LOG VIEW AND ACTIONS ---

/**
 * Adds a "View" link to the row actions for email logs.
 */
function c360_add_email_log_row_actions($actions, $post) {
    if ($post->post_type === 'email_log') {
        $view_url = add_query_arg(array('page' => 'client360_view_email_log', 'log_id' => $post->ID), admin_url('admin.php'));
        $new_actions = array();
        $new_actions['edit'] = $actions['edit'];
        $new_actions['view_log'] = '<a href="' . esc_url($view_url) . '">' . __('View', 'client360-crm') . '</a>';
        $new_actions['trash'] = $actions['trash'];
        return $new_actions;
    }
    return $actions;
}
add_filter('post_row_actions', 'c360_add_email_log_row_actions', 10, 2);
/**
 * Gets all the data for the read-only email log view page.
 */
function c360_get_email_log_details_for_view($log_id) {
    $related_id = get_post_meta($log_id, '_email_related_to', true);
    $related_post = $related_id ? get_post($related_id) : null;
    $related_link = $related_post ? '<a href="' . get_edit_post_link($related_post->ID) . '">' . esc_html($related_post->post_title) . '</a>' : 'N/A';

    return array(
        'Related To Type' => get_post_meta($log_id, '_email_related_to_type', true),
        'Recipient' => $related_link,
        'Date' => get_post_meta($log_id, '_email_start_date', true),
    );
}
// --- DOCUMENT EXPLORER FUNCTIONS ---

/**
 * Generates the HTML for the file explorer view.
 */
function c360_get_file_explorer_html() {
    $html = '<ul style="list-style-type: none; padding-left: 0;">';
    $folders = get_terms(['taxonomy' => 'document_folder', 'hide_empty' => false, 'parent' => 0]);

    // List top-level folders
    foreach ($folders as $folder) {
        $html .= '<li style="margin-bottom: 10px;">';
        $html .= '<span class="dashicons dashicons-category"></span> <strong>' . esc_html($folder->name) . '</strong>';
        $html .= c360_get_docs_in_folder_html($folder->term_id);
        $html .= '</li>';
    }

    // List files not in any folder
    $files_no_folder = get_posts(['post_type' => 'document', 'numberposts' => -1, 'tax_query' => [
        ['taxonomy' => 'document_folder', 'operator' => 'NOT EXISTS']
    ]]);

    foreach ($files_no_folder as $file) {
        $file_url = get_post_meta($file->ID, '_c360_file_url', true);
        $html .= '<li style="margin-left: 20px;">';
        $html .= '<span class="dashicons dashicons-media-default"></span> ';
        $html .= '<a href="' . esc_url($file_url) . '" target="_blank">' . esc_html($file->post_title) . '</a>';
        $html .= ' <a href="' . get_delete_post_link($file->ID, '', true) . '" style="color:red; text-decoration:none;" onclick="return confirm(\'Are you sure you want to delete this file?\')">&times;</a>';
        $html .= '</li>';
    }

    $html .= '</ul>';
    return $html;
}

/**
 * Helper to get documents within a specific folder.
 */
function c360_get_docs_in_folder_html($folder_id) {
    $html = '<ul style="margin-left: 20px; list-style-type: none;">';
    $files = get_posts(['post_type' => 'document', 'numberposts' => -1, 'tax_query' => [
        ['taxonomy' => 'document_folder', 'field' => 'term_id', 'terms' => $folder_id]
    ]]);

    foreach ($files as $file) {
         $file_url = get_post_meta($file->ID, '_c360_file_url', true);
         $html .= '<li>';
         $html .= '<span class="dashicons dashicons-media-default"></span> ';
         $html .= '<a href="' . esc_url($file_url) . '" target="_blank">' . esc_html($file->post_title) . '</a>';
         $html .= ' <a href="' . get_delete_post_link($file->ID, '', true) . '" style="color:red; text-decoration:none;" onclick="return confirm(\'Are you sure you want to delete this file?\')">&times;</a>';
         $html .= '</li>';
    }
    $html .= '</ul>';
    return $html;
}
/**
 * Get lead creation data for the last 10 days for a chart.
 */
function c360_get_daily_leads_chart_data() {
    $dates = [];
    $counts = [];
    for ($i = 9; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $dates[] = date('M d', strtotime($date));

        $args = array(
            'post_type' => 'lead',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'date_query' => array(
                array(
                    'year'  => date('Y', strtotime($date)),
                    'month' => date('m', strtotime($date)),
                    'day'   => date('d', strtotime($date)),
                ),
            ),
        );
        $query = new WP_Query($args);
        $counts[] = $query->post_count;
    }
    return array('labels' => $dates, 'data' => $counts);
}
// --- EMPLOYEE LOGIN SECURITY ---

/**
 * Prevents employees with an 'inactive' status from logging in.
 *
 * @param WP_User $user The user object.
 * @param string $password The password entered by the user.
 * @return WP_User|WP_Error The user object if login is allowed, or a WP_Error if blocked.
 */
function c360_block_inactive_employee_login( $user, $password ) {
    // Check if the user object is valid and has the employee role.
    if ( isset( $user->roles ) && is_array( $user->roles ) && in_array( 'c360_employee', $user->roles ) ) {
        // Check the custom status meta field.
        $status = get_user_meta( $user->ID, 'c360_status', true );
        if ( $status === 'inactive' ) {
            // If inactive, return an error to block the login.
            return new WP_Error( 'c360_inactive_user', __( '<strong>ERROR</strong>: Your account has been deactivated. Please contact Admin!', 'client360-crm' ) );
        }
    }
    return $user;
}
add_filter( 'wp_authenticate_user', 'c360_block_inactive_employee_login', 10, 2 );