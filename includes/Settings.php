<?php
/**
 * Class Settings
 */

namespace CustomerIO;


/**
 * Class for managing the settings page fields and options
 */
class Settings {

	/**
	 * Fire hooks and actions
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_init', [ $this, 'customerio_register_settings' ] );
	}

	/**
	 * Get the various sites to configure the customer.io plugin for
	 *
	 * @return void
	 */
	public static function get_customerio_sites() {
		return [
			[ 'id' => 'spectator_com', 'name' => 'Spectator.com' ],
			[ 'id' => 'spectator_aus', 'name' => 'Spectator Australia' ],
			[ 'id' => 'unherd', 'name' => 'UnHerd' ],
			[ 'id' => 'apollo', 'name' => 'Apollo' ],
		];
	}

	/**
	 * Registers custom WordPress settings used in the plugin
	 *
	 * @return void
	 */
	function customerio_register_settings() {
		register_setting( 'customerio_settings_group', 'customerio_api_key' );
		register_setting( 'customerio_settings_group', 'customerio_site_id' );
		register_setting( 'customerio_settings_group', 'customerio_track_api_key' );
		register_setting( 'customerio_settings_group', 'customerio_selected_site' );
	}
}
