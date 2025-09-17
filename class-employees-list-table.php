<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class C360_Employees_List_Table extends WP_List_Table {

    public function __construct() {
        parent::__construct([
            'singular' => __('Employee', 'client360-crm'),
            'plural'   => __('Employees', 'client360-crm'),
            'ajax'     => false
        ]);
    }

    public function get_columns() {
        return [
            'cb'          => '<input type="checkbox" />',
            'first_name'  => __('First Name', 'client360-crm'),
            'last_name'   => __('Last Name', 'client360-crm'),
            'phone'       => __('Phone Number', 'client360-crm'),
            'email'       => __('Email Address', 'client360-crm'),
            'status'      => __('Status', 'client360-crm'),
            'action'      => __('Action', 'client360-crm'),
        ];
    }

    public function prepare_items() {
        $columns = $this->get_columns();
        $hidden = [];
        $sortable = [];
        $this->_column_headers = [$columns, $hidden, $sortable];
        
        $args = ['role' => 'c360_employee'];

        // Search
        $search_term = isset($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : '';
        if (!empty($search_term)) {
            $args['search'] = '*' . $search_term . '*';
            $args['search_columns'] = ['user_login', 'user_email', 'display_name'];
        }

        $user_query = new WP_User_Query($args);
        $this->items = $user_query->get_results();
    }

    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'first_name':
                return $item->first_name;
            case 'last_name':
                return $item->last_name;
            case 'phone':
                 return get_user_meta($item->ID, 'billing_phone', true) ?: 'N/A';
            case 'email':
                return '<a href="mailto:' . esc_attr($item->user_email) . '">' . esc_html($item->user_email) . '</a>';
            case 'status':
                $status = get_user_meta($item->ID, 'c360_status', true);
                if ($status === 'inactive') {
                    return '<span style="color:red;">Inactive</span>';
                }
                return '<span style="color:green;">Active</span>';
            case 'action':
                $nonce_status = wp_create_nonce('c360_change_status_nonce_' . $item->ID);
                $nonce_pass = wp_create_nonce('c360_change_pass_nonce_' . $item->ID);

                $status = get_user_meta($item->ID, 'c360_status', true);
                $new_status = ($status === 'inactive') ? 'active' : 'inactive';
                
                $status_url = add_query_arg([
                    'page' => 'client360_employee_management',
                    'action' => 'c360_change_status',
                    'user_id' => $item->ID,
                    'new_status' => $new_status,
                    '_wpnonce' => $nonce_status
                ], admin_url('admin.php'));

                $pass_url = add_query_arg([
                    'page' => 'client360_employee_management',
                    'action' => 'c360_change_password',
                    'user_id' => $item->ID,
                    '_wpnonce' => $nonce_pass
                ], admin_url('admin.php'));

                return sprintf(
                    '<a href="%s" class="button">%s</a> <a href="%s" class="button" onclick="return c360_change_password_prompt(event);">%s</a>',
                    esc_url($status_url),
                    __('Change Status', 'client360-crm'),
                    esc_url($pass_url),
                    __('Change Password', 'client360-crm')
                );
            default:
                return '---';
        }
    }
    
    public function column_cb($item) {
        return sprintf('<input type="checkbox" name="employee[]" value="%s" />', $item->ID);
    }
}

