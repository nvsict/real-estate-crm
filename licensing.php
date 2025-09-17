<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Handles all license key validation and status checking.
 */
class Client360_License_Manager {

    const OPTION_NAME = 'c360_license_data';
    const DEMO_DURATION = 7 * DAY_IN_SECONDS;

    // --- YOUR PYTHON-GENERATED HASHES ---
    const DEMO_KEY_HASH = 'a0b58955cc0dcbac6e46e925ed9183a2b71271246c2d14283036e5983e8d4917';
    const FULL_KEY_HASH = 'fc21b7b3ded9d0b5a7deb4fd86c570786b8761721e46cd83dc6f69227450dfb5';

    private $license_data;
    private $status;


    public function __construct() {
        $this->license_data = get_option(self::OPTION_NAME, array(
            'key' => '',
            'activation_date' => null,
            'status' => 'invalid'
        ));
        $this->status = $this->check_license();
    }

    /**
     * Checks the current license status by hashing the stored key and comparing it.
     * @return string 'valid', 'demo', 'expired', 'invalid'
     */
    public function check_license() {
        $key = isset($this->license_data['key']) ? $this->license_data['key'] : '';
        if (empty($key)) {
            return 'invalid';
        }

        $hashed_key_check = hash('sha256', $key);

        // Check for full license
        if ($hashed_key_check === self::FULL_KEY_HASH) {
            return 'valid';
        }

        // Check for demo license
        if ($hashed_key_check === self::DEMO_KEY_HASH) {
            $activation_date = isset($this->license_data['activation_date']) ? (int) $this->license_data['activation_date'] : 0;
            // Use WordPress's robust current_time function for accurate comparison
            if ( (current_time('timestamp') - $activation_date) > self::DEMO_DURATION ) {
                return 'expired';
            }
            return 'demo';
        }

        return 'invalid';
    }

    /**
     * Gets the current license status.
     * @return string
     */
    public function get_status() {
        return $this->status;
    }

    /**
     * Gets the remaining days for a demo license.
     * @return int
     */
    public function get_demo_days_remaining() {
        if ($this->get_status() !== 'demo') {
            return 0;
        }
        $activation_date = (int) $this->license_data['activation_date'];
        $elapsed = current_time('timestamp') - $activation_date;
        $remaining_seconds = self::DEMO_DURATION - $elapsed;
        return max(0, ceil($remaining_seconds / DAY_IN_SECONDS));
    }

    /**
     * Activates a new license key.
     * @param string $new_key The new license key to activate.
     * @return bool True on success, false on failure.
     */
    public function activate_license($new_key) {
        $new_key = trim($new_key);
        $hashed_key_check = hash('sha256', $new_key);

        $new_data = array(
            'key' => $new_key,
            'activation_date' => null,
            'status' => 'invalid'
        );

        // Activating a full license
        if ($hashed_key_check === self::FULL_KEY_HASH) {
            $new_data['status'] = 'valid';
            update_option(self::OPTION_NAME, $new_data);
            return true;
        }

        // Activating a demo license
        if ($hashed_key_check === self::DEMO_KEY_HASH) {
            if ( empty($this->license_data['activation_date']) || $this->license_data['key'] !== $new_key ) {
                $new_data['status'] = 'demo';
                // Use WordPress's robust current_time function for accurate timestamping
                $new_data['activation_date'] = current_time('timestamp');
                update_option(self::OPTION_NAME, $new_data);
                return true;
            }
        }
        
        // If the key is invalid, clear the existing data.
        delete_option(self::OPTION_NAME);
        return false;
    }
    
    /**
     * Returns the raw license key.
     * @return string
     */
    public function get_key() {
        return isset($this->license_data['key']) ? $this->license_data['key'] : '';
    }
}

