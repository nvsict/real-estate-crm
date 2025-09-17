<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Client360_CRM
 * @subpackage Client360_CRM/includes
 */
class Client360_Activator {

	/**
	 * Main activation method.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		self::create_default_lead_statuses();
	}

	/**
	 * Create default lead statuses.
	 */
	private static function create_default_lead_statuses() {
		$statuses = array(
			'NYC'          => 'New/Yet to Contact',
			'Not answered' => 'Lead has been called but did not answer.',
			'Qualified'    => 'Lead has been contacted and is a potential customer.',
			'Successful'   => 'Lead has been converted to a customer.',
			'Agents'       => 'Lead is another agent or internal contact.',
			'Waste'        => 'Lead is not a potential customer.',
		);

		foreach ( $statuses as $status => $description ) {
			// Check if term already exists
			if ( ! term_exists( $status, 'lead_status' ) ) {
				wp_insert_term(
					$status,
					'lead_status',
					array(
						'description' => $description,
						'slug'        => sanitize_title( $status ),
					)
				);
			}
		}
	}

}

