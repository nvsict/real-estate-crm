<?php
/**
 * Plugin Name:       Real Estate CRM
 * Plugin URI:        https://github.com/yourusername/real-estate-crm
 * Description:       A comprehensive CRM tailored for real estate businesses, built for WordPress.
 * Version:           1.0.0
 * Author:            Mohit Tarkar
 * Author URI:        https://wa.me/918384844353
 * License:           Proprietary
 * Text Domain:       real-estate-crm
 *
 * @copyright  2025 Mohit Tarkar
 *
 * This software is licensed under a proprietary license.
 * You may not modify, distribute, sublicense, or sell this software
 * without explicit written permission from the copyright holder.
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'C360_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'C360_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Require the licensing class immediately
require_once C360_PLUGIN_DIR . 'includes/licensing.php';

/**
 * The core plugin class.
 */
final class Client360_CRM {

    protected static $_instance = null;
    public $license_manager;

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        // 1. Instantiate the license manager first.
        $this->license_manager = new Client360_License_Manager();
        
        // 2. Load dependencies based on license status.
        $this->load_dependencies();

        // 3. Initialize hooks.
        $this->init_hooks();
    }

    public function load_dependencies() {
        // Always load the settings page so users can enter a license.
        require_once C360_PLUGIN_DIR . 'includes/settings.php';
        require_once C360_PLUGIN_DIR . 'includes/admin-pages.php';
        
        // Only load the rest of the plugin if the license is valid.
        if ( $this->is_license_active() ) {
            require_once C360_PLUGIN_DIR . 'includes/activator.php';
            require_once C360_PLUGIN_DIR . 'includes/roles.php';
            require_once C360_PLUGIN_DIR . 'includes/post-types.php';
            require_once C360_PLUGIN_DIR . 'includes/metaboxes.php';
            require_once C360_PLUGIN_DIR . 'includes/functions.php';
            require_once C360_PLUGIN_DIR . 'includes/lead-converter.php';
            require_once C360_PLUGIN_DIR . 'includes/bulk-uploader.php';
            // This line is crucial for the Employee Management page to work.
            require_once C360_PLUGIN_DIR . 'includes/class-employees-list-table.php';
        }
    }

    private function init_hooks() {
        // Always load settings page hooks.
        add_action('admin_init', 'c360_register_settings');

        if ( $this->is_license_active() ) {
            register_activation_hook( __FILE__, array( 'Client360_Activator', 'activate' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        } else {
            // Show license notice on all admin pages if not active.
            add_action( 'admin_notices', array($this, 'show_license_notice') );
        }
    }
    
    public function is_license_active() {
        $status = $this->license_manager->get_status();
        return ( $status === 'valid' || $status === 'demo' );
    }

    public function show_license_notice() {
        $status = $this->license_manager->get_status();
        $settings_url = admin_url('admin.php?page=client360_settings');

        if ($status === 'invalid') {
            $message = sprintf(
                __('Please <a href="%s">enter a valid license key</a> to use Client 360 CRM.', 'client360-crm'),
                esc_url($settings_url)
            );
        } elseif ($status === 'expired') {
            $message = sprintf(
                __('Your demo license for Client 360 CRM has expired. Please <a href="%s">enter a full license key</a> to continue.', 'client360-crm'),
                esc_url($settings_url)
            );
        } else {
            return;
        }

        // Ensure the notice is only shown on relevant pages.
        $screen = get_current_screen();
        if ($screen && strpos($screen->id, 'client360') !== false) {
             echo '<div class="notice notice-error"><p>' . $message . '</p></div>';
        }
    }

    /**
 * Enqueue styles for the admin area.
 */
public function enqueue_styles( $hook ) {
    // Dashboard page CSS
    if ( 'toplevel_page_client360_dashboard' === $hook ) {
        wp_enqueue_style(
            'c360-admin-dashboard', 
            C360_PLUGIN_URL . 'assets/css/admin-style.css'
        );
    }

    // Calendar page CSS
    if ( 'client360_page_client360_calendar' === $hook ) {
        wp_enqueue_style(
            'fullcalendar', 
            'https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css'
        );
    }
}

    /**
     * Enqueue scripts for the admin area.
     */
    public function enqueue_scripts( $hook ) {
        global $post_type;

        // Load our custom admin script on the Lead list page
        if ( 'edit.php' === $hook && 'lead' === $post_type ) {
            wp_enqueue_script( 'c360-admin-script', C360_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), '1.0.2', true );
        }

        // For the dashboard chart
if ( 'toplevel_page_client360_dashboard' === $hook ) {
    wp_enqueue_script( 'chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', array(), null, true );

    $report_data = c360_get_lead_status_chart_data();
    $daily_data = c360_get_daily_leads_chart_data();

    wp_add_inline_script( 'chartjs', '
        document.addEventListener("DOMContentLoaded", function() {
            // Report Chart (Leads by Status)
            var reportCtx = document.getElementById("leadReportChart").getContext("2d");
            var reportChart = new Chart(reportCtx, {
                type: "bar",
                data: {
                    labels: ' . json_encode($report_data['labels']) . ',
                    datasets: [{
                        label: "Lead Count",
                        data: ' . json_encode($report_data['data']) . ',
                        backgroundColor: "rgba(139, 92, 246, 0.5)",
                        borderColor: "rgba(139, 92, 246, 1)",
                        borderWidth: 1
                    }]
                },
                options: { scales: { y: { beginAtZero: true } } }
            });

            // Daily Leads Chart
            var dailyCtx = document.getElementById("dailyLeadsChart").getContext("2d");
            var dailyChart = new Chart(dailyCtx, {
                type: "line",
                data: {
                    labels: ' . json_encode($daily_data['labels']) . ',
                    datasets: [{
                        label: "New Leads per Day",
                        data: ' . json_encode($daily_data['data']) . ',
                        backgroundColor: "rgba(59, 130, 246, 0.2)",
                        borderColor: "rgba(59, 130, 246, 1)",
                        borderWidth: 2,
                        tension: 0.1
                    }]
                },
                options: { scales: { y: { beginAtZero: true } } }
            });

            // Download functionality
            document.getElementById("downloadLeadReport").addEventListener("click", function(){
                var url = reportChart.toBase64Image();
                var a = document.createElement("a");
                a.href = url;
                a.download = "lead-report.png";
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            });
        });
    ' );
}
        // For the calendar page
        if ( 'client360_page_client360_calendar' === $hook ) {
            wp_enqueue_script( 'fullcalendar', 'https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js', array(), null, true );
            $events = c360_get_calendar_events();
            wp_add_inline_script( 'fullcalendar', '
                document.addEventListener("DOMContentLoaded", function() {
                    var calendarEl = document.getElementById("c360-calendar");
                    var calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: "dayGridMonth",
                        headerToolbar: {
                            left: "prev,next today",
                            center: "title",
                            right: "dayGridMonth,timeGridWeek,timeGridDay"
                        },
                        events: ' . json_encode($events) . '
                    });
                    calendar.render();
                });
            ' );
        }
    }
}


/**
 * Begins execution of the plugin.
 *
 * The main instance of the plugin is stored in a global variable.
 * This is done so that it can be accessed from the settings page.
 */
function client360_crm_run() {
    global $client360_crm;
    if (!isset($client360_crm)) {
        $client360_crm = Client360_CRM::instance();
    }
    return $client360_crm;
}
client360_crm_run();

