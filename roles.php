<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Add custom user roles and assign capabilities for the CRM.
 * This function runs on init to ensure capabilities are always correct.
 */
function c360_add_roles_and_caps() {
    // --- Define capabilities for all CRM post types ---
    $cpt_caps = [
        'lead'        => ['lead', 'leads'],
        'contact'     => ['contact', 'contacts'],
        'campaign'    => ['campaign', 'campaigns'],
        'property'    => ['property', 'properties'],
        'task'        => ['task', 'tasks'],
        'meeting_log' => ['meeting_log', 'meeting_logs'],
        'call_log'    => ['call_log', 'call_logs'],
        'email_log'   => ['email_log', 'email_logs'],
        'payment'     => ['payment', 'payments'],
        'document'    => ['document', 'documents'],
    ];

    // --- Grant all capabilities to the Administrator Role ---
    $admin_role = get_role('administrator');
    if ($admin_role) {
        foreach ($cpt_caps as $cpt) {
            $admin_role->add_cap("edit_{$cpt[0]}");
            $admin_role->add_cap("read_{$cpt[0]}");
            $admin_role->add_cap("delete_{$cpt[0]}");
            $admin_role->add_cap("edit_{$cpt[1]}");
            $admin_role->add_cap("edit_others_{$cpt[1]}");
            $admin_role->add_cap("publish_{$cpt[1]}");
            $admin_role->add_cap("read_private_{$cpt[1]}");
            $admin_role->add_cap("delete_{$cpt[1]}");
            $admin_role->add_cap("delete_private_{$cpt[1]}");
            $admin_role->add_cap("delete_published_{$cpt[1]}");
            $admin_role->add_cap("delete_others_{$cpt[1]}");
            $admin_role->add_cap("edit_private_{$cpt[1]}");
            $admin_role->add_cap("edit_published_{$cpt[1]}");
        }
    }

    // --- Re-create the Employee Role to ensure it's clean ---
    if (get_role('c360_employee')) {
        remove_role('c360_employee');
    }

    add_role(
        'c360_employee',
        __( 'Employee', 'client360-crm' ),
        array(
            'read'         => true,
            'edit_posts'   => true,
            'delete_posts' => true,
            'upload_files' => true,
        )
    );
    
    // Grant CPT capabilities to the newly created employee role
    $employee_role = get_role('c360_employee');
     if ($employee_role) {
        foreach ($cpt_caps as $cpt) {
            $employee_role->add_cap("edit_{$cpt[1]}");
            $employee_role->add_cap("publish_{$cpt[1]}");
            $employee_role->add_cap("delete_{$cpt[1]}");
            $employee_role->add_cap("read_private_{$cpt[1]}");
            $employee_role->add_cap("delete_published_{$cpt[1]}");
            $employee_role->add_cap("edit_published_{$cpt[1]}");
        }
    }
}
add_action( 'init', 'c360_add_roles_and_caps' );

