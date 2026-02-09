<?php
/**
 * Customer.io WordPress Plugin
 *
 * @wordpress-plugin
 * Plugin Name: Customer.io integration
 * Version:     0.1.0
 * Description: Track user activity and send it to Customer.io for better marketing automation.
 * Plugin URI:  https://coeditor.com
 * Author:      CoEditor / Matt Tompkins 
 * Author URI:  https://coeditor.com
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CUSTOMER_IO_PLUGIN_VERSION', '0.2.0' );
define( 'CUSTOMER_IO_CACHE_VERSION', '1.0' );
define( 'CUSTOMER_IO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CUSTOMER_IO_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'CUSTOMER_IO_PLUGIN_INC', CUSTOMER_IO_PLUGIN_PATH . 'includes/' );
define( 'CUSTOMER_IO_PLUGIN_ASSETS', CUSTOMER_IO_PLUGIN_URL . 'assets/' );

/**
 * IMPORTANT FIX:
 * This MUST be a filesystem path, not a URL
 */
define( 'CUSTOMER_IO_PLUGIN_PAGES', CUSTOMER_IO_PLUGIN_PATH . 'admin-pages/' );

/**
 * The primary class for all Customer.io plugin logic
 */
class CustomerIOPlugin {

	/**
	 * Initialise the plugin and its hooks
	 */
	public function init() {
	
		if ( file_exists( CUSTOMER_IO_PLUGIN_PATH . 'vendor/autoload.php' ) ) {
			require_once CUSTOMER_IO_PLUGIN_PATH . 'vendor/autoload.php';
		}

		add_action( 'rest_api_init', [ $this, 'register_rest' ] );

		$this->register_classes();
	}

	/**
	 * Register all classes for the plugin
	 * 
	 * @return void
	 */
	public function register_classes() {
		$classes = [
			\CustomerIO\AdminPages::class,
			\CustomerIO\Settings::class,
			\CustomerIO\TrackingScripts::class,
		];

		foreach ( $classes as $class ) {
			$instance = new $class();
			if ( method_exists( $instance, 'register' ) ) {
				$instance->register();
			}
		}
	}

	/**
	 * Register REST API endpoints
	 *
	 * @return void
	 */
	public function register_rest() {
		$endpoint_factory = new \CustomerIO\Rest\EndpointFactory();
		$endpoint_factory->register();
	}
}

if ( class_exists( 'CustomerIOPlugin' ) ) {
	$plugin = new CustomerIOPlugin();
	$plugin->init();
}
